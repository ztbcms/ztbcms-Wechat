<extend name="../../Admin/View/Common/element_layout"/>
<block name="content">
    <div id="app" v-cloak>
        <el-card>
            <el-form :model="form" label-width="130px">
                <el-form-item label="名称">
                    <el-input v-model="form.name"></el-input>
                </el-form-item>
                <el-form-item label="类型">
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
                <el-form-item v-if="form.account_type == 'office'" label="token">
                    <el-input v-model="form.token"></el-input>
                    <div class="el-tip">接受服务推送消息需要配置令牌token(从微信公众号『开发-基本配置』-『服务器配置』中获取)</div>
                </el-form-item>
                <el-form-item v-if="form.account_type == 'office'" label="aes_key">
                    <el-input v-model="form.aes_key"></el-input>
                    <div class="el-tip">消息加解密密钥(从微信公众号『开发-基本配置』-『服务器配置』中获取)</div>
                </el-form-item>
                <el-form-item label="支付cert_path">
                    <el-input type="textarea"
                              v-model="form.cert_path"
                              :autosize="{ minRows: 2, maxRows: 6 }"
                              placeholder="请输入微信支付的 apiclient_cert.pem文件内容"></el-input>
                </el-form-item>
                <el-form-item label="支付key_path">
                    <el-input type="textarea"
                              v-model="form.key_path"
                              :autosize="{ minRows: 2, maxRows: 6 }"
                              placeholder="请输入微信支付的 apiclient_key.pem文件内容"></el-input>
                </el-form-item>

                <el-form-item label="" style="border-top: 1px solid gainsboro;margin-top: 10px;padding-top: 10px;">
                    <el-button type="primary" @click="submitEvent">确 定</el-button>
                </el-form-item>
            </el-form>

        </el-card>


    </div>
    <script>
        $(document).ready(function () {
            new Vue({
                el: "#app",
                data: {
                    form: {
                        id: '',
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
                },
                mounted() {
                    this.form.id = this.getUrlQuery('id');
                    this.getDetail()
                },
                methods: {
                    uploadSuccessKey(res) {
                        if (res.status) {
                            this.form.key_path = this.key_path
                        } else {
                            layer.msg(res.msg)
                        }
                    },
                    uploadSuccessCert(res) {
                        if (res.status) {
                            this.form.cert_path = this.cert_path
                        } else {
                            layer.msg(res.msg)
                        }
                    },
                    getDetail() {
                        var _this = this;
                        if (!this.form.id) {
                            return
                        }
                        $.ajax({
                            url: "/Wechat/Wechat/getOfficeDetail?id=" + _this.form.id,
                            dataType: 'json',
                            type: 'get',
                            success: function (res) {
                                console.log("res", res);
                                if (res.status) {
                                    _this.form = res.data;
                                }
                            }
                        })
                    },
                    submitEvent() {
                        var _this = this;
                        $.ajax({
                            url: "{:U('Wechat/Wechat/doEditOffice')}",
                            data: _this.form,
                            dataType: 'json',
                            type: 'post',
                            success: function (res) {
                                if (res.status) {
                                    layer.msg('操作成功');
                                    _this.getDetail();
                                    setTimeout(function () {
                                        window.parent.layer.closeAll();
                                    }, 2000);
                                } else {
                                    layer.msg(res.msg)
                                }
                            }
                        })
                    }
                }
            })
        });
    </script>
</block>

