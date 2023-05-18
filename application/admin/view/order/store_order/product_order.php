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
                <div class="layui-card-header">商品订单</div>
                <div class="layui-card-body">
                    <form class="layui-form" lay-filter="form" action="" @submit.prevent>
                        <div class="layui-form-item">
                            <label class="layui-form-label">订单状态：</label>
                            <div class="layui-input-block">
                                <div class="layui-btn-group">
                                    <button :class="[where.status === item.value ? 'layui-btn-normal' : 'layui-btn-primary', 'layui-btn layui-btn-sm']" @click="where.status = item.value" type="button" v-for="item in orderStatus">
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
                        <!-- <div class="layui-form-item">
                            <label class="layui-form-label">订单类型：</label>
                            <div class="layui-input-block">
                                <button class="layui-btn layui-btn-normal layui-btn-sm" :class="{'layui-btn-primary':where.type!==item.value}" @click="where.type = item.value" type="button" v-for="item in orderType">{{item.name}}
                                    <span v-if="item.count!=undefined" :class="item.class!=undefined ? 'layui-badge': 'layui-badge layui-bg-gray' ">{{item.count}}</span></button>
                            </div>
                        </div> -->
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
    <span style="color: {{d.color}};">{{d.pink_name}}</span>　　
</script>
<!--用户信息-->
<script type="text/html" id="userinfo">
    {{d.nickname==null ? '暂无昵称':d.nickname}}/{{d.uid}}
