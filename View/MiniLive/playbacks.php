<extend name="../../Admin/View/Common/element_layout"/>
<block name="content">
    <div id="app" v-cloak>
        <el-card>
            <div slot="header" class="clearfix">
                <span>直播回放列表</span>
            </div>
            <div v-if="playUrl" style="text-align: center">
                <video style="width: 500px;height: 300px" controls="controls" :src="playUrl"></video>
            </div>
            <div>
                <el-table
                        :data="lists"
                        border
                        style="width: 100%">

                    <el-table-column
                            label="序号"
                            align="center"
                            width="40">
                        <template slot-scope="scope">
                            {{scope.$index}}
                        </template>
                    </el-table-column>
                    <el-table-column
                            prop="media_url"
                            label="播放地址"
                            align="center"
                            min-width="120">
                    </el-table-column>
                    <el-table-column
                            label="过期时间"
                            align="center"
                            width="100">
                        <template slot-scope="scope">
                            {{scope.row.expire_time|getFormatDatetime}}
                        </template>
                    </el-table-column>

                    <el-table-column
                            fixed="right"
                            label="操作"
                            align="center"
                            width="100">
                        <template slot-scope="scope">
                            <el-button @click="playUrl=scope.row.media_url" type="primary">播放</el-button>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
        </el-card>
    </div>

    <script>
        $(document).ready(function () {
            new Vue({
                el: "#app",
                data: {
                    lists: [],
                    app_id: '{$app_id}',
                    roomid: '{$room_id}',
                    playUrl: ''
                },
                mounted() {
                    this.getPlaybacks();
                },
                methods: {
                    getPlaybacks: function () {
                        var that = this;
                        this.httpGet("/Wechat/MiniLive/getPlaybacks", {
                            app_id: that.app_id,
                            roomId: that.roomid
                        }, function (res) {
                            if (res.status) {
                                var lists = [];
                                for (var i in res.data) {
                                    if (res.data[i].media_ext !== 'm3u8') {
                                        lists.push(res.data[i]);
                                    }
                                }
                                that.lists = lists;
                            } else {
                                that.$message.error(res.msg);
                            }
                        })
                    },
                    searchEvent() {
                        this.page = 1;
                        this.getList();
                    },
                    currentChangeEvent(page) {
                        this.page = page;
                        this.getList();
                    },

                }
            })
        });
    </script>
</block>