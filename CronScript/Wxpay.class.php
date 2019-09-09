<?php

// +----------------------------------------------------------------------
// | 计划任务 - 微信支付执行
// +----------------------------------------------------------------------

namespace Wechat\CronScript;

use Cron\Base\Cron;
use Wechat\Model\OfficesModel;
use Wechat\Service\WxpayService;


class WxpayCorn extends Cron
{

    //任务主体
    public function run($cronId)
    {
        \Think\Log::record("我执行了计划任务事例 Wxpay.class.php！");
        $officesModel = new OfficesModel();
        $offices = $officesModel->select();
        foreach ($offices as $office) {
            try {
                $wxpayService = new WxpayService($office['app_id']);
                //执行退款操作
                $wxpayService->doRefundOrder();
                //执行红包发放
                $wxpayService->doRedpackOrder();
                //执行企业付款
                $wxpayService->doMchpayOrder();
            } catch (\Exception $exception) {
                \Think\Log::record("执行计划任务事例 Wxpay.class.php，发生错误：".$exception->getMessage());
                continue;
            }
        }
    }
}