</script>
<!--支付状态-->
<script type="text/html" id="paid">
    {{#  if(d.pay_type==1){ }}
            <p>{{d.pay_type_name}}</p>
    {{#  }else{ }}
        {{# if(d.pay_type_info!=undefined){ }}
            <p><span>线下支付</span></p>
            <p><button type="button" lay-event='offline_btn' class="offline_btn btn btn-w-m btn-white">立即支付</button></p>
        {{# }else{ }}
            <p>{{d.pay_type_name}}</p>
        {{# } }}
    {{# }; }}
</script>
<!--订单状态-->
<script type="text/html" id="status">
    {{d.status_name}}
    {{#  if(d.paid==1 && d.refund_status==1){ }}
    <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="$eb.createModalFrame('退款原因','{:Url('reason_refund')}?oid={{d.id}}',{w:800,h:600})">退款原因</button>
    {{# }; }}
</script>
<!--商品信息-->
<script type="text/html" id="info">
    {{# if(d.type==2 && d._info){ }}
    {{#  layui.each(d._info, function(index, item){ }}
    <div>
        <span style="float: left;margin-right: 10px;"><img width="30" height="30" src="{{item.cart_info.productInfo.image}}"></span>
        <span>{{item.cart_info.productInfo.store_name}}</span><span> | ￥{{item.cart_info.truePrice}}×{{item.cart_info.cart_num}}</span>
    </div>
    {{#  }); }}
    {{# };}}
</script>
<script type="text/html" id="act">
    {{#  if(d._status==1 && d.nickname!=null){ }}
    <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="dropdown(this)">
        <i class="layui-icon">&#xe625;</i>操作
    </button>
    <ul class="layui-nav-child layui-anim layui-anim-upbit">
        <li>
            <a href="javascript:void(0);" onclick="$eb.createModalFrame('订单详情','{:Url('order_info')}?oid={{d.id}}')">
                <i class="fa fa-file-text"></i> 订单详情
            </a>
        </li>
        <li>
            <a href="javascript:void(0);" onclick="$eb.createModalFrame('修改订单','{:Url('edit')}?id={{d.id}}')">
                <i class="fa fa-file-text"></i> 修改订单
            </a>
        </li>
        <li>
            <a lay-event='marke' href="javascript:void(0);" >
                <i class="fa fa-paste"></i> 订单备注
            </a>
        </li>
        <li>
            <a href="javascript:void(0);" onclick="$eb.createModalFrame('订单记录','{:Url('order_status')}?oid={{d.id}}')">
                <i class="fa fa-newspaper-o"></i> 订单记录
            </a>
        </li>
        <li>
            <a lay-event='delete' href="javascript:void(0);" >
                <i class="fa fa-trash"></i> 删除
            </a>
        </li>
    </ul>
    {{#  }else if(d._status==2 && d.nickname!=null){ }}
    {{# if(d.type==2){ }}
    <button class="layui-btn layui-btn-normal layui-btn-xs" type="button" onclick="$eb.createModalFrame('发送货','{:Url('deliver_goods')}?id={{d.id}}',{w:400,h:250})">
        <i class="fa fa-cart-plus"></i>发送货</button>
    {{# } }}
    <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="dropdown(this)">
        <i class="layui-icon">&#xe625;</i>操作
    </button>
    <ul class="layui-nav-child layui-anim layui-anim-upbit">
        <li>
            <a href="javascript:void(0);" onclick="$eb.createModalFrame('订单详情','{:Url('order_info')}?oid={{d.id}}')">
                <i class="fa fa-file-text"></i> 订单详情
            </a>
        </li>
        <li>
            <a lay-event='marke' href="javascript:void(0);" >
                <i class="fa fa-paste"></i> 订单备注
            </a>
        </li>
        {{#  if(d.pay_price!=d.refund_price){ }}
        <li>
            <a href="javascript:void(0);" onclick="$eb.createModalFrame('退款','{:Url('refund_y')}?id={{d.id}}',{w:400,h:300})">
                <i class="fa fa-history"></i> 立即退款
            </a>
        </li>
        {{#  if(d.refund_status==1){ }}
        <li>
            <a href="javascript:void(0);" onclick="$eb.createModalFrame('不退款','{:Url('refund_n')}?id={{d.id}}',{w:400,h:300})">
                <i class="fa fa-openid"></i> 不退款
            </a>
        </li>
        {{# } ;}}
        {{# } ;}}
        {{#  if(d.use_gold!=d.back_gold){ }}
        <li>
            <a href="javascript:void(0);" onclick="$eb.createModalFrame('退{$gold_name}','{:Url('gold_back')}?id={{d.id}}',{w:400,h:300})">
                <i class="fa fa-history"></i> 退{$gold_name}
            </a>
        </li>
        {{# } ;}}
        <li>
            <a href="javascript:void(0);" onclick="$eb.createModalFrame('订单记录','{:Url('order_status')}?oid={{d.id}}')">
                <i class="fa fa-newspaper-o"></i> 订单记录
            </a>
        </li>
        <li>
            <a lay-event='delete' href="javascript:void(0);" >
                <i class="fa fa-trash"></i> 删除
            </a>
        </li>
    </ul>
    {{#  }else if(d._status==3 && d.nickname!=null){ }}
    <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="dropdown(this)">
        <i class="layui-icon">&#xe625;</i>操作
    </button>
    <ul class="layui-nav-child layui-anim layui-anim-upbit">
        <li>
            <a href="javascript:void(0);" onclick="$eb.createModalFrame('订单详情','{:Url('order_info')}?oid={{d.id}}')">
                <i class="fa fa-file-text"></i> 订单详情
            </a>
        </li>
        <li>
            <a lay-event='marke' href="javascript:void(0);">
                <i class="fa fa-paste"></i> 订单备注
            </a>
        </li>
        {{# if(d.pay_price != d.refund_price){ }}
        <li>
            <a href="javascript:void(0);" onclick="$eb.createModalFrame('退款','{:Url('refund_y')}?id={{d.id}}',{w:400,h:300})">
                <i class="fa fa-history"></i>立即退款
            </a>
        </li>
        {{#  if(d.refund_status==1){ }}
        <li>
            <a href="javascript:void(0);" onclick="$eb.createModalFrame('不退款','{:Url('refund_n')}?id={{d.id}}',{w:400,h:300})">
                <i class="fa fa-openid"></i> 不退款
            </a>
        </li>
        {{# } ;}}
        {{# } ;}}
        {{#  if(d.use_gold!=d.back_gold){ }}
        <li>
            <a href="javascript:void(0);" onclick="$eb.createModalFrame('退{$gold_name}','{:Url('gold_back')}?id={{d.id}}',{w:400,h:300})">
                <i class="fa fa-history"></i> 退{$gold_name}
            </a>
        </li>
        {{# } ;}}
        <li>
            <a href="javascript:void(0);" onclick="$eb.createModalFrame('订单记录','{:Url('order_status')}?oid={{d.id}}')">
                <i class="fa fa-newspaper-o"></i> 订单记录
            </a>
        </li>
        <li>
            <a lay-event='delete' href="javascript:void(0);" >
                <i class="fa fa-trash"></i> 删除
            </a>
        </li>
    </ul>
    {{#  }else if(d._status==4 && d.nickname!=null){ }}
    <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="dropdown(this)">
        <i class="layui-icon">&#xe625;</i>操作
    </button>
    <ul class="layui-nav-child layui-anim layui-anim-upbit">
        <li>
            <a href="javascript:void(0);" onclick="$eb.createModalFrame('订单详情','{:Url('order_info')}?oid={{d.id}}')">
                <i class="fa fa-file-text"></i> 订单详情
            </a>
        </li>
        <li>
            <a lay-event='marke' href="javascript:void(0);" >
                <i class="fa fa-paste"></i> 订单备注
            </a>
        </li>
        <li>
            <a lay-event='danger' href="javascript:void(0);">
                <i class="fa fa-cart-arrow-down"></i> 已收货
            </a>
        </li>
        {{#  if(d.pay_price != d.refund_price){ }}
        <li>
            <a href="javascript:void(0);" onclick="$eb.createModalFrame('退款','{:Url('refund_y')}?id={{d.id}}',{w:400,h:300})">
                <i class="fa fa-history"></i> 立即退款
            </a>
        </li>
        {{#  if(d.refund_status==1){ }}
        <li>
            <a href="javascript:void(0);" onclick="$eb.createModalFrame('不退款','{:Url('refund_n')}?id={{d.id}}',{w:400,h:300})">
                <i class="fa fa-openid"></i> 不退款
            </a>
        </li>
        {{# } ;}}
        {{# } }}
        {{#  if(d.use_gold!=d.back_gold){ }}
        <li>
            <a href="javascript:void(0);" onclick="$eb.createModalFrame('退{$gold_name}','{:Url('gold_back')}?id={{d.id}}',{w:400,h:300})">
                <i class="fa fa-history"></i> 退{$gold_name}
            </a>
        </li>
        {{# } ;}}
        <li>
            <a href="javascript:void(0);" onclick="$eb.createModalFrame('订单记录','{:Url('order_status')}?oid={{d.id}}')">
                <i class="fa fa-newspaper-o"></i> 订单记录
            </a>
        </li>
        <li>
            <a lay-event='delete' href="javascript:void(0);" >
                <i class="fa fa-trash"></i> 删除
            </a>
        </li>
    </ul>
    {{#  }else if(d._status==5 && d.nickname!=null || d._status==6 && d.nickname!=null){ }}
    <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="dropdown(this)">
        <i class="layui-icon">&#xe625;</i>操作
    </button>
    <ul class="layui-nav-child layui-anim layui-anim-upbit">
        <li>
            <a href="javascript:void(0);" onclick="$eb.createModalFrame('订单详情','{:Url('order_info')}?oid={{d.id}}')">
                <i class="fa fa-file-text"></i> 订单详情
            </a>
        </li>
        <li>
            <a lay-event='marke' href="javascript:void(0);" >
                <i class="fa fa-paste"></i> 订单备注
            </a>
        </li>
        {{#  if(d.pay_price != d.refund_price){ }}
        <li>
            <a href="javascript:void(0);" onclick="$eb.createModalFrame('退款','{:Url('refund_y')}?id={{d.id}}',{w:400,h:300})">
                <i class="fa fa-history"></i> 立即退款
            </a>
        </li>
        {{#  if(d.refund_status==1){ }}
        <li>
            <a href="javascript:void(0);" onclick="$eb.createModalFrame('不退款','{:Url('refund_n')}?id={{d.id}}',{w:400,h:300})">
                <i class="fa fa-openid"></i> 不退款
            </a>
        </li>
        {{# } ;}}
        {{# } }}
        {{#  if(d.use_gold!=d.back_gold){ }}
        <li>
            <a href="javascript:void(0);" onclick="$eb.createModalFrame('退{$gold_name}','{:Url('gold_back')}?id={{d.id}}',{w:400,h:300})">
                <i class="fa fa-history"></i> 退{$gold_name}
            </a>
        </li>
        {{# } ;}}
        <li>
            <a href="javascript:void(0);" onclick="$eb.createModalFrame('订单记录','{:Url('order_status')}?oid={{d.id}}')">
                <i class="fa fa-newspaper-o"></i> 订单记录
            </a>
        </li>
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
        <li>
            <a lay-event='marke' href="javascript:void(0);" >
                <i class="fa fa-paste"></i> 订单备注
            </a>
        </li>
        {{#  if(d.pay_price != d.refund_price){ }}
        <li>
            <a href="javascript:void(0);" onclick="$eb.createModalFrame('退款','{:Url('refund_y')}?id={{d.id}}',{w:400,h:300})">
                <i class="fa fa-history"></i> 立即退款
            </a>
        </li>
        {{#  if(d.refund_status==1){ }}
        <li>
            <a href="javascript:void(0);" onclick="$eb.createModalFrame('不退款','{:Url('refund_n')}?id={{d.id}}',{w:400,h:300})">
                <i class="fa fa-openid"></i> 不退款
            </a>
        </li>
        {{# } ;}}
        {{# } }}
        {{#  if(d.use_gold!=d.back_gold){ }}
        <li>
            <a href="javascript:void(0);" onclick="$eb.createModalFrame('退{$gold_name}','{:Url('gold_back')}?id={{d.id}}',{w:400,h:300})">
                <i class="fa fa-history"></i> 退{$gold_name}
            </a>
        </li>
        {{# } ;}}
        <li>
            <a href="javascript:void(0);" onclick="$eb.createModalFrame('订单记录','{:Url('order_status')}?oid={{d.id}}')">
                <i class="fa fa-newspaper-o"></i> 订单记录
            </a>
        </li>
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
    })
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
        })
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
    var real_name='<?=$real_name?>';
    var orderCount=<?=json_encode($orderCount)?>;
    require(['vue'],function(Vue) {
        new Vue({
            el: "#app",
            data: {
                badge: [],
                orderStatus: [
                    {name: '全部', value: ''},
                    {name: '未支付', value: 0,count:orderCount.wz},
                    {name: '已支付', value: 1,count:orderCount.wf,class:true},
                    {name: '待收货', value: 2,count:orderCount.sh,class:true},
                    {name: '待评价', value: 3,count:orderCount.pj,class:true},
                    {name: '已完成', value: 4,count:orderCount.wc,class:true},
                    {name: '退款中', value: -1,count:orderCount.tk},
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
                orderType:[
                    {name: '商品订单', value: 8,count:orderCount.sp,class:true},
                ],
                where:{
                    data:'',
                    status:'',
                    type:8,
                    types:2,
                    real_name:real_name || '',
                    excel:0,
                    spread_type:'',
                },
                showtime: false,
            },
            watch: {

            },
            methods: {
                setData:function(item){
                    this.where.data = item.value;
                    layui.form.val('form', {
                        'date': ''
                    });
                },
                getBadge:function() {
                    var that=this;
                    layList.basePost(layList.Url({c:'order.store_order',a:'getBadge'}),this.where,function (rem) {
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
                    this.where.excel=1;
                    location.href=layList.U({c:'order.store_order',a:'order_list',q:this.where});
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
                layList.tableList('List',"{:Url('order_list',['real_name'=>$real_name,'types'=>2])}",function (){
                    return [
                        {field: 'order_id', title: '订单号',width:'14%',templet:'#order_id'},
                        {field: 'nickname', title: '用户信息',templet:'#userinfo',align: 'center',width:'12%'},
                        {field: 'info', title: '商品信息',templet:"#info"},
                        {field: 'pay_price', title: '实际支付',align: 'center',width:'7%'},
                        {field: 'use_gold', title: '抵扣{$gold_name}',align: 'center',width:'7%'},
                        {field: 'paid', title: '支付状态',templet:'#paid',align: 'center',width:'10%'},
                        {field: 'status', title: '订单状态',templet:'#status',align: 'center',width:'10%'},
                        {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'12%'},
                    ];
                });
                layList.tool(function (event,data,obj) {
                    switch (event) {
                        case 'marke':
                            var url =layList.U({c:'order.store_order',a:'remark'}),
                                id=data.id,
                                make=data.remark;
                            $eb.$alert('textarea',{title:'请修改内容',value:make},function (result) {
                                if(result){
                                    $.ajax({
                                        url:url,
                                        data:'remark='+result+'&id='+id,
                                        type:'post',
                                        dataType:'json',
                                        success:function (res) {
                                            layList.msg(res.msg,function () {
                                                location.reload();
                                            });
                                        }
                                    })
                                }else{
                                    $eb.$swal('error','请输入要备注的内容');
                                }
                            });
                            break;
                        case 'danger':
                            var url =layList.U({c:'order.store_order',a:'take_delivery',p:{id:data.id}});
                            $eb.$swal('delete',function(){
                                $eb.axios.get(url).then(function(res){
                                    if(res.status == 200 && res.data.code == 200) {
                                        layList.msg(res.data.msg,function () {
                                            location.reload();
                                        });
                                    }else
                                        return Promise.reject(res.data.msg || '收货失败')
                                }).catch(function(err){
                                    $eb.$swal('error',err);
                                });
                            },{'title':'您确定要修改收货状态吗？','text':'修改后将无法恢复,请谨慎操作！','confirm':'是的，我要修改'})
                            break;
                        case 'offline_btn':
                            var url =layList.U({c:'order.store_order',a:'offline',p:{id:data.id}}),pay_price =data.pay_price;
                            $eb.$swal('delete',function(){
                                $eb.axios.get(url).then(function(res){
                                    if(res.status == 200 && res.data.code == 200) {
                                        $eb.$swal('success',res.data.msg);
                                    }else
                                        return Promise.reject(res.data.msg || '收货失败')
                                }).catch(function(err){
                                    $eb.$swal('error',err);
                                });
                            },{'title':'您确定要修改已支付'+pay_price+'元的状态吗？','text':'修改后将无法恢复,请谨慎操作！','confirm':'是的，我要修改'})
                            break;
                        case 'delete':
                            var url=layList.U({c:'order.store_order',a:'delete',q:{id:data.id}});
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
                            })
                            break;
                    }
                });
            }
        })
    });
</script>
{/block}
