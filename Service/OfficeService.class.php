<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-08-09
 * Time: 10:07.
 */

namespace Wechat\Service;


use EasyWeChat\Factory;
use System\Service\BaseService;
use Wechat\Model\OfficeEventMessageModel;
use Wechat\Model\OfficeMediaModel;
use Wechat\Model\OfficeMessageModel;
use Wechat\Model\OfficesModel;
use Think\Exception;

class OfficeService extends BaseService
{
    public $app = null;
    protected $app_id = null;


    function __construct($app_id)
    {
        //获取授权小程序资料
        $officeModel = new OfficesModel();
        $office = $officeModel->where(['app_id' => $app_id, 'account_type' => OfficesModel::ACCOUNT_TYPE_OFFICE])->find();
        if ($office) {
            $config = [
                'app_id'        => $office['app_id'],
                'secret'        => $office['secret'],
                'token'         => $office['token'],          // Token
                'aes_key'       => $office['aes_key'],        // EncodingAESKey，兼容与安全模式下请一定要填写！！！
                // 下面为可选项
                // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
                'response_type' => 'array',

                'log' => [
                    'level' => 'debug',
                    'file'  => __DIR__.'/wechat.log',
                ],
            ];
            $this->app_id = $app_id;
            $this->app = Factory::officialAccount($config);
        } else {
            throw new Exception("找不到该小程序信息");
        }
    }

    /**
     * 获取网页开发的jssdk
     *
     * @param       $url
     * @param array $APIs
     * @param bool  $debug
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @return array
     */
    function getJssdk($url, $APIs = [], $debug = false)
    {
        $this->app->jssdk->setUrl($url);
        $res = $this->app->jssdk->buildConfig($APIs, $debug);
        if ($res) {
            return self::createReturn(true, ['config' => $res], '获取成功');
        } else {
            return self::createReturn(false, [], '获取失败');
        }
    }

    /**
     * 处理事件消息
     *
     * @param $message
     *
     * @throws Exception
     * @return bool
     */
    function handleEventMessage($message)
    {
        $postData = [
            'app_id'         => $this->app_id,
            'to_user_name'   => $message['ToUserName'],
            'from_user_name' => $message['FromUserName'],
            'create_time'    => $message['CreateTime'],
            'msg_type'       => $message['MsgType'],
            'event'          => $message['Event'],
            'event_key'      => empty($message['EventKey']) ? '' : $message['EventKey'],
            'ticket'         => empty($message['Ticket']) ? '' : $message['Ticket'],
            'latitude'       => empty($message['Latitude']) ? '' : $message['Latitude'],
            'longitude'      => empty($message['Longitude']) ? '' : $message['Longitude'],
            'precision'      => empty($message['Precision']) ? '' : $message['Precision'],
        ];
        $officeEventMessageModel = new OfficeEventMessageModel();
        $res = $officeEventMessageModel->add($postData);
        return !!$res;
    }

    /**
     *  处理普通消息
     *
     * @param $message
     *
     * @throws Exception
     * @return bool
     */
    function handleMessage($message)
    {
        $postData = [
            'app_id'         => $this->app_id,
            'to_user_name'   => $message['ToUserName'],
            'from_user_name' => $message['FromUserName'],
            'create_time'    => $message['CreateTime'],
            'msg_type'       => $message['MsgType'],
            'msg_id'         => $message['MsgId'],
            'content'        => empty($message['Content']) ? '' : $message['Content'],
            'pic_url'        => empty($message['PicUrl']) ? '' : $message['PicUrl'],
            'media_id'       => empty($message['MediaId']) ? '' : $message['MediaId'],
            'format'         => empty($message['Format']) ? '' : $message['Format'],
            'recognition'    => empty($message['Recognition']) ? '' : $message['Recognition'],
            'thumb_media_id' => empty($message['ThumbMediaId']) ? '' : $message['ThumbMediaId'],
            'location_x'     => empty($message['Location_X']) ? '' : $message['Location_X'],
            'location_y'     => empty($message['Location_Y']) ? '' : $message['Location_Y'],
            'scale'          => empty($message['Scale']) ? '' : $message['Scale'],
            'label'          => empty($message['Label']) ? '' : $message['Label'],
            'title'          => empty($message['Title']) ? '' : $message['Title'],
            'description'    => empty($message['Description']) ? '' : $message['Description'],
            'url'            => empty($message['Url']) ? '' : $message['Url'],
        ];

        $officeMessageModel = new OfficeMessageModel();
        $res = $officeMessageModel->add($postData);
        return !!$res;
    }

    /**
     * 保存临时文件的素材
     *
     * @param $mediaId
     *
     * @return array
     */
    function saveMedia($mediaId)
    {
        $directory = C("UPLOADFILEPATH").'wechat/media/';
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        try {
            $stram = $this->app->media->get($mediaId);
            if ($stram instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
                // 以内容 md5 为文件名
                $fileName = $stram->saveAs($directory, md5($mediaId));
                $url = cache("Config.sitefileurl").'wechat/qrcode/'.$fileName;
                $filePath = C("UPLOADFILEPATH").'wechat/qrcode/'.$fileName;
                $explodeName = explode('.', $filePath);
                $result = [
                    'app_id'      => $this->app_id,
                    'media_id'    => $mediaId,
                    'file_url'    => $url,
                    'file_path'   => $filePath,
                    'file_type'   => $explodeName[1],
                    'create_time' => time(),
                ];
                $officeMediaModel = new OfficeMediaModel();
                $officeMediaModel->add($result);
                return self::createReturn(true, $result, '获取成功');
            } else {
                return self::createReturn(false, [], '获取失败');
            }
        } catch (\Exception $exception) {
            return self::createReturn(false, [], '获取失败:'.$exception->getMessage());
        }
    }
}