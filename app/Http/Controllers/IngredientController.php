<?php

namespace App\Http\Controllers;
use App\Ingredients;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;

class IngredientController extends Controller
{
    public function list(Request $request){
        $ingredients=Ingredients::orderBy('name')->get();
        return view('admin-views.ingredients.list',compact('ingredients'));
    }

    public function create(Request $request){{
        return view('admin-views.ingredients.index');
    }}

    public function add(Request $request){
        $request->validate([
            'name' => 'required',
            'price'=>'required',
            'minimum'=>'required'
        ]);
        //dd($request->all());
        
        if ($request->file('image')) {
            //dd("x");
            $image_name = Carbon::now()->toDateString() . "-" . uniqid() . "." . 'png';
            if (!Storage::disk('public')->exists('ingredient')) {
                Storage::disk('public')->makeDirectory('ingredient');
            }
            $note_img = Image::make($request->file('image'))->stream();
            Storage::disk('public')->put('ingredient/' . $image_name, $note_img);
        } else {
            $image_name = 'def.png';
        }

        Ingredients::create([
            'name'=>$request->get('name'),
            'price'=>$request->get('price'),
            'minimum_integer'=>$request->get('minmum',1),
            'image'=>$image_name
        ]);
        Toastr::success('Ingredient added!');
        return back();

    }
}
