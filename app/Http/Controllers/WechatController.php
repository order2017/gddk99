<?php

namespace App\Http\Controllers;

use App\Common\Common;
use App\Member;
use Illuminate\Http\Request;
use EasyWeChat\Foundation\Application;
use Overtrue\Socialite\SocialiteManager;
use Cache;

class WechatController extends Controller
{

    // -----------------------微信公众号--关注公众号-----------------------------
    protected  $option = [
        'debug'     => true,
        'app_id'    => 'wx3f8962decbe79ba4',
        'secret'    => '18277875d53776b5dcf05676563acce2',
        'token'     => 'CWI4y86blVB8OhUQg4BnMF',
        'log' => [
            'level' => 'debug',
            'file'  => '/tmp/easywechat.log',
        ],
        'oauth' => [
            'scopes' =>['snsapi_base'],
            'callback' => '/mobile/oauth_callback',
        ],
    ];

    // -----------------------微信公众号网页授权-----------------------------
    protected  $options = [
        'debug'     => true,
        'app_id'    => 'wx3f8962decbe79ba4',
        'secret'    => '18277875d53776b5dcf05676563acce2',
        'token'     => 'CWI4y86blVB8OhUQg4BnMF',
        'log' => [
            'level' => 'debug',
            'file'  => '/tmp/easywechat.log',
        ],
        'oauth' => [
            'scopes' =>['snsapi_userinfo'],
            'callback' => '/mobile/ws-callback',
        ],
    ];

    // -----------------------开放平台网页第三方登录-----------------------------
    protected $config = [
        'wechat' => [
            'client_id'     => 'wx9f3dd1dd7cc72602',
            'client_secret' => 'b2600888426c904583800ac5a9de4a8f',
            'redirect'      => 'http://www.gddk99.com/mobile/wx-callback',
        ]
    ];

    //******************************* 封装方法区域 ***********************************************

    // 封装存储数据方法
    protected function BackData($result){
        $member = Member::where('wechat_openid',$result['openid'])->first();
        $memberId =$member['member_id'];
        $row = (new Common())->if_empty($memberId);
        if ($row == 0){
            Cache::pull('mobile_user');
            $mem = new Member();
            $mem->wechat_openid = $result['openid'];
            $mem->wechat_nickname = $result['nickname'];
            $mem->member_sex = $result['sex'];
            $mem->wechat_headimgurl = $result['headimgurl'];
            $mem->is_member = Member::IS_MEMBER;
            $mem->save();
            $rows = Member::find($mem->getQueueableId());
            Cache::add('mobile_user',$rows,Member::FAIL_TIME);
        }
        Cache::add('mobile_user',$member,Member::FAIL_TIME);

        return redirect()->action('WechatController@login');
    }

    // 登录成功进入对应页面方法
    protected function enter(){
        return redirect('/mobile/index');
    }

    // 渠道入口登录判断
    public function Channel(){

        $scope = Cache::get('scope');

        if ($scope=='snsapi_userinfo'){
            return redirect()->action('WechatController@WsLogin');
        }elseif($scope=='snsapi_base'){
            return redirect()->action('WechatController@login');
        }elseif($scope=='snsapi_login'){
            return redirect()->action('WechatController@WxLogin');
        }else{
            return redirect()->action('WechatController@WsLogin');
        }

    }


    // -----------------------微信公众号--关注公众号-----------------------------

    public function serve(){
        $app = new Application($this->option);
        $server = $app->server;
        $user = $app->user;
        $server->setMessageHandler(function($message) use ($user) {
            $fromUser = $user->get($message->FromUserName);
            return "{$fromUser->nickname} 您好！欢迎关注 广东贷款网";
        });
        $server->serve()->send();
    }

    public function login(){
        // 未登录
        $app = new Application($this->option);
        $oauth = $app->oauth;
        if (!Cache::has('mobile_user')){
            return $oauth->redirect();
        }

        // 已登录
        return $this->enter();
    }

    public function oauth_callback(){
        $app = new Application($this->option);
        $oauth = $app->oauth;
        $user = $oauth->user();
        $token = $user['token']->toArray();
        $scope =$token['scope'];
        $openId =$user['id'];
        $userService = $app->user;
        $result = $userService->get($openId)->toArray();

        // --------------获取到用户资料，存储数据库------------

        Cache::add('scope',$scope,1);
        return $this->BackData($result);
    }


    //  -----------------------微信公众号网页授权-----------------------------
    public function WsLogin(){

        $app = new Application($this->options);
        $oauth = $app->oauth;
        return $oauth->redirect();

    }
    public function WsCallback(){

        $app = new Application($this->options);
        $oauth = $app->oauth;
        $user = $oauth->user();
        $token = $user['token']->toArray();
        $scope = $token['scope'];
        $result = $user['original'];

        // --------------获取到用户资料，存储数据库------------

        Cache::add('scope',$scope,1);
        return $this->BackData($result);

    }


    //  -----------------------微信开放平台网页授权-----------------------------

    public function WxLogin(){
        $socialite = new SocialiteManager($this->config);
        return $socialite->driver('wechat')->redirect();
    }

    public function WxCallback(){
        $socialite = new SocialiteManager($this->config);
        $user = $socialite->driver('wechat')->user();
        dd($user);
    }

}
