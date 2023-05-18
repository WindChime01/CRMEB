{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">搜索条件</div>
                <div class="layui-card-body">
                    <div class="layui-form layui-form-pane">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">专题名称</label>
                                <div class="layui-input-inline">
                                    <input type="text" autocomplete="off" class="layui-input" name="title" id="demoReload" placeholder="请输入专题名称">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">讲师</label>
                                <div class="layui-input-inline">
                                    <select name="mer_id"  id="mer_id">
                                        <option value="">全部</option>
                                        <option value="0">总平台</option>
                                        {volist name='mer_list' id='vc'}
                                        <option  value="{$vc.id}">{$vc.mer_name}</option>
                                        {/volist}
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">专题类型</label>
                                <div class="layui-input-inline">
                                    <select name="type" id="test">
                                        <option value="">全部</option>
                                        <option value="1">图文专题</option>
                                        <option value="2">音频专题</option>
                                        <option value="3">视频专题</option>
                                        <option value="4">直播专题</option>
                                        <option value="5">专栏专题</option>
                                        <option value="6">轻专题</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="" lay-filter="search" >
                                    <i class="layui-icon">&#xe615;</i>搜索
                                </button>
                                <button class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload()"><i class="layui-icon layui-icon-refresh"></i> 刷新</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--产品列表-->
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <script type="text/html" id="image">
                        <img style="cursor: pointer;width: 80px;height: 40px;" lay-event='open_image' src="{{d.image}}">
                    </script>
                    <script type="text/html" id="is_light">
                        {{#  if(d.is_light==1){ }}
                        轻专题
                        {{# }else{ }}
                        普通专题
                        {{#  }; }}
                    </script>
                    <script type="text/html" id="is_show">
                        <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_show' lay-text='显示|隐藏'  {{ d.is_show == 1 ? 'checked' : '' }}>
                    </script>
                    <script type="text/html" id="act">
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" lay-event='give'>
                            赠送专题
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
    var uid="{$uid}";
    var $ = layui.jquery;
    var layer = layui.layer;
    //实例化form
    layList.form.render();
    //加载列表
    layList.tableList('List',"{:Url('get_relation_source_list')}",function () {
        return [
            {field: 'id', title: '编号', width:60,align: 'center'},
            {field: 'mer_name', title: '讲师', width:80},
            {field: 'types', title: '类型',align: 'center'},
            {field: 'is_light', title: '专题类别',templet:'#is_light',align:'center'},
            {field: 'title', title: '专题标题',align: 'center'},
            {field: 'image', title: '封面',templet:'#image',align: 'center'},
            {title: '操作',align:'center',toolbar:'#act',minWidth:81},
        ];
    });
    //查询
    layList.search('search',function(where){
        layList.reload({
            title: where.title,
            mer_id: where.mer_id,
            type: where.type
        },true);
    });
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'delect':
                var url=layList.U({a:'delete',q:{id:data.id, model_type:'special'}});
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
            case 'give':
                var url=layList.U({a:'save_give',q:{uid:uid,special_id:data.id}});
                $eb.$swal('delete',function(){
                    $eb.axios.get(url).then(function(res){
                        if(res.status == 200 && res.data.code == 200) {
                            window.location.reload();
                            $eb.$swal('success',res.data.msg);
                        }else
                            return Promise.reject(res.data.msg || '删除失败')
                    }).catch(function(err){
                        $eb.$swal('error',err);
                    });
                }, {
                    title:'确定赠送专题吗?',
                    text:'通过后无法撤销，请谨慎操作！',
                    confirm:'确认'
                });
                break;
            case 'open_image':
                $eb.openImage(data.image);
                break;
        }
    })

</script>
{/block}
