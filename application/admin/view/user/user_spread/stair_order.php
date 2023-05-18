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
                            <label class="layui-form-label">订单类型：</label>
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
                            <label class="layui-form-label">订单号：</label>
                            <div class="layui-input-inline">
                                <input type="text" name="order_id" v-model="where.order_id" placeholder="请输入姓名、电话、UID、订单号" autocomplete="off" id="date" class="layui-input" style="height: 30px;">
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
        <div :class="item.col!=undefined ? 'layui-col-sm'+item.col+' '+'layui-col-md'+item.col+' layui-col-xs'+item.col:'layui-col-sm6 layui-col-md3'" v-for="item in badge" v-if="item.count > 0">
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
                        <div class="layui-btn-group conrelTable">
                            <button class="layui-btn layui-btn-sm layui-btn-normal" type="button" data-type="refresh"><i class="layui-icon layui-icon-refresh" ></i>刷新</button>
                        </div>
                    </div>
                    <table class="layui-hide" id="userList" lay-filter="userList"></table>
                    <script type="text/html" id="time">
                        <p>下单：{{d._add_time}}</p>
                        <p>支付：{{d._pay_time}}</p>
                    </script>
                    <script type="text/html" id="user_info">
                        <p>{{d.user_info}}</p>
                    </script>
                    <script type="text/html" id="order_id">
                        <a href="javascript:;" lay-event="order_id">{{d.order_id}}</a>
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
    var action={
        refresh:function () {
            layList.reload();
        }
    },uid = {$uid};
    layList.form.render();
    layList.tableList('userList',"{:Url('get_stair_order_list',['uid'=>$uid])}",function () {
        return [
            {field: 'order_id', title: '订单ID',templet:'#order_id'},
            {field: 'user_info', title: '用户信息' ,templet:'#user_info'},
            {field: 'time', title: '时间',templet:'#time'},
            {field: 'pay_price', title: '订单金额'},
            {field: 'number_price', title: '返佣金额'}
        ];
    });
    $('.conrelTable').find('button').each(function () {
        var type=$(this).data('type');
        $(this).on('click',function () {
            action[type] && action[type]();
        })
    });
    layList.tool(function (event,data,obj) {
        switch (event){
            case 'order_id':
                $eb.createModalFrame('订单列表',layList.U({c:'order.store_order',a:'index',q:{real_name:data.order_id}}),{w:1100});
                break;
        }
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
                    {name:'一级推广人订单',value:'1'},
                    {name:'二级推广人订单',value:'2'},
                ],
                where:{
                    data:'',
                    order_id: '',
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
                    layList.baseGet(layList.Url({a:'get_stair_order_badge',q:that.where}),function (rem) {
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
