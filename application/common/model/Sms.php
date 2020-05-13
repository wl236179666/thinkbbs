<?php

namespace app\common\model;

use think\Env;
use think\facade\Cache;
use think\facade\Config;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;
use think\Loader;

require_once '../extend/lx198/SendUtility.php';

class Sms
{
    protected $easysms;

    public function __construct()
    {
        $cfg = Config::get('easysms.');
        if(!is_array($cfg)){
            $cfg = [];
        }
        $this->easysms = new EasySms($cfg);
    }

    /**
     * 发送短信
     * @Author   zhanghong(Laifuzi)
     * @param    string             $mobile 手机号码
     * @return   Object                     FunctionResult
     */
    public function sendCode($mobile,$type='aliyun')
    {
        $app_env = Config::get('app.env');
        if($app_env == 'production'){
            $code = mt_rand(100000, 999999);
            if($type == 'aliyun'){
                $this->sendByaliyun($mobile, $code);
            }else{
                $content = '您的验证码是'.$code.'。如非本人操作，请忽略本短信';
                $this -> sendBylexin($mobile,$content);
            }

        }else{
            $code = 123456;
        }
        // 短信发送成功后把发送的验证码保存在redis里
        Cache::store('redis')->set($mobile, $code, 60);
        return true;
    }

    public function sendByAliyun($mobile, $code)
    {
        try {
            $result = $this->easysms->send($mobile, [
                'template' => 'SMS_183267170',
                'data' => [
                    'code' => $code
                ],
            ]);
        } catch (NoGatewayAvailableException $exception) {
            throw new \Exception($exception->getException('aliyun')->getMessage());
        }

        return $result;
    }

    // 发送乐信短信
    public function sendBylexin($mobile,$content)
    {
        $config = Config::get('lexinsms.');
        try {
            $sms = new \SendUtility($config);
            $result = $sms->send($mobile,$content);
        } catch (NoGatewayAvailableException $exception) {
            throw new \Exception($exception->getException('aliyun')->getMessage());
        }

        return $result;
    }
}
