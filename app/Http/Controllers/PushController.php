<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Notifications\PushDemo;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Notification;
use Illuminate\Support\Facades\DB;

use App\Models\Configuration;

class PushController extends Controller
{
    /**
     * Store the PushSubscription.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request){
        $this->validate($request,[
            'endpoint'    => 'required',
            'keys.auth'   => 'required',
            'keys.p256dh' => 'required'
        ]);
        $endpoint = $request->endpoint;
        $token = $request->keys['auth'];
        $key = $request->keys['p256dh'];
        $user = Auth::user();
        $user->updatePushSubscription($endpoint, $key, $token);

        return response()->json(['success' => true],200);
    }

    public function push(){

        $config = Configuration::first();
        if($config->webPush == 1)
        {
            $notify = new PushDemo("titolo", "messaggio", "testolink", "link", '/img/icoIry.png', '/favicon.ico',);
            Notification::send(User::all(),$notify);
        }
        return redirect()->back();
    }

}
