<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-08-08
 * Time: 10:31.
 */

namespace Wechat\Controller;


use Common\Controller\Base;
use EasyWeChat\Kernel\Exceptions\Exception;
use Wechat\Service\WxpayService;

class WxpayNotifyController extends Base
{
    /**
     * 因为跳掉地址不能带有参数，所有该url要可以重写  xxx/Wechat/WxpayNotify/index/appid/{appid}
     *
     * @param $appid
     *
     * @throws \Think\Exception
     */
    function index($appid)
    {
        $wxpay = new WxpayService($appid);
        try {
            $response = $wxpay->handlePaidNotify(function ($message, $fail) {
                //TODO 微信支付业务调用成功
            });
            echo $response->send();
        } catch (Exception $exception) {
            echo $exception->getMessage();
        }
    }
}