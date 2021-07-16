<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Conversation;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ConversationController extends Controller
{
    public function list()
    {
        $conversations = DB::table('conversations')
            ->latest()
            ->get();
        return view('admin-views.messages.index', compact('conversations'));
    }

    public function view($user_id)
    {
        $convs = Conversation::where(['user_id' => $user_id])->get();
        Conversation::where(['user_id' => $user_id])->update(['checked' => 1]);
        $user = User::find($user_id);
        return response()->json([
            'view' => view('admin-views.messages.partials._conversations', compact('convs', 'user'))->render()
        ]);
    }

    public function store(Request $request, $user_id)
    {
        $validator = Validator::make($request->all(), [
            'reply' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([], 403);
        }

        DB::table('conversations')->insert([
            'user_id' => $user_id,
            'reply' => $request->reply,
            'checked' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $convs = Conversation::where(['user_id' => $user_id])->get();
        $user = User::find($user_id);
        return response()->json([
            'view' => view('admin-views.messages.partials._conversations', compact('convs', 'user'))->render()
        ]);
        $conv = new Conversation;
       // $token = $request->user()->cm_firebase_token::where(['id' => $user_id]);
        $conv->message = $request->message;
        $conv->image = $image_name;
        $conv->save();
       // $find=\App\User::find($token);
        //$token=  DB::table('users')::select('cm_firebase_token')->where(['id' => $user_id])->get();
        $fcm_token = $user->cm_firebase_token;
        $data = [
            'title' => 'You have new message',
            'description' => 'message',
        ];
        //$token=User::whereNotNull('cm_firebase_token')->pluck('cm_firebase_token')->all();
       // $data='You have a message.';
        Helpers::send_push_notif_to_device_message($fcm_token, $data);
    }
}
