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
<div class="layui-fluid">
    <div v-cloak id="app" class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">直播贡献</div>
                <div class="layui-card-body">
                    <form class="layui-form" lay-filter="form" action="">
                        <div class="layui-form-item">
                            <label class="layui-form-label">搜索内容：</label>
                            <div class="layui-input-inline" style="width: 350px;">
                                <input type="text" name="user_info" v-model="where.user_info" placeholder="请输入用户昵称或手机号" autocomplete="off" class="layui-input" style="height: 30px;">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">直播间：</label>
                            <div class="layui-input-inline" style="width: 350px;">
                                <select name="live_id" lay-filter="live_id">
                                    <option value="">全部</option>
                                    <option v-for="item in live_studio" :value="item.id">{{ item.live_title }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">礼物搜索：</label>
                            <div class="layui-input-inline" style="width: 350px;">
                                <select name="gift_id" lay-filter="gift_id">
                                    <option value="">全部</option>
                                    <option v-for="item in giftList" :value="item.id">{{ item.live_gift_name }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">创建时间：</label>
                            <div class="layui-input-inline" data-type="date" style="width: auto;">
                                <div class="layui-btn-group">
                                    <button :class="[where.date === item.value ? 'layui-btn-normal' : 'layui-btn-primary', 'layui-btn layui-btn-sm']" type="button" v-for="item in dateList" @click="setData(item)">{{item.name}}</button>
                                </div>
                            </div>
                            <div class="layui-input-inline">
                                <input type="text" name="date" placeholder="自定义" autocomplete="off" id="date" class="layui-input" style="height: 30px;">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                                <button @click="search" type="button" class="layui-btn layui-btn-sm layui-btn-normal">
                                    <i class="layui-icon">&#xe615;</i> 搜索</button>
                                <button @click="refresh" type="reset" class="layui-btn layui-btn-normal layui-btn-sm">
                                    <i class="layui-icon">&#xe669;</i> 刷新</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- 中间详细信息-->
            <templet v-for="(item, index) in badge">
                <div v-if="item.count > 0" :key="index" :class="item.col!=undefined ? 'layui-col-sm'+item.col+' '+'layui-col-md'+item.col:'layui-col-sm6 layui-col-md3'">
                    <div class="layui-card">
                        <div class="layui-card-header">
                            {{item.name}}
                            <span class="layui-badge layuiadmin-badge" :class="item.background_color">{{item.field}}</span>
                        </div>
                        <div class="layui-card-body">
                            <p class="layuiadmin-big-font">{{item.count}}</p>
                            <p v-if="item.content!=undefined">
                                {{item.content}}
                                <span class="layuiadmin-span-color">{{item.sum}}<i :class="item.class"></i></span>
                            </p>
                        </div>
                    </div>
                </div>
            </templet>
            <!--enb-->
        </div>
    </div>
    <!--产品列表-->
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-body">
                <table class="layui-hide" id="List" lay-filter="List"></table>
                <script type="text/html" id="image">
                    <img style="cursor: pointer;width: 80px;height: 40px;" lay-event='open_image' src="{{d.gift_image}}">
                </script>
            </div>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name="script"}
<script>
    var live_id="{$live_id}";
    //加载列表
    layList.tableList({o:'List', done:function () {}},"{:Url('live_reward_list')}?live_id="+live_id,function (){
        return [
            {field: 'id', title: '编号', width:60,align: 'center'},
            {field: 'live_title', title: '直播间',align: 'center'},
            {field: 'nickname', title: '用户名',align: 'center'},
            {field: 'avatar', title: '头像',align: 'center',templet: '<p><img class="avatar" style="cursor: pointer" class="open_image" data-image="{{d.avatar}}" src="{{d.avatar}}" alt="{{d.nickname}}"></p>'},
            {field: 'gift_name', title: '礼物',align: 'center'},
            {field: 'gift_image', title: '礼物图片',templet:'#image',align: 'center'},
            {field: 'gift_price', title: '礼物价格（{$gold_info["gold_name"]}）',align: 'center'},
            {field: 'gift_num', title: '礼物数量',align: 'center'},
            {field: 'total_price', title: '总额（{$gold_info["gold_name"]}）', align: 'center'},
            {field: 'add_time', title: '贡献时间',align: 'center'},
        ];
    });
    //查询
    layList.search('search',function(where){
        layList.reload(where,true);
    });
    layList.switch('gis_show',function (odj,value) {
        var is_show_value = 0
        if(odj.elem.checked==true){
            var is_show_value = 1
        }
        action.set_value('is_show',value,is_show_value,'live_goods');
    });
    //快速编辑
    layList.edit(function (obj) {
        var id=obj.data.live_goods_id,value=obj.value;
        switch (obj.field) {
            case 'gsort':
                if(value < 0) return layList.msg('排序不能小于0');
                action.set_value('sort',id,value,'live_goods');
                break;
            case 'gfake_sales':
                action.set_value('fake_sales',id,value,'live_goods');
                break;
        }
    });
    //监听并执行排序
    layList.sort(['live_goods_id','gsort'],true);
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'open_image':
                $eb.openImage(data.image);
                break;
        }
    })
    var live_studio='<?=$live_studio;?>';
    require(['vue'],function(Vue) {
        new Vue({
            el: "#app",
            data: {
                badge: [],
                giftList:[],
                live_studio: JSON.parse(live_studio),
                dateList: [
                    {name: '全部', value: ''},
                    {name: '今天', value: 'today'},
                    {name: '昨天', value: 'yesterday'},
                    {name: '本周', value: 'week'},
                    {name: '本月', value: 'month'},
                    {name: '本季度', value: 'quarter'},
                    {name: '本年', value: 'year'},
                ],
                where:{
                    date:'',
                    user_info:'',
                    live_id:live_id,
                    gift_id:0,
                },
                showtime: false,
            },
            methods: {
                setData:function(item){
                    this.where.date = item.value;
                    layui.form.val('form', {
                        'date': ''
                    });
                },
                getBadge:function() {
                    var that=this;
                    layList.basePost(layList.Url({c:'live.aliyun_live',a:'getBadge'}),this.where,function (rem) {
                        that.badge=rem.data;
                    });
                },
                liveGiftList:function() {
                    var that=this;
                    layList.baseGet(layList.Url({c:'live.aliyun_live',a:'liveGiftList'}),function (rem) {
                        that.giftList=rem.data;
                        that.$nextTick(function () {
                            layui.form.render();
                            layui.form.on('select(gift_id)', function (data) {
                                that.where.gift_id = data.value;
                            });
                        });
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
                layui.form.render();
                that.getBadge();
                that.liveGiftList();
                layList.laydate.render({
                    elem: '#date',
                    trigger:'click',
                    range: true,
                    done: function (value){
                        that.where.date = value;
                    }
                });
                this.$nextTick(function () {
                    layui.form.val('form', {
                        live_id: live_id
                    });
                    layui.form.on('select(live_id)', function (data) {
                        that.where.live_id = data.value;
                    });
                });
            }
        })
    });
</script>
{/block}

