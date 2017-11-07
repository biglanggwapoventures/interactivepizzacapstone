<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\IngredientRequest;
use App\Ingredient;
use App\IngredientCategory;
use Illuminate\Http\Request;

class IngredientsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = IngredientCategory::query();

        $query->when($request->category_id, function ($query) use ($request) {
            $query->whereId($request->category_id);
        });

        $query->when($request->ingredient, function ($query) use ($request) {
            $query->whereHas('ingredients', function ($q) use ($request) {
                $q->where('description', 'like', "%{$request->ingredient}%");
            });
            $query->with(['ingredients' => function ($q) use ($request) {
                $q->where('description', 'like', "%{$request->ingredient}%");
            }]);
        });

        $query->unless($request->ingredient, function ($query) use ($request) {
            $query->with('ingredients');
        });

        return view('admin.ingredients.index', [
            'items' => $query->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.ingredients.manage', [
            'data' => new Ingredient,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(IngredientRequest $request)
    {
        $ingredientPost = array_except($request->validated(), 'photo');

        $ingredientPost['photo'] = $request->file('photo')->store(
            'ingredients', 'public'
        );

        Ingredient::create($ingredientPost);

        return redirect(route('ingredients.index'));
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
    public function edit(Ingredient $ingredient)
    {
        return view('admin.ingredients.manage', [
            'data' => $ingredient,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(IngredientRequest $request, $id)
    {
        $ingredientPost = array_except($request->validated(), 'photo');

        if ($request->has('photo')) {
            $ingredientPost['photo'] = $request->file('photo')->store(
                'ingredients', 'public'
            );
        }

        Ingredient::whereId($id)->update($ingredientPost);

        return redirect(route('ingredients.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $exists = \DB::table('pizza_ingredients AS pi')
            ->where('pi.ingredient_id', $id)
            ->exists();

        if (!$exists) {
            Ingredient::destroy($id);
            return redirect(route('ingredients.index'));
        }

        return redirect(route('ingredients.index'))->with('deleteError', 'Cannot delete selected ingredient because it is still in use!');
    }
}
