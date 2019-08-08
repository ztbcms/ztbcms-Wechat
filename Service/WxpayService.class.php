<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-08-08
 * Time: 08:46.
 */

namespace Wechat\Service;


use EasyWeChat\Factory;
use System\Service\BaseService;
use Think\Exception;
use Wechat\Model\OfficesModel;
use Wechat\Model\WxpayMchpayModel;
use Wechat\Model\WxpayOrderModel;
use Wechat\Model\WxpayRefundModel;

class WxpayService extends BaseService
{
    public $payment = null;
    protected $app_id = null;
    protected $errorMsg = '';

    function __construct($app_id, $isSandbox = false)
    {
        //获取授权小程序资料
        $officeModel = new OfficesModel();
        $office = $officeModel->where(['app_id' => $app_id])->find();
        if ($office) {
            $config = [
                'app_id'        => $office['app_id'],
                'mch_id'        => $office['mch_id'],
                'key'           => $office['key'],
                'sandbox'       => $isSandbox,
                'cert_path'     => $office['cert_path'], // XXX: 绝对路径！！！！
                'key_path'      => $office['key_path'],      // XXX: 绝对路径！！！！
                // 下面为可选项
                // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
                'response_type' => 'array',
                'log'           => [
                    'level' => 'debug',
                    'file'  => __DIR__.'/wechat.log',
                ],
            ];
            $this->app_id = $app_id;
            $this->payment = Factory::payment($config);
        } else {
            throw new Exception("找不到该小程序信息");
        }
    }

    /**
     * 微信支付结果回调
     *
     * @param $func
     *
     * @throws \EasyWeChat\Kernel\Exceptions\Exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function handlePaidNotify($func)
    {
        return $this->payment->handlePaidNotify(function ($message, $fail) use ($func) {
            $outTradeNo = $message['out_trade_no'];
            $wxpayOrderModel = new WxpayOrderModel();
            $order = $wxpayOrderModel->where(['out_trade_no' => $outTradeNo])->find();
            if ($order) {
                $updateData = [
                    'mch_id'         => $message['mch_id'],
                    'nonce_str'      => $message['nonce_str'],
                    'sign'           => $message['sign'],
                    'result_code'    => $message['result_code'],
                    'err_code'       => empty($message['err_code']) ? '' : $message['err_code'],
                    'err_code_des'   => empty($message['err_code_des']) ? '' : $message['err_code_des'],
                    'open_id'        => $message['openid'],
                    'is_subscribe'   => $message['is_subscribe'],
                    'trade_type'     => $message['trade_type'],
                    'bank_type'      => $message['bank_type'],
                    'total_fee'      => $message['total_fee'],
                    'cash_fee'       => $message['cash_fee'],
                    'transaction_id' => $message['transaction_id'],
                    'out_trade_no'   => $message['out_trade_no'],
                    'time_end'       => $message['time_end'],
                    'update_time'    => time()
                ];
                $wxpayOrderModel->where(['id' => $order['id']])->save($updateData);
                $func($message, $fail);
            } else {
                $fail('订单不存在');
            }
        });
    }

    /**
     * 执行企业付款操作
     *
     * @throws Exception
     * @return array
     */
    function doMchpayOrder()
    {
        $wxpayMchpayModel = new WxpayMchpayModel();
        $where = [
            'app_id'            => $this->app_id,
            'status'            => WxpayRefundModel::STATUS_NO, //处理未完成的退款
            'next_process_time' => ['lt', time()],//处理时间小于现在时间
            'process_count'     => ['lt', 7],//处理次数小于7次
        ];
        $mchpayOrders = $wxpayMchpayModel->where($where)->select();
        $nextProcessTimeArray = [60, 300, 900, 3600, 10800, 21600, 86400];
        foreach ($mchpayOrders as $mchpayOrder) {
            try {
                $mchpayRes = $this->payment->transfer->toBalance([
                    'partner_trade_no' => $mchpayOrder['partner_trade_no'], // 商户订单号，需保持唯一性(只能是字母或者数字，不能包含有符号)
                    'openid'           => $mchpayOrder['open_id'],
                    'check_name'       => 'NO_CHECK', // NO_CHECK：不校验真实姓名, FORCE_CHECK：强校验真实姓名
                    'amount'           => $mchpayOrder['amount'], // 企业付款金额，单位为分
                    'desc'             => $mchpayOrder['description'], // 企业付款操作说明信息。必填
                ]);
                if ($mchpayRes['result_code'] == 'SUCCESS' && $mchpayRes['return_code'] == 'SUCCESS') {
                    $postData = [
                        'status'            => WxpayMchpayModel::STATUS_YES,
                        'refund_result'     => json_encode($mchpayRes),
                        'next_process_time' => time() + (empty($nextProcessTimeArray[$mchpayOrder['next_process_count']]) ? 86400 : $nextProcessTimeArray[$mchpayOrder['next_process_count']]),
                        'process_count'     => $mchpayOrder['next_process_count'] + 1,
                        'update_time'       => time()
                    ];
                    $wxpayMchpayModel->where(['id' => $mchpayOrder['id']])->save($postData);
                } else {
                    $postData = [
                        'status'            => WxpayMchpayModel::STATUS_NO,
                        'refund_result'     => json_encode($mchpayRes),
                        'next_process_time' => time() + (empty($nextProcessTimeArray[$mchpayOrder['next_process_count']]) ? 86400 : $nextProcessTimeArray[$mchpayOrder['next_process_count']]),
                        'process_count'     => $mchpayOrder['next_process_count'] + 1,
                        'update_time'       => time()
                    ];
                    $wxpayMchpayModel->where(['id' => $mchpayOrder['id']])->save($postData);
                }
            } catch (\EasyWeChat\Kernel\Exceptions\Exception $exception) {
                $postData = [
                    'status'            => WxpayMchpayModel::STATUS_NO,
                    'refund_result'     => $exception->getMessage(),
                    'next_process_time' => time() + (empty($nextProcessTimeArray[$mchpayOrder['next_process_count']]) ? 86400 : $nextProcessTimeArray[$mchpayOrder['next_process_count']]),
                    'process_count'     => $mchpayOrder['next_process_count'] + 1,
                    'update_time'       => time()
                ];
                $wxpayMchpayModel->where(['id' => $mchpayOrder['id']])->save($postData);
            }
        }
        return self::createReturn(true, [], '处理完毕');
    }

    /**
     * 提交企业付款申请
     *
     * @param $openId
     * @param $amount
     * @param $description
     *
     * @throws Exception
     * @return array
     */
    function createMchpay($openId, $amount, $description = "企业付款")
    {
        $partnerTradeNo = date("YmdHis").rand(100000, 999990);
        $postData = [
            'app_id'            => $this->app_id,
            'partner_trade_no'  => $partnerTradeNo,
            'open_id'           => $openId,
            'amount'            => $amount,
            'description'       => $description,
            'status'            => WxpayMchpayModel::STATUS_NO,
            'next_process_time' => time(),
            'process_count'     => 0,
            'create_time'       => time()
        ];
        $wxpayMchpayModel = new WxpayMchpayModel();
        $res = $wxpayMchpayModel->add($postData);
        if ($res) {
            return self::createReturn(true, [], '申请企业付款成功，等待处理');
        } else {
            return self::createReturn(false, [], '');
        }
    }


    /**
     * 执行退款操作
     *
     * @throws Exception
     * @return array
     */
    function doRefundOrder()
    {
        $wxpayRefundModel = new WxpayRefundModel();
        $where = [
            'app_id'            => $this->app_id,
            'status'            => WxpayRefundModel::STATUS_NO, //处理未完成的退款
            'next_process_time' => ['lt', time()],//处理时间小于现在时间
            'process_count'     => ['lt', 7],//处理次数小于7次
        ];
        $refundOrders = $wxpayRefundModel->where($where)->select();
        $nextProcessTimeArray = [60, 300, 900, 3600, 10800, 21600, 86400];
        foreach ($refundOrders as $refundOrder) {
            try {
                $refundRes = $this->payment->refund->byOutTradeNumber($refundOrder['out_trade_no'], $refundOrder['out_refund_no'], $refundOrder['total_fee'], $refundOrder['refund_fee'], [
                    'refund_desc' => $refundOrder['refund_description'] ? $refundOrder['refund_description'] : '无',
                ]);
                if ($refundRes['result_code'] == 'SUCCESS' && $refundRes['return_code'] == 'SUCCESS') {
                    $postData = [
                        'status'            => WxpayRefundModel::STATUS_YES,
                        'refund_result'     => json_encode($refundRes),
                        'next_process_time' => time() + (empty($nextProcessTimeArray[$refundOrder['next_process_count']]) ? 86400 : $nextProcessTimeArray[$refundOrder['next_process_count']]),
                        'process_count'     => $refundOrder['next_process_count'] + 1,
                        'update_time'       => time()
                    ];
                    $wxpayRefundModel->where(['id' => $refundOrder['id']])->save($postData);
                } else {
                    $postData = [
                        'status'            => WxpayRefundModel::STATUS_NO,
                        'refund_result'     => json_encode($refundRes),
                        'next_process_time' => time() + (empty($nextProcessTimeArray[$refundOrder['next_process_count']]) ? 86400 : $nextProcessTimeArray[$refundOrder['next_process_count']]),
                        'process_count'     => $refundOrder['next_process_count'] + 1,
                        'update_time'       => time()
                    ];
                    $wxpayRefundModel->where(['id' => $refundOrder['id']])->save($postData);
                }
            } catch (\EasyWeChat\Kernel\Exceptions\Exception $exception) {
                $postData = [
                    'status'            => WxpayRefundModel::STATUS_NO,
                    'refund_result'     => $exception->getMessage(),
                    'next_process_time' => time() + (empty($nextProcessTimeArray[$refundOrder['next_process_count']]) ? 86400 : $nextProcessTimeArray[$refundOrder['next_process_count']]),
                    'process_count'     => $refundOrder['next_process_count'] + 1,
                    'update_time'       => time()
                ];
                $wxpayRefundModel->where(['id' => $refundOrder['id']])->save($postData);
            }
        }
        return self::createReturn(true, [], '处理完毕');
    }

    /**
     * 提交退款处理
     *
     * @param $outTradeNo
     * @param $totalFee
     * @param $refundFee
     * @param $refundDescription
     *
     * @throws Exception
     * @return array
     */
    function createRefund($outTradeNo, $totalFee, $refundFee, $refundDescription)
    {
        $outRefundNo = date("YmdHis").rand(100000, 999990);
        $postData = [
            'app_id'             => $this->app_id,
            'out_trade_no'       => $outTradeNo,
            'out_refund_no'      => $outRefundNo,
            'total_fee'          => $totalFee,
            'refund_fee'         => $refundFee,
            'refund_description' => $refundDescription,
            'status'             => WxpayRefundModel::STATUS_NO,
            'next_process_time'  => time(),
            'process_count'      => 0,
            'create_time'        => time()
        ];
        $wxpayRefundModel = new WxpayRefundModel();
        $res = $wxpayRefundModel->add($postData);
        if ($res) {
            return self::createReturn(true, [], '申请退款成功，等待处理');
        } else {
            return self::createReturn(false, [], '');
        }
    }

    /**
     * 创建订单
     *
     * @param        $openId
     * @param        $outTradeNo
     * @param        $totalFee
     * @param        $notifyUrl
     * @param string $body
     * @param string $tradeType
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @return bool|mixed
     */
    function createUnity($openId, $outTradeNo, $totalFee, $notifyUrl, $body = "微信支付", $tradeType = "JSAPI")
    {
        $result = $this->payment->order->unify([
            'body'         => $body,
            'out_trade_no' => $outTradeNo,
            'total_fee'    => $totalFee,
            'notify_url'   => $notifyUrl, // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'trade_type'   => $tradeType, // 请对应换成你的支付方式对应的值类型
            'openid'       => $openId,
        ]);
        if ($result['result_code'] == 'SUCCESS' && $result['return_code'] == 'SUCCESS') {
            return $result['prepay_id'];
        }
        $this->errorMsg = $result['return_msg'];
        return false;
    }

    /**
     * 获取jssdk支付配置
     *
     * @param        $openId
     * @param        $outTradeNo
     * @param        $totalFee
     * @param        $notifyUrl
     * @param string $body
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \Think\Exception
     * @return array
     */
    function getJssdkPayConfig($openId, $outTradeNo, $totalFee, $notifyUrl, $body = "微信支付")
    {
        $prepayId = $this->createUnity($openId, $outTradeNo, $totalFee, $notifyUrl, $body, "JSAPI");
        if (!$prepayId) {
            return self::createReturn(false, [], '微信支付下单失败：'.$this->errorMsg);
        }
        $postData = [
            'app_id'       => $this->app_id,
            'open_id'      => $openId,
            'out_trade_no' => $outTradeNo,
            'total_fee'    => $totalFee,
            'create_time'  => time(),
            'notify_url'   => $notifyUrl
        ];
        //添加支付订单入库
        $wxpayOrderModel = new WxpayOrderModel();
        $wxpayOrderModel->add($postData);
        $res = $this->payment->jssdk->sdkConfig($prepayId);
        return createReturn(true, $res, '获取成功');
    }

    /**
     * 获取小程序支付配置
     *
     * @param        $openId
     * @param        $outTradeNo
     * @param        $totalFee
     * @param        $notifyUrl
     * @param string $body
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \Think\Exception
     * @return array
     */
    function getMiniPayConfig($openId, $outTradeNo, $totalFee, $notifyUrl, $body = "微信支付")
    {
        $prepayId = $this->createUnity($openId, $outTradeNo, $totalFee, $notifyUrl, $body, "JSAPI");
        if (!$prepayId) {
            return self::createReturn(false, [], '微信支付下单失败：'.$this->errorMsg);
        }
        $postData = [
            'app_id'       => $this->app_id,
            'open_id'      => $openId,
            'out_trade_no' => $outTradeNo,
            'total_fee'    => $totalFee,
            'create_time'  => time(),
            'notify_url'   => $notifyUrl
        ];
        //添加支付订单入库
        $wxpayOrderModel = new WxpayOrderModel();
        $wxpayOrderModel->add($postData);
        $res = $this->payment->jssdk->bridgeConfig($prepayId, false);
        return createReturn(true, $res, '获取成功');
    }
}