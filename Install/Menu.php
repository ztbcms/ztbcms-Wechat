<?php

return [
    [
        //父菜单ID，NULL或者不写系统默认，0为顶级菜单
        "parentid" => 0,
        //地址，[模块/]控制器/方法
        "route"    => "Wechat/%/%",
        //类型，1：权限认证+菜单，0：只作为菜单
        "type"     => 0,
        //状态，1是显示，0不显示（需要参数的，建议不显示，例如编辑,删除等操作）
        "status"   => 1,
        //名称
        "name"     => "微信管理",
        //备注
        "remark"   => "微信管理",
        //子菜单列表
        "child"    => [
            [
                "route"  => "Wechat/Wechat/index",
                "type"   => 0,
                "status" => 1,
                "name"   => "公众号管理",
            ],
            [
                "route"  => "Wechat/Mini/index",
                "type"   => 0,
                "status" => 1,
                "name"   => "小程序",
                "child"  => [
                    [
                        "route"  => "Wechat/Mini/users",
                        "type"   => 0,
                        "status" => 1,
                        "name"   => "授权用户",
                    ],
                    [
                        "route"  => "Wechat/Mini/codeList",
                        "type"   => 0,
                        "status" => 1,
                        "name"   => "小程序码",
                    ], [
                        "route"  => "Wechat/Mini/templateList",
                        "type"   => 0,
                        "status" => 1,
                        "name"   => "消息模板",
                    ],
                    [
                        "route"  => "Wechat/MiniSubscribeMessage/lists",
                        "type"   => 0,
                        "status" => 1,
                        "name"   => "订阅消息",
                    ],
                    [
                        "route"  => "Wechat/MiniSubscribeMessage/lists",
                        "type"   => 0,
                        "status" => 1,
                        "name"   => "消息发送记录",
                    ],
                    [
                        "route"  => "Wechat/MiniLive/lists",
                        "type"   => 0,
                        "status" => 1,
                        "name"   => "直播管理",
                    ],
                ]
            ], [
                "route"  => "Wechat/Office/index",
                "type"   => 0,
                "status" => 1,
                "name"   => "公众号",
                "child"  => [
                    [
                        "route"  => "Wechat/Office/users",
                        "type"   => 0,
                        "status" => 1,
                        "name"   => "授权用户",
                    ],
                    [
                        "route"  => "Wechat/Office/qrcodeList",
                        "type"   => 0,
                        "status" => 1,
                        "name"   => "参数二维码",
                    ], [
                        "route"  => "Wechat/Office/templateList",
                        "type"   => 0,
                        "status" => 1,
                        "name"   => "消息模板",
                    ], [
                        "route"  => "Wechat/Office/messageList",
                        "type"   => 0,
                        "status" => 1,
                        "name"   => "内容消息",
                    ], [
                        "route"  => "Wechat/Office/eventMessageList",
                        "type"   => 0,
                        "status" => 1,
                        "name"   => "事件消息",
                    ]
                ]
            ], [
                "route"  => "Wechat/Wxpay/index",
                "type"   => 0,
                "status" => 1,
                "name"   => "微信支付",
                "child"  => [
                    [
                        "route"  => "Wechat/Wxpay/orders",
                        "type"   => 0,
                        "status" => 1,
                        "name"   => "支付订单",
                    ], [
                        "route"  => "Wechat/Wxpay/refunds",
                        "type"   => 0,
                        "status" => 1,
                        "name"   => "退款订单",
                    ], [
                        "route"  => "Wechat/Wxpay/redpacks",
                        "type"   => 0,
                        "status" => 1,
                        "name"   => "发送红包",
                    ], [
                        "route"  => "Wechat/Wxpay/mchpays",
                        "type"   => 0,
                        "status" => 1,
                        "name"   => "企业付款",
                    ]
                ]
            ],
        ],
    ],
];
