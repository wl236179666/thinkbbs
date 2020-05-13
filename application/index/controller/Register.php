<?php

namespace app\index\controller;

use think\Request;
use app\common\model\User;
use app\common\exception\ValidateException;
use app\common\model\Sms;
use think\facade\Session;

class Register extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return $this -> fetch();
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request,User $user)
    {
        if(!$request -> isPost() || !$request -> isAjax()){
            $message = '对不起，你访问页面不存在。';
            // 在跳转前把错误提示消息写入 session 里
            Session::flash('danger',$message);
            return $this -> error($message);
        }

        try{
            //保存表单提交数据
            $param = $request -> post();
            $user = User::register($param);
        }catch(ValidateException $e){
            return $this -> error($e -> getMessage(),null,['error' => $e -> getData()]);
        }catch(\Exception $e){
            return $this -> error('对不起，注册失败。');
        }
        $message = '恭喜你注册成功！';
        // 在调用 success 返回前把注册成功提示消息写入 session 里
        Session::flash('success', $message);
        //成功后跳转到注册表单页面
        return $this->success($message, '[page.root]');
    }

    /**
     * 验证字段值是否唯一
     * @Author   zhanghong(Laifuzi)
     */
    public function check_unique(Request $request)
    {
        if(!$request -> isAjax()){
            return $this -> redirect('[page.signup]');
        }

        $param = $request -> post();
        $is_valid = User::checkFieldUnique($param);
        if($is_valid){
            echo("true");
        }else{
            echo("false");
        }
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }

    /**
     * 发送注册验证码
     */
    public function send_code(Request $request)
    {
        if(!$request->isAjax()){
            return $this->redirect('[page.signup]');
        }else if(!$request->isPost()){
            return $this->error('对不起，你访问页面不存在。');
        }

        $mobile = $request->post('mobile');
        if(empty($mobile)){
            return $this->error('对不起，注册手机号码不能为空。');
        }
        $param = ['name' => 'mobile', 'mobile' => $mobile];
        if(User::checkFieldUnique($param)){
            return $this->error('对不起，你填写的手机号码已注册。');
        }

        try {
            $sms = new Sms();
            $sms->sendCode($mobile);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }

        return $this->success('验证码发送成功。');
    }
}
