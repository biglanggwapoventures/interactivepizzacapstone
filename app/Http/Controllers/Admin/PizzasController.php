<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PizzaRequest;
use App\Pizza;
use Illuminate\Http\Request;

class PizzasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stocks = Pizza::getSellableQuantities();
        return view('admin.pizzas.index', [
            'items' => Pizza::with('sizes')->get(),
            'stocks' => $stocks,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.pizzas.manage', [
            'data' => new Pizza,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PizzaRequest $request)
    {
        $pizzaInput = array_except($request->validated(), 'photo');

        $pizzaInput['photo'] = $request->file('photo')->store(
            'pizzas', 'public'
        );

        $pizza = Pizza::create($pizzaInput);

        return redirect()->route('pizzas.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Pizza $pizza)
    {
        return view('admin.pizzas.manage', [
            'data' => $pizza,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PizzaRequest $request, $id)
    {
        $pizzaInput = array_except($request->validated(), 'photo');

        if ($request->has('photo')) {
            $pizzaInput['photo'] = $request->file('photo')->store(
                'pizzas', 'public'
            );
        }

        Pizza::whereId($id)->update($pizzaInput);

        return redirect()->route('pizzas.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Pizza::destroy($id);

        return redirect()->route('pizzas.index');
    }
}
