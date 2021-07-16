<?php

namespace App\CentralLogics;

use App\Inventory;
use App\Model\Branch;
use App\Model\Product;
use App\Model\Review;
use Illuminate\Http\Request;

class ProductLogic
{
    public static function get_product($id)
    {
        return Product::active()->with(['reviews'])->where('id', $id)->first();
    }

    public static function get_latest_products($limit = 10, $offset = 1)
    {  
        $paginator = Product::active()->with(['reviews'])->latest()->paginate($limit, ['*'], 'page', $offset);
        /*$paginator->count();*/
        return [
            'total_size' => $paginator->total(),
            'default'=>true,
            'limit' => $limit,
            'offset' => $offset,
            'products' => $paginator->items()
        ];
    }

    public static function get_related_products(Request $request,$product_id)
    {
        $product = Product::find($product_id);
        $product_ids = ProductLogic::find_products_of_closest_pantry($request);
        return Product::active()->with(['rating'])->where('category_ids', $product->category_ids)
            ->whereIn('id',$product_ids)
            ->where('id', '!=', $product->id)
            ->limit(10)
            ->get();
    }

    public static function search_products(Request $request,$name, $limit = 10, $offset = 1)
    {
        
        $product_ids = ProductLogic::find_products_of_closest_pantry($request) ;
        $countFlag = count($product_ids);
        $key = explode(' ', $name);
        $paginator = Product::active()->with(['rating']);

        $paginator = $paginator->whereIn('id',$product_ids);

        $paginator = $paginator->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->paginate($limit, ['*'], 'page', $offset);
        
        

        return [
            'default'=>false,
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'products' => $paginator->items()
        ];
    }

    public static function get_product_review($id)
    {
        $reviews = Review::where('product_id', $id)->get();
        return $reviews;
    }

    public static function get_rating($reviews)
    {
        $rating5 = 0;
        $rating4 = 0;
        $rating3 = 0;
        $rating2 = 0;
        $rating1 = 0;
        foreach ($reviews as $key => $review) {
            if ($review->rating == 5) {
                $rating5 += 1;
            }
            if ($review->rating == 4) {
                $rating4 += 1;
            }
            if ($review->rating == 3) {
                $rating3 += 1;
            }
            if ($review->rating == 2) {
                $rating2 += 1;
            }
            if ($review->rating == 1) {
                $rating1 += 1;
            }
        }
        return [$rating5, $rating4, $rating3, $rating2, $rating1];
    }

    public static function get_overall_rating($reviews)
    {
        $totalRating = count($reviews);
        $rating = 0;
        foreach ($reviews as $key => $review) {
            $rating += $review->rating;
        }
        if ($totalRating == 0) {
            $overallRating = 0;
        } else {
            $overallRating = number_format($rating / $totalRating, 2);
        }

        return [$overallRating, $totalRating];
    }


    public static function find_products_of_closest_pantry(Request $request){
        $long=$request->get('longitude');
        $lat =$request->get('latitude');
        $product_ids = [];
        //dd($products);
        
        if($long!=null){
            
           
            $branches = Branch::select('id')->whereBetween('longitude',[($long-0.1802),($long+0.1802)])
                    ->whereBetween('latitude',[($lat-0.1802),($lat+0.1802)])->get()->toArray();
            
            
            $product_ids = [];
            
            foreach($branches as $branch){
                $p = Inventory::where('count','>',2) 
                        ->where('branchId',$branch['id'])
                        ->get();
                if($p->count()){
                    foreach($p as $inventory)
                    {
                        array_push($product_ids,$inventory->productId);
                    }
                }
            }
            
        }
        return $product_ids ;
    }
}
