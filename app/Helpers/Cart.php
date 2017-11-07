<?php

namespace App\Helpers;

use App\Beverage;
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
    const BEVERAGE_KEY = 'beverage';

    const VAT = 0.12;

    public static function getTotal()
    {
        return collect([
            self::getPremade()->sum('total_amount'),
            self::getCustom()->sum('total_amount'),
            self::getBeverages()->sum('amount'),
        ])->sum();
    }

    public static function getGrossAmount()
    {
        $total = self::getTotal();
        return $total - self::getVatable();
    }

    public static function getVatable()
    {
        return self::getTotal() * self::VAT;
    }

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
            self::BEVERAGE_KEY => self::getBeveragesRaw(),
        ];
    }

    public static function getItemCount()
    {
        return self::getPremadeOrdersRaw()->count() + self::getCustomOrdersRaw()->count();
    }

    public static function pizzaCount()
    {
        return self::getPremadeOrdersRaw()->sum('quantity') + self::getCustomOrdersRaw()->sum('quantity');
    }

    public static function clear()
    {
        Session::put(self::CUSTOM_ORDER_KEY, []);

        Session::put(self::ORDER_KEY, []);

        Session::put(self::BEVERAGE_KEY, []);
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
        switch ($orderType) {
            case 'PREMADE':
                return self::ORDER_KEY;

            case 'BEVERAGE':
                return self::BEVERAGE_KEY;

            case 'CUSTOM':
                return self::CUSTOM_ORDER_KEY;
        }

        return null;
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

    public static function getBeverages()
    {
        $beverages = self::getBeveragesRaw();

        if ($beverages->isEmpty()) {
            return collect();
        }

        return Beverage::find($beverages->keys())->map(function ($beverage) use ($beverages) {
            $ordered = $beverages->get($beverage->id);
            $beverage->ordered_quantity = $ordered['quantity'];
            $beverage->amount = floatval($beverage->unit_price) * intval($beverage->ordered_quantity);
            return $beverage;
        });
    }

    public static function getBeveragesRaw()
    {
        return collect(Session::get(self::BEVERAGE_KEY) ?: []);
    }

    public static function addBeverage($beverageId, $quantity)
    {
        $beverages = Session::get(self::BEVERAGE_KEY);

        if (isset($beverages[$beverageId])) {
            $beverages[$beverageId]['quantity'] += $quantity;
        } else {
            $beverages[$beverageId] = [
                'quantity' => $quantity,
                'beverage_id' => $beverageId,
            ];
        }

        Session::put(self::BEVERAGE_KEY, $beverages);
    }

    public static function saveBeveragesTo(Order $order)
    {
        $beverages = self::getBeveragesRaw()->mapWithKeys(function ($beverage, $id) {
            return [
                $id => [
                    'quantity' => $beverage['quantity'],
                ],
            ];
        });

        $order->beverages()->attach($beverages);
    }

}
