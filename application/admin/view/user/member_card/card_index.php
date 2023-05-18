{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <div class="layui-row layui-col-space15">
                        <div class="layui-col-md12">
                            <div class="layui-tab layui-tab-brief" lay-filter="tab">
                                <ul class="layui-tab-title">
                                    <li lay-id="list" {eq name='activity_type' value='1'}class="layui-this" {/eq} >
                                    <a href="{eq name='activity_type' value='1'}javascript:;{else}{:Url('index',['activity_type'=>1])}{/eq}">批次列表</a>
                                    </li>
                                    <li lay-id="list" {eq name='activity_type' value='2'}class="layui-this" {/eq}>
                                    <a href="{eq name='activity_type' value='2'}javascript:;{else}{:Url('card_index',['activity_type'=>2])}{/eq}">会员卡列表</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="layui-col-md12">
                            <form class="layui-form layui-form-pane" action="">
                                <div class="layui-form-item">
                                    <div class="layui-inline">
                                        <label class="layui-form-label">批次</label>
                                        <div class="layui-input-inline">
                                            <select name="card_batch_id">
                                                <option value="">全部</option>
                                                {foreach $batch_list as $vo}
                                                <option value="{$vo.id}" {if condition="$card_batch_id eq $vo['id']"} selected {/if}>{$vo.title}</option>
                                                {/foreach}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <label class="layui-form-label">卡号</label>
                                        <div class="layui-input-inline">
                                            <input type="text" name="card_number" class="layui-input" placeholder="请输入卡号">
                                            <input type="hidden" name="activity_type" value="{$activity_type}">
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <label class="layui-form-label">电话</label>
                                        <div class="layui-input-inline">
                                            <input type="text" name="phone" class="layui-input" placeholder="请输入电话">

                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <label class="layui-form-label">是否领取</label>
                                        <div class="layui-input-inline">
                                            <select name="is_use">
                                                <option value="">全部</option>
                                                <option value="1">领取</option>
                                                <option value="0">未领取</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <label class="layui-form-label">是否激活</label>
                                        <div class="layui-input-inline">
                                            <select name="is_status">
                                                <option value="">全部</option>
                                                <option value="1">激活</option>
                                                <option value="0">冻结</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <div class="layui-input-inline">
                                            <button class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="search" lay-filter="search">
                                                <i class="layui-icon">&#xe615;</i>搜索
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!--产品列表-->
                        <div class="layui-col-md12">
                            <div class="layui-btn-group">
                                <button class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload()"><i class="layui-icon">&#xe669;</i>刷新</button>
                            </div>
                            <table class="layui-hide" id="List" lay-filter="List"></table>
                            <script type="text/html" id="is_status">
                                <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_status' lay-text='激活|冻结'  {{ d.status == 1 ? 'checked' : '' }}>
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name="script"}
<script>
    //实例化form
    layList.form.render();
    //加载列表
    layList.tableList({o:'List', done:function () {}},"{:Url('card_list',['card_batch_id'=>$card_batch_id])}",function (){
        return [
            {field: 'id', title: '编号', sort: true,event:'id',width:'6%',align: 'center'},
            {field: 'card_number', title: '卡号',align: 'center'},
            {field: 'card_password', title: '密码',align: 'center'},
            {field: 'username', title: '领取人',align: 'center'},
            {field: 'user_phone', title: '领取人电话',align: 'center'},
            {field: 'use_time', title: '领取时间',align: 'center'},
            {field: 'status', title: '是否激活', templet:'#is_status',align: 'center'}
        ];
    });
    //自定义方法
    var action= {
        set_value: function (field, id, value, model_type) {
            layList.baseGet(layList.Url({
                a: 'set_value',
                q: {field: field, id: id, value: value, model_type:model_type}
            }), function (res) {
                layList.msg(res.msg);
            }, function (err) {
                layList.msg(err.msg);
            });
        },
        //打开新添加页面
        open_add: function (url,title) {
            layer.open({
                type: 2 //Page层类型
                ,area: ['100%', '100%']
                ,title: title
                ,shade: 0.6 //遮罩透明度
                ,maxmin: true //允许全屏最小化
                ,anim: 1 //0-6的动画形式，-1不开启
                ,content: url
                ,end:function() {
                    location.reload();
                }
            });
        }
    };
    //查询
    layList.search('search',function(where){
        layList.reload(where,true);
    });
    layList.switch('is_status',function (odj,value) {
        var is_status_value = 0
        if(odj.elem.checked==true){
            var is_status_value = 1
        }
        action.set_value('status',value,is_status_value,'member_card');
    });
    layList.sort(['id','sort'],true);
</script>
{/block}

