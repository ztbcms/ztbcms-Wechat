<extend name="../../Admin/View/Common/element_layout"/>
<block name="content">
    <div id="app" v-cloak>
        <el-card>
            <div slot="header" class="clearfix">
                <span>公众号列表</span>
            </div>
            <div>
                <el-button @click="addEvent" type="primary">添加公众号</el-button>
            </div>
            <div style="margin-top: 10px">
                <el-table
                        :data="offices"
                        border
                        style="width: 100%">
                    <el-table-column
                            prop="name"
                            align="center"
                            label="名称"
                            min-width="100">
                    </el-table-column>
                    <el-table-column
                            label="类型"
                            align="center"
                            min-width="80">
                        <template slot-scope="scope">
                            <span v-if="scope.row.account_type=='mini'">小程序</span>
                            <span v-else>公众号</span>
                        </template>
                    </el-table-column>
                    <el-table-column
                            label="开发信息"
                            align="center"
                            min-width="240">
                        <template slot-scope="scope">
                            <div style="text-align: left">
                                <p>APP_ID : {{ scope.row.app_id }}</p>
                                <p>SECRET : {{ scope.row.secret }}</p>
                            </div>
                        </template>
                    </el-table-column>

                    <el-table-column
                            label="微信支付信息"
                            align="center"
                            min-width="240">
                        <template slot-scope="scope">
                            <div style="text-align: left">
                                <p>mch_id : {{ scope.row.mch_id }}</p>
                                <p>key : {{ scope.row.key }}</p>
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
                            label="操作"
                            align="center"
                            min-width="180">
                        <template slot-scope="scope">
                            <el-button @click="editEvent(scope.row)" type="primary" size="small">编辑</el-button>
                            <el-button @click="deleteEvent(scope.row)" type="danger" size="small">删除</el-button>
                        </template>
                    </el-table-column>
                </el-table>

                <div class="pagination-container">
                    <el-pagination
                            background
                            layout="prev, pager, next, jumper"
                            :total="total"
                            v-show="total>0"
                            :current-page.sync="form.page"
                            :page-size.sync="form.limit"
                            @current-change="getList"
                    >
                    </el-pagination>
                </div>
            </div>
        </el-card>

    </div>

    <style>
        .pagination-container {
            padding: 32px 16px;
        }
    </style>

    <script>
        $(document).ready(function () {
            new Vue({
                el: "#app",
                data: {
                    offices: [],
                    form: {
                        page: 1,
                        limit: 10,
                    },
                    total: 0
                },
                mounted() {
                    this.getList()
                },
                methods: {
                    deleteEvent: function (item) {
                        var _this = this;
                        layer.confirm('是否确认删除"' + item.name + '" ？', {}, function () {
                            _this.doDeleteItem(item)
                        })
                    },
                    doDeleteItem: function (item) {
                        var _this = this;
                        //确认删除
                        $.ajax({
                            url: "{:U('Wechat/Wechat/deleteOffice')}",
                            data: {id: item.id},
                            dataType: 'json',
                            type: 'post',
                            success: function (res) {
                                if (res.status) {
                                    layer.msg('删除成功')
                                    _this.getList()
                                } else {
                                    layer.msg(res.msg)
                                }
                            }
                        })
                    },
                    editEvent: function (editItem) {
                        var _this = this
                        layer.open({
                            type: 2,
                            title: '操作',
                            content: "/Wechat/Wechat/editOffice?id=" + editItem.id,
                            area: ['80%', '70%'],
                            end: function () {
                                _this.getList()
                            }
                        })
                    },
                    getList: function () {
                        var _this = this;
                        $.ajax({
                            url: "{:U('Wechat/Wechat/index')}",
                            data: this.form,
                            dataType: 'json',
                            type: 'get',
                            success: function (res) {
                                if (res.status) {
                                    _this.offices = res.data.items
                                    _this.total = res.data.total_items
                                    _this.form.page = res.data.page
                                    _this.form.limit = res.data.limit
                                }
                            }
                        })
                    },

                    addEvent: function () {
                        var _this = this
                        layer.open({
                            type: 2,
                            title: '操作',
                            content: "/Wechat/Wechat/editOffice",
                            area: ['80%', '70%'],
                            end: function () {
                                _this.getList()
                            }
                        })
                    }
                }
            })
        })
    </script>
</block>

