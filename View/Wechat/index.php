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
            <el-tabs v-model="activeName"  @tab-click="handleClick">
                <el-tab-pane label="开发配置" name="first">
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
                        <el-form-item v-if="form.account_type == 'office'" label="token">
                            <el-input v-model="form.token"></el-input>
                            <div class="el-tip">接受服务推送消息需要配置token</div>
                        </el-form-item>
                        <el-form-item v-if="form.account_type == 'office'" label="aes_key">
                            <el-input v-model="form.aes_key"></el-input>
                        </el-form-item>
                    </el-form>
                </el-tab-pane>
                <el-tab-pane label="微信支付" name="second">
                    <el-form :model="form" label-width="130px">
                        <el-form-item label="微信支付mch_id">
                            <el-input v-model="form.mch_id"></el-input>
                        </el-form-item>
                        <el-form-item label="微信支付key">
                            <el-input v-model="form.key"></el-input>
                        </el-form-item>
                        <el-form-item label="支付cert_path">
                            <el-upload
                                    class="upload-demo"
                                    action="{:U('Wechat/Wechat/uploadfile')}"
                                    :on-success="uploadSuccessCert"
                                    :show-file-list="false">

                                <el-button size="small" type="primary">点击上传</el-button>
                                <div slot="tip" class="el-upload__tip">{{cert_path}}</div>
                                <div slot="tip" class="el-upload__tip">请上传微信支付的 apiclient_cert.pem文件</div>
                            </el-upload>
                        </el-form-item>
                        <el-form-item label="支付key_path">
                            <el-upload
                                    class="upload-demo"
                                    action="{:U('Wechat/Wechat/uploadfile')}"
                                    :on-success="uploadSuccessKey"
                                    :show-file-list="false">
                                <el-button size="small" type="primary">点击上传</el-button>
                                <div slot="tip" class="el-upload__tip">{{key_path}}</div>
                                <div slot="tip" class="el-upload__tip">请上传微信支付的 apiclient_key.pem文件</div>
                            </el-upload>
                        </el-form-item>
                    </el-form>
                </el-tab-pane>
            </el-tabs>
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
                        secret: "",
                        mch_id: "",
                        key: "",
                        cert_path: "",
                        key_path: "",
                        token: "",
                        aes_key: "",
                    },
                    key_path: "",
                    cert_path: "",
                    offices: [],
                    activeName: 'first'
                },
                mounted() {
                    this.getDetail()
                },
                methods: {
                    uploadSuccessKey(res) {
                        console.log("uploadSuccessKey", res);
                        if (res.status) {
                            this.key_path = res.data.path;
                            this.form.key_path = this.key_path
                            // this.form = Object.assign(this.form, {key_path: res.data.path});
                        } else {
                            this.$message.error(res.msg)
                        }
                    },
                    uploadSuccessCert(res) {
                        console.log("uploadSuccessCert", res);
                        if (res.status) {
                            this.cert_path = res.data.path;
                            this.form.cert_path = this.cert_path
                        } else {
                            this.$message.error(res.msg)
                        }
                    },
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
                        this.cert_path = this.form.cert_path;
                        this.key_path = this.form.key_path;
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
                        this.form = {
                            id: 0,
                            name: "",
                            account_type: "office",
                            app_id: "",
                            secret: "",
                            mch_id: "",
                            key: "",
                            cert_path: "",
                            key_path: "",
                            token: "",
                            aes_key: ""
                        };
                        this.cert_path = "";
                        this.key_path = "";
                        this.dialogFormVisible = true
                    }
                }
            })
        });
    </script>
</block>

