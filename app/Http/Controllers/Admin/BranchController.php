<?php

namespace App\Http\Controllers\Admin;

use App\BranchLocation;
use App\Http\Controllers\Controller;
use App\Model\Branch;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::orderBy('created_at')->paginate(10);
        return view('admin-views.branch.index', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:branches',
            'email' => 'required|unique:branches',
            'password' => 'required',
            'longitude' => 'required',
            'latitude' => 'required',
            'pin' => 'required'
        ], [
            'name.required' => 'Name is required!',
        ]);

        $branch = new Branch();
        $branch->name = $request->name;
        $branch->email = $request->email;
        $branch->longitude = $request->longitude;
        $branch->latitude = $request->latitude;
        $branch->coverage = $request->coverage ? $request->coverage : 0;
        $branch->address = $request->address;
        $branch->password = bcrypt($request->password);
        $branch->save();

        BranchLocation::create([
            'branch_id' => $branch->id,
            'longitude' => $branch->longitude,
            'latitude'  => $branch->latitude,
            'city'      => $request->get('city'),
            'road'      => $request->get('road'),
            'address'   => $request->get('address'),
            'pin'       => $request->get('pin')
        ]);
        Toastr::success('Branch added successfully!');
        return back();
    }

    public function edit($id)
    {
        $branch = Branch::find($id);
        return view('admin-views.branch.edit', compact('branch'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required'
        ], [
            'name.required' => 'Name is required!',
        ]);

        $branch = Branch::find($id);
        $branch->name = $request->name;
        $branch->email = $request->email;
        $branch->longitude = $request->longitude;
        $branch->latitude = $request->latitude;
        $branch->coverage = $request->coverage ? $request->coverage : 0;
        $branch->address = $request->address;
        if ($request['password'] != null) {
            $branch->password = bcrypt($request->password);
        }
        $branch->save();
        
        try {
            $location = $branch->location;
            $location->longitude = $request->get('longitude',$location->longitude);
            $location->latitude = $request->get('latitude',$location->latitude);
            $location->city = $request->get('city',$location->city);
            $location->road = $request->get('road',$location->road);
            $location->pin = $request->get('pin',$location->pin);
            $location->address = $request->get('address',$location->address);
            $location->save();
        } catch (\Throwable $th) {
            //throw $th;

            BranchLocation::create([
                'branch_id'=>$branch->id,
                'longitude'=>$request->get('longitude'),
                'latitude'=>$request->get('latitude'),
                'city' => $request->get('city'),
                'road' => $request->get('road'),
                'pin'  => $request->get('pin'),
                'address'=>$request->get('address')
            ]);
        }

        
        Toastr::success('Branch updated successfully!');
        return back();
    }

    public function delete(Request $request)
    {
        $branch = Branch::find($request->id);
        $branch->delete();
        Toastr::success('Branch removed!');
        return back();
    }
}
