{extend name="public/container"}
{block name="head"}
<style>
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
    <div class="layui-row layui-col-space15"  >
        <div class="layui-col-md12" id="app" v-cloak>
            <div class="layui-card">
                <div class="layui-card-header">搜索条件</div>
                <div class="layui-card-body">
                    <div class="layui-form layui-form-pane">
                        <div class="layui-form-item">
                            <div class="layui-form-item">
                                <label class="layui-form-label">状态：</label>
                                <div class="layui-input-block">
                                    <div class="layui-btn-group">
                                        <button :class="[where.status === item.value ? 'layui-btn-normal' : 'layui-btn-primary', 'layui-btn layui-btn-sm']" @click="where.status = item.value" type="button" v-for="item in orderStatus">
                                            {{item.name}}
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-col-lg12">
                                <label class="layui-form-label">创建时间:</label>
                                <div class="layui-input-block" data-type="data">
                                    <div class="layui-input-inline" data-type="data" style="width: auto;">
                                        <div class="layui-btn-group">
                                            <button :class="[where.data === item.value ? 'layui-btn-normal' : 'layui-btn-primary', 'layui-btn layui-btn-sm']" type="button" v-for="item in dataList" @click="setData(item)">{{item.name}}</button>
                                        </div>
                                    </div>
                                    <div class="layui-input-inline">
                                        <input type="text" name="date" placeholder="自定义" id="date" autocomplete="off" class="layui-input" style="height: 30px;" ref="date_time">
                                    </div>
                                </div>
                            </div>
                            <div class="layui-col-lg12">
                                <div class="layui-input-block">
                                    <button class="layui-btn layui-btn-sm layui-btn-normal" type="button" @click="search"><i class="layui-icon layui-icon-search"></i>搜索</button>
                                    <button class="layui-btn layui-btn-sm layui-btn-normal" type="button" @click="refresh"><i class="layui-icon layui-icon-refresh" ></i>刷新</button>
                                    <button type="button" class="layui-btn layui-btn-primary layui-btn-sm export" @click="excel"><i class="layui-icon">&#xe67d;</i>导出</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">学习记录</div>
                <div class="layui-card-body">
                    <table class="layui-hide" id="userList" lay-filter="userList"></table>
                    <script type="text/html" id="level">
                        {{#  if(d.level==1){ }}
                        会员
                        {{#  }else{ }}
                        非会员
                        {{# }; }}
                    </script>
                    <script type="text/html" id="act">
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" lay-event='percentage'>
                            查看学员的具体学习进度
                        </button>
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
<script>
    var id={$id};
    layList.tableList('userList',"{:Url('specialLearningRecordsList')}?id={$id}&uid={$uid}",function () {
        return [
            {field: 'uid', title: 'UID', width:'10%',align: 'center'},
            {field: 'nickname', title: '昵称',align: 'center',width:'15%'},
            {field: 'phone', title: '电话',width:'15%',align: 'center'},
            {field: 'level', title: '身份',width:'15%',align: 'center',templet:'#level'},
            {field: 'last_study_time', title: '最后学习时间',width:'15%',align: 'center'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'30%'}
        ];
    });
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'percentage':
                layer.open({
                    type: 2,
                    title: data.nickname + '—学习进度',
                    content: '{:Url('percentage')}?uid=' + data.uid + "&special_id="+data.special_id + "&type="+data.type+"&is_light="+data.is_light,
                    area: ['80%', '90%'],
                    maxmin: true
                });
                break;
        }
    });
    require(['vue'],function(Vue) {
        new Vue({
            el: "#app",
            data: {
                badge: [],
                orderStatus: [
                    {name: '全部', value: 0},
                    {name: '已获得', value: 1},
                ],
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
                    id:id,
                    excel:0,
                    status:0,
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
                search:function () {
                    this.where.excel=0;
                    this.where.id=id;
                    layList.reload(this.where,true);
                },
                excel:function () {
                    this.where.id=id;
                    this.where.excel=1;
                    location.href=layList.U({a:'specialLearningRecordsList',q:this.where});
                },
                refresh:function () {
                    window.location.reload();
                }
            },
            mounted:function () {
                var that=this;
                layList.laydate.render({
                    elem:this.$refs.date_time,
                    trigger:'click',
                    eventElem:this.$refs.time,
                    range:true,
                    change:function (value){
                        that.where.data=value;
                    }
                });
            }
        })
    });
</script>
{/block}
