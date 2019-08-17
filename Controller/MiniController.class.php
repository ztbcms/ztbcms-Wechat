<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-07-19
 * Time: 09:35.
 */

namespace Wechat\Controller;


use Common\Controller\AdminBase;
use Wechat\Model\MiniCodeModel;
use Wechat\Model\MiniTemplateListModel;
use Wechat\Model\MiniUsersModel;
use Wechat\Model\OfficesModel;
use Wechat\Service\MiniCodeService;
use Wechat\Service\MiniTemplateService;

class MiniController extends AdminBase
{
    /**
     * 创建小程序码
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \Think\Exception
     */
    function createCode()
    {
        $appId = I('post.app_id');
        $type = I('post.type');
        $path = I('post.path');
        $scene = I('post.scene');
        $miniCodeService = new MiniCodeService($appId);
        if ($type == MiniCodeModel::CODE_TYPE_LIMIT) {
            $res = $miniCodeService->getMiniCode($path.$scene);
        } else {
            $opstional = [];
            if ($path) {
                $opstional['page'] = $path;
            }
            $res = $miniCodeService->getUnlimitMiniCode($scene, $opstional);
        }
        $this->ajaxReturn($res);
    }

    /**
     * 删除小程序码
     *
     * @throws \Think\Exception
     */
    function deleteCode()
    {
        $id = I('post.id');
        $miniCodeModel = new MiniCodeModel();
        $miniCode = $miniCodeModel->where(['id' => $id])->find();
        if ($miniCode) {
            $res = $miniCodeModel->where(['id' => $miniCode['id']])->delete();
            if ($res) {
                $this->ajaxReturn(self::createReturn(true, [], '删除成功'));
            } else {
                $this->ajaxReturn(self::createReturn(false, [], '删除失败'));
            }
        } else {
            $this->ajaxReturn(self::createReturn(false, [], '找不到小程序码'));
        }
    }

    /**
     * 获取小程序码
     */
    function codeList()
    {
        if (IS_AJAX) {
            $appId = I('get.app_id', '');
            $page = I('get.page', 1);
            $limit = I('get.limit', 20);
            $where = [];
            if ($appId) {
                $where['app_id'] = ['like', '%'.$appId.'%'];
            }
            $miniCodeModel = new MiniCodeModel();
            $res = $miniCodeModel->where($where)->page($page, $limit)->order('id DESC')->select();
            $totalCount = $miniCodeModel->where($where)->count();
            $this->ajaxReturn(self::createReturnList(true, $res ? $res : [], $page, $limit, $totalCount, ceil($totalCount / $limit), '获取成功'));
        }
        $this->display('codelist');
    }

    /**
     * 发送模板消息测试
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \Think\Exception
     */
    function sendTemplateMsg()
    {
        $appId = I('post.app_id');
        $touserOpenid = I('post.touser_openid');
        $templateId = I('post.template_id');
        $keywords = I('post.keywords');
        $page = I('post.page');
        $sendData = [];
        foreach ($keywords as $keyword) {
            $sendData[$keyword['key']] = $keyword['value'];
        }
        $templateService = new MiniTemplateService($appId);
        $res = $templateService->sendTemplateMessage($touserOpenid, $templateId, $page, $sendData);
        $this->ajaxReturn($res);
    }

    /**
     * 删除消息模板
     *
     * @throws \Think\Exception
     */
    function deleteTemplate()
    {
        $id = I('post.id');
        $miniTemplateModel = new MiniTemplateListModel();
        $template = $miniTemplateModel->where(['id' => $id])->find();
        if ($template) {
            $res = $miniTemplateModel->where(['id' => $template['id']])->delete();
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
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \Think\Exception
     */
    function syncTemplateList()
    {
        //获取所有的小程序
        $miniOfiiceModel = new OfficesModel();
        $minioffices = $miniOfiiceModel->where(['account_type' => OfficesModel::ACCOUNT_TYPE_MINI])->field("app_id")->select();
        foreach ($minioffices as $minioffice) {
            $appId = $minioffice['app_id'];
            $templateService = new MiniTemplateService($appId);
            $templateService->getTemplateList();
        }
        $this->ajaxReturn(self::createReturn(true, [], '同步成功'));
    }

    /**
     * 展示消息模板列表
     */
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
            $miniTemplateModel = new MiniTemplateListModel();
            $res = $miniTemplateModel->where($where)->page($page, $limit)->order('id DESC')->select();
            $totalCount = $miniTemplateModel->where($where)->count();
            $this->ajaxReturn(self::createReturnList(true, $res ? $res : [], $page, $limit, $totalCount, ceil($totalCount / $limit), '获取成功'));
        }
        $this->display('templatelist');
    }

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
            $res = $miniUsersModel->where($where)->page($page, $limit)->order('id DESC')->select();
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