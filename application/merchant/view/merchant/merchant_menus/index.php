{extend name="public/container"}
{block name="content"}
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <div class="ibox-title">
                <button type="button" class="btn btn-w-m btn-primary" onclick="$eb.createModalFrame(this.innerText,'{:Url('create')}')">添加菜单</button>
                <div class="ibox-tools">

                </div>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="m-b-xs">
                        <form action="" class="form-inline">
                            <i class="fa fa-search" style="margin-right: 10px;"></i>
                            <select name="is_show" aria-controls="editable" class="form-control input-sm">
                                <option value="">是否显示</option>
                                <option value="1" {eq name="params.is_show" value="1"}selected="selected"{/eq}>显示</option>
                                <option value="0" {eq name="params.is_show" value="0"}selected="selected"{/eq}>不显示</option>
                            </select>
                        <div class="input-group">
                            <input type="text" name="keyword" value="{$params.keyword}" placeholder="请输入关键词" class="input-sm form-control"> <span class="input-group-btn">
                                    <button type="submit" class="btn btn-sm btn-primary"> 搜索</button> </span>
                        </div>
                        </form>
                    </div>

                </div>
                <div class="table-responsive">
                    <table class="table table-striped  table-bordered">
                        <thead>
                        <tr>

                            <th class="text-center">编号</th>
                            <th class="text-center">按钮名</th>
                            <th class="text-center">父级</th>
                            <th class="text-center">模块名</th>
                            <th class="text-center">控制器名</th>
                            <th class="text-center">方法名</th>
                            <th class="text-center">是否显示</th>
<!--                            <th class="text-center">内容管理</th>-->
                            <th class="text-center">操作</th>
                        </tr>
                        </thead>
                        <tbody class="">
                        {volist name="list" id="vo"}
                        <tr>
                            <td class="text-center">
                                {$vo.id}
                            </td>
                            <td class="text-center">
                                {$vo.menu_name}
                            </td>
                            <td class="text-center">
                                {$vo.pid}
                            </td>
                            <td class="text-center">
                                {$vo.module}
                            </td>
                            <td class="text-center">
                                {$vo.controller}
                            </td>
                            <td class="text-center">
                                {$vo.action}
                            </td>
                            <td class="text-center">
                                <i class="fa {eq name='vo.is_show' value='1'}fa-check text-navy{else/}fa-close text-danger{/eq}"></i>
                            </td>
<!--                            <td class="text-center">-->
<!--                                <button type="button" class="btn btn-xs btn-primary" onclick="$eb.createModalFrame(this.innerText,'{:Url('edit_content',array('id'=>$vo['id']))}')"><i class="fa fa-pencil"></i> 编辑内容</button>-->
<!--                            </td>-->
                            <td class="text-center">
                                <button class="btn btn-info btn-xs" type="button"  onclick="$eb.createModalFrame(this.innerText,'{:Url('edit',array('id'=>$vo['id']))}')"><i class="fa fa-paste"></i> 编辑</button>
                                <button class="btn btn-warning btn-xs" data-url="{:Url('delete',array('id'=>$vo['id']))}" type="button"><i class="fa fa-warning"></i> 删除
                                </button>
                            </td>
                        </tr>
                        {/volist}
                        </tbody>
                    </table>
                </div>
                {include file="public/inner_page"}
            </div>
        </div>
    </div>
    <script>
        $('.btn-warning').on('click',function(){
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
    </script>
</div>
{/block}
