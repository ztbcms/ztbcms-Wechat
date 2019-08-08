<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-08-08
 * Time: 19:42.
 */

namespace Wechat\Model;


use Think\Model;

class WxpayMchpayModel extends Model
{
    const STATUS_YES = 1;
    const STATUS_NO = 0;
    protected $tableName = 'wechat_wxpay_mchpay';
    protected $_validate = [
        ['app_id', 'require', '必须输入公众号appid！'],
    ];
}