{extend name="public/container"}
{block name="content"}
<style>
    .backlog-body{
        padding: 10px 15px;
        background-color: #f8f8f8;
        color: #999;
        border-radius: 2px;
        transition: all .3s;
        -webkit-transition: all .3s;
        overflow: hidden;
        max-height: 84px;
    }
    .backlog-body h3{
        margin-bottom: 10px;
    }
    .right-icon{
        position: absolute;
        right: 10px;
    }
    .backlog-body p cite {
        font-style: normal;
        font-size: 17px;
        font-weight: 300;
        color: #009688;
    }
    .layuiadmin-badge, .layuiadmin-btn-group, .layuiadmin-span-color {
        position: absolute;
        right: 15px;
    }
    .layuiadmin-badge {
        top: 50%;
        margin-top: -9px;
        color: #01AAED;
    }
    .layui-tab-title li{
        min-width: 119px;
    }
</style>
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12 layui-col-sm12 layui-col-lg12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <div class="layui-tab layui-tab-card">
                        <div class="layui-card-header">
                            <button class="layui-btn layui-btn-normal layui-btn-sm" type="button" data-type="refresh" onclick="window.location.reload()">
                                <i class="layui-icon">&#xe669;</i>刷新
                            </button>
                        </div>
                        <ul class="layui-tab-title" style="position: absolute;left: 25px;">
                            <li class="layui-this">专题获得</li>
                            <li>资料获得</li>
                            <li>试卷获得</li>
                        </ul>
                        <div class="layui-tab-content" id="content" style="margin-top: 30px;" v-cloak>
                            <div class="layui-tab-item layui-show">
                                <table class="layui-table">
                                    <thead>
                                    <tr>
                                        <th style="width: 20%;text-align: center;">获得方式</th>
                                        <th style="width: 40%;text-align: center;">专题名称</th>
                                        <th style="width: 20%;text-align: center;">获得时间</th>
                                        <th style="width: 10%;text-align: center;">操作</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(item,index) in PayList">
                                        <td class="text-center">{{item.type}}</td>
                                        <td>{{item.title}}</td>
                                        <td>{{item.add_time}}</td>
                                        <td class="text-center"><button class="layui-btn layui-btn-danger layui-btn-xs" @click="del_special(item,index)">删除</button></td>
                                    </tr>
                                    <tr v-show="PayList.length<=0" style="text-align: center">
                                        <td colspan="6">暂无数据</td>
                                    </tr>
                                    </tbody>
                                </table>
                                <div ref="page_buy" v-show="count.pay_count > limit" style="text-align: right;"></div>
                            </div>
                            <div class="layui-tab-item">
                                <table class="layui-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 20%;text-align: center;">获得方式</th>
                                            <th style="width: 40%;">资料名称</th>
                                            <th style="width: 20%;text-align: center;">获得时间</th>
                                            <th style="width: 10%;text-align: center;">操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item1,index1) in dataList">
                                            <td class="text-center">{{item1.type}}</td>
                                            <td>{{item1.title}}</td>
                                            <td>{{item1.add_time}}</td>
                                            <td class="text-center"><button class="layui-btn layui-btn-danger layui-btn-xs" @click="del_data(item1,index1)">删除</button></td>
                                        </tr>
                                        <tr v-show="dataList.length<=0" style="text-align: center">
                                            <td colspan="6">暂无数据</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div ref="page_order" v-show="count.data_count > limit" style="text-align: right;"></div>
                            </div>
                            <div class="layui-tab-item">
                                <table class="layui-table">
                                    <thead>
                                    <tr>
                                        <th style="width: 20%;text-align: center;">获得方式</th>
                                        <th style="width: 10%;">试卷类型</th>
                                        <th style="width: 40%;">试卷名称</th>
                                        <th style="width: 20%;text-align: center;">获得时间</th>
                                        <th style="width: 10%;text-align: center;">操作</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(item2,index2) in TestPaperList">
                                        <td class="text-center">{{item2.source}}</td>
                                        <td class="text-center">{{item2.type==1 ? '练习' : '考试'}}</td>
                                        <td>{{item2.title}}</td>
                                        <td>{{item2.add_time}}</td>
                                        <td class="text-center"><button class="layui-btn layui-btn-danger layui-btn-xs" @click="del_test(item2,index2)">删除</button></td>
                                    </tr>
                                        <tr v-show="TestPaperList.length<=0" style="text-align: center">
                                            <td colspan="4">暂无数据</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div ref="test_page" v-show="count.test_count > limit" style="text-align: right;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
<script>
    var count=<?=json_encode($count)?>,
        $uid=<?=$uid?>;
    require(['vue'],function(Vue) {
        new Vue({
            el: "#content",
            data: {
                limit:10,
                uid:$uid,
                dataList:[],
                PayList:[],
                TestPaperList:[],
                count:count,
                page:{
                    pay_page:1,
                    test_page:1,
                    data_page:1
                },
            },
            watch:{
                'page.data_page':function () {
                    this.getOneDataList();
                },
                'page.pay_page':function () {
                    this.getBuySpecilList();
                },
                'page.test_page':function () {
                    this.getBuyTestPaperList();
                }
            },
            methods:{
                del_special:function(item,index){
                    var that=this;
                    layList.layer.confirm('确认要删除此条记录？', {
                        btn: ['确认','取消'] //按钮
                    }, function(){
                        layList.baseGet(layList.U({a:"del_special_buy",q:{id:item.id}}),function (res) {
                            that.PayList.splice(index,1);
                            that.$set(that,'PayList',that.PayList);
                            layList.msg(res.msg);
                        });
                    }, function(){
                        layList.msg('已取消');
                    });
                },
                del_data:function(item,index){
                    var that=this;
                    layList.layer.confirm('确认要删除此条记录？', {
                        btn: ['确认','取消'] //按钮
                    }, function(){
                        layList.baseGet(layList.U({a:"del_data_buy",q:{id:item.id}}),function (res) {
                            that.dataList.splice(index,1);
                            that.$set(that,'dataList',that.dataList);
                            layList.msg(res.msg);
                        });
                    }, function(){
                        layList.msg('已取消');
                    });
                },
                del_test:function(item,index){
                    var that=this;
                    layList.layer.confirm('确认要删除此条记录？', {
                        btn: ['确认','取消'] //按钮
                    }, function(){
                        layList.baseGet(layList.U({a:"del_test_buy",q:{id:item.id}}),function (res) {
                            that.TestPaperList.splice(index,1);
                            that.$set(that,'TestPaperList',that.TestPaperList);
                            layList.msg(res.msg);
                        });
                    }, function(){
                        layList.msg('已取消');
                    });
                },
                getBuySpecilList:function(){
                    this.request('getUserBuySpecilList',this.page.pay_page,'PayList');
                },
                getBuyTestPaperList:function(){
                    this.request('getUserBuyTestPaperList',this.page.test_page,'TestPaperList');
                },
                getOneDataList:function () {
                    this.request('getUserBuyDataList',this.page.data_page,'dataList');
                },
                request:function (action,page,name) {
                    var that=this;
                    layList.baseGet(layList.U({a:action,p:{page:page,limit:this.limit,uid:this.uid}}),function (res) {
                        that.$set(that,name,res.data)
                    });
                }
            },
            mounted:function () {
                this.getBuySpecilList();
                this.getBuyTestPaperList();
                this.getOneDataList();
                var that=this;
                layList.laypage.render({
                    elem: that.$refs.page_order
                    ,count:that.count.data_count
                    ,limit:that.limit
                    ,theme: '#1E9FFF',
                    jump:function(obj){
                        that.page.data_page=obj.curr;
                    }
                });
                layList.laypage.render({
                    elem: that.$refs.page_buy
                    ,count:that.count.pay_count
                    ,limit:that.limit
                    ,theme: '#1E9FFF',
                    jump:function(obj){
                        that.page.pay_page=obj.curr;
                    }
                });
                layList.laypage.render({
                    elem: that.$refs.test_page
                    ,count:that.count.test_count
                    ,limit:that.limit
                    ,theme: '#1E9FFF',
                    jump:function(obj){
                        that.page.test_page=obj.curr;
                    }
                });
            }
        });
    });
</script>
{/block}
