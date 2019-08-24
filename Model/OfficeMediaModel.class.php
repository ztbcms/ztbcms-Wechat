<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-08-24
 * Time: 18:02.
 */

namespace Wechat\Model;


use Think\Model;

class OfficeMediaModel extends Model
{
    protected $tableName = 'wechat_office_media';
    protected $_validate = [
        ['app_id', 'require', 'appid 必要填写！'],
    ];
}