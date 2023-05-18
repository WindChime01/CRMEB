{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15" id="app" v-cloak>
        <div class="layui-col-md12" >
            <div class="layui-card">
                <div class="layui-card-header">搜索条件</div>
                <div class="layui-card-body">
                    <div class="layui-carousel layadmin-carousel layadmin-shortcut" lay-anim="" lay-indicator="inside" lay-arrow="none" style="background:none">
                        <div class="layui-card-body">
                            <div class="layui-row layui-col-space10 layui-form-item">
                                <div class="layui-col-lg12">
                                    <label class="layui-form-label">核销状态:</label>
                                    <div class="layui-input-block">
                                        <button class="layui-btn layui-btn-sm" :class="{'layui-btn-primary':where.status!==item.value}" @click="where.status = item.value" type="button" v-for="item in orderType">{{item.name}}
                                           </button>
                                    </div>
                                </div>
                                <div class="layui-col-lg12">
                                    <label class="layui-form-label">搜索内容:</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="real_name" style="width: 50%" v-model="where.real_name" placeholder="请输入搜索订单号或核销码" class="layui-input">
                                    </div>
                                </div>
                                <input type="hidden" name="id" style="width: 50%" value="{$aid}" class="layui-input">
                                <div class="layui-col-lg12">
                                    <div class="layui-input-block">
                                        <button @click="search" type="button" class="layui-btn layui-btn-sm layui-btn-normal">
                                            <i class="layui-icon layui-icon-search"></i>搜索</button>
                                        <button @click="excel" type="button" class="layui-btn layui-btn-warm layui-btn-sm export" type="button">
                                            <i class="fa fa-floppy-o" style="margin-right: 3px;"></i>导出</button>
                                        <button @click="refresh" type="reset" class="layui-btn layui-btn-primary layui-btn-sm">
                                            <i class="layui-icon layui-icon-refresh" ></i>刷新</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
    </div>
    <div class="layui-row layui-col-space15" >
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <table class="layui-hide" id="userList" lay-filter="userList"></table>
                    <!--订单状态-->
                    <script type="text/html" id="status">
                        {{d.status_name}}
                        {{#  if(d.paid==1 && d.refund_status==2){ }}
                        <p>金额:{{d.refund_price}}</p>
                        <p>时间:{{d.refund_reason_time}}</p>
                        {{# }; }}
                    </script>
                    <script type="text/html" id="act">
                        {{# if(d.status){ }}
                        <button type="button" class="layui-btn layui-btn-xs layui-btn-primary layui-btn-sm">已核销</button>
                        {{# }else{ }}
                        <button type="button" class="layui-btn layui-btn-xs"  lay-event='write_off_code' style="padding: 1px 14px;">核销 </button>
                        {{# } }}
                        {{# if(d.pay_price!=d.refund_price){ }}
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="parent.$eb.createModalFrame('退款','{:Url('refund_y')}?id={{d.id}}',{w:400,h:300})" style="padding: 1px 14px;"><i class="fa fa-history"></i>订单退款</button>
                        {{# } }}
                        <button class="layui-btn layui-btn-danger layui-btn-xs" type="button" lay-event='delete'>
                            <i class="layui-icon">&#xe640;</i>删除
                        </button>
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
    layList.tableList('userList',"{:Url('get_sign_up_list',['id'=>$aid,'type'=>1])}",function () {
        return [
            {field: 'id', title: '编号',  width: '6%',align:'center'},
            {field: 'order_id', title: '订单号',align:'left',width: '12%'},
            {field: 'userInfo', title: '报名信息',templet:'#nickname',align:'left',width: '12.4%'},
            {field: 'number', title: '报名人数',align:'center',width: '6%'},
            {field: 'addTime', title: '报名时间' ,templet:'#ticket_price',align:'center', width:'10%'},
            {field: 'pay_price', title: '实付金额',templet:'#stock',align:'center', width:'8%'},
            {field: 'pay_type', title: '支付方式',align:'center', width:'5%'},
            {field: 'status', title: '订单状态',templet:'#status',align: 'center', width:'8%'},
            {field: 'code', title: '核销码',align:'center', width:'12%'},
            {field: 'write_off', title: '核销状态',align:'center', width:'6%'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'15%'},
        ];
    });
    $('.conrelTable').find('button').each(function () {
        var type=$(this).data('type');
        $(this).on('click',function () {
            action[type] && action[type]();
        })
    });
    layList.tool(function (event,data,obj) {
        switch (event) {
                case 'write_off_code':
                var url=layList.U({c:'ump.event_registration',a:'scanCodeSignIn',q:{id:data.id}});
                    parent.$eb.$swal('delete',function(){
                        parent.$eb.axios.get(url).then(function(res){
                            if(res.data.code == 200) {
                                window.location.reload();
                                parent.$eb.$swal('success', res.data.msg);
                            }else
                                parent.$eb.$swal('error',res.data.msg||'操作失败!');
                        });
                    },{
                        title:'确定对该订单核销吗?',
                        text:'通过后无法撤销，请谨慎操作！',
                        confirm:'核销'
                    });
                break;
            case 'open_image':
                parent.$eb.openImage(data.pic);
                break;
            case 'delete':
                var url=layList.U({c:'ump.event_registration',a:'order_delete',q:{id:data.id}});
                parent.$eb.$swal('delete',function(){
                    parent.$eb.axios.get(url).then(function(res){
                        if(res.status == 200 && res.data.code == 200) {
                            parent.$eb.$swal('success',res.data.msg);
                            obj.del();
                        }else
                            return Promise.reject(res.data.msg || '删除失败')
                    }).catch(function(err){
                        parent.$eb.$swal('error',err);
                    });
                });
                break;
        }
    });
    var id="{$aid}";
    require(['vue'],function(Vue) {
        new Vue({
            el: "#app",
            data: {
                orderType:[
                    {name: '全部', value: ''},
                    {name: '未核销', value: 1},
                    {name: '已核销', value: 2},
                ],
                where:{
                    id:id,
                    status:'',
                    type:1,
                    real_name:'',
                    excel:0,
                },
                showtime: false,
                badge: [],
            },
            methods: {
                search:function () {
                    this.getBadge();
                    this.where.excel=0;
                    layList.reload(this.where,true);
                },
                refresh:function () {
                    location.reload();
                },
                getBadge:function() {
                    var that=this;
                    layList.basePost(layList.Url({c:'ump.event_registration',a:'getBadge'}),this.where,function (rem) {
                        that.badge=rem.data;
                    });
                },
                excel:function () {
                    this.where.excel=1;
                    location.href=layList.U({c:'ump.event_registration',a:'get_sign_up_list',q:this.where});
                }
            },
            mounted:function () {
                var that=this;
                that.getBadge();
            }
        })
    });
</script>
{/block}
