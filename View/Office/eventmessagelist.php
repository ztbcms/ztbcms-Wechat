<extend name="../../Admin/View/Common/element_layout"/>
<block name="content">
    <div id="app">
        <el-card>
            <div slot="header" class="clearfix">
                <span>事件消息列表</span>
            </div>
            <div>
                <el-form :inline="true" :model="searchData" class="demo-form-inline">
                    <el-form-item label="appid">
                        <el-input v-model="searchData.app_id" placeholder="请输入小程序appid"></el-input>
                    </el-form-item>
                    <el-form-item label="open_id">
                        <el-input v-model="searchData.open_id" placeholder="发送用户openid"></el-input>
                    </el-form-item>
                    <el-form-item label="事件类型">
                        <el-select v-model="searchData.event" placeholder="请选择">
                            <el-option
                                    value="">
                                全部
                            </el-option>
                            <el-option
                                    value="subscribe">
                                关注
                            </el-option>
                            <el-option
                                    value="unsubscribe">
                                取消关注
                            </el-option>
                            <el-option
                                    value="SCAN">
                                扫描
                            </el-option>
                            <el-option
                                    value="LOCATION">
                                地理位置
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click="searchEvent">查询</el-button>
                    </el-form-item>
                </el-form>
            </div>
            <div>
                <el-table
                        :data="users"
                        border
                        style="width: 100%">
                    <el-table-column
                            prop="app_id"
                            align="center"
                            label="appid"
                            min-width="180">
                    </el-table-column>
                    <el-table-column
                            prop="from_user_name"
                            label="发送用户openid"
                            align="center"
                            min-width="180">
                    </el-table-column>
                    <el-table-column
                            prop="to_user_name"
                            label="接收者"
                            align="center"
                            min-width="180">
                    </el-table-column>
                    <el-table-column
                            prop="event"
                            label="事件类型"
                            align="center"
                            min-width="100">
                    </el-table-column>
                    <el-table-column
                            prop="event_key"
                            label="事件关键词"
                            align="center"
                            min-width="100">
                    </el-table-column>
                    <el-table-column
                            label="地理位置信息"
                            align="center"
                            min-width="200">
                        <template slot-scope="scope">
                            <div v-if=" scope.row.event == 'LOCATION'">
                                <div><b>纬度</b>：{{ scope.row.latitude }}</div>
                                <div><b>经度</b>：{{ scope.row.longitude }}</div>
                                <div><b>精确度</b>：{{ scope.row.precision }}</div>
                            </div>
                            <div v-else>
                                -
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column
                            align="center"
                            label="创建时间"
                            min-width="180">
                        <template slot-scope="scope">
                            {{scope.row.create_time|getFormatDatetime}}
                        </template>
                    </el-table-column>
                    <el-table-column
                            fixed="right"
                            label="操作"
                            align="center"
                            min-width="100">
                        <template slot-scope="scope">
                            <el-button @click="deleteEvent(scope.row)" type="danger">删除</el-button>
                        </template>
                    </el-table-column>
                </el-table>
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
    </style>
    <script>
        $(document).ready(function () {
            new Vue({
                el: "#app",
                data: {
                    searchData: {
                        open_id: "",
                        app_id: "",
                        event: ""
                    },
                    users: [],
                    page: 1,
                    limit: 20,
                    totalPages: 0,
                    totalItems: 0
                },
                mounted() {
                    this.getEventMessage();
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
                                _this.httpPost('{:U("Wechat/Office/deleteEventMessage")}', postData, function (res) {
                                    if (res.status) {
                                        _this.$message.success('删除成功');
                                        _this.getEventMessage();
                                    } else {
                                        _this.$message.error(res.msg);
                                    }
                                })
                            }
                        });
                    },
                    searchEvent() {
                        this.page = 1;
                        this.getEventMessage();
                    },
                    currentChangeEvent(page) {
                        this.page = page;
                        this.getEventMessage();
                    },
                    getEventMessage: function () {
                        var _this = this;
                        var where = Object.assign({
                            page: this.page,
                            limit: this.limit
                        }, this.searchData);
                        $.ajax({
                            url: "{:U('Wechat/Office/eventMessageList')}",
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

