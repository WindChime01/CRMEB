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
                                <label class="layui-form-label">试卷名称</label>
                                <div class="layui-input-inline">
                                    <input type="text" autocomplete="off" class="layui-input" name="title" id="demoReload" placeholder="请输入试卷名称">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">试卷类型</label>
                                <div class="layui-input-block">
                                    <select name="type" id="type_test">
                                        <option value="">全部</option>
                                        <option value="1">练习</option>
                                        <option value="2">考试</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">讲师</label>
                                <div class="layui-input-inline">
                                    <select name="mer_id"  id="mer_test">
                                        <option value="">全部</option>
                                        <option value="0">总平台</option>
                                        {volist name='mer_list' id='vc'}
                                        <option  value="{$vc.id}">{$vc.mer_name}</option>
                                        {/volist}
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" data-type="reload">搜索</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--试卷列表-->
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">试卷列表</div>
                <div class="layui-card-body">
                    <div class="layui-btn-container">
                        <button class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload()"><i class="layui-icon layui-icon-refresh"></i> 刷新</button>
                    </div>
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <script type="text/html" id="type">
                        {{# if(d.type==1){ }}
                        练习
                        {{# }else if(d.type==2){ }}
                        考试
                        {{# } }}
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
    var table_date=new Array();//用于保存当前页数据
    var ids=new Array();    //用于保存选中的数据
    //实例化form
    layList.form.render();
    var table = layui.table;
    table.render({
        elem: '#List'
        ,url:"{:Url('getTestPaperList')}"
        ,cols: [[
            {type: 'checkbox'},
            {field: 'id', title: '编号', width:60},
            {field: 'type', title: '试卷类型', width:'12%', align: 'center',templet:'#type'},
            {field: 'cate', title: '试卷分类', width:'12%', align: 'center'},
            {field: 'mer_name', title: '讲师', width:80},
            {field: 'title', title: '试卷标题'},
        ]]
        ,id: 'testReload'
        ,page: {
            theme: '#0092dc'
        }
        ,limit:10
        ,done:function (res,curr,count) {
            table_date=res.data;
            for(var i=0;i< res.data.length;i++){
                if(ids.length>0){
                    for (var j = 0; j < ids.length; j++) {
                        if(res.data[i].id == ids[j].id) {
                            res.data[i]["LAY_CHECKED"]='true';/*设置勾选*/
                            /*找到对应数据改变勾选样式*/
                            var index= res.data[i]['LAY_TABLE_INDEX'];
                            $('tr[data-index=' + index + '] input[type="checkbox"]').prop('checked', true);
                            $('tr[data-index=' + index + '] input[type="checkbox"]').next().addClass('layui-form-checked');
                        }
                    }
                }
            }
            var checkStatus = table.checkStatus('List');/*获得选中的值 和判断是否是全选 isAll true全选 isAlL false 没有全选*/
            if(checkStatus.isAll){
                $('.layui-table-header th[data-field="0"] input[type="checkbox"]').prop('checked', true);
                $('.layui-table-header th[data-field="0"] div[class="layui-unselect layui-form-checkbox"]').addClass('layui-form-checked');
            }
            removeArrayRepElement(ids);
            $("#check_source_tmp",window.parent.document).val(JSON.stringify(ids));
        }
    });
    var $ = layui.$, active = {
        reload: function(){
            //执行重载
            table.reload('testReload', {
                page: {
                    curr: 1 //重新从第 1 页开始
                }
                ,where: {
                    title: $('#demoReload').val(),
                    type: $('#type_test').val(),
                    mer_id: $('#mer_test').val()
                }
            }, 'data');
        }
    };
    $('.layui-btn').on('click', function(){
        var type = $(this).data('type');
        active[type] ? active[type].call(this) : '';
    });
    //删除重复
    function removeArrayRepElement(arr) {
        for (var i = 0; i < arr.length; i++) {
            for (var j = 0; j < arr.length; j++) {
                if (arr[i].id == arr[j].id && i != j) {
                    arr.splice(j, 1);
                }
            }
        }
        return arr;
    }
    table.on('checkbox(List)', function (obj) {
        if(obj.checked==true){
            if(obj.type=='one'){
                ids.push(obj.data);
            }else{
                for(var i=0;i<table_date.length;i++){
                    ids.push(table_date[i]);
                }
            }
            ids=removeArrayRepElement(ids);
        }else{
            if(obj.type=='one'){
                for(var i=0;i<ids.length;i++){
                    if(ids[i].id==obj.data.id){
                        ids.remove(i);
                    }
                }
            }else{
                for(var i=0;i<ids.length;i++){
                    for(var j=0;j<table_date.length;j++){
                        if(ids[i].id==table_date[j].id){
                            ids.remove(i);
                        }
                    }
                }
            }
        }
        $("#check_source_tmp",window.parent.document).val(JSON.stringify(ids));
    });
    Array.prototype.remove=function(dx){
        if(isNaN(dx)||dx>this.length){return false;}
        for(var i=0,n=0;i<this.length;i++)
        {
            if(this[i]!=this[dx]){
                this[n++]=this[i];
            }
        }
        this.length-=1;
    };
</script>
{/block}