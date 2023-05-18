{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">消息通知</div>
        <div class="layui-card-body">
            <div class="layui-btn-container">
                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm synchro"><i class="layui-icon layui-icon-download-circle"></i>同步微信模版消息</button>
                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" data-type="add"><i class="layui-icon layui-icon-add-1"></i>添加消息管理</button>
                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" data-type="refresh"><i class="layui-icon layui-icon-refresh-1"></i>刷新</button>
            </div>
            <blockquote class="layui-elem-quote">
                {notempty name='data'}
                    <p>主营行业：{$data.primary_industry.first_class} | {$data.primary_industry.second_class}</p>
                    <p>副营行业：{$data.secondary_industry.first_class} | {$data.secondary_industry.second_class}</p>
                {else /}
                    <p>主营行业：未选择</p>
                    <p>副营行业：未选择</p>
                {/notempty}
            </blockquote>
            <table id="List" lay-filter="List"></table>
            <script type="text/html" id="is_wechat">
                <input type='checkbox' name='is_wechat' lay-skin='switch' value="{{d.id}}" lay-filter='is_wechat' lay-text='显示|隐藏'  {{ d.is_wechat == 1 ? 'checked' : '' }}>
            </script>
            <script type="text/html" id="is_sms">
                <input type='checkbox' name='is_sms' lay-skin='switch' value="{{d.id}}" lay-filter='is_sms' lay-text='显示|隐藏'  {{ d.is_sms == 1 ? 'checked' : '' }}>
            </script>
            <script type="text/html" id="act">
                <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" lay-event="weixin">
                    <i class="layui-icon">&#xe642;</i>模版设置
                </button>
                {{# if(d.temp_id){ }}
                <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" lay-event="sms">
                    <i class="layui-icon">&#xe642;</i>短信设置
                </button>
                {{#  }else{ }}
                <button type="button" class="layui-btn layui-btn-xs layui-btn-disabled">
                    <i class="layui-icon">&#xe642;</i>短信设置
                </button>
                {{#  }; }}
                <button type="button" class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">
                    <i class="layui-icon">&#xe640;</i>删除
                </button>
            </script>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name="script"}
<script>
    var $ = layui.jquery;
    var layer = layui.layer;
    //实例化form
    layList.form.render();
    //加载列表
    layList.tableList({o:'List', done:function () {}},"{:Url('system_message_list')}",function (){
        return [
            {field: 'id', title: '编号', align: 'center',width:'5%'},
            {field: 'name', title: '消息名称',align: 'left',width:'12%'},
            {field: 'template_const', title: '模版常数',align: 'left',width:'20%'},
            {field: 'tempkey', title: '微信模板编号',align:'center',width:'12%'},
            {field: 'temp_id', title: '短信模板ID',align:'center',width:'10%'},
            {field: 'is_wechat', title: '公众号模板',templet:'#is_wechat',align: 'center',width:'10%'},
            {field: 'is_sms', title: '发送短信',align: 'center',width:'10%',templet:function(d){
                    var is_checked = d.is_sms == 1 ? "checked" : "";
                    if(d.temp_id != ''){
                        return "<input type='checkbox' name='is_sms' lay-skin='switch' value='"+d.id+"' lay-filter='is_sms' lay-text='显示|隐藏' "+is_checked+">";
                    }else{
                        return ''
                    }

              }},
            {field: 'right', title: '操作',align:'center',toolbar:'#act'}
        ];
    });
    //自定义方法
    var action= {
        set_value: function (field, id, value) {
            layList.baseGet(layList.Url({
                a: 'set_value',
                q: {field: field, id: id, value: value}
            }), function (res) {
                layList.msg(res.msg);
            });
        },
    };
    //监听并执行排序
    layList.sort(['id'],true);
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'del':
                var url=layList.U({a:'delete',q:{id:data.id}});
                $eb.$swal('delete',function(){
                    $eb.axios.get(url).then(function(res){
                        if(res.status == 200 && res.data.code == 200) {
                            $eb.$swal('success',res.data.msg);
                            location.reload();
                        }else
                            return Promise.reject(res.data.msg || '删除失败')
                    }).catch(function(err){
                        $eb.$swal('error',err);
                    });
                });
                break;
            case 'weixin':
                $eb.createModalFrame('模版设置',layList.Url({a:'edit',p:{tempkey:data.tempkey}}),{w:800,h:600});
                break;
            case 'sms':
                $eb.createModalFrame('短信设置',layList.Url({a:'sms',p:{id:data.id}}),{w:800,h:600});
                break;
        }
    });
    //是否显示快捷按钮操作
    layList.switch('is_wechat',function (odj,id) {
        var value= odj.elem.checked==true ? 1 : 0;
        layList.baseGet(layList.Url({a:'set_value',p:{field:'is_wechat',id:id,value:value}}),function (res) {
            layList.msg(res.msg);
        },function (err) {
            layList.msg(err.msg,function () {
                location.reload();
            });
        });
    });
    //是否显示快捷按钮操作
    layList.switch('is_sms',function (odj,id) {
        var value= odj.elem.checked==true ? 1 : 0;
        layList.baseGet(layList.Url({a:'set_value',p:{field:'is_sms',id:id,value:value}}),function (res) {
            layList.msg(res.msg);
        },function (err) {
            layList.msg(err.msg,function () {
                location.reload();
            });
        });
    });
    $('.layui-btn').on('click', function (event) {
        var target = event.target;
        var type = target.dataset.type;
        if ('add' === type) {
            layer.open({
                type: 2,
                title: '添加消息管理',
                content: '{:Url('create')}',
                area: ['55%', '70%'],
                maxmin: true
            });
        } else if ('refresh' === type) {
            window.location.reload();
        }
    });
    $('.synchro').on('click', function (event) {
        var url=layList.Url({a:'synchronousWechatTemplate'});
        $eb.$swal('delete',function(){
            $eb.axios.get(url).then(function(res){
                if(res.data.code == 200) {
                    window.location.reload();
                    $eb.$swal('success', res.data.msg);
                }else
                    $eb.$swal('error',res.data.msg||'操作失败!');
            });
        },{
            title:'确定同步微信模版消息?',
            text:'通过后无法撤销，请谨慎操作！',
            confirm:'确定同步'
        });
    });
</script>
{/block}
