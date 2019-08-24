<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-08-21
 * Time: 16:41.
 */

namespace Wechat\Controller;


use Common\Controller\AdminBase;
use Wechat\Model\OfficeQrcodeModel;
use Wechat\Model\OfficesModel;
use Wechat\Model\OfficeTemplateListModel;
use Wechat\Model\OfficeUsersModel;
use Wechat\Service\OfficeQrcodeService;
use Wechat\Service\OfficeTemplateService;

class OfficeController extends AdminBase
{

    /**
     * 创建小程序码
     */
    function createQrcode()
    {
        $appId = I('post.app_id');
        $type = I('post.type');
        $expireTime = I('post.expire_time');
        $param = I('post.param');
        $officeQrcodeService = new OfficeQrcodeService($appId);
        if ($type == OfficeQrcodeModel::QRCODE_TYPE_TEMPORARY) {
            //将过期时间转化成秒
            $expireTime = $expireTime * 86400;
            $res = $officeQrcodeService->temporary($param, $expireTime);
        } else {
            $res = $officeQrcodeService->forever($param);
        }
        $this->ajaxReturn($res);
    }

    /**
     * 删除小程序码
     */
    function deleteCode()
    {
        $id = I('post.id');
        $officeQrcodeModel = new OfficeQrcodeModel();
        $miniCode = $officeQrcodeModel->where(['id' => $id])->find();
        if ($miniCode) {
            $res = $officeQrcodeModel->where(['id' => $miniCode['id']])->delete();
            if ($res) {
                $this->ajaxReturn(self::createReturn(true, [], '删除成功'));
            } else {
                $this->ajaxReturn(self::createReturn(false, [], '删除失败'));
            }
        } else {
            $this->ajaxReturn(self::createReturn(false, [], '找不到小程序码'));
        }
    }

    function qrcodeList()
    {
        if (IS_AJAX) {
            $appId = I('get.app_id', '');
            $page = I('get.page', 1);
            $limit = I('get.limit', 20);
            $where = [];
            if ($appId) {
                $where['app_id'] = ['like', '%'.$appId.'%'];
            }
            $officeQrcodeModel = new OfficeQrcodeModel();
            $res = $officeQrcodeModel->where($where)->page($page, $limit)->order('id DESC')->select();
            $totalCount = $officeQrcodeModel->where($where)->count();
            $this->ajaxReturn(self::createReturnList(true, $res ? $res : [], $page, $limit, $totalCount, ceil($totalCount / $limit), '获取成功'));
        }
        $this->display('qrcodelist');
    }

    /**
     * 发送模板消息测试
     */
    function sendTemplateMsg()
    {
        $appId = I('post.app_id');
        $touserOpenid = I('post.touser_openid');
        $templateId = I('post.template_id');
        $keywords = I('post.keywords');
        $page = I('post.page');
        $pageType = I('post.page_type');
        $miniAppid = I('post.mini_appid');
        $sendData = [];
        foreach ($keywords as $keyword) {
            $sendData[$keyword['key']] = $keyword['value'];
        }
        $miniProgram = [];
        if ($pageType == 'mini') {
            $miniProgram = [
                'appid'    => $miniAppid,
                'pagepath' => $page
            ];
        }
        $templateService = new OfficeTemplateService($appId);
        $res = $templateService->sendTemplateMsg($touserOpenid, $templateId, $sendData, $page, $miniProgram);
        $this->ajaxReturn($res);
    }

    /**
     * 删除消息模板
     */
    function deleteTemplate()
    {
        $id = I('post.id');
        $officeTemplateListModel = new OfficeTemplateListModel();
        $template = $officeTemplateListModel->where(['id' => $id])->find();
        if ($template) {
            $res = $officeTemplateListModel->where(['id' => $template['id']])->delete();
            if ($res) {
                $this->ajaxReturn(self::createReturn(true, [], '删除成功'));
            } else {
                $this->ajaxReturn(self::createReturn(false, [], '删除失败'));
            }
        } else {
            $this->ajaxReturn(self::createReturn(false, [], '找不到模板消息记录'));
        }
    }

    /**
     * 同步消息模板
     */
    function syncTemplateList()
    {
        //获取所有的公众号
        $ofiiceModel = new OfficesModel();
        $minioffices = $ofiiceModel->where(['account_type' => OfficesModel::ACCOUNT_TYPE_OFFICE])->field("app_id")->select();
        foreach ($minioffices as $minioffice) {
            $appId = $minioffice['app_id'];
            $templateService = new OfficeTemplateService($appId);
            $templateService->getTemplateList();
        }
        $this->ajaxReturn(self::createReturn(true, [], '同步成功'));
    }

    function templateList()
    {
        if (IS_AJAX) {
            $appId = I('get.app_id', '');
            $title = I('get.title', '');
            $page = I('get.page', 1);
            $limit = I('get.limit', 20);
            $where = [];
            if ($appId) {
                $where['app_id'] = ['like', '%'.$appId.'%'];
            }
            if ($title) {
                $where['title'] = ['like', '%'.$title.'%'];
            }
            $officeTemplateModel = new OfficeTemplateListModel();
            $res = $officeTemplateModel->where($where)->page($page, $limit)->order('id DESC')->select();
            $totalCount = $officeTemplateModel->where($where)->count();
            $this->ajaxReturn(self::createReturnList(true, $res ? $res : [], $page, $limit, $totalCount, ceil($totalCount / $limit), '获取成功'));
        }
        $this->display('templatelist');
    }

    /**
     * 获取用户列表
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
            $officeUsersModel = new OfficeUsersModel();
            $res = $officeUsersModel->where($where)->page($page, $limit)->order('id DESC')->select();
            $totalCount = $officeUsersModel->where($where)->count();
            $this->ajaxReturn(self::createReturnList(true, $res ? $res : [], $page, $limit, $totalCount, ceil($totalCount / $limit), '获取成功'));
        }
        $this->display('users');
    }


    /**
     * 删除用户
     */
    function deleteUsers()
    {
        $id = I('post.id', 0);
        $officeUsersModel = new OfficeUsersModel();
        $user = $officeUsersModel->where(['id' => $id])->find();
        if ($user) {
            $res = $officeUsersModel->where(['id' => $user['id']])->delete();
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