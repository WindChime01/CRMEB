{extend name="public/container"}
{block name="head_top"}
<style>
    .layui-btn-xs{margin-left: 0px !important;}
    legend{
        width: auto;
        border: none;
        font-weight: 700 !important;
    }
    .site-demo-button{
        padding-bottom: 20px;
        padding-left: 10px;
    }
    .layui-form-label{
        width: auto;
    }
    .layui-input-block input{
        width: 50%;
        height: 34px;
    }
    .layui-form-item{
        margin-bottom: 0;
    }
    .layui-input-block .time-w{
        width: 200px;
    }
    .layui-btn-group button i{
        line-height: 30px;
        margin-right: 3px;
        vertical-align: bottom;
    }
    .back-f8{
        background-color: #F8F8F8;
    }
    .layui-input-block button{
        border: 1px solid #e5e5e5;
    }
</style>
{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <div class="layui-row layui-col-space15">
                        <div class="layui-col-md12">
                            <div class="layui-btn-group">
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" data-type="refresh" onclick="window.location.reload()">
                                    <i class="layui-icon">&#xe669;</i>刷新
                                </button>
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" data-type="refresh" onclick="javascript:history.back(-1);">
                                    返回
                                </button>
                            </div>
                            <table class="layui-hide" id="userList" lay-filter="userList"></table>
                            <script type="text/html" id="nickname">
                                <a href="javascript:;"style="color:#0092DC">{{d.nickname ? d.nickname :'暂无昵称'}}</a>
                            </script>
                            <script type="text/html" id="barDemo">
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" lay-event='chat_list' style="margin: 2px;">
                                    <i class="fa fa-commenting-o"></i> 查看对话
                                </button>
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
    layList.form.render();
    var uid={$uid},to_uid={$to_uid};
    layList.tableList('userList',"{:Url('get_chat_list')}?uid="+uid+'&to_uid='+to_uid,function () {
        return [
            {field: 'nickname', title: '用户名称',templet:"#nickname",align: 'center',width:'20%'},
            {field: 'avatar', title: '头像', event:'open_image',width:'15%', align: 'center', templet: '<div><img class="avatar open_image" style="cursor: pointer" height="50" data-image="{{d.avatar}}" src="{{d.avatar}}" alt="{{d.nickname}}"></div>'},
            {field: 'msn', title: '发送消息',align: 'center'},
            {field: 'add_time', title: '发送时间',align: 'center',width:'24%'},
        ];
    });
</script>
{/block}
