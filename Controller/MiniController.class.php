<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-07-19
 * Time: 09:35.
 */

namespace Wechat\Controller;


use Common\Controller\AdminBase;
use Wechat\Model\MiniUsersModel;

class MiniController extends AdminBase
{
    /**
     * 获取用户信息
     *
     * @throws \Think\Exception
     */
    function users()
    {
        if (IS_AJAX) {
            $appId = I('get.app_id', '');
            $openId = I('get.open_id', '');
            $nickName = I('get.nick_name', '');
            $page = I('get.page', 1);
            $limit = I('get.limit', 20);
            $where = [];
            if ($appId) {
                $where['app_id'] = ['like', '%'.$appId.'%'];
            }
            if ($openId) {
                $where['open_id'] = ['like', '%'.$openId.'%'];
            }
            if ($nickName) {
                $where['nick_name'] = ['like', '%'.$nickName.'%'];
            }
            $miniUsersModel = new MiniUsersModel();
            $res = $miniUsersModel->where($where)->page($page, $limit)->select();
            $totalCount = $miniUsersModel->where($where)->count();
            $this->ajaxReturn(self::createReturnList(true, $res ? $res : [], $page, $limit, $totalCount, ceil($totalCount / $limit), '获取成功'));
        }
        $this->display();
    }

    /**
     * 删除用户
     *
     * @throws \Think\Exception
     */
    function deleteUsers()
    {
        $id = I('post.id', 0);
        $miniUsersModel = new MiniUsersModel();
        $user = $miniUsersModel->where(['id' => $id])->find();
        if ($user) {
            $res = $miniUsersModel->where(['id' => $user['id']])->delete();
            if ($res) {
                $this->ajaxReturn(self::createReturn(true, [], '删除成功'));
            } else {
                $this->ajaxReturn(self::createReturn(false, [], '删除失败'));
            }
        } else {
            $this->ajaxReturn(self::createReturn(false, [], '找不到删除信息'));
        }
    }
}