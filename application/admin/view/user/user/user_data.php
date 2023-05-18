{extend name="public/container"}
{block name='head'}
<style>
    .clearfix:after{content:'';visibility:hidden;display:block;height:0;clear:both;}
    .data .avatar{width: 100px;height: 100px;float: left;margin-left: 10px}
    .data .item{color: #000000;font-size: 20px;text-align: center;float: left;line-height: 100px;padding-left: 20px;}
    .data .avatar img{width: 100px;height: 100px;border-radius: 50%}
    .data .item .text{color: #1E9FFF;cursor: pointer}
    .data .item-right{float: right;padding-right: 25px}
    .data .item-right button{border-radius: 5px;}
    .data .form table td{border: none;font-size: 14px;}
    .data .form table tr:hover{background-color:#ffffff!important;}
    .layui-table td, .layui-table th {position: relative;line-height: 10px;padding: 8px 15px;}
</style>
{/block}
{block name="content"}
<div class="layui-fluid data">
    <div class="layui-row layui-col-space15" id="app" v-cloak>
        <div class="layui-col-md12 layui-col-sm12 layui-col-lg12">
            <div class="layui-card">
                <div class="layui-card-header">用户资料</div>
                <div class="layui-card-body">
                    <div class="layui-col-md-12 clearfix">
                        <div class="layui-col-md6">
                            <div class="avatar">
                                <img :src="userinfo.avatar" alt="">
                            </div>
                            <div class="item" v-text="userinfo.nickname"></div>
                            <div class="item" v-if="userinfo.is_promoter">
                                <span class="layui-badge layui-bg-blue" style="height: 24px;line-height: 24px;font-size: 20px">推广人</span>
                            </div>
                            <div class="item" v-else>
                                <span class="layui-badge layui-bg-blue" style="height: 24px;line-height: 24px;font-size: 20px">无</span>
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md-12 clearfix">
                        <div class="layui-col-md6">
                            <div class="layui-form form">
                                <table class="layui-table">
                                    <tbody>
                                        <tr>
                                            <td class="text-right">昵  称:</td>
                                            <td>{{userinfo.nickname}}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-right">手机号:</td>
                                            <td>{{userinfo.phone}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="layui-col-md6">
                            <div class="layui-form form">
                                <table class="layui-table">
                                    <colgroup>
                                        <col width="150">
                                        <col width="200">
                                        <col>
                                    </colgroup>
                                    <tbody>
                                        <tr>
                                            <td class="text-right">推广人上级:</td>
                                            <td>{{userinfo.spread_name}}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-right">已推广人数:</td>
                                            <td>{{count.spread_count}}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-right">直推订单金额:</td>
                                            <td>￥{{userinfo.spread_one}}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-right">佣金总额:</td>
                                            <td>￥{{userinfo.bill_sum}}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-right">待提现:</td>
                                            <td>￥{{userinfo.brokerage_price}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-card">
                <div class="layui-card-header">用户详细记录</div>
                <div class="layui-card-body">
                    <div class="layui-tab" lay-filter="tab">
                        <ul class="layui-tab-title">
                            <li class="layui-this">我的课程</li>
                            <li>推广记录</li>
                        </ul>
                        <div class="layui-tab-content">
                            <div class="layui-tab-item layui-show">
                                <div class="layui-form">
                                    <table class="layui-table">
                                        <thead>
                                            <tr>
                                                <th>时间</th>
                                                <th>课程</th>
                                                <th>实付金额</th>
                                                <th>开通类型</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="item in pay_list">
                                                <td v-text="item.add_time">贤心</td>
                                                <td v-text="item.title"></td>
                                                <td v-text="item.pay_price"></td>
                                                <td v-text="item.type"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div ref="pay_page" v-show="count.pay_count > limit" style="text-align: right;"></div>
                                </div>
                            </div>
                            <div class="layui-tab-item">
                                <div class="layui-tab" lay-filter="commission">
                                    <ul class="layui-tab-title">
                                        <li class="layui-this">佣金记录</li>
                                        <li>直推订单</li>
                                        <li>他的下级</li>
                                    </ul>
                                    <div class="layui-tab-content">
                                        <div class="layui-tab-item layui-show">
                                            <div class="layui-col-md12">
                                                <div class="layui-form-item">
                                                    <div class="layui-inline">
                                                        <label class="layui-form-label">开始日期</label>
                                                        <div class="layui-input-inline">
                                                            <input type="text" name="date" id="start_time" placeholder="请选择" autocomplete="off" class="layui-input">
                                                        </div>
                                                    </div>
                                                    <div class="layui-inline">
                                                        <label class="layui-form-label">结束日期</label>
                                                        <div class="layui-input-inline">
                                                            <input type="text" name="date" id="end_time" placeholder="请选择" autocomplete="off" class="layui-input">
                                                        </div>
                                                    </div>
                                                    <div class="layui-inline">
                                                        <button class="layui-btn layui-btn-normal layui-btn-sm" type="button" @click="bill_excel()">导出</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="layui-form">
                                                <table class="layui-table">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 20%">时间</th>
                                                            <th style="width: 11%">入账/结算</th>
                                                            <th style="width: 13%">记录说明</th>
                                                            <th style="width: 10%">佣金金额</th>
                                                            <th style="width: 30%">备注</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="item in bill_list">
                                                            <td v-text="item.add_time"></td>
                                                            <td v-text="item._type"></td>
                                                            <td v-text="item.title"></td>
                                                            <td v-text="item.number"></td>
                                                            <td v-text="item.mark"></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <div ref="bill_page" v-show="count.bill_count > limit" style="text-align: right;"></div>
                                            </div>
                                        </div>
                                        <div class="layui-tab-item">
                                            <div class="layui-col-md12">
                                                <div class="layui-form-item">
                                                    <div class="layui-inline">
                                                        <label class="layui-form-label">开始日期</label>
                                                        <div class="layui-input-inline">
                                                            <input type="text" name="date" id="start_date" lay-verify="date" placeholder="请选择" autocomplete="off" class="layui-input">
                                                        </div>
                                                    </div>
                                                    <div class="layui-inline">
                                                        <label class="layui-form-label">结束日期</label>
                                                        <div class="layui-input-inline">
                                                            <input type="text" name="date" id="end_date" lay-verify="date" placeholder="请选择" autocomplete="off" class="layui-input">
                                                        </div>
                                                    </div>
                                                    <div class="layui-inline">
                                                        <button class="layui-btn layui-btn-normal layui-btn-sm" type="button" @click="bill_excel(1)">导出</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="layui-form">
                                                <table class="layui-table">
                                                    <thead>
                                                        <tr>
                                                            <th>时间</th>
                                                            <th>订单号</th>
                                                            <th>用户名</th>
                                                            <th>产品名称</th>
                                                            <th>订单金额</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="item in order_list">
                                                            <td v-text="item.add_time"></td>
                                                            <td v-text="item.order_id"></td>
                                                            <td v-text="item.nickname"></td>
                                                            <td v-text="item.title"></td>
                                                            <td v-text="item.pay_price"></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <div ref="order_page" v-show="count.order_count > limit" style="text-align: right;"></div>
                                            </div>
                                        </div>
                                        <div class="layui-tab-item">
                                            <div class="layui-form">
                                                <table class="layui-table">
                                                    <thead>
                                                        <tr>
                                                            <th>用户Uid</th>
                                                            <th>用户名</th>
                                                            <th>联系方式</th>
                                                            <th>出售产品(单)</th>
                                                            <th>售出金额(元)</th>
                                                            <th>佣金(元)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="item in spread_list">
                                                            <td v-text="item.uid"></td>
                                                            <td v-text="item.nickname"></td>
                                                            <td v-text="item.phone"></td>
                                                            <td v-text="item.order_count"></td>
                                                            <td v-text="item.sum_pay_price"></td>
                                                            <td v-text="item.sum_number"></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <div ref="spread_page" v-show="count.spread_count> limit" style="text-align: right;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<script type="text/javascript" src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name='script'}
<script>
    var uid={$uid},count={$count},gradeList={$gradeList};
    require(['vue'],function(Vue) {
        new Vue({
            el: "#app",
            data: {
                avtive:0,
                commAvtive:0,
                userinfo:{},
                pay_list:[],
                bill_list:[],
                order_list:[],
                spread_list:[],
                gradeList:gradeList,
                subjectList:[],
                specialList:[],
                bill_date_start:'',
                bill_date_end:'',
                start_date:'',
                end_date:'',
                limit:10,
                count:count,
                page:{
                    pay_page:1,
                    bill_page:1,
                    order_page:1,
                    spread_page:1
                },
                ContentIndex:null,
                GiveIndex:null,
            },
            watch:{
                'page.pay_page':function () {
                    this.getPayList();
                },
                'page.bill_page':function () {
                    this.getBillList();
                },
                'page.order_page':function () {
                    this.getOrderList();
                },
                'page.spread_page':function () {
                    this.getSpreadList();
                },
            },
            methods:{
                bill_excel:function(type){
                    if(type){
                        if(this.start_date=='' && this.end_date) return layList.msg('请选择开始时间');
                        if(this.start_date && this.end_date=='') return layList.msg('请选择结束时间');
                        window.location.href=layList.U({a:'get_order_list',q:{excel:1,start_date:this.start_date,end_date:this.end_date,uid:uid}});
                    }else{
                        if(this.bill_date_start=='' && this.bill_date_end) return layList.msg('请选择开始时间');
                        if(this.bill_date_start && this.bill_date_end=='') return layList.msg('请选择结束时间');
                        window.location.href=layList.U({a:'get_bill_list',q:{excel:1,start_date:this.bill_date_start,end_date:this.bill_date_end,uid:uid}});
                    }
                },
                give:function(){
                    var that=this;
                    layList.form.render();
                    this.GiveIndex=layList.layer.open({
                        type: 1,
                        skin: 'layui-layer-rim', //加上边框
                        area: ['750px', '340px'], //宽高
                        content:$(this.$refs.give),
                        title:'赠送专题',
                        cancel:function () {
                            that.$refs.give.style.display='none';
                        }
                    });
                },
                getUserInfo:function(){
                    var that=this;
                    layList.baseGet(layList.U({a:'get_user_info',q:{uid:uid}}),function (res) {
                        that.userinfo=res.data;
                    });
                },
                getSpreadList:function(){
                    this.request('get_spread_list',this.page.spread_page,'spread_list');
                },
                getOrderList:function(){
                    this.request('get_order_list',this.page.order_page,'order_list');
                },
                getBillList:function(){
                    this.request('get_bill_list',this.page.bill_page,'bill_list');
                },
                getPayList:function(){
                    this.request('get_pay_list',this.page.pay_page,'pay_list');
                },
                request:function (action,page,name){
                    var that=this;
                    layList.baseGet(layList.U({a:action,q:{page:page,limit:this.limit,uid:uid}}),function (res) {
                        that.$set(that,name,res.data)
                    });
                }
            },
            mounted:function () {
                var that=this;
                that.$nextTick(function () {
                    that.getUserInfo();
                    that.getPayList();
                    that.getBillList();
                    that.getOrderList();
                    that.getSpreadList();
                    layList.date({
                        elem:'#start_time',
                        type:'datetime',
                        done:function (value){
                            that.bill_date_start=value;
                        }
                    });
                    layList.date({
                        elem:'#end_time',
                        type:'datetime',
                        done:function (value){
                            that.bill_date_end=value;
                        }
                    });

                    layList.date({
                        elem:'#start_date',
                        type:'datetime',
                        done:function (value){
                            that.start_date=value;
                        }
                    });
                    layList.date({
                        elem:'#end_date',
                        type:'datetime',
                        done:function (value){
                            that.end_date=value;
                        }
                    });

                    layList.select('grade',function (odj) {
                        layList.baseGet(layList.U({a:'get_subjec_list',q:{grade_id:odj.value}}),function (res) {
                            that.$set(that,'subjectList',res.data);
                            that.$nextTick(function () {
                                layList.form.render('select');
                            });
                        });
                    })
                    layList.select('subject',function (odj) {
                        layList.baseGet(layList.U({a:'get_special_list',q:{subjec_id:odj.value}}),function (res) {
                            that.$set(that,'specialList',res.data);
                            that.$nextTick(function () {
                                layList.form.render('select');
                            });
                        });
                    })
                    layList.search('save_give',function (data) {
                        layList.basePost(layList.U({a:'save_give'}),{uid:uid,special_id:data.special_id},function (res) {
                            layList.msg(res.msg,function () {
                                that.$refs.give.style.display='none';
                                layList.layer.close(that.GiveIndex);
                                location.reload();
                            })
                        },function (res) {
                            layList.msg(res.msg);
                        });
                    });
                    layList.element.on('tab(tab)',function (data) {
                        that.avtive=data.index;
                    })
                    layList.element.on('tab(commission)',function (data) {
                        that.commAvtive=data.index;
                    })
                    layList.laypage.render({
                        elem: that.$refs.pay_page
                        ,count:that.count.pay_count
                        ,limit:that.limit
                        ,theme: '#1E9FFF',
                        jump:function(obj){
                            that.page.pay_page=obj.curr;
                        }
                    });
                    layList.laypage.render({
                        elem: that.$refs.bill_page
                        ,count:that.count.bill_count
                        ,limit:that.limit
                        ,theme: '#1E9FFF',
                        jump:function(obj){
                            that.page.bill_page=obj.curr;
                        }
                    });
                    layList.laypage.render({
                        elem: that.$refs.order_page
                        ,count:that.count.order_count
                        ,limit:that.limit
                        ,theme: '#1E9FFF',
                        jump:function(obj){
                            that.page.order_page=obj.curr;
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
                });
            }
        })
    })

</script>
{/block}
