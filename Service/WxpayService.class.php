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
use Wechat\Model\WxpayOrderModel;

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