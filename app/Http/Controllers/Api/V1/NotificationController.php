<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Model\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    
    public function get_notifications(){
        try {
            $SERVER_API_KEY = 'AAAAj8SZSCs:APA91bFanKR11la1m9XpwUT4AnHs0av6p4DS1oFflqb0d72nfeYjwfSm9deBQTsuStRkPTgSasTTiQ2IW1R5z6qqXuzM4P6wL0Q6yh5moHrNLIhXwmL2GKSSptOM6qdhJWIINANywC8e';
            $data = [
                "notification" => [
                    "title" => $request->title,
                    "body" => $request->body,  
                ]
            ];
            $headers = [
                'Authorization: key=' . $SERVER_API_KEY,
                'Content-Type: application/json',
            ];
            $ch = curl_init();
      
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
                   
            $response = curl_exec($ch);
      
            dd($response);
            $dataString = json_encode($data);
            return response()->json(Notification::active()->get(), 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }
}
