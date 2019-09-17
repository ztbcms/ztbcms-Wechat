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

/**
 * 微信(公众号+小程序)管理
 * Class WechatController
 * @package Wechat\Controller
 */
class WechatController extends AdminBase
{
    function index()
    {
        if (IS_AJAX) {
            $accountType = I('get.account_type');
            $page = I('get.page', 1);
            $limit = I('get.limit', 20);
            $where = [];
            if ($accountType) {
                $where['account_type'] = $accountType;
            }
            $OfficesModel = new OfficesModel();
            $offices = $OfficesModel->where($where)->order("id DESC")->page($page)->limit($limit)->select();
            $total_items = $OfficesModel->where($where)->count();
            $total_pages = ceil($total_items) / $limit;
            $this->ajaxReturn(self::createReturnList(true, $offices, $page, $limit, $total_items, $total_pages));
        } else {
            $this->display();
        }
    }

    /**
     * 获取详情
     */
    function getOfficeDetail()
    {
        $id = I('id');
        $OfficesModel = new OfficesModel();
        $res = $OfficesModel->where(['id' => ['EQ', $id]])->find();
        $this->ajaxReturn(self::createReturn(true, $res));
    }

    /**
     * 编辑公众号
     */
    function editOffice()
    {
        $this->display();
    }

    /**
     * 新增/编辑公众号
     *
     */
    function doEditOffice()
    {
        $id = I('post.id');
        $name = I('post.name');
        $accountType = I('post.account_type');
        $appId = I('post.app_id');
        $secret = I('post.secret');
        $mchId = I('post.mch_id');
        $key = I('post.key');
        $certPath = I('post.cert_path');
        $keyPath = I('post.key_path');
        $postData = [
            'name' => $name,
            'account_type' => $accountType,
            'app_id' => $appId,
            'secret' => $secret,
            'mch_id' => $mchId,
            'key' => $key,
            'cert_path' => $certPath,
            'key_path' => $keyPath
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

    /**
     * 上传文件接口
     */
    function uploadfile()
    {
        $upload = new \UploadFile();
        $upload->savePath = C("UPLOADFILEPATH") . 'wechat/cert/';
        if (!file_exists($upload->savePath)) {
            $res = mkdir($upload->savePath, 0766, true);
            if (!$res) {
                $this->ajaxReturn(self::createReturn(false, null, '无法创建文件目录，请检查上传目录 d/ 是否有读写权限'));
            }
        }

        $res = $upload->upload();
        if ($res) {
            $file = $upload->getUploadFileInfo()[0];
            $path = $file['savepath'] . $file['savename'];
            $this->ajaxReturn(self::createReturn(true, ['path' => $path], '上传成功'));
        } else {
            $this->ajaxReturn(self::createReturn(false, [], $upload->getErrorMsg()));
        }
    }
}