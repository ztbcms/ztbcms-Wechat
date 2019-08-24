<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-07-18
 * Time: 15:46.
 */

namespace Wechat\Model;

use Think\Model;

class OfficesModel extends Model
{
    const ACCOUNT_TYPE_OFFICE = "office";
    const ACCOUNT_TYPE_MINI = "mini";
    protected $tableName = 'wechat_offices';

    protected $_validate = [
        ['name', 'require', '必须输入名称！'],
        ['app_id', 'require', '必须输入公众号appid！'],
        ['secret', 'require', '必须输入公众号secret！'],
    ];

    function editOffice($data, $editId = 0)
    {
        if ($editId > 0) {
            $data['id'] = $editId;
            $data['update_time'] = time();
            if ($this->data($data)) {
                return $this->save();
            }
        } else {
            $data['create_time'] = time();
            if ($this->create($data)) {
                return $this->add();
            }
        }
        return false;
    }
}