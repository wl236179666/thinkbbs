<?php

namespace app\Common\model;

use think\Model;
use think\facade\Session;
use app\common\validate\User as Validate;
use app\common\validate\Login as LoginValidate;
use app\common\exception\ValidateException;

class User extends Model
{
    public const CURRENT_KEY = 'current_user';

    /**
     * 验证字段值是否唯一
     * @Author   zhanghong(Laifuzi)
     * @param array $data 验证字段和字段值
     * @param integer $id 用户ID
     * @return   boolean
     */
    public static function checkFieldUnique($data, $id = 0)
    {
        // 验证字段名必须存在
        if (!isset($data['field'])) {
            return false;
        }

        //验证字段名
        $field_name = $data['field'];

        // 验证字段值必须存在
        if (!isset($data[$field_name])) {
            return false;
        }

        $field_value = $data[$field_name];

        $query = static::where($field_name, $field_value);
        if ($id > 0) {
            $query->where('id', '<>', $id);
        }

        if ($query->count()) {
            return false;
        }

        return true;
    }

    /**
     * 注册新用户
     * @Author   zhanghong(Laifuzi)
     * @param array $data 表单提交数据
     * @return   User                     新注册用户信息
     */
    public static function register($data)
    {
        $validate = new Validate;
        if (!$validate->batch(true)->check($data)) {
            $e = new ValidateException('注册数据验证失败');
            $e->setData($validate->getError());
            throw $e;
        }

        try {
            $user = new static;
            $user->allowField(['name', 'mobile', 'password'])->save($data);
        } catch (\Exception $e) {
            throw new \Exception('创建用户失败');
        }

        return $user;
    }

    /**
     * 密码保存时进行加密
     * @Author   zhanghong(Laifuzi)
     * @param string $value 原始密码
     */
    public function setPasswordAttr($value)
    {
        return password_hash($value, PASSWORD_DEFAULT);
    }

    /**
     * 用户登录
     * @Author   wangliang
     * @param    string             $mobile   登录手机号码
     * @param    string             $password 登录密码
     * @return   User
     */
    public static function login($mobile, $password)
    {
        $error = [];

        $validate = new LoginValidate();
        $data = [
            'mobile' => $mobile,
            'password' => $password
        ];
        if(!$validate -> batch(true) -> check($data)){
            $e = new ValidateException('登录数据验证失败');
            $e -> setData($validate -> getError());
            throw $e;
        }

        $user = static::where('mobile', $mobile)->find();

        if(empty($user)){
            // 传输注册手机号码不存在
            $error['mobile'] = '注册的用户不存在';
        }else if (!password_verify($password, $user->password)){
            // 传输登录密码错误
            $error['mobile'] = '登录手机或密码错误';
        }
        if(!empty($error)){
            $e = new ValidateException('登录数据验证失败');
            $e -> setData($error);
            throw $e;
        }
        // 把去除登录密码以外的信息存储到 Session 里
        unset($user['password']);
        Session::set(static::CURRENT_KEY, $user);

        return $user;
    }

    /**
     * 当前登录用户
     * @return   User
     */
    public static function currentUser()
    {
        return Session::get(static::CURRENT_KEY);
    }

    //退出登录
    public static function logout()
    {
        Session::delete(static::CURRENT_KEY);
        return true;
    }

}
