<?php

namespace App\Http\Controllers\Admin;

use App\DeliveryPersonnel as Personnel;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeliveryPersonnelRequest;
use Illuminate\Http\Request;

class DeliveryPersonnelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.delivery-personnel.index', [
            'items' => Personnel::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.delivery-personnel.manage', [
            'data' => new Personnel,
        ]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DeliveryPersonnelRequest $request)
    {
        Personnel::create($request->validated());

        return redirect(route('delivery-personnel.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Personnel $deliveryPersonnel)
    {
        // dd(Personnel::find($id)->toArray());
        return view('admin.delivery-personnel.manage', [
            'data' => $deliveryPersonnel,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DeliveryPersonnelRequest $request, $id)
    {
        Personnel::whereId($id)->update($request->validated());

        return redirect(route('delivery-personnel.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Personnel::destroy($id);

        return redirect(route('ingredients.index'));
    }
}
