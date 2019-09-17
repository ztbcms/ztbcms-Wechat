<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-08-07
 * Time: 18:28.
 */

namespace Wechat\Model;


use Think\Model;

class MiniPhoneNumberModel extends Model
{
    protected $tableName = 'wechat_mini_phone_number';
    protected $_validate = [
        ['app_id', 'require', 'appid 必要填写！'],
    ];
}