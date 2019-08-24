<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-07-18
 * Time: 11:40.
 */

namespace Wechat\Controller;

use Common\Controller\AdminBase;
use Wechat\Model\OfficesModel;

class WechatController extends AdminBase
{
    function index()
    {
        if (IS_AJAX) {
            $OfficesModel = new OfficesModel();
            $offices = $OfficesModel->order("id DESC")->select();
            $this->ajaxReturn(self::createReturn(true, $offices, '获取成功'));
        } else {
            $this->display();
        }
    }

    /**
     * 新增/编辑公众号
     *
     * @throws \Think\Exception
     */
    function editOffice()
    {
        $id = I('post.id');
        $name = I('post.name');
        $accountType = I('post.account_type');
        $appId = I('post.app_id');
        $secret = I('post.secret');
        $mchId = I('post.mch_id');
        $key = I('post.key');
        $postData = [
            'name'         => $name,
            'account_type' => $accountType,
            'app_id'       => $appId,
            'secret'       => $secret,
            'mch_id'       => $mchId,
            'key'          => $key
        ];
        $OfficesModel = new OfficesModel();
        $res = $OfficesModel->editOffice($postData, $id);
        if ($res) {
            $this->ajaxReturn(self::createReturn(true, [], ''));
        } else {
            $this->ajaxReturn(self::createReturn(false, [], $OfficesModel->getError()));
        }
    }

    /**
     * 删除公众号
     *
     * @throws \Think\Exception
     */
    function deleteOffice()
    {
        $id = I('post.id');
        if (!$id) {
            $this->ajaxReturn(self::createReturn(false, [], '找不到该记录'));
        }
        $OfficesModel = new OfficesModel();
        $res = $OfficesModel->where(['id' => $id])->delete();
        if ($res) {
            $this->ajaxReturn(self::createReturn(true, [], ''));
        } else {
            $this->ajaxReturn(self::createReturn(false, [], $OfficesModel->getError()));
        }
    }
}