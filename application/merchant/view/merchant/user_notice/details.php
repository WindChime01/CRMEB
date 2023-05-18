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
</style>
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12 layui-col-sm12 layui-col-lg12">
            <div class="layui-card">
                <div class="layui-card-header">详情</div>
                <div class="layui-card-body">
                   
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
                orderList:[],
                integralList:[],
                SignList:[],
                CouponsList:[],
                balanceChangList:[],
                SpreadList:[],
                count:count,
                page:{
                    order_page:1,
                    integral_page:1,
                    sign_page:1,
                    copons_page:1,
                    balancechang_page:1,
                    spread_page:1,
                },
            },
            watch:{
                'page.order_page':function () {
                    this.getOneorderList();
                },
                'page.integral_page':function () {
                    this.getOneIntegralList();
                },
                'page.sign_page':function () {
                    this.getOneSignList();
                },
                'page.copons_page':function () {
                    this.getOneCouponsList();
                },
                'page.balancechang_page':function () {
                    this.getOneBalanceChangList();
                },
                'page.spread_page':function () {
                    this.getSpreadList();
                }
            },
            methods:{
                getSpreadList:function(){
                    this.request('getSpreadList',this.page.spread_page,'SpreadList');
                },
                getOneorderList:function () {
                    this.request('getOneorderList',this.page.order_page,'orderList');
                },
                getOneIntegralList:function () {
                    this.request('getOneIntegralList',this.page.integral_page,'integralList');
                },
                getOneSignList:function () {
                    this.request('getOneSignList',this.page.sign_page,'SignList');
                },
                getOneCouponsList:function () {
                    this.request('getOneCouponsList',this.page.copons_page,'CouponsList');
                },
                getOneBalanceChangList:function () {
                    this.request('getOneBalanceChangList',this.page.balancechang_page,'balanceChangList');
                },
                request:function (action,page,name) {
                    var that=this;
                    layList.baseGet(layList.U({a:action,p:{page:page,limit:this.limit,uid:this.uid}}),function (res) {
                        that.$set(that,name,res.data)
                    });
                }
            },
            mounted:function () {
                this.getOneorderList();
                this.getOneIntegralList();
                this.getOneSignList();
                this.getOneCouponsList();
                this.getOneBalanceChangList();
                this.getSpreadList();
                var that=this;
                layList.laypage.render({
                    elem: that.$refs.page_order
                    ,count:that.count.order_count
                    ,limit:that.limit
                    ,theme: '#1E9FFF',
                    jump:function(obj){
                        that.page.order_page=obj.curr;
                    }
                });
                layList.laypage.render({
                    elem: that.$refs.integral_page
                    ,count:that.count.integral_count
                    ,limit:that.limit
                    ,theme: '#1E9FFF',
                    jump:function(obj){
                        that.page.integral_page=obj.curr;
                    }
                });
                layList.laypage.render({
                    elem: that.$refs.Sign_page
                    ,count:that.count.sign_count
                    ,limit:that.limit
                    ,theme: '#1E9FFF',
                    jump:function(obj){
                        that.page.sign_page=obj.curr;
                    }
                });
                layList.laypage.render({
                    elem: that.$refs.copons_page
                    ,count:that.count.coupon_count
                    ,limit:that.limit
                    ,theme: '#1E9FFF',
                    jump:function(obj){
                        that.page.copons_page=obj.curr;
                    }
                });
                layList.laypage.render({
                    elem: that.$refs.balancechang_page
                    ,count:that.count.balanceChang_count
                    ,limit:that.limit
                    ,theme: '#1E9FFF',
                    jump:function(obj){
                        that.page.balancechang_page=obj.curr;
                    }
                });

                layList.laypage.render({
                    elem: that.$refs.spread_page
                    ,count:that.count.spread_count
                    ,limit:that.limit
                    ,theme: '#1E9FFF',
                    jump:function(obj){
                        that.page.spread_page=obj.curr;
                    }
                });
            }
        });
    });
</script>
{/block}