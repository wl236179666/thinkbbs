<?php

namespace app\common\model;

use think\facade\Cache;
use think\facade\Config;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;

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
    public function sendCode($mobile)
    {
        $app_env = Config::get('app.env');
        if($app_env == 'production'){
            $code = mt_rand(100000, 999999);
            $this->sendByAliyun($mobile, $code);
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
}
