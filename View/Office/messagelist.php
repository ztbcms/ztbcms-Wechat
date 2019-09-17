<extend name="../../Admin/View/Common/element_layout"/>
<block name="content">
    <div id="app">
        <el-card>
            <div slot="header" class="clearfix">
                <span>消息列表</span>
            </div>
            <div>
                <div class="alert-msg">
                    <p>1.消息接受需要开启"服务配置"：微信公众平台>开发>基本配置>服务配置（启用）</p>
                    <p>2.填写 服务器地址(URL)：http://{xxx}/Wechat/Server/push/appid/{appid}</p>
                    <p>3.填写token，aes_key，注意token验证，需要在服务器先配置</p>
                </div>
                <el-form :inline="true" :model="searchData" class="demo-form-inline">
                    <el-form-item label="appid">
                        <el-input v-model="searchData.app_id" placeholder="请输入小程序appid"></el-input>
                    </el-form-item>
                    <el-form-item label="open_id">
                        <el-input v-model="searchData.open_id" placeholder="发送用户openid"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click="searchEvent">查询</el-button>
                    </el-form-item>
                </el-form>
            </div>
            <div>
                <el-tabs @tab-click="handleClick" v-model="searchData.msg_type" type="border-card">
                    <el-tab-pane name="text" label="文本消息">
                        <include file="./app/Application/Wechat/View/Office/MessageType/content.php"/>
                    </el-tab-pane>
                    <el-tab-pane name="image" label="图片消息">
                        <include file="./app/Application/Wechat/View/Office/MessageType/image.php"/>
                    </el-tab-pane>
                    <el-tab-pane name="video" label="视频消息">
                        <include file="./app/Application/Wechat/View/Office/MessageType/video.php"/>
                    </el-tab-pane>
                    <el-tab-pane name="voice" label="音频消息">
                        <include file="./app/Application/Wechat/View/Office/MessageType/voice.php"/>
                    </el-tab-pane>
                    <el-tab-pane name="location" label="位置消息">
                        <include file="./app/Application/Wechat/View/Office/MessageType/location.php"/>
                    </el-tab-pane>
                    <el-tab-pane name="link" label="链接消息">
                        <include file="./app/Application/Wechat/View/Office/MessageType/link.php"/>
                    </el-tab-pane>
                </el-tabs>

            </div>
            <div class="page-container">
                <el-pagination
                        background
                        :page-size="limit"
                        :page-count="totalPages"
                        :current-page="page"
                        :total="totalItems"
                        layout="prev, pager, next"
                        @current-change="currentChangeEvent">
                </el-pagination>
            </div>
        </el-card>
    </div>
    <style>
        .avatar {
            width: 60px;
            height: 60px;
        }

        .page-container {
            margin-top: 0px;
            text-align: center;
            padding: 10px;
        }

        .alert-msg {
            background-color: #e8f3fe;
            padding: 20px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            color: #7dbcfc;
            font-size: 14px;
        }
    </style>
    <script>
        $(document).ready(function () {
            new Vue({
                el: "#app",
                data: {
                    searchData: {
                        open_id: "",
                        app_id: "",
                        msg_type: "text"
                    },
                    users: [],
                    page: 1,
                    limit: 20,
                    totalPages: 0,
                    totalItems: 0,
                },
                mounted() {
                    this.getMessage();
                },
                methods: {
                    deleteEvent(row) {
                        var postData = {
                            id: row.id
                        };
                        console.log('callback', postData);
                        var _this = this;
                        this.$confirm('是否确认删除该记录', '提示', {
                            callback: function (e) {
                                if (e !== 'confirm') {
                                    return;
                                }
                                _this.httpPost('{:U("Wechat/Office/deleteMessage")}', postData, function (res) {
                                    if (res.status) {
                                        _this.$message.success('删除成功');
                                        _this.getMessage();
                                    } else {
                                        _this.$message.error(res.msg);
                                    }
                                })
                            }
                        });
                    },
                    handleClick(e) {
                        let name = e.name;
                        console.log('handleClick', name);
                        this.getMessage();
                    },
                    searchEvent() {
                        this.page = 1;
                        this.getMessage();
                    },
                    currentChangeEvent(page) {
                        this.page = page;
                        this.getMessage();
                    },
                    getMessage: function () {
                        var _this = this;
                        var where = Object.assign({
                            page: this.page,
                            limit: this.limit
                        }, this.searchData);
                        $.ajax({
                            url: "{:U('Wechat/Office/messageList')}",
                            dataType: 'json',
                            type: 'get',
                            data: where,
                            success: function (res) {
                                console.log("res", res);
                                if (res.status) {
                                    _this.users = res.data.items;
                                    _this.page = res.data.page;
                                    _this.limit = res.data.limit;
                                    _this.totalPages = res.data.total_pages;
                                    _this.totalItems = res.data.total_items
                                }
                            }
                        })
                    }
                }
            })
        });
    </script>
</block>

