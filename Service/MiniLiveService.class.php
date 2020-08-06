<?php

namespace Wechat\Service;

use Wechat\Model\MiniLiveModel;

class MiniLiveService extends MiniService
{

    /**
     * 同步获取直播列表
     * @param string $app_id
     * @param string $secret
     * @return array
     */
     function sysMiniLive(){
         $res = $this->app->live->getRooms(0,100);
         $room_info = $res['room_info'];
         $MiniLiveModel = new MiniLiveModel();
         //清除旧记录
         $MiniLiveModel->where(['app_id'=>$this->app_id])->delete();
         //更换新记录
         foreach ($room_info as $k => $v){
             $v['app_id'] = $this->app_id;
             $v['live_name'] = $v['name'];
             unset($v['name']);
             $MiniLiveModel->add($v);
         }
         return createReturn(true,'','同步成功');
    }

    /**
     * 获取视频回放
     * @param int $roomId
     * @return array
     */
    function getPlaybacks($roomId = 0){
        $res = $this->app->live->getPlaybacks($roomId);
        return createReturn(true,$res,'获取成功');
    }
}