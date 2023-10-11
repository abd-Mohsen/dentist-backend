<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubOrderController extends Controller
{
    public function index()
    {
        //all suborders for a supplier
        // make 2 requests, one for all suborders, second for the pending suborders
    }



    public function store(Request $request)
    {
        //no need
    }



    public function show(string $id)
    {
        //
    }



    public function update(Request $request, string $id)
    {
        //update in product_order table
    }



    public function destroy(string $id)
    {
        //cancel ?
    }
}
