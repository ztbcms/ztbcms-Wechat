<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-08-23
 * Time: 20:10.
 */

namespace Wechat\Controller;


use Common\Controller\AdminBase;
use Wechat\Model\OfficesModel;
use Wechat\Model\WxpayMchpayModel;
use Wechat\Model\WxpayOrderModel;
use Wechat\Model\WxpayRedpackModel;
use Wechat\Model\WxpayRefundModel;
use Wechat\Service\WxpayService;

class WxpayController extends AdminBase
{
    /**
     * 调用企业支付的处理
     *
     * @throws \Think\Exception
     */
    function handleMchpay()
    {
        //获取所有的公众号
        $ofiiceModel = new OfficesModel();
        $minioffices = $ofiiceModel->field("app_id")->select();
        foreach ($minioffices as $minioffice) {
            $appId = $minioffice['app_id'];
            $wxpayService = new WxpayService($appId);
            $wxpayService->doMchpayOrder();
        }
        $this->ajaxReturn(self::createReturn(true, [], '处理成功'));
    }

    /**
     * 删除企业付款记录
     *
     * @throws \Think\Exception
     */
    function deleteMchpays()
    {
        $id = I('post.id');
        if (!$id) {
            $this->ajaxReturn(self::createReturn(false, [], '找不到该记录'));
        }
        $wxpayMchpayModel = new WxpayMchpayModel();
        $res = $wxpayMchpayModel->where(['id' => $id])->delete();
        if ($res) {
            $this->ajaxReturn(self::createReturn(true, [], ''));
        } else {
            $this->ajaxReturn(self::createReturn(false, [], $wxpayMchpayModel->getError()));
        }
    }

    /**
     * 显示企业付款记录
     */
    function mchpays()
    {
        if (IS_AJAX) {
            $appId = I('get.app_id', '');
            $openId = I('get.open_id', '');
            $partnerTradeNo = I('get.partner_trade_no', '');
            $page = I('get.page', 1);
            $limit = I('get.limit', 20);
            $where = [];
            if ($appId) {
                $where['app_id'] = ['like', '%'.$appId.'%'];
            }
            if ($openId) {
                $where['open_id'] = ['like', '%'.$openId.'%'];
            }
            if ($partnerTradeNo) {
                $where['partner_trade_no'] = ['like', '%'.$partnerTradeNo.'%'];
            }
            $wxpayMchpayModel = new WxpayMchpayModel();
            $res = $wxpayMchpayModel->where($where)->page($page, $limit)->order('id DESC')->select();
            $totalCount = $wxpayMchpayModel->where($where)->count();
            $this->ajaxReturn(self::createReturnList(true, $res ? $res : [], $page, $limit, $totalCount, ceil($totalCount / $limit), '获取成功'));
        }
        $this->display('mchpays');
    }

    /**
     * 调用发送红包的处理
     *
     * @throws \Think\Exception
     */
    function handleRedpack()
    {
        //获取所有的公众号
        $ofiiceModel = new OfficesModel();
        $minioffices = $ofiiceModel->field("app_id")->select();
        foreach ($minioffices as $minioffice) {
            $appId = $minioffice['app_id'];
            $wxpayService = new WxpayService($appId);
            $wxpayService->doRedpackOrder();
        }
        $this->ajaxReturn(self::createReturn(true, [], '处理成功'));
    }

    /**
     * 删除红包记录
     *
     * @throws \Think\Exception
     */
    function deleteRedpacks()
    {
        $id = I('post.id');
        if (!$id) {
            $this->ajaxReturn(self::createReturn(false, [], '找不到该记录'));
        }
        $wxpayRedpackModel = new WxpayRedpackModel();
        $res = $wxpayRedpackModel->where(['id' => $id])->delete();
        if ($res) {
            $this->ajaxReturn(self::createReturn(true, [], ''));
        } else {
            $this->ajaxReturn(self::createReturn(false, [], $wxpayRedpackModel->getError()));
        }
    }

