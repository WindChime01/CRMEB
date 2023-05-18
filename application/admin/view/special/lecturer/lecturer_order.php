{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  >
        <div class="layui-col-md12" id="app" v-cloak>
            <div class="layui-card">
                <div class="layui-card-header">搜索条件</div>
                <div class="layui-card-body">
                    <form class="layui-form layui-form-pane" action="">
                        <div class="layui-form-item">
                            <div class="layui-col-lg12">
                                <label class="layui-form-label">创建时间:</label>
                                <div class="layui-input-block" data-type="data">
                                    <button class="layui-btn layui-btn-normal layui-btn-sm" type="button" v-for="item in dataList" @click="setData(item)" :class="{'layui-btn-primary':where.data!=item.value}">{{item.name}}</button>
                                    <button class="layui-btn layui-btn-sm" type="button" ref="time" @click="setData({value:'zd',is_zd:true})" :class="{'layui-btn-primary':where.data!='zd'}">自定义</button>
                                    <button type="button" class="layui-btn layui-btn-sm layui-btn-primary" v-show="showtime==true" ref="date_time">{$year.0} - {$year.1}</button>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <div class="layui-input-inline">
                                    <button class="layui-btn layui-btn-sm layui-btn-normal" type="button" @click="search"><i class="layui-icon layui-icon-search"></i>搜索</button>
                                    <button class="layui-btn layui-btn-sm layui-btn-normal" type="button" @click="refresh"><i class="layui-icon layui-icon-refresh" ></i>刷新</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!--end-->
            <!-- 中间详细信息-->
            <div :class="item.col!=undefined ? 'layui-col-sm'+item.col+' '+'layui-col-md'+item.col:'layui-col-sm6 layui-col-md4'" v-for="item in badge" v-if="item.count > 0">
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
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">课程订单</div>
                <div class="layui-card-body">
                    <table class="layui-hide" id="userList" lay-filter="userList"></table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
<script>
    var lecturer_id={$lecturer_id};
    layList.tableList('userList',"{:Url('lecturer_order_list')}?lecturer_id={$lecturer_id}",function () {
        return [
            {field: 'order_id', title: '订单号', sort: true,event:'order_id',width:'20%',templet:'#order_id'},
            {field: 'nickname', title: '昵称',align: 'center'},
            {field: 'title', title: '专题名称',width:'21%',align: 'center'},
            {field: 'total_price', title: '专题价格',width:'10%',align: 'center'},
            {field: 'pay_price', title: '实际支付',width:'10%',align: 'center'},
            {field: 'pay_type_name', title: '支付方式',width:'10%',align: 'center'},
            {field: 'status_name', title: '支付状态',templet:'#paid',width:'8%',align: 'center'},
        ];
    });
    require(['vue'],function(Vue) {
        new Vue({
            el: "#app",
            data: {
                badge: [],
                dataList: [
                    {name: '全部', value: ''},
                    {name: '昨天', value: 'yesterday'},
                    {name: '今天', value: 'today'},
                    {name: '本周', value: 'week'},
                    {name: '本月', value: 'month'},
                    {name: '本季度', value: 'quarter'},
                    {name: '本年', value: 'year'},
                ],
                where:{
                    data:''
                },
                showtime: false,
            },
            methods: {
                setData:function(item){
                    var that=this;
                    if(item.is_zd==true){
                        that.showtime=true;
                        this.where.data=this.$refs.date_time.innerText;
                    }else{
                        this.showtime=false;
                        this.where.data=item.value;
                    }
                },
                getBadge:function() {
                    var that=this;
                    this.where.lecturer_id=lecturer_id;
                    layList.basePost(layList.Url({c:'special.lecturer',a:'getBadge'}),this.where,function (rem) {
                        that.badge=rem.data;
                    });
                },
                search:function () {
                    this.getBadge();
                    layList.reload(this.where,true);
                },
                refresh:function () {
                    window.location.reload();
                }
            },
            mounted:function () {
                var that=this;
                that.getBadge();
                layList.laydate.render({
                    elem:this.$refs.date_time,
                    trigger:'click',
                    range:true,
                    type: 'datetime',
                    change:function (value){
                        that.where.data=value;
                    }
                });
            }
        })
    });
</script>
{/block}
