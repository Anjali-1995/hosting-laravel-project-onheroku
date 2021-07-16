<?php

namespace App\Http\Controllers;

use App\CentralLogics\Helpers;
use App\CentralLogics\ProductLogic;
use App\Tags;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class TagsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tags = Tags::all();

        return view('admin-views.tags.list',\compact('tags'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin-views.tags.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'key'=>'unique:tags|required|string'
        ]);

        Tags::create([
            'key'=>$request->key
        ]);

        Toastr::success('tag added');

        return back();
    }

    public function get_all_products_by_tags(Request $request,$id){
        $product_ids = ProductLogic::find_products_of_closest_pantry($request);

        $tag = Tags::find($id);
        if($tag==null){
            return response()->json(['error'=>'no such tag found'],403);
        }
        //dd($tag->products);
        $products = $tag->products;//()->whereIn('id',$product_ids);
        $products = Helpers::product_data_formatting($products,True);
        return response()->json(['products'=>$products],200);

        
    }

   
    public function update(Request $request, Tags $tags)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Tags  $tags
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tags $tags)
    {
        //
    }
}