    /**
     * 显示红包记录
     */
    function redpacks()
    {
        if (IS_AJAX) {
            $appId = I('get.app_id', '');
            $openId = I('get.open_id', '');
            $mchBillno = I('get.mch_billno', '');
            $page = I('get.page', 1);
            $limit = I('get.limit', 20);
            $where = [];
            if ($appId) {
                $where['app_id'] = ['like', '%'.$appId.'%'];
            }
            if ($openId) {
                $where['open_id'] = ['like', '%'.$openId.'%'];
            }
            if ($mchBillno) {
                $where['mch_billno'] = ['like', '%'.$mchBillno.'%'];
            }
            $wxpayRedpackModel = new WxpayRedpackModel();
            $res = $wxpayRedpackModel->where($where)->page($page, $limit)->order('id DESC')->select();
            $totalCount = $wxpayRedpackModel->where($where)->count();
            $this->ajaxReturn(self::createReturnList(true, $res ? $res : [], $page, $limit, $totalCount, ceil($totalCount / $limit), '获取成功'));
        }
        $this->display('redpacks');
    }

    /**
     * 调用退款的处理
     *
     * @throws \Think\Exception
     */
    function handleRefund()
    {
        //获取所有的公众号
        $ofiiceModel = new OfficesModel();
        $minioffices = $ofiiceModel->field("app_id")->select();
        foreach ($minioffices as $minioffice) {
            $appId = $minioffice['app_id'];
            $wxpayService = new WxpayService($appId);
            $wxpayService->doRefundOrder();
        }
        $this->ajaxReturn(self::createReturn(true, [], '处理成功'));
    }

    /**
     * 删除退款申请
     *
     * @throws \Think\Exception
     */
    function deleteRefund()
    {
        $id = I('post.id');
        if (!$id) {
            $this->ajaxReturn(self::createReturn(false, [], '找不到该记录'));
        }
        $wxpayRefundModel = new WxpayRefundModel();
        $res = $wxpayRefundModel->where(['id' => $id])->delete();
        if ($res) {
            $this->ajaxReturn(self::createReturn(true, [], ''));
        } else {
            $this->ajaxReturn(self::createReturn(false, [], $wxpayRefundModel->getError()));
        }
    }

    /**
     * 退款申请记录
     */
    function refunds()
    {
        if (IS_AJAX) {
            $appId = I('get.app_id', '');
            $openId = I('get.open_id', '');
            $outTradeNo = I('get.out_trade_no', '');
            $page = I('get.page', 1);
            $limit = I('get.limit', 20);
            $where = [];
            if ($appId) {
                $where['app_id'] = ['like', '%'.$appId.'%'];
            }
            if ($openId) {
                $where['open_id'] = ['like', '%'.$openId.'%'];
            }
            if ($outTradeNo) {
                $where['out_trade_no'] = ['like', '%'.$outTradeNo.'%'];
            }
            $wxpayRefundModel = new WxpayRefundModel();
            $res = $wxpayRefundModel->where($where)->page($page, $limit)->order('id DESC')->select();
            $totalCount = $wxpayRefundModel->where($where)->count();
            $this->ajaxReturn(self::createReturnList(true, $res ? $res : [], $page, $limit, $totalCount, ceil($totalCount / $limit), '获取成功'));
        }
        $this->display('refunds');
    }

    /**
     * 删除支付订单
     *
     * @throws \Think\Exception
     */
    function deleteOrder()
    {
        $id = I('post.id');
        if (!$id) {
            $this->ajaxReturn(self::createReturn(false, [], '找不到该记录'));
        }
        $wxpayOrderModel = new WxpayOrderModel();
        $res = $wxpayOrderModel->where(['id' => $id])->delete();
        if ($res) {
            $this->ajaxReturn(self::createReturn(true, [], ''));
        } else {
            $this->ajaxReturn(self::createReturn(false, [], $wxpayOrderModel->getError()));
        }
    }

    /**
     * 显示支付订单
     */
    function orders()
    {
        if (IS_AJAX) {
            $appId = I('get.app_id', '');
            $openId = I('get.open_id', '');
            $outTradeNo = I('get.out_trade_no', '');
            $page = I('get.page', 1);
            $limit = I('get.limit', 20);
            $where = [];
            if ($appId) {
                $where['app_id'] = ['like', '%'.$appId.'%'];
            }
            if ($openId) {
                $where['open_id'] = ['like', '%'.$openId.'%'];
            }
            if ($outTradeNo) {
                $where['out_trade_no'] = ['like', '%'.$outTradeNo.'%'];
            }
            $wxpayOrderModel = new WxpayOrderModel();
            $res = $wxpayOrderModel->where($where)->page($page, $limit)->order('id DESC')->select();
            $totalCount = $wxpayOrderModel->where($where)->count();
            $this->ajaxReturn(self::createReturnList(true, $res ? $res : [], $page, $limit, $totalCount, ceil($totalCount / $limit), '获取成功'));
        }
        $this->display('orders');
    }
}