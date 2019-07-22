<extend name="../../Admin/View/Common/element_layout"/>
<block name="content">
    <div id="app">
        <el-card>
            <div slot="header" class="clearfix">
                <span>小程序用户列表</span>
            </div>
            <div>
                <el-table
                    :data="users"
                    border
                    style="width: 100%">
                    <el-table-column
                        prop="name"
                        align="center"
                        label="昵称"
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
    </div>
    <script>
        $(document).ready(function () {
            new Vue({
                el: "#app",
                data: {
                    users: []
                },
                mounted() {
                },
                methods: {
                }
            })
        });
    </script>
</block>

