<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-08-24
 * Time: 15:26.
 */

namespace Wechat\Model;


use Think\Model;

class OfficeMessageModel extends Model
{
    protected $tableName = 'wechat_office_message';
    protected $_validate = [
        ['app_id', 'require', 'appid 必要填写！'],
    ];
}