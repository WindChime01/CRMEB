{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">通知列表</div>
                <div class="layui-card-body">
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <!--消息状态-->
                    <script type="text/html" id="type">
                            <p>系统消息</p>
                    </script>
                    <!--消息状态-->
                
                    <script type="text/html" id="act">
                    <!--  <button type="button" class="layui-btn layui-btn-xs" lay-event="details"><i class="layui-icon layui-icon-edit"></i>详情</button> -->
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
    layList.tableList('List',"{:Url('index')}",function (){
        return [
            {field: 'id', title: '编号', sort: true,event:'id',width:'4%'},
            {field: 'user', title: '发送人',width:'8%'},
            {field: 'title', title: '通知标题',width:'14%'},
            {field: 'content', title: '通知内容'},
            // {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'10%'},
        ];
    });
    //下拉框
    $(document).click(function (e) {
        $('.layui-nav-child').hide();
    })
        //监听并执行 uid 的排序
    layList.tool(function (event,data) {
        var layEvent = event;
        switch (layEvent){
            case 'edit':
                $eb.createModalFrame('编辑',layList.Url({a:'edit',p:{uid:data.uid}}));
                break;
            case 'details':
                $eb.createModalFrame(data.nickname+'-会员详情',layList.Url({a:'details',p:{uid:data.uid}}));
                break;
        }
    });
    function dropdown(that) {
        var oEvent = arguments.callee.caller.arguments[0] || event;
        oEvent.stopPropagation();
        var offset = $(that).offset();
        var index = $(that).parents('tr').data('index');
        $('.layui-nav-child').each(function (key) {
            if (key != index) {
                $(this).hide();
            }
        })
        if($(document).height() < offset.top+$(that).next('ul').height()){
            $(that).next('ul').css({
                'padding': 10,
                'top': offset.top-$(that).next('ul').height()-30,
                'min-width': 'inherit',
                'left': offset.left - $(that).width() / 2,
                'position': 'fixed'
            }).toggle();
        }else{
            $(that).next('ul').css({
                'padding': 10,
                'top': offset.top + 30,
                'min-width': 'inherit',
                'left': offset.left - $(that).width() / 2,
                'position': 'fixed'
            }).toggle();
        }
    }
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'send':
                var url =layList.U({c:'user.user_notice',a:'send',p:{id:data.id}});
                $eb.$swal('delete',function(){
                    $eb.axios.get(url).then(function(res){
                        if(res.status == 200 && res.data.code == 200) {
                            $eb.$swal('success',res.data.msg);
                            obj.update({is_send:1,send_time:'{:date("Y-m-d H:i:s",time())}'});
                        }else
                            return Promise.reject(res.data.msg || '发送失败')
                    }).catch(function(err){
                        $eb.$swal('error',err);
                    });
                },{
                    title:"您确定要发送这条信息吗",
                    text:"发送后将无法修改通知信息，请谨慎操作！",
                    confirm:"是的，我要发送！",
                    cancel:"让我再考虑一下"
                })
                break;
        }
    })

    $('.btn-warning').on('click',function(){
        window.t = $(this);
        var _this = $(this),url =_this.data('url');
        $eb.$swal('delete',function(){
            $eb.axios.get(url).then(function(res){
                if(res.status == 200 && res.data.code == 200) {
                    $eb.$swal('success',res.data.msg);
                    _this.parents('tr').remove();
                }else
                    return Promise.reject(res.data.msg || '删除失败')
            }).catch(function(err){
                $eb.$swal('error',err);
            });
        })
    });
    $('.btn-send').on('click',function(){
        var _this = $(this),url =_this.data('url');
        $eb.$swal('delete',function(){
            $eb.axios.get(url).then(function(res){
                if(res.status == 200 && res.data.code == 200) {
                    $eb.$swal('success',res.data.msg);
                    window.location.reload();
                }else
                    return Promise.reject(res.data.msg || '发送失败')
            }).catch(function(err){
                $eb.$swal('error',err);
            });
        },{
            title:"您确定要发送这条信息吗",
            text:"发送后将无法修改通知信息，请谨慎操作！",
            confirm:"是的，我要发送！",
            cancel:"让我再考虑一下"
        })
    });
    $('.head_image').on('click',function (e) {
        var image = $(this).data('image');
        $eb.openImage(image);
    })
</script>
{/block}
