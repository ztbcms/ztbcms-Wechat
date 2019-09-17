<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-08-08
 * Time: 10:11.
 */

namespace Wechat\Model;


use Think\Model;

class WxpayOrderModel extends Model
{
    protected $tableName = 'wechat_wxpay_order';
    protected $_validate = [
        ['app_id', 'require', '必须输入公众号appid！'],
    ];
}