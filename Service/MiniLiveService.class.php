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
    function sysMiniLive()
    {
        $res = $this->app->live->getRooms(0, 100);
        $room_info = $res['room_info'];
        $MiniLiveModel = new MiniLiveModel();
        //清除旧记录
        $MiniLiveModel->where(['app_id' => $this->app_id])->delete();
        //更换新记录
        foreach ($room_info as $k => $v) {
            $v['app_id'] = $this->app_id;
            $v['live_name'] = $v['name'];
            unset($v['name']);
            $MiniLiveModel->add($v);
        }
        return createReturn(true, '', '同步成功');
    }

    /**
     * 获取视频回放
     * @param int $roomId
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return array
     */
    function getPlaybacks($roomId = 0)
    {

        $list = D('MiniLiveReplay')->where(['roomid' => $roomId, 'app_id' => $this->app_id])->select();
        if ($list) {
            return self::createReturn(true, $list, 'ok');
        }
        $res = $this->app->broadcast->getPlaybacks($roomId);

        if (isset($res['errcode']) && $res['errcode'] == 0) {
            $liveReplay = $res['live_replay'];
            $liveReplayData = [];
            foreach ($liveReplay as $item) {
                $liveReplayData[] = [
                    'app_id' => $this->app_id,
                    'roomid' => $roomId,
                    'media_url' => $item['media_url'],
                    'media_ext' => pathinfo($item['media_url'], PATHINFO_EXTENSION),
                    'expire_time' => strtotime($item['expire_time']),
                    'create_time' => strtotime($item['create_time']),
                ];
            }
            D('MiniLiveReplay')->addAll($liveReplayData);
            return createReturn(true, $liveReplayData, '获取成功');
        } else {
            return createReturn(false, $res, '获取失败 ' . $res['errmsg']);
        }
    }
}