<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-08-15
 * Time: 17:05.
 */

namespace Wechat\Model;


use Think\Model;

class OfficeQrcodeModel extends Model
{
    const QRCODE_TYPE_TEMPORARY = 0;
    const QRCODE_TYPE_FOREVER = 1;
    protected $tableName = 'wechat_office_qrcode';
    protected $_validate = [
        ['app_id', 'require', 'appid 必要填写！'],
    ];
}