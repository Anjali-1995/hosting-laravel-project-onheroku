<?php

namespace App\CentralLogics;


use Illuminate\Support\Facades\Http;
use App\Ingredients;
use App\Model\AddOn;
use App\Model\BusinessSetting;
use App\Model\Currency;
use App\Model\DMReview;
use App\Model\Order;
use App\Model\Review;
use App\Nutrients;
use App\Recipe;
use Exception;
use Illuminate\Support\Carbon;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Throwable;

class Helpers
{
    public static function error_processor($validator)
    {
        $err_keeper = [];
        foreach ($validator->errors()->getMessages() as $index => $error) {
            array_push($err_keeper, ['code' => $index, 'message' => $error[0]]);
        }
        return $err_keeper;
    }

    public static function combinations($arrays)
    {
        $result = [[]];
        foreach ($arrays as $property => $property_values) {
            $tmp = [];
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, [$property => $property_value]);
                }
            }
            $result = $tmp;
        }
        return $result;
    }

    public static function variation_price($product, $variation)
    {
        $match = json_decode($variation, true)[0];
        $result = 0;
        foreach (json_decode($product['variations'], true) as $property => $value) {
            if ($value['type'] == $match['type']) {
                $result = $value['price'];
            }
        }
        return $result;
    }

    public static function product_data_formatting($data, $multi_data = false)
    {
        //dd($data);   
        $storage = [];
        if ($multi_data == true) {

            foreach ($data as $item) {
                //dd($item->tags);
                $variations = [];
                $item['category_ids'] = json_decode($item['category_ids']);
                $item['attributes'] = json_decode($item['attributes']);
                $item['choice_options'] = json_decode($item['choice_options']);
                try{
                    $item['add_ons'] = AddOn::whereIn('id', json_decode($item['add_ons']))->get();
                }catch(Throwable $e){
                    $item['add_ons'] = [];
                }
                
                try{
                    $item['recipe'] = Recipe::where('product_id',$item['id'])->first();
                    $item['nutrient'] = Nutrients::where('product_id',$item['id'])->first();
                    $item['ingredients'] = $item->ingredients; 
                }catch(Exception $e){
                    //dd($e);
                }
                try{
                    foreach (json_decode($item['variations'], true) as $var) {
                        array_push($variations, [
                            'type' => $var['type'],
                            'price' => (double)$var['price']
                        ]);
                    }
                    $tem['tags'] = $item->tags;
                }catch(Throwable $e){
                    
                }
                
                try{
                    $_countRating = [0,0,0,0,0];
                    foreach($item['reviews']->toArray() as $reviews){
                        $_countRating[$reviews['rating']-1]+=1;
                    }
                    
                    $rating = [
                        'average'=>$item['reviews']->avg('rating'),
                        'product_id'=>$item['id'],
                        'total_rating'=>$item['reviews']->count(),
                        'count_rating'=>$_countRating
                    ];
                    
                    $item['rating']=$rating;
                    $item['variations'] = $variations;
                    
                }catch(Exception $e){

                }
                
                
                array_push($storage, $item);
            }
            $data = $storage;
        } else {
            $variations = [];
            $data['category_ids'] = json_decode($data['category_ids']);
            $data['attributes'] = json_decode($data['attributes']);
            $data['choice_options'] = json_decode($data['choice_options']);
            $data['add_ons'] = AddOn::whereIn('id', json_decode($data['add_ons']))->get();
            foreach (json_decode($data['variations'], true) as $var) {
                array_push($variations, [
                    'type' => $var['type'],
                    'price' => (double)$var['price']
                ]);
            }
            $data['variations'] = $variations;
            try{
                $_countRating = [0,0,0,0,0];
                foreach($data['reviews']->toArray() as $reviews){
                    $_countRating[$reviews['rating']-1]+=1;
                }
                
                $rating = [
                    'average'=> array_sum($_countRating)/5,
                    'product_id'=>$data['id'],
                    'total_rating'=>$data['reviews']->count(),
                    'count_rating'=>$_countRating
                ];
                
            $data['rating']=$rating;
            $data['tags'] = $data->tags;
            $data['ingredients'] = $data->ingredients; 
            }catch(Exception $e){
                //dd($e)
            }
           
            $data['recipe'] = Recipe::where('product_id',$data['id'])->first();
            
            $data['nutrient'] = Nutrients::where('product_id',$data['id'])->first();
            
        }

        return $data;
    }


    
    public static function order_data_formatting($data, $multi_data = false)
    {
        $storage = [];
        if ($multi_data == true) {
            foreach ($data as $item) {
                $item['add_on_ids'] = json_decode($item['add_on_ids']);
                array_push($storage, $item);
            }
            $data = $storage;
        } else {
            $data['add_on_ids'] = json_decode($data['add_on_ids']);
        }

        return $data;
    }

    public static function get_business_settings($name)
    {
        $config = null;
        foreach (BusinessSetting::all() as $setting) {
            if ($setting['key'] == $name) {
                $config = json_decode($setting['value'], true);
            }
        }
        return $config;
    }

    public static function currency_code()
    {
        $currency_code = BusinessSetting::where(['key' => 'currency'])->first()->value;
        return $currency_code;
    }

    public static function currency_symbol()
    {
        $currency_symbol = Currency::where(['currency_code' => Helpers::currency_code()])->first()->currency_symbol;
        return $currency_symbol;
    }

    public static function send_push_notif_to_device($fcm_token, $data)
    {
        return Helpers::send_notification($fcm_token,$data);
    }

    public static function send_push_notif_to_device_message($fcm_token, $data)
    {
        /*https://fcm.googleapis.com/v1/projects/myproject-b5ae1/messages:send*/
       // $key = BusinessSetting::where(['key' => 'push_notification_key'])->first()->value;
        /*$project_id = BusinessSetting::where(['key' => 'fcm_project_id'])->first()->value;*/
        $key = 'AAAAj8SZSCs:APA91bFanKR11la1m9XpwUT4AnHs0av6p4DS1oFflqb0d72nfeYjwfSm9deBQTsuStRkPTgSasTTiQ2IW1R5z6qqXuzM4P6wL0Q6yh5moHrNLIhXwmL2GKSSptOM6qdhJWIINANywC8e';
        $url = "https://fcm.googleapis.com/fcm/send";
        $header = array("authorization: key=" . $key . "",
            "content-type: application/json"
        );

        $postdata = '{
            "to" : "' . $fcm_token . '",
            "data" : {
                "body" : "' . $data['title'] . '",
              }
        }';

        $ch = curl_init();
        $timeout = 120;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        // Get URL content
        $result = curl_exec($ch);
        // close handle to release resources
        curl_close($ch);

        return $result;
    }

    public static function send_push_notif_to_topic($data)
    {
        /*https://fcm.googleapis.com/v1/projects/myproject-b5ae1/messages:send*/
       // $key = BusinessSetting::where(['key' => 'AAAAj8SZSCs:APA91bFanKR11la1m9XpwUT4AnHs0av6p4DS1oFflqb0d72nfeYjwfSm9deBQTsuStRkPTgSasTTiQ2IW1R5z6qqXuzM4P6wL0Q6yh5moHrNLIhXwmL2GKSSptOM6qdhJWIINANywC8e'])->first()->value;
        /*$topic = BusinessSetting::where(['key' => 'fcm_topic'])->first()->value;*/
        /*$project_id = BusinessSetting::where(['key' => 'fcm_project_id'])->first()->value;*/
        $key = 'AAAAj8SZSCs:APA91bFanKR11la1m9XpwUT4AnHs0av6p4DS1oFflqb0d72nfeYjwfSm9deBQTsuStRkPTgSasTTiQ2IW1R5z6qqXuzM4P6wL0Q6yh5moHrNLIhXwmL2GKSSptOM6qdhJWIINANywC8e';

        $url = "https://fcm.googleapis.com/fcm/send";
        $header = array("authorization: key=" . $key . "",
            "content-type: application/json"
        );
        $postdata = '{
            "to" : "/topics/notify",
            "data" : {
                "title":"' . $data->title . '",
                "body" : "' . $data->description . '",
                "image" : "' . $data->image . '",
                "is_read": 0
              }
        }';

        $ch = curl_init();
        $timeout = 120;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        // Get URL content
        $result = curl_exec($ch);
        // close handle to release resources
        curl_close($ch);

        return $result;
    }

    public static function rating_count($product_id, $rating)
    {
        return Review::where(['product_id' => $product_id, 'rating' => $rating])->count();
    }

    public static function dm_rating_count($deliveryman_id, $rating)
    {
        return DMReview::where(['delivery_man_id' => $deliveryman_id, 'rating' => $rating])->count();
    }

    public static function tax_calculate($product, $price)
    {
        if ($product['tax_type'] == 'percent') {
            $price_tax = ($price / 100) * $product['tax'];
        } else {
            $price_tax = $product['tax'];
        }
        return $price_tax;
    }

    public static function discount_calculate($product, $price)
    {
        if ($product['discount_type'] == 'percent') {
            $price_discount = ($price / 100) * $product['discount'];
        } else {
            $price_discount = $product['discount'];
        }
        return $price_discount;
    }

    public static function max_earning()
    {
        $data = Order::where(['order_status' => 'delivered'])->select('id', 'created_at', 'order_amount')
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('m');
            });

        $max = 0;
        foreach ($data as $month) {
            $count = 0;
            foreach ($month as $order) {
                $count += $order['order_amount'];
            }
            if ($count > $max) {
                $max = $count;
            }
        }
        return $max;
    }

    public static function max_orders()
    {
        $data = Order::select('id', 'created_at')
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('m');
            });

        $max = 0;
        foreach ($data as $month) {
            $count = 0;
            foreach ($month as $order) {
                $count += 1;
            }
            if ($count > $max) {
                $max = $count;
            }
        }
        return $max;
    }

    public static function order_status_update_message($status)
    {
        if ($status == 'pending') {
            $data = BusinessSetting::where('key', 'order_pending_message')->first()->value;
        } elseif ($status == 'confirmed') {
            $data = BusinessSetting::where('key', 'order_confirmation_msg')->first()->value;
        } elseif ($status == 'processing') {
            $data = BusinessSetting::where('key', 'order_processing_message')->first()->value;
        } elseif ($status == 'out_for_delivery') {
            $data = BusinessSetting::where('key', 'out_for_delivery_message')->first()->value;
        } elseif ($status == 'delivered') {
            $data = BusinessSetting::where('key', 'order_delivered_message')->first()->value;
        } elseif ($status == 'delivery_boy_delivered') {
            $data = BusinessSetting::where('key', 'delivery_boy_delivered_message')->first()->value;
        } elseif ($status == 'del_assign') {
            $data = BusinessSetting::where('key', 'delivery_boy_assign_message')->first()->value;
        } elseif ($status == 'ord_start') {
            $data = BusinessSetting::where('key', 'delivery_boy_start_message')->first()->value;
        } else {
            $data = '{"status":"0","message":""}';
        }

        $res = json_decode($data, true);

        if ($res['status'] == 0) {
            return 0;
        }
        return $res['message'];
    }

    public static function day_part()
    {
        $part = "";
        $morning_start = date("h:i:s", strtotime("5:00:00"));
        $afternoon_start = date("h:i:s", strtotime("12:01:00"));
        $evening_start = date("h:i:s", strtotime("17:01:00"));
        $evening_end = date("h:i:s", strtotime("21:00:00"));

        if (time() >= $morning_start && time() < $afternoon_start) {
            $part = "morning";
        } elseif (time() >= $afternoon_start && time() < $evening_start) {
            $part = "afternoon";
        } elseif (time() >= $evening_start && time() <= $evening_end) {
            $part = "evening";
        } else {
            $part = "night";
        }

        return $part;
    }

    public static function env_update($key,$value){
        $path = base_path('.env');
        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                $key.'='.env($key), $key.'='.$value, file_get_contents($path)
            ));
        }
    }

    public static function env_key_replace($key_from,$key_to,$value){
        $path = base_path('.env');
        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                $key_from.'='.env($key_from), $key_to.'='.$value, file_get_contents($path)
            ));
        }
    }

    public static  function remove_dir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") Helpers::remove_dir($dir."/".$object); else unlink($dir."/".$object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    public static function send_notification($fcm_token,$data){
        $factory = (new Factory)->withServiceAccount(storage_path('firebase_cred.json'));
        $messaging = $factory->createMessaging();

        $message = CloudMessage::withTarget('token',$fcm_token)
                        ->withNotification(Notification::fromArray([
                            "title"=>$data['title'],"body"=>$data['description'],"image"=>$data['image'],"order_id"=>$data['order_id']
                        ]))
                        ->withData($data);

        $response = $messaging->send($message);

        return $response;
    }

    public static function send_notification_message($fcm_token,$data){
        $factory = (new Factory)->withServiceAccount(storage_path('firebase_cred.json'));
        $messaging = $factory->createMessaging();

        $message = CloudMessage::withTarget('token',$fcm_token)
                        ->withNotification(Notification::fromArray([
                            "title"=>$data['title'],"body"=>$data['description']
                        ]))
                        ->withData($data);

        $response = $messaging->send($message);

        return $response;
    }

}
