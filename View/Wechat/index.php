<extend name="../../Admin/View/Common/element_layout"/>
<block name="content">
    <div id="app">
        <el-card>
            <div slot="header" class="clearfix">
                <span>公众号列表</span>
                <el-button @click="addEvent" style="float: right; padding: 3px 0" type="text">添加公众号</el-button>
            </div>
            <div>
                <el-table
                        :data="offices"
                        border
                        style="width: 100%">
                    <el-table-column
                            prop="name"
                            align="center"
                            label="名称"
                            min-width="180">
                    </el-table-column>
                    <el-table-column
                            label="类型"
                            align="center"
                            min-width="100">
                        <template slot-scope="scope">
                            <span v-if="scope.row.account_type=='mini'">小程序</span>
                            <span v-else>公众号</span>
                        </template>
                    </el-table-column>
                    <el-table-column
                            prop="app_id"
                            label="app_id"
                            align="center"
                            min-width="180">
                    </el-table-column>
                    <el-table-column
                            prop="secret"
                            label="secret"
                            align="center"
                            min-width="250">
                    </el-table-column>
                    <el-table-column
                            prop="mch_id"
                            label="微信支付mch_id"
                            align="center"
                            min-width="180">
                    </el-table-column>
                    <el-table-column
                            prop="key"
                            label="微信支付key"
                            align="center"
                            min-width="250">
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
                            <el-button @click="editEvent(scope.row)" type="primary">编辑</el-button>
                            <el-button @click="deleteEvent(scope.row)" type="danger">删除</el-button>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
        </el-card>

        <!-- 添加-->
        <el-dialog width="600px" :title="form.id==0?'添加公众号':'编辑公众号'" :visible.sync="dialogFormVisible">
            <el-form :model="form" label-width="130px">
                <el-form-item label="公众号名称">
                    <el-input v-model="form.name"></el-input>
                </el-form-item>
                <el-form-item label="公众号类型">
                    <el-radio-group v-model="form.account_type">
                        <el-radio label="office">公众号</el-radio>
                        <el-radio label="mini">小程序</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="app_id">
                    <el-input v-model="form.app_id"></el-input>
                </el-form-item>
                <el-form-item label="secret">
                    <el-input v-model="form.secret"></el-input>
                </el-form-item>
                <el-form-item label="微信支付mch_id">
                    <el-input v-model="form.mch_id"></el-input>
                </el-form-item>
                <el-form-item label="微信支付key">
                    <el-input v-model="form.key"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogFormVisible = false">取 消</el-button>
                <el-button type="primary" @click="submitEvent">确 定</el-button>
            </div>
        </el-dialog>
    </div>
    <script>
        $(document).ready(function () {
            new Vue({
                el: "#app",
                data: {
                    dialogFormVisible: false,
                    form: {
                        id: 0,
                        name: "",
                        account_type: "office",
                        app_id: "",
                        secret: ""
                    },
                    offices: []
                },
                mounted() {
                    this.getDetail()
                },
                methods: {
                    deleteEvent(deleteItem) {
                        var _this = this;
                        this.$confirm('是否确认删除"' + deleteItem.name + '" ？').then(res => {
                            //确认删除
                            $.ajax({
                                url: "{:U('Wechat/Wechat/deleteOffice')}",
                                data: {id: deleteItem.id},
                                dataType: 'json',
                                type: 'post',
                                success: function (res) {
                                    if (res.status) {
                                        _this.$message.success("删除成功");
                                        _this.getDetail()
                                    } else {
                                        _this.$message.error(res.msg);
                                    }
                                }
                            })
                        }).catch(() => {
                        })
                    },
                    editEvent(editItem) {
                        this.form = editItem;
                        this.dialogFormVisible = true
                    },
                    getDetail() {
                        var _this = this;
                        $.ajax({
                            url: "{:U('Wechat/Wechat/index')}",
                            dataType: 'json',
                            type: 'get',
                            success: function (res) {
                                console.log("res", res);
                                if (res.status) {
                                    _this.offices = res.data;
                                    _this.dialogFormVisible = false
                                }
                            }
                        })
                    },
                    submitEvent() {
                        console.log("submitEvent", this.form);
                        var _this = this
                        $.ajax({
                            url: "{:U('Wechat/Wechat/editOffice')}",
                            data: this.form,
                            dataType: 'json',
                            type: 'post',
                            success: function (res) {
                                if (res.status) {
                                    _this.$message.success("操作成功");
                                    _this.getDetail()
                                } else {
                                    _this.$message.error(res.msg);
                                }
                            }
                        })
                    },
                    addEvent: function () {
                        this.form.id = 0;
                        this.dialogFormVisible = true
                    }
                }
            })
        });
    </script>
</block>

