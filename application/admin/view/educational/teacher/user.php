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
                        <form class="layui-form layui-form-pane">
                            <div class="layui-form-item">
                                <div class="layui-inline">
                                    <label class="layui-form-label">用户搜索：</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="nickname" lay-verify="nickname" style="width: 100%" autocomplete="off" placeholder="请输入姓名、编号、手机号" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="" lay-filter="search" >
                                            <i class="layui-icon layui-icon-search"></i>搜索
                                        </button>
                                        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" onclick="window.location.reload()">
                                            <i class="layui-icon">&#xe669;</i>刷新
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="layui-col-md12">
                        <table class="layui-hide" id="userList" lay-filter="userList"></table>
                        <script type="text/html" id="nickname">
                            <a href="javascript:;"style="color:#0092DC">{{d.nickname ? d.nickname :'暂无昵称'}}</a>
                        </script>
                        <script type="text/html" id="barDemo">
                            <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" lay-event='add_teacher' style="margin: 2px;">
                                选择
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
    layList.tableList('userList',"{:Url('user_list')}",function () {
        return [
            {field: 'uid', title: 'UID', align: 'center'},
            {field: 'avatar', title: '头像', event:'open_image', align: 'center', templet: '<div><img class="avatar open_image" style="cursor: pointer" height="50" data-image="{{d.avatar}}" src="{{d.avatar}}" alt="{{d.nickname}}"></div>'},
            {field: 'nickname', title: '姓名',templet:"#nickname",align: 'center'},
            {field: 'action', title: '操作', align: 'center', toolbar: '#barDemo'}
        ];
    });
    layList.search('search',function(where){
        layList.reload(where,true);
    });
    //监听并执行 uid 的排序
    layList.tool(function (event,data) {
        var layEvent = event;
        switch (layEvent){
            case 'add_teacher':
                var uid=data.uid;
                if(uid){
                    $eb.createModalFrame('添加老师',layList.Url({c:'educational.teacher',a:'create',p:{uid:uid}}),{'w':1200});
                }else{
                    layList.msg('请正确选择要成为老师的用户');
                }
                break;
        }
    });
</script>
{/block}
