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
    static function sysMiniLive($app_id = '',$secret = ''){
        $url = "https://api.weixin.qq.com/wxa/business/getliveinfo?access_token=".self::getAccessToken($app_id,$secret);
        $data = [
            'start' => '0',
            'limit' => '100'
        ];
        $res = json_decode(self::http_post($url, $data,true),true);
        $room_info = $res['room_info'];
        $MiniLiveModel = new MiniLiveModel();
        //清除旧记录
        $MiniLiveModel->where(['app_id'=>$app_id])->delete();
        //更换新记录
        foreach ($room_info as $k => $v){
            $v['app_id'] = $app_id;
            $v['live_name'] = $v['name'];
            unset($v['name']);
            $MiniLiveModel->add($v);
        }
        return createReturn(true,'','同步成功');
    }

    /**
     * 直接获取AccessToken
     * @param null $appid
     * @return array
     */
    static function getAccessToken($appid = null,$secret = null){
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential';
        $params = [
            'appid' => $appid,
            'secret' => $secret
        ];
        foreach ($params as $key => $value) {
            $url .= '&' . $key . '=' . $value;
        }
        $res = json_decode(self::http_get($url),true);
        return $res['access_token'];
    }


    /**
     * POST 请求
     *
     * @param string  $url
     * @param array   $param
     * @param boolean $is_json
     * @return string content
     */
    static function http_post($url, $param, $is_json = false) {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== false) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, true);
        if ($is_json) {
            curl_setopt($oCurl, CURLOPT_POSTFIELDS, json_encode($param));
            curl_setopt($oCurl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        } else {
            curl_setopt($oCurl, CURLOPT_POSTFIELDS, $param);
        }
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

    /**
     * GET 请求
     *
     * @param       $url
     * @param array $params
     * @return bool|mixed
     */
    static function http_get($url, $params = []) {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== false) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
        }

        if (is_array($params && count($params) > 0)) {
            $url .= '?';
            foreach ($params as $key => $value) {
                $url .= $key . '=' . $value . '&';
            }
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

}