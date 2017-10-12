<?php

namespace App\Helpers;

use App\IngredientCategory;
use App\Order;
use App\PizzaSize;
use App\PremadePizzaOrderDetail;
use DB;
use Session;

class Cart
{
    const CUSTOM_ORDER_KEY = 'custom_orders';
    const ORDER_KEY = 'orders';

    public static function getPremade()
    {
        $data = self::getPremadeOrdersRaw()->pluck('quantity', 'pizza_size_id');

        if ($data->isEmpty()) {
            return $data;
        }

        return PizzaSize::select('id', 'size', 'pizza_id', 'unit_price')
            ->whereIn('id', $data->keys())
            ->with(['pizza' => function ($query) {
                $query->select('id', 'name', 'photo');
            }])
            ->get()
            ->each(function (&$pizza) use ($data) {
                $pizza->ordered_quantity = $data->get($pizza->id);
                $pizza->total_amount = floatval($pizza->ordered_quantity) * floatval($pizza->unit_price);
            });
    }

    public static function getCustom()
    {
        $data = self::getCustomOrdersRaw();
        if ($data->isEmpty()) {
            return $data;
        }
        return $data->mapWithKeys(function ($item, $id) {
            $order = collect($item['item']);
            $items = IngredientCategory::select('id', 'description', 'custom_pizza_sequence')
                ->whereIn('id', $order->pluck('category_id'))
                ->with(['ingredients' => function ($query) use ($order) {
                    $query->whereIn('id', $order->pluck('items')->flatten());
                }])
                ->get();

            $size = $item['size'];

            $amount = $items->sum(function ($category) use ($size) {
                $size = strtolower($size);
                return $category->ingredients->sum("custom_unit_price_{$size}");
            });

            return [
                $id => [
                    'items' => $items,
                    'ordered_quantity' => $item['quantity'],
                    'unit_price' => $amount,
                    'total_amount' => $item['quantity'] * $amount,
                    'size' => $size,
                ],
            ];
        });
    }

    public static function getPremadeOrdersRaw()
    {
        return collect(Session::get(self::ORDER_KEY) ?: []);
    }

    public static function getCustomOrdersRaw()
    {
        return collect(Session::get(self::CUSTOM_ORDER_KEY) ?: []);
    }

    public static function savePremadePizzaTo(Order $order)
    {
        $premadeOrders = self::getPremadeOrdersRaw();
        $premadeOrders->when($premadeOrders->isNotEmpty(), function ($orders) use ($order) {
            $ordered = $orders->map(function ($pizza) {
                return new PremadePizzaOrderDetail(array_only($pizza, ['pizza_size_id', 'quantity']));
            });
            $order->premadePizzaOrderDetails()->saveMany($ordered);
        });
        return new static;
    }

    public static function saveCustomPizzaTo(Order $order)
    {
        $customOrders = self::getCustomOrdersRaw();
        $customOrders->when($customOrders->isNotEmpty(), function ($orders) use ($order) {
            $orders->each(function ($pizza) use ($order) {
                $custom = $order->customPizzaOrder()->create(array_only($pizza, ['size', 'quantity']));

                $ingredients = array_map(function ($id) {
                    return ['ingredient_id' => $id];
                }, array_flatten(array_column($pizza['item'], 'items')));

                $custom->usedIngredients()->createMany($ingredients);
                // $custom->decrementStocks();
            });
        });
        return new static;
    }

    public static function thenClearPremade()
    {
        Session::put(self::ORDER_KEY, []);
        return new static;
    }

    public static function thenClearCustom()
    {
        Session::put(self::CUSTOM_ORDER_KEY, []);
        return new static;
    }

    public static function addCustomOrder($customOrder)
    {
        $orders = Session::get(self::CUSTOM_ORDER_KEY);
        $item = [str_random(4) => $customOrder];
        if (!$orders) {
            Session::put(self::CUSTOM_ORDER_KEY, $item);
            return;
        }
        $orders += $item;
        Session::put(self::CUSTOM_ORDER_KEY, $orders);
    }

    public static function updateCustomOrder($customOrder)
    {
        $orders = Session::get(self::CUSTOM_ORDER_KEY);
        if (isset($orders[$customOrder['id']])) {
            $orders[$customOrder['id']] = array_except($customOrder, 'id');
            Session::put(self::CUSTOM_ORDER_KEY, $orders);
        }
    }

    public static function addOrder($order)
    {
        $orders = Session::get(self::ORDER_KEY);
        $item = [$order['pizza_size_id'] => $order];

        if (!$orders) {
            Session::put(self::ORDER_KEY, $item);
            return;
        }

        if (isset($orders[$order['pizza_size_id']])) {
            $orders[$order['pizza_size_id']]['quantity'] += $order['quantity'];
        } else {
            $orders += $item;
        }
        Session::put(self::ORDER_KEY, $orders);
    }

    public static function all()
    {
        return [
            self::CUSTOM_ORDER_KEY => self::getCustomOrdersRaw(),
            self::ORDER_KEY => self::getPremadeOrdersRaw(),
        ];
    }

    public static function getItemCount()
    {
        return self::getPremadeOrdersRaw()->count() + self::getCustomOrdersRaw()->count();
    }

    public static function clear()
    {
        Session::put(self::CUSTOM_ORDER_KEY, []);

        Session::put(self::ORDER_KEY, []);
    }

    public static function remove($orderType, $id)
    {
        $key = self::getKey($orderType);

        $orders = Session::get($key);

        unset($orders[$id]);

        Session::put($key, $orders);
    }

    public static function updateQuantity($orderType, $id, $quantity)
    {
        $key = self::getKey($orderType);

        $orders = Session::get($key);

        if (isset($orders[$id])) {
            $orders[$id]['quantity'] = $quantity;
        }

        Session::put($key, $orders);
    }

    private static function getKey($orderType)
    {
        return $orderType === 'PREMADE' ? self::ORDER_KEY : self::CUSTOM_ORDER_KEY;
    }

    public static function get($key, $itemType)
    {
        $orders = Session::get(self::getKey($itemType));
        if (isset($orders[$key])) {
            return $orders[$key];
        }
        return null;
    }

    public static function getErrorsFromCustomPizzas()
    {
        $errors = [];
        $custom = self::getCustomOrdersRaw();
        // $usedIngredients = array_flatten($custom->pluck('item.items'));
        //
        $stock = DB::table('ingredients')->get()->pluck(null, 'id');

        $custom->each(function ($pizza, $key) use ($stock, &$errors) {
            collect($pizza['item'])->each(function ($category) use ($pizza, $stock, &$errors, $key) {
                collect($category['items'])->each(function ($ingredient) use ($pizza, $stock, &$errors, $key) {
                    $onHand = $stock->get($ingredient);
                    $size = strtolower($pizza['size']);
                    $needed = ($onHand->{"custom_quantity_needed_{$size}"}) * $pizza['quantity'];
                    if (!$onHand || $onHand->remaining_quantity < $needed) {
                        $message = "{$onHand->description} out of stock 😭";
                        // $message = "{$onHand->description} needs: {$needed}, remaining: {$onHand->remaining_quantity}";
                        if (isset($errors[$key])) {
                            $errors[$key][] = $message;
                        } else {
                            $errors[$key] = [$message];
                        }

                    }
                });
            });
        });

        return $errors;

    }

}
