{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">视频回放设置</div>
                <div class="layui-card-body">
                    <form class="layui-form" action="">
                        <div class="layui-form-item">
                            <label class="layui-form-label">回放设置</label>
                            <div class="layui-input-block">
                                <input type="radio" name="is_playback" value="1" lay-filter="is_playback" title="开启回放" {if $is_playback==1}checked{/if}>
                                <input type="radio" name="is_playback" value="0" lay-filter="is_playback" title="关闭回放" {if $is_playback==0}checked{/if}>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="layui-col-md12">

            <div class="layui-card">
                <div class="alert alert-info" role="alert">
                    远程同步阿里oss存储上直播回放视频地址到本地，请选择时间（区间为4天）。
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="layui-card-header">同步条件</div>
                <div class="layui-card-body">
                    <form class="layui-form layui-form-pane" action="javascript:void(0)" method='POST' id="syn_live_list">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">时间范围</label>
                                <div class="layui-input-inline" style="width: 200px;">
                                    <input type="text" name="syn_start_time" placeholder="开始时间" id="syn_start_time" class="layui-input">
                                </div>
                                <div class="layui-form-mid">-</div>
                                <div class="layui-input-inline" style="width: 200px;">
                                    <input type="text" name="syn_end_time" placeholder="结束时间" id="syn_end_time" class="layui-input">
                                </div>
                                <input type="hidden" name="stream_name" value="{$stream_name}">
                                <input type="hidden" name="record_id" value="{$record_id}">
                            </div>
                            <div class="layui-inline">
                                <div class="layui-input-inline">
                                    <button class="layui-btn layui-btn-sm layui-btn-normal playback">拉取直播回放</button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
            <div class="layui-card">
                <div class="layui-card-header">搜索条件</div>
                <div class="layui-card-body">
                    <form class="layui-form layui-form-pane" action="" >
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">时间范围</label>
                                <div class="layui-input-inline" style="width: 200px;">
                                    <input type="text" name="start_time" placeholder="开始时间" id="start_time" class="layui-input">
                                </div>
                                <div class="layui-form-mid">-</div>
                                <div class="layui-input-inline" style="width: 200px;">
                                    <input type="text" name="end_time" placeholder="结束时间" id="end_time" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <div class="layui-input-inline">
                                    <button class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="search" lay-filter="search">
                                        <i class="layui-icon layui-icon-search"></i>搜索</button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--产品列表-->
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">录制文件列表</div>
                <div class="layui-card-body">
                    <div class="alert alert-info" role="alert">
                        [回放名称][排序]可进行快速修改,双击或者单击进入编辑模式,失去焦点可进行自动保存 排序前后台按倒序排列
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="layui-btn-container">
                        <button class="layui-btn layui-btn-sm" onclick="window.location.reload()"><i class="layui-icon layui-icon-refresh"></i>  刷新</button>
                    </div>
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <script type="text/html" id="is_show">
                        <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_show' lay-text='显示|隐藏'  {{ d.is_show == 1 ? 'checked' : '' }}>
                    </script>
                    <script type="text/html" id="avatar">
                        <img style="cursor: pointer" lay-event='open_image' src="{{d.avatar}}">
                    </script>
                    <script type="text/html" id="checkboxstatus">
                        <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_show' lay-text='上架|下架'  {{ d.is_show == 1 ? 'checked' : '' }}>
                    </script>
                    <script type="text/html" id="act">
                        <button class="layui-btn layui-btn-xs" lay-event='look'>
                            <i class="fa fa-eye"></i> 预览
                        </button>
                        <button class="layui-btn layui-btn-xs layui-btn-normal" lay-event='download'>
                            <i class="fa fa-download"></i> 下载
                        </button>
                        {{# if(d.RecordId == record_id){ }}
                        <button class="layui-btn layui-btn-xs layui-btn-danger" lay-event='del_record'>
                            <i class="fa fa-cogs"></i> 取消回放
                        </button>
                        {{# }else{ }}
                        <button class="layui-btn layui-btn-xs layui-btn-normal d.RecordId" lay-event='set_record'>
                            <i class="fa fa-cog"></i> 回放设置
                        </button>
                        {{# } }}
                        <button class="layui-btn layui-btn-xs layui-btn-normal" lay-event='delete'>
                            <i class="fa fa-download"></i> 删除
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
    var stream_name='{$stream_name}';
    var record_id='{$record_id}';
    $('.playback').click(function () {
        var formData = new FormData($("#syn_live_list")[0]);
        var url="{:Url('get_live_record')}";
        $eb.$swal('delete',function(){
            $.ajax({
                type:"POST",                             //请求的类型
                url:url,                  //请求的路径
                data: formData,                    //请求的参数
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (res) {
                    var res=JSON.parse(res);
                    if (res.data.length>0) {
                        $eb.$swal('success',"同步成功 ");//成功返回触发的方法
                    }else{
                        $eb.$swal('error',res.msg);
                    }
                    window.location.reload();
                },
                //请求失败触发的方法
                error:function(res){
                    $eb.$swal('error',"异常错误");
                    window.location.reload();
                }
            })
        },{title:'拉取直播回放',text:'拉取成功后，回放回放视频地址会储存到本地',confirm:'是的，我要拉取'});
    });
    //实例化form
    layList.form.render();
    layList.date({elem:'#start_time',type:'datetime'});
    layList.date({elem:'#syn_start_time',type:'datetime'});
    layList.date({elem:'#end_time',type:'datetime'});
    layList.date({elem:'#syn_end_time',type:'datetime'});
    //加载列表
    layList.tableList('List',"{:Url('get_live_record_list')}?stream_name={$stream_name}&record_id={$record_id}",function (){
        return [
            {field: 'stream_name', title: '直播间号',align:"center",width:'10%'},
            {field: 'playback_name', title: '回放名称',edit:'playback_name',align:"center",width:'16%'},
            {field: 'playback_url', title: '地址',align:"center",width:'20%'},
            {field: 'StartTime', title: '开始时间',align:'center',width:'10%'},
            {field: 'EndTime', title: '结束时间',align:'center',width:'10%'},
            {field: 'sort', title: '排序',sort: true,event:'sort',edit:'sort',align: 'center',width:'6%'},
            {field: 'is_show', title: '状态',templet:"#checkboxstatus",width:'9%'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act'},
        ];
    });
    layList.form.on('radio(is_playback)', function(data){
        layList.baseGet(layList.U({a:'set_value',q:{stream_name:stream_name,field:'is_playback',value:data.value}}),function (res) {
            layList.msg(res.msg);
        },function (res) {
            layList.msg(res.msg);
        })
    });
    //显示回放
    layList.switch('is_show',function (odj,value) {
        if(odj.elem.checked==true){
            layList.baseGet(layList.Url({c:'live.aliyun_live',a:'set_show',p:{is_show:1,id:value}}),function (res) {
                layList.msg(res.msg);
            });
        }else{
            layList.baseGet(layList.Url({c:'live.aliyun_live',a:'set_show',p:{is_show:0,id:value}}),function (res) {
                layList.msg(res.msg);
            });
        }
    });
    //自定义方法
    var action= {
        set_value: function (field, id, value) {
            layList.baseGet(layList.Url({
                a: 'set_playback_value',
                q: {field: field, id: id, value: value}
            }), function (res) {
                layList.msg(res.msg);
            });
        },
    };
    //查询
    layList.search('search',function(where){
        layList.reload(where,true);
    });
    layList.switch('is_show',function (odj,value) {
        if(odj.elem.checked==true){
            layList.baseGet(layList.Url({a:'set_show',p:{is_show:1,id:value}}),function (res) {
                layList.msg(res.msg);
            });
        }else{
            layList.baseGet(layList.Url({a:'set_show',p:{is_show:0,id:value}}),function (res) {
                layList.msg(res.msg);
            });
        }
    });
    //快速编辑
    layList.edit(function (obj) {
        var id=obj.data.id,value=obj.value;
        switch (obj.field) {
            case 'playback_name':
                action.set_value('playback_name',id,value);
                break;
            case 'sort':
                if(value < 0) return layList.msg('排序不能小于0');
                action.set_value('sort',id,value);
                break;
        }
    });
    //监听并执行排序
    layList.sort(['id','sort'],true);
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'delete':
                var url=layList.U({a:'delete_live_playback',q:{id:data.id}});
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
                },{title:'删除直播间回放',text:'删除后无法恢复',confirm:'是的，我要删除'});
                break;
            case 'look':
                $eb.createModalFrame('直播间号【'+stream_name+'】的直播回放,回放时间：'+data.start_time+' - '+data.end_time,layList.U({a:'live_record_look',q:{record_url:data.playback_url}}),{w:890,h:450});
                break;
            case 'download':
                $eb.createModalFrame('直播间号【'+stream_name+'】的直播下载',layList.U({a:'download',q:{record_url:data.playback_url}}),{w:900});
                break;
            case 'set_record':
                var url = layList.U({a:'set_record',q:{stream_name:stream_name,record_id:data.RecordId,field:'is_record',value:1}});
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
                },{title:'设置为直播间回放',text:'设置成功后，没有开启直播将自动播放此回放',confirm:'是的，我要设置'});
                break;
            case 'del_record':
                var url = layList.U({a:'set_record',q:{stream_name:stream_name,record_id:data.RecordId,field:'is_record',value:0}});
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
                },{title:'取消直播间回放',text:'设置成功后，没有开启直播将不会回放',confirm:'是的，我要取消'});
                break;
        }
    })
</script>
{/block}
