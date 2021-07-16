<?php

namespace App\Http\Controllers;

use App\BranchLocation;
use App\CentralLogics\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BranchLocationController extends Controller
{
    public function get_pantry(Request $request){
        $validator = Validator::make($request->all(),[
            'latitude'=>'required',
            'longitude'=>'required',
            'pin'=>'required',
        ]);

        if($validator->fails()){
            return response()->json(['errors'=>Helpers::error_processor($validator)],403);
        }

        $lat1 = floatval($request->latitude)-0.1802;
        $lat2 = floatval($request->latitude)+0.1802;

        $long1 = floatval($request->longitude)-0.1802;
        $long2 = floatval($request->longitude)+0.1802;

        $pin1= intval($request->pin)-20;
        $pin2 = intval($request->pin)+20;

        $branches = BranchLocation::whereIn('latitude',[$lat1,$lat2])
                                ->whereIn('longitude',[$long1,$long2])->orWhere('pin',$request->pin)->get();

        return response()->json(['pantries'=>$branches],200);
    }


}
