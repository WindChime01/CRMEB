{extend name="public/container"}
{block name="head"}
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
<div v-cloak id="app" class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <!--搜索条件-->
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">活动报名订单</div>
                <div class="layui-card-body">
                    <form class="layui-form" lay-filter="form" action="" @submit.prevent>
                        <div class="layui-form-item">
                            <label class="layui-form-label">订单状态：</label>
                            <div class="layui-input-block">
                                <div class="layui-btn-group">
                                    <button :class="[where.type === item.value ? 'layui-btn-normal' : 'layui-btn-primary', 'layui-btn layui-btn-sm']" @click="where.type = item.value" type="button" v-for="item in orderStatus">
                                        {{item.name}}
                                        <span v-if="item.count !== undefined" :class="{ 'layui-bg-gray': item.class === undefined }" class="layui-badge">{{item.count}}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">创建时间：</label>
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
                            <label class="layui-form-label">搜索内容：</label>
                            <div class="layui-input-inline">
                                <input type="text" name="real_name" v-model="where.real_name" placeholder="请输入订单号、昵称、UID" autocomplete="off" class="layui-input" style="height: 30px;">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                                <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" @click="search">
                                    <i class="layui-icon">&#xe615;</i>搜索
                                </button>
                                <button class="layui-btn layui-btn-primary layui-btn-sm export" @click="excel">
                                    <i class="layui-icon">&#xe67d;</i>导出
                                </button>
                                <button class="layui-btn layui-btn-primary layui-btn-sm export" @click="refresh">
                                    <i class="layui-icon">&#xe669;</i>刷新
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--end-->
        <!-- 中间详细信息-->
        <div :class="item.col!=undefined ? 'layui-col-sm'+item.col+' '+'layui-col-md'+item.col:'layui-col-sm6 layui-col-md3'" v-for="item in badge" v-if="item.count > 0">
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
        <!--列表-->
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                </div>
            </div>
        </div>
        <!--end-->
    </div>

</div>
<!--订单-->
<script type="text/html" id="order_id">
    <h4>{{d.order_id}}</h4>
    <span style="color: {{d.color}};">{{d.order_name}}</span>　　
</script>
<!--用户信息-->
<script type="text/html" id="userinfo">
    {{d.nickname==null ? '暂无昵称':d.nickname}}/{{d.uid}}
</script>
<!--支付状态-->
<script type="text/html" id="paid">
    <p>{{d.pay_type}}</p>
</script>
<!--订单状态-->
<script type="text/html" id="status">
    {{d.status_name}}
    {{#  if(d.paid==1 && d.refund_status==2){ }}
    <p>退款时间:{{d.refund_reason_time}}</p>
    {{# }; }}
</script>
<!--商品信息-->
<script type="text/html" id="info">
    {{#  if(d._info.image){ }}
    <img style="float: left;margin-right: 10px;" src="{{d._info.image}}" width="45" height="25">
    {{# };}}
    <span>{{d._info.title}}</span>
</script>
<script type="text/html" id="act">
        {{#  if(d._status==1 && d.nickname!=null){ }}
        <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="dropdown(this)">
            <i class="layui-icon">&#xe625;</i>操作
        </button>
        <ul class="layui-nav-child layui-anim layui-anim-upbit">
            <li>
                <a lay-event='delete' href="javascript:void(0);" >
                    <i class="fa fa-trash"></i> 删除
                </a>
            </li>
        </ul>
        {{#  }else if(d._status==2 && d.nickname!=null){ }}
        <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="dropdown(this)">
            <i class="layui-icon">&#xe625;</i>操作
        </button>
        <ul class="layui-nav-child layui-anim layui-anim-upbit">
            <li>
                <a href="javascript:void(0);" onclick="$eb.createModalFrame('订单详情','{:Url('order_info')}?oid={{d.id}}')">
                    <i class="fa fa-file-text"></i> 订单详情
                </a>
            </li>
            {{#  if(d.pay_price!=d.refund_price){ }}
            <li>
                <a href="javascript:void(0);" onclick="$eb.createModalFrame('退款','{:Url('refund_y')}?id={{d.id}}',{w:400,h:300})">
                    <i class="fa fa-history"></i> 立即退款
                </a>
            </li>
            {{# } ;}}
            <li>
                <a lay-event='delete' href="javascript:void(0);" >
                    <i class="fa fa-trash"></i> 删除
                </a>
            </li>
        </ul>
        {{#  }else if(d._status==7 && d.nickname!=null){ }}
        <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="dropdown(this)">
            <i class="layui-icon">&#xe625;</i>操作
        </button>
        <ul class="layui-nav-child layui-anim layui-anim-upbit">
            <li>
                <a href="javascript:void(0);" onclick="$eb.createModalFrame('订单详情','{:Url('order_info')}?oid={{d.id}}')">
                    <i class="fa fa-file-text"></i> 订单详情
                </a>
            </li>
            {{#  if(d.pay_price!=d.refund_price){ }}
            <li>
                <a href="javascript:void(0);" onclick="$eb.createModalFrame('退款','{:Url('refund_y')}?id={{d.id}}',{w:400,h:300})">
                    <i class="fa fa-history"></i> 立即退款
                </a>
            </li>
            {{# } ;}}
            <li>
                <a lay-event='delete' href="javascript:void(0);" >
                    <i class="fa fa-trash"></i> 删除
                </a>
            </li>
        </ul>
        {{#  }else if(d.nickname==null){ }}
        <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="dropdown(this)">
            <i class="layui-icon">&#xe625;</i>操作
        </button>
        <ul class="layui-nav-child layui-anim layui-anim-upbit">
            <li>
                <a lay-event='delete' href="javascript:void(0);" >
                    <i class="fa fa-trash"></i> 删除
                </a>
            </li>
        </ul>
        {{#  }; }}
</script>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name="script"}
<script>
    //下拉框
    $(document).click(function (e) {
        $('.layui-nav-child').hide();
    });
    function dropdown(that){
        var oEvent = arguments.callee.caller.arguments[0] || event;
        oEvent.stopPropagation();
        var offset = $(that).offset();
        var top=offset.top-$(window).scrollTop();
        var index = $(that).parents('tr').data('index');
        $('.layui-nav-child').each(function (key) {
            if (key != index) {
                $(this).hide();
            }
        });
        if($(document).height() < top+$(that).next('ul').height()){
            $(that).next('ul').css({
                'padding': 10,
                'top': - ($(that).parent('td').height() / 2 + $(that).height() + $(that).next('ul').height()/2),
                'min-width': 'inherit',
                'position': 'absolute'
            }).toggle();
        }else{
            $(that).next('ul').css({
                'padding': 10,
                'top':$(that).parent('td').height() / 2 + $(that).height(),
                'min-width': 'inherit',
                'position': 'absolute'
            }).toggle();
        }
    }
    var orderCount=<?=json_encode($orderCount)?>;
    require(['vue'],function(Vue) {
        new Vue({
            el: "#app",
            data: {
                badge: [],
                orderStatus: [
                    {name: '全部', value: ''},
                    {name: '已支付', value: 1,count:orderCount.wf,class:true},
                    {name: '已退款', value: -2,count:orderCount.yt},
                ],
                dataList: [
                    {name: '全部', value: ''},
                    {name: '今天', value: 'today'},
                    {name: '昨天', value: 'yesterday'},
                    {name: '本周', value: 'week'},
                    {name: '本月', value: 'month'},
                    {name: '本季度', value: 'quarter'},
                    {name: '本年', value: 'year'},
                ],
                where:{
                    data:'',
                    status:'',
                    type:'',
                    real_name:'',
                    excel:0,
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
                    layList.basePost(layList.Url({c:'ump.event_registration',a:'getBadge'}),this.where,function (rem) {
                        that.badge=rem.data;
                    });
                },
                search:function () {
                    this.getBadge();
                    this.where.excel=0;
                    layList.reload(this.where,true);
                },
                refresh:function () {
                    window.location.reload();
                },
                excel:function () {
                    this.where.excel=2;
                    location.href=layList.U({c:'ump.event_registration',a:'get_sign_up_list',q:this.where});
                }
            },
            mounted:function () {
                var that=this;
                that.getBadge();
                layui.laydate.render({
                    elem: '#date',
                    range: true,
                    done: function (value){
                        that.where.data = value;
                    }
                });
                layList.tableList('List',"{:Url('get_sign_up_list')}",function (){
                    return [
                        {field: 'order_id', title: '订单号', width:'14%',templet:'#order_id'},
                        {field: 'nickname', title: '用户信息',templet:'#userinfo',align: 'center', width:'10%'},
                        {field: 'info', title: '活动信息',templet:"#info"},
                        {field: 'pay_price', title: '实际支付',align: 'center', width:'10%'},
                        {field: 'paid', title: '支付状态',templet:'#paid',align: 'center', width:'10%'},
                        {field: 'refund_price', title: '退款金额',align: 'center', width:'10%'},
                        {field: 'status', title: '订单状态',templet:'#status',align: 'center', width:'16%'},
                        {field: 'right', title: '操作',align:'center',toolbar:'#act', width:'8%'},
                    ];
                });
                layList.tool(function (event,data,obj) {
                    switch (event) {
                        case 'delete':
                            var url=layList.U({c:'ump.event_registration',a:'order_delete',q:{id:data.id}});
                            $eb.$swal('delete',function(){
                                $eb.axios.get(url).then(function(res){
                                    if(res.status == 200 && res.data.code == 200) {
                                        $eb.$swal('success',res.data.msg);
                                        obj.del();
                                    }else
                                        return Promise.reject(res.data.msg || '删除失败')
                                }).catch(function(err){
                                    $eb.$swal('error',err);
                                });
                            });
                            break;
                    }
                });
            }
        })
    });
</script>
{/block}
