<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-08-24
 * Time: 14:45.
 */

namespace Wechat\Controller;


use Common\Controller\Base;
use Wechat\Service\OfficeService;

class ServerController extends Base
{
    /**
     * 接收消息
     *
     * @param $appid
     */
    function push($appid)
    {
        try {
            $officeService = new OfficeService($appid);
            $response = $officeService->app->server->push(function ($message) use ($officeService) {
                switch ($message['MsgType']) {
                    case 'event':
                        $officeService->handleEventMessage($message);
                        break;
                }
            });
            $response->send();
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }
}