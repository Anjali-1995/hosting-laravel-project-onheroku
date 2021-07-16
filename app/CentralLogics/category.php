<?php

namespace App\CentralLogics;

use App\Inventory;
use App\Model\Branch;
use App\Model\Category;
use App\Model\Product;
use Illuminate\Http\Request;

class CategoryLogic
{
    public static function parents()
    {
        return Category::where('position', 0)->get();
    }

    public static function child($parent_id)
    {
        return Category::where(['parent_id' => $parent_id])->get();
    }

    public static function products(Request $request,$category_id)
    {
        $product_ids=ProductLogic::find_products_of_closest_pantry($request);
        $type = $request->get('type');

        $products = Product::active()->whereIn('id',$product_ids);
        if($type){
            $products = $products->where('type',$type);
        }

        $products = $products->get();


        
        
        $product_ids = [];
        foreach ($products as $product) {
            foreach (json_decode($product['category_ids'], true) as $category) {
                if ($category['id'] == $category_id) {
                    array_push($product_ids, $product['id']);
                }
            }
        }
        return Product::with('reviews')->whereIn('id', $product_ids)->get();
    }

    public static function all_products($id)
    {
        $cate_ids=[];
        array_push($cate_ids,(int)$id);
        foreach (CategoryLogic::child($id) as $ch1){
            array_push($cate_ids,$ch1['id']);
            foreach (CategoryLogic::child($ch1['id']) as $ch2){
                array_push($cate_ids,$ch2['id']);
            }
        }

        $products = Product::active()->get();
        $product_ids = [];
        foreach ($products as $product) {
            foreach (json_decode($product['category_ids'], true) as $category) {
                if (in_array($category['id'],$cate_ids)) {
                    array_push($product_ids, $product['id']);
                }
            }
        }

        return Product::with('rating')->whereIn('id', $product_ids)->get();
    }
}
