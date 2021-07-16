<?php

namespace App\Http\Controllers\Branch;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Inventory;
use App\Jobs\DecrementFromInventory;
use App\Model\Order;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function list($status)
    {
        Order::where(['checked' => 0, 'branch_id' => auth('branch')->id()])->update(['checked' => 1]);
        if ($status != 'all') {
            $orders = Order::with(['customer'])
                ->latest()->where(['order_status' => $status, 'branch_id' => auth('branch')->id()])
                ->paginate(25);
        } else {
            $orders = Order::with(['customer'])->where(['branch_id' => auth('branch')->id()])->latest()->paginate(25);
        }

        return view('branch-views.order.list', compact('orders', 'status'));
    }

    public function search(Request $request){
        $key = explode(' ', $request['search']);
        $orders=Order::where(['branch_id'=>auth('branch')->id()])->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('id', 'like', "%{$value}%")
                    ->orWhere('order_status', 'like', "%{$value}%")
                    ->orWhere('transaction_reference', 'like', "%{$value}%");
            }
        })->get();
        return response()->json([
            'view'=>view('branch-views.order.partials._table',compact('orders'))->render()
        ]);
    }

    public function details($id)
    {
        $order = Order::with('details')->where(['id' => $id, 'branch_id' => auth('branch')->id()])->first();
        if (isset($order)) {
            return view('branch-views.order.order-view', compact('order'));
        } else {
            Toastr::info('No more orders!');
            return back();
        }
    }

    public function status(Request $request)
    {
        $order = Order::where(['id' => $request->id, 'branch_id' => auth('branch')->id()])->first();

        if ($order['delivery_man_id'] == null && $request->order_status == 'out_for_delivery') {
            Toastr::warning('Please assign delivery man first!');
            return back();
        }

        $branch = auth('branch')->user();

        if($request->order_status == 'delivered' && $order->order_status!='delivered' ){
            //dd($request->order_status);
            DecrementFromInventory::dispatch($branch,$order);
        }

        $order->order_status = $request->order_status;
        $order->save();
        $fcm_token = $order->customer->cm_firebase_token;
        $value = Helpers::order_status_update_message($request->order_status);
        try {
            if ($value) {
                $data = [
                    'title' => 'Order',
                    'description' => $value,
                    'image' => '',
                ];
                Helpers::send_push_notif_to_device($fcm_token, $data);
            }
        } catch (\Exception $e) {
            Toastr::warning('Push notification failed!');
        }

        Toastr::success('Order status updated!');
        return back();
    }

    public function add_delivery_man($order_id, $delivery_man_id)
    {
        if ($delivery_man_id == 0) {
            return response()->json([], 401);
        }
        $order = Order::where(['id' => $order_id, 'branch_id' => auth('branch')->id()])->first();
        $order->delivery_man_id = $delivery_man_id;
        $order->save();

        //$token= DeliveryMan::select('fcm_token')->where(['id' =>$delivery_man_id ])->get();

        //$fcm_token = $token[0]['fcm_token'];
        $fcm_token = 'eNABbdhRTAixAeDqcVfaXj:APA91bEHfcT68nzAflUK29z9y-wP5pIDU5QR00faTjb1IMzJAsDJinndm-BSBWVmN0SKTQNSo_58FOR8ejgCOlk1YMk9rlgtygyTSO_kVfUqitoZfiCVe8SiQUCBacXIwp3WxqkeBSb4';
        $value = Helpers::order_status_update_message('del_assign');
        try {
            if ($value) {
                $data = [
                    'title' => 'Order',
                    'description' => $value,
                    'image' => '',
                ];
                Helpers::send_push_notif_to_device($fcm_token, $data);
            }
        } catch (\Exception $e) {

        }

        Toastr::success('Order deliveryman added!');
        return response()->json([], 200);
    }

    public function payment_status(Request $request)
    {
        $order = Order::where(['id' => $request->id, 'branch_id' => auth('branch')->id()])->first();
        if ($request->payment_status == 'paid' && $order['transaction_reference'] == null && $order['payment_method'] != 'cash_on_delivery') {
            Toastr::warning('Add your payment reference code first!');
            return back();
        }
        $order->payment_status = $request->payment_status;
        $order->save();
        Toastr::success('Payment status updated!');
        return back();
    }

    public function update_shipping(Request $request, $id)
    {
        $request->validate([
            'contact_person_name' => 'required',
            'address_type' => 'required',
            'contact_person_number' => 'required',
            'address' => 'required'
        ]);

        $address = [
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'address_type' => $request->address_type,
            'address' => $request->address,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'created_at' => now(),
            'updated_at' => now()
        ];

        DB::table('customer_addresses')->where('id', $id)->update($address);
        Toastr::success('Payment status updated!');
        return back();
    }

    public function generate_invoice($id)
    {
        $order = Order::where(['id' => $id, 'branch_id' => auth('branch')->id()])->first();
        return view('branch-views.order.invoice', compact('order'));
    }

    public function add_payment_ref_code(Request $request, $id)
    {
        Order::where(['id' => $id, 'branch_id' => auth('branch')->id()])->update([
            'transaction_reference' => $request['transaction_reference']
        ]);

        Toastr::success('Payment reference code is added!');
        return back();
    }

    public function analyze_availability(Request $request){

        $request->validate([
            'lat' => 'required',
            'lng' => 'required',
            'dist' => 'required'
        ]);

        $lat = $request->lat;
        $lng = $request->lng;
        $distance = $request->dist;

        $query = DB::select(DB::raw('SELECT id, ( 3959 * acos( cos( radians(' . $lat . ') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(' . $lng . ') ) + sin( radians(' . $lat .') ) * sin( radians(latitude) ) ) ) AS distance FROM branch_locations HAVING distance < ' . $distance . ' ORDER BY distance') );

    if(!$query){
        return response()->json([
            'message' => 'No Stores',
        ], 200);
    }

    $ids = [];

    //Extract the id's
    foreach($query as $q){
        array_push($ids, $q->id);
    }

    // Replace the below statement with the desired effect

    $results = "replace this with the desired query0";


    return response()->json([
        'message' => 'Store Avai!able',
        'store' => $results
    ], 200);

}

}
