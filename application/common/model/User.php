<?php

namespace app\Common\model;

use think\Model;
use app\common\validate\User as Validate;
use app\common\exception\ValidateException;

class User extends Model
{
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
}
