<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\IngredientCategoryRequest;
use App\IngredientCategory;
use Illuminate\Http\Request;

class IngredientCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.ingredient-categories.index', [
            'items' => IngredientCategory::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.ingredient-categories.manage', [
            'data' => new IngredientCategory,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(IngredientCategoryRequest $request)
    {
        IngredientCategory::create($request->validated());

        return redirect(route('ingredient-categories.index'));
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
    public function edit(IngredientCategory $ingredientCategory)
    {
        return view('admin.ingredient-categories.manage', [
            'data' => $ingredientCategory,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(IngredientCategoryRequest $request, $id)
    {
        IngredientCategory::whereId($id)->update($request->validated());

        return redirect(route('ingredient-categories.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $exists = \DB::table('ingredients AS i')
            ->where('i.ingredient_category_id', $id)
            ->exists();

        if (!$exists) {
            IngredientCategory::destroy($id);
            return redirect(route('ingredient-categories.index'));
        }

        return redirect(route('ingredient-categories.index'))->with('deleteError', 'Cannot delete selected ingredient category because it is still in use!');
    }
}
