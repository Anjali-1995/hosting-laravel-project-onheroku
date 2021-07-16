<?php

namespace App\Http\Controllers;

use App\Inventory;
use App\Model\Branch;
use App\Model\Product;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class InventoryController extends Controller
{


    public function listBranch(Request $request ){
        $inventory = $this->getFromInventoryBranch($request);
        return view('branch-views.listInventory',compact('inventory'));
    }

    public function listAdmin(Request $request,$branchId){
        $inventory = $this->getFromInventoryAdmin($request,$branchId);
        return view('admin-views.inventory.listInventory',compact(['inventory','branchId']));
    }

    public function createBranch(Request $request){
        $products = Product::all();
        return view('branch-views.createInventory',compact('products'));
    }

    public function createAdmin(Request $request,$branchId){
        $products = Product::all();
        return view('admin-views.inventory.createInventory',compact('products'));
    }

    public function substractFromInventoryAdmin(Request $request,$branchId,$inventoryId){
        $inventory = Inventory::where('id',$inventoryId)->where('branchId',$branchId)->first();
        if($inventory==null){
            return abort(404);
        }
        
        $count = abs($request->get('count',1));
        if($count>$inventory->count){
            Toastr::warning('maxium amount that can be deducted is '.$inventory->count);
            return back();
        }
        $inventory->decrementCount($count);

        return back();
    }
    
    # Objective 1

    public function addToInventoryBranch(Request $request){
        $BranchId = auth('branch')->id();
        $branch = Branch::find($BranchId);
        $ProductId   = $request->get('product');

        $noOfProducts = \abs($request->get('count',1));

        $prod  = Product::find($ProductId);

        if(!$prod){
            Toastr::error('No such item found');
            return back();
        }

        #here we are getting the inventory of that particular branch and then checking if we previously added this 
        #to inventory or not. If previously added then we are just increasing the count 
        #else we will add.
        $inventory = Inventory::where('branchId',$branch->id)->where('productId',$ProductId)->get();
        //dd($inventory->exists());
        if($inventory->count()){
            //dd($inventory);
            $inventory = $inventory->first();
            $inventory->incrementCount($noOfProducts);
        }
        else{
            Inventory::create([
                'branchId'=>$BranchId,
                'productId'=>$ProductId,
                'count'=>$noOfProducts
            ]);
        }  

        Toastr::success('Added to inventory');

        
        return back();

    }

    public function addToInventoryAdmin(Request $request,$branchId){
        $branch = Branch::find($branchId);
        if(!$branch){
            return abort(404);
        }

        
        $prod = Product::find($request->get('product',-1));
        if(!$prod){
            Toastr::error('No such product found!');
            return back();
        }

        #here we are getting the inventory of that particular branch and then checking if we previously added this 
        #to inventory or not. If previously added then we are just increasing the count 
        #else we will add.
        $inventory = Inventory::where('branchId',$branch->id)->where('productId',$prod->id)->get();
       
        if($inventory->count()){
            $inventory = $inventory->first();
            $inventory->incrementCount($request->get('count',1));
        }else{
            Inventory::create([
                'productId' => $prod->id,
                'branchId'  => $branch->id,
                'count'     => $request->get('count',1)
            ]);
        }

        Toastr::success('Added to inventory');
        return back();
    }


    # Objective 2


    public function getFromInventoryBranch($request){
        $inventory = Inventory::where('branchId',auth('branch')->id())->get();

        //for now just adding 
        return $inventory;

    }

    public function getFromInventoryAdmin($request,$branchId){
        //$branch   = Branch::find($branchId);

        $inventory = Inventory::where('branchId',$branchId)->get();
        //dd($inventory);
        //for now just adding 
        return $inventory;
    }
    
    


}
