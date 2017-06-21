<?php

namespace App\Http\Controllers\Mobile;

use App\Info;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Cache;

class ClientController extends Controller
{
    public function ClientList(){
        return view('mobile.client-list');
    }

    public function ClientListStore(Request $request){

        $name = $request->get('info_name');
        $sex = $request->get('info_sex');
        $quota = $request->get('info_quota');
        $mobile = $request->get('info_mobile');
        $memeberID = session('wechat_user')[0]['member_id'];

        $info_sms =$request->get('info_sms');
        $cacheSms = Cache::get('sms');
        if ($info_sms != $cacheSms){
            return redirect('mobile/client-list')->with('message', '2');
        }

        $info = new Info();
        $info ->info_name = $name;
        $info ->info_sex = $sex;
        $info ->info_quota = $quota;
        $info ->info_mobile = $mobile;
        $info ->member_id = $memeberID? $memeberID :'0';

        if ($info ->save()){
            Cache::forget('sms');
            return redirect('mobile/client-list')->with('message', '1');
        }else{
            return redirect('mobile/client-list')->with('message', '0');
        }

    }
}
