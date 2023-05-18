{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">数据库维护</div>
        <div class="layui-card-body">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md12">
                    <table class="layui-hide" id="fileList" lay-filter="fileList"></table>
                    <script type="text/html" id="fileListtool">
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" lay-event="import"><i class="layui-icon layui-icon-edit"></i>倒入</button>
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" lay-event="delFile"><i class="layui-icon layui-icon-edit"></i>删除</button>
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" lay-event="downloadFile"><i class="layui-icon layui-icon-edit"></i>下载</button>
                    </script>
                </div>
                <div class="layui-col-md12">
                    <script type="text/html" id="toolbarDemo">
                        <div class="layui-btn-container">
                            <button class="layui-btn layui-btn-normal layui-btn-sm" lay-event="backup">备份</button>
                            <button class="layui-btn layui-btn-normal layui-btn-sm" lay-event="optimize">优化表</button>
                            <button class="layui-btn layui-btn-normal layui-btn-sm" lay-event="repair">修复表</button>
                        </div>
                    </script>
                    <table class="layui-hide" id="tableListID" lay-filter="tableListID"></table>
                    <script type="text/html" id="barDemo">
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" lay-event="see"><i class="layui-icon">&#xe60a;</i>详情</button>
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
<script>
    function ipmosrting(time,part = null,start = null) {
        var datas = {
            time:time,
            part:part,
            start:start
        };
        $.ajax({
            url: layList.Url({a: 'import'}),
            data: datas,
            type: 'post',
            dataType: 'json',
            success: function (res) {
                if(res.data){
                    if(res.code){
                        setTimeout(ipmosrting(time,res.data.part,res.data.start),2000);
                    }
                }else{
                    layList.msg(res.msg);
                    return false;
                }

            },
            error: function (err) {
                console.log(err)
            }
        });

    }
    layui.use(['table', 'layer'], function(){
        var layer = layui.layer;
        var fileList = layui.table;
        var tableList = layui.table;
        //加载sql备份列表
        var buckdata = fileList.render({
            elem: '#fileList'
            ,url:"{:Url('fileList')}"
            ,cols: [[
                {field: 'filename', title: '备份名称', sort: true},
                {field: 'part', title: 'part'},
                {field: 'size', title: '大小'},
                {field: 'compress', title: 'compress'},
                {field: 'backtime', title: '时间'},
                {title: '操作', align: 'center', toolbar: '#fileListtool'}
            ]]
            ,page: false
        });

        //监听工具条
        fileList.on('tool(fileList)', function(obj){
            var data = obj.data;
            var layEvent = obj.event;
            switch (layEvent){
                case 'import':
                    layer.confirm('真的倒入该备份吗？', function(index){
                        ipmosrting(data.time,null,null);
                        layer.close(index);
                    });
                    break;
                case 'delFile':
                    layer.confirm('真的删除该备份吗？', function(index){
                        layList.basePost(layList.Url({a:'delFile'}),{feilname:data.time},function (res) {
                            layer.msg(res.msg);
                            buckdata.reload();
                        });
                        obj.del();
                        layer.close(index);
                    });
                    break;
                case 'downloadFile':
                    location.href = layList.Url({a:'downloadFile',p:{feilname:data.time}});
                    break;
            }

        });
        //加载table
        tableList.render({
            elem: '#tableListID'
            ,url:"{:Url('tablelist')}"
            ,toolbar: '#toolbarDemo'
            ,cols: [[
                {type:'checkbox'},
                {field: 'name', title: '表名称', sort: true},
                {field: 'comment', title: '备注' },
                {field: 'engine', title: '类型', sort: true},
                {field: 'data_length', title: '大小', sort: true,totalRow: true},
                {field: 'update_time', title: '更新时间', sort: true},
                {field: 'rows', title: '行数'},
                {title: '操作', align: 'center', toolbar: '#barDemo'}
            ]]
            ,page: false
        });
        //头工具栏事件
        tableList.on('toolbar(tableListID)', function(obj){
            var checkStatus = tableList.checkStatus(obj.config.id);
            var data = checkStatus.data;
            var tables = [];
            $.each(data, function (name, value) {
                if (value['name'] != undefined) tables.push(value['name']);
            });
            if(tables.length < 1 ){
                return false;
            }
            switch(obj.event){
                case 'backup':
                    layList.basePost(layList.Url({a:'backup'}),{tables:tables},function (res) {
                        layer.msg(res.msg,{icon:1,time:1000,end:function(){
                            buckdata.reload();
                        }});
                    });
                    break;
                case 'optimize':
                    layList.basePost(layList.Url({a:'optimize'}),{tables:tables},function (res) {
                        layer.msg(res.msg);
                    });
                    break;
                case 'repair':
                    layList.basePost(layList.Url({a:'repair'}),{tables:tables},function (res) {
                        layer.msg(res.msg);
                    });
                    break;
            };
        });

        //监听并执行操作
        tableList.on('tool(tableListID)', function(obj){
            var data = obj.data;
            if(obj.event === 'see'){
                $eb.createModalFrame('表名:['+data.name+'] '+data.comment,layList.Url({a:'seetable',p:{tablename:data.name}}),{w:1000,h:600});
            }
        });

    });

</script>
{/block}
