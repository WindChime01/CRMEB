{extend name="public/container"}
{block name="head_top"}
<style>
    .layui-form-label {
        width: 100px;
        padding: 5px 15px;
    }
    .layui-input-block {
        margin-left: 100px;
    }
    .layui-btn-group .layui-btn-normal {
        border: 1px solid #0092DC;
        border-left: none;
    }
    .layui-btn-group .layui-btn-normal:first-child {
        border-left: 1px solid #0092DC;
    }
</style>
{/block}
{block name="content"}
<div class="layui-fluid">
    <div v-cloak id="app" class="layui-row layui-col-space15">
        <!--搜索条件-->
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">搜索条件</div>
                <div class="layui-card-body">
                    <form class="layui-form" lay-filter="form" action="">
                        <div class="layui-form-item">
                            <label class="layui-form-label">时间选择：</label>
                            <div class="layui-input-inline" data-type="data" style="width: auto;">
                                <div class="layui-btn-group">
                                    <button :class="[where.data === item.value ? 'layui-btn-normal' : 'layui-btn-primary', 'layui-btn layui-btn-sm']" type="button" v-for="item in dataList" @click="setData(item)">{{item.name}}</button>
                                </div>
                            </div>
                            <div class="layui-input-inline">
                                <input type="text" name="date" placeholder="自定义" autocomplete="off" id="date" class="layui-input" style="height: 30px;">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">用户类型：</label>
                            <div class="layui-input-block">
                                <div class="layui-btn-group">
                                    <button :class="[where.type === item.value ? 'layui-btn-normal' : 'layui-btn-primary', 'layui-btn layui-btn-sm']" @click="where.type = item.value" type="button" v-for="item in spread_type">
                                        {{item.name}}
                                        <span v-if="item.count !== undefined" class="layui-badge layui-bg-gray">{{item.count}}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">用户昵称：</label>
                            <div class="layui-input-inline">
                                <input type="text" name="nickname" v-model="where.nickname" placeholder="请输入姓名、电话、UID" autocomplete="off" class="layui-input" style="height: 30px;">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                                <button @click="search" type="button" class="layui-btn layui-btn-sm layui-btn-normal">
                                    <i class="layui-icon layui-icon-search"></i>搜索</button>
                                <button @click="refresh" type="reset" class="layui-btn layui-btn-primary layui-btn-sm">
                                    <i class="layui-icon layui-icon-refresh" ></i>刷新</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--end-->
        <!-- 中间详细信息-->
        <div :class="item.col!=undefined ? 'layui-col-sm'+item.col+' '+'layui-col-md'+item.col +' layui-col-xs'+item.col:'layui-col-sm4 layui-col-md3'" v-for="item in badge" v-if="item.count > 0">
            <div class="layui-card">
                <div class="layui-card-header">
                    {{item.name}}
                    <span class="layui-badge layuiadmin-badge" :class="item.background_color">{{item.field}}</span>
                </div>
                <div class="layui-card-body">
                    <p class="layuiadmin-big-font">{{item.count}}</p>
                    <p v-show="item.content!=undefined">
                        {{item.content}}
                        <span class="layuiadmin-span-color">{{item.sum}}<i :class="item.class"></i></span>
                    </p>
                </div>
            </div>
        </div>
        <!--enb-->
    </div>
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">分销员列表</div>
                <div class="layui-card-body">
                    <div class="layui-btn-container">
                    </div>
                    <table class="layui-hide" id="userList" lay-filter="userList"></table>
                    <script type="text/html" id="avatar">
                        <img style="cursor: pointer" lay-event='open_image' src="{{d.avatar}}">
                    </script>
                    <!--用户信息-->
                    <script type="text/html" id="userinfo">
                        昵称：{{d.nickname==null ? '暂无信息':d.nickname}}
                        <br>电话：{{d.phone==null ? '暂无信息':d.phone}}
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name="script"}
<script>
    var uid = {$uid};
    layList.form.render();
    layList.tableList('userList',"{:Url('get_stair_list',['uid'=>$uid])}",function () {
        return [
            {field: 'uid', title: 'UID',width:'6%',align: 'center'},
            {field: 'avatar', title: '头像',templet:'#avatar',width:'10%',align: 'center'},
            {field: 'real_name', title: '用户信息',templet:'#userinfo',width:'20%'},
            {field: 'promoter_name', title: '是否推广员',width:'8%',align: 'center'},
            {field: 'spread_count', title: '推广人数',sort: true,align: 'center'},
            {field: 'order_count', title: '订单数',sort: true,align: 'center'},
            {field: 'add_time', title: '关注时间',width:'14%',sort: true,align: 'center'},
        ];
    });
    require(['vue'],function(Vue) {
        new Vue({
            el: "#app",
            data: {
                badge: [],
                dataList: [
                    {name: '全部', value: ''},
                    {name: '今天', value: 'today'},
                    {name: '昨天', value: 'yesterday'},
                    {name: '本周', value: 'week'},
                    {name: '本月', value: 'month'},
                    {name: '本季度', value: 'quarter'},
                    {name: '本年', value: 'year'},
                ],
                spread_type:[
                    {name:'全部',value:''},
                    {name:'一级推广人',value:'1'},
                    {name:'二级推广人',value:'2'},
                ],
                where:{
                    data:'',
                    nickname: '',
                    type:'',
                    uid:uid
                },
                showtime: false,
            },
            watch:{

            },
            methods:{
                getBadge:function(){
                    var that=this;
                    layList.baseGet(layList.Url({a:'get_stair_badge',q:that.where}),function (rem) {
                        that.badge=rem.data;
                    });
                },
                setData:function(item){
                    this.where.data = item.value;
                    layui.form.val('form', {
                        'date': ''
                    });
                },
                search:function () {
                    this.where.excel=0;
                    this.getBadge();
                    layList.reload(this.where,true);
                },
                refresh:function () {
                    layList.reload();
                    this.getBadge();
                }
            },
            mounted:function () {
                var that = this;
                this.getBadge();
                layList.laydate.render({
                    elem: '#date',
                    trigger: 'click',
                    range: true,
                    done: function (value){
                        that.where.data = value;
                    }
                });
            }
        })
    });
</script>
{/block}
