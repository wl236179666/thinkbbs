<?php

namespace app\Common\model;

use think\Model;

class User extends Model
{
    /**
     * 验证字段值是否唯一
     * @Author   zhanghong(Laifuzi)
     * @param    array              $data 验证字段和字段值
     * @param    integer            $id   用户ID
     * @return   boolean
     */
    public static function checkFieldUnique($data,$id = 0)
    {
        // 验证字段名必须存在
        if(!isset($data['field'])){
            return false;
        }

        //验证字段名
        $field_name = $data['field'];

        // 验证字段值必须存在
        if(!isset($data[$field_name])){
            return false;
        }

        $field_value = $data[$field_name];

        $query = static::where($field_name,$field_value);
        if($id > 0){
            $query -> where('id','<>',$id);
        }

        if($query -> count()){
            return false;
        }

        return true;
    }
}
