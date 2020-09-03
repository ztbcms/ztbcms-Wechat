<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-09-09
 * Time: 09:41.
 */

namespace Wechat\Controller;


use Common\Controller\Base;
use Wechat\Service\OfficeService;
use Wechat\Service\WxpayService;

/**
 * 获取jssdk
 * Class IndexController
 * @package Wechat\Controller
 */
class IndexController extends Base
{
    /**
     * 获取指定appid的jssdk
     *
     * @param $appid
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Think\Exception
     */
    function getJssdk($appid)
    {
        $url = I('get.url', '', '');
        $officeService = new OfficeService($appid);
        $res = $officeService->getJssdk(urldecode($url));
        $this->ajaxReturn($res);
    }

    function wxpay($appid)
    {
        $openId = I('open_id', 'oizoj0S6LsEtbTZYhGYa3lTih_Dc');
        $wxpayService = new WxpayService($appid);
        $notifyUrl = urlDomain(get_url()) . "/Wechat/WxpayNotify/index/appid/{$appid}";
        $res = $wxpayService->getJssdkPayConfig($openId, time() . rand(1000, 9999), 1, $notifyUrl);
        $this->ajaxReturn($res);
    }
}