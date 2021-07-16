<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Model\Order;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api;

class RazorPayController extends Controller
{
    public function payWithRazorpay(Request $request,$order_id){

        
        $order = Order::with('details')->findOrFail($order_id);
        if($order->payment_status == "paid"){
            return redirect('payment-success');
        }
        $sub_total = 0;
        $total_tax = 0;
        $total_dis_on_pro = 0;
        $add_ons_cost = 0;
        if($order['order_type']=='take_away')
        {
            $del_c=0;
        }else{
            $del_c=$order['delivery_charge'];
        }
                                            
        foreach($order->details as $detail){
            if($detail->product){
                $add_on_qtys = json_decode($detail['add_on_qtys']);
                
                foreach(json_decode($detail['add_on_ids'],true) as $key2 =>$id){
                    $addon=\App\Model\AddOn::find($id);
                    $add_on_qty = $add_on_qtys?$add_on_qtys[$key2]:1;
                    $add_ons_cost+=$addon['price']*$add_on_qty;
                    
                }
                $amount=($detail['price']-$detail['discount_on_product'])*$detail['quantity'];
                $sub_total+=$amount;
                $total_tax+=$detail['tax_amount']*$detail['quantity'];


            }
        }

        if($order['order_type']=='take_away') $del_c=0;
        else $del_c=$order['delivery_charge'];

        $amount = $sub_total+$del_c+$total_tax+$add_ons_cost-$order['coupon_discount_amount'];

        $user = $order->customer;

        $data = [
            'user'=>$user,
            'amount'=>round($amount*100),
            'amount_to_show'=>$amount,
            'currency'=>Helpers::currency_symbol(),
            'order_id'=>$order_id
        ];
        return view('razor-pay',compact('data'));
    }

    public function payment(Request $request,$order_id){
        $input = $request->all();
  
        $api = new Api(env('RAZORPAY_KEY'), env('RAZOR_SECRET'));
  
        $payment = $api->payment->fetch($input['razorpay_payment_id']);
  
        if(count($input)  && !empty($input['razorpay_payment_id'])) {
            try {
                $response = $api->payment->fetch($input['razorpay_payment_id'])->capture(array('amount'=>$payment['amount']));
                //dd($response); 
                $order = Order::find($order_id);
                $tr_ref = $input['razorpay_payment_id'];

                $order->payment_status = "paid";
                $order->transaction_reference=$tr_ref;
                $order->payment_method = 'razor_pay';
                $order->order_status = "confirmed";

                $order->save();
                

                $fcm_token = $order->customer->cm_firebase_token;
                $value = Helpers::order_status_update_message('confirmed');
                if ($value) {
                    $data = [
                        'title' => 'Order',
                        'description' => $value,
                        'order_id' => $order['id'],
                        'image' => '',
                    ];
                    Helpers::send_push_notif_to_device($fcm_token, $data);
                }
            } catch (Exception $e) {
                dd($e);
                //Session::put('error',$e->getMessage());
                return redirect(route('payment-fail'));
            }

            //Toastr::success('Payment Success');
            return redirect('payment-success');
        }
    }
}
