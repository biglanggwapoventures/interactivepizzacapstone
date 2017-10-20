<?php

namespace App\Http\Controllers\Admin;

use App\Beverage;
use App\Http\Controllers\Controller;
use App\Http\Requests\BeverageRequest;
use Illuminate\Http\Request;

class BeveragesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.beverages.index', [
            'items' => Beverage::get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.beverages.manage', [
            'data' => new Beverage,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BeverageRequest $request)
    {
        $data = array_merge($request->validated(), ['is_beverage' => 1]);
        Beverage::create($data);

        return redirect(route('beverages.index'));
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
    public function edit(Beverage $beverage)
    {
        return view('admin.beverages.manage', [
            'data' => $beverage,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BeverageRequest $request, $id)
    {
        Beverage::whereId($id)->update($request->validated());

        return redirect(route('beverages.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Beverage::destroy($id);

        return redirect(route('beverages.index'));
    }
}
