{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <form action="" class="layui-form layui-form-pane">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <select name="status">
                                <option value="">状态</option>
                                <option value="1" {eq name="where.status" value="1"}selected="selected"{/eq}>显示</option>
                                <option value="0" {eq name="where.status" value="0"}selected="selected"{/eq}>不显示</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <input type="text" placeholder="请输入分类昵称" name="title" value="{$where.title}" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-inline">
                        <button type="submit" class="layui-btn layui-btn-normal layui-btn-sm"><i class="layui-icon">&#xe615;</i>搜索</button>
                    </div>
                </div>
            </form>
            <div class="layui-btn-container">
                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm add-config-category"><i class="layui-icon">&#xe608;</i>添加配置分类</button>
                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm add-config"><i class="layui-icon">&#xe608;</i>添加配置</button>
            </div>
            <table class="layui-table">
                <thead>
                    <tr>
                        <th>编号</th>
                        <th>分类昵称</th>
                        <th>分类字段</th>
                        <th>是否显示</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    {volist name="list" id="vo"}
                    <tr>
                        <td>{$vo.id}</td>
                        <td>
                            <a href="{:url('sonconfigtab',array('tab_id'=>$vo['id']))}" style="cursor: pointer">{$vo.title}</a>
                        </td>
                        <td>{$vo.eng_title}</td>
                        <td>
                            {if condition="$vo.status eq 1"}
                            <i class="fa fa-check text-navy"></i>
                            {elseif condition="$vo.status eq 2"/}
                            <i class="fa fa-close text-danger"></i>
                            {/if}
                        </td>
                        <td>
                            <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="$eb.createModalFrame('编辑','{:Url('edit',array('id'=>$vo['id']))}')">
                                <i class="layui-icon">&#xe642;</i>编辑
                            </button>
                            {if condition="$vo['id'] gt 2"}
                            <button type="button" class="layui-btn layui-btn-danger layui-btn-xs" data-id="{$vo.id}" data-url="{:Url('delete',array('id'=>$vo['id']))}" >
                                <i class="layui-icon">&#xe640;</i>删除
                            </button>
                            {/if}
                        </td>
                    </tr>
                    {/volist}
                </tbody>
            </table>
            {include file="public/inner_page"}
        </div>
    </div>
</div>
{/block}
{block name="script"}
<script>
    var form = layui.form;
    form.render();
    $('.add-config-category').on('click',function (e) {
        $eb.createModalFrame('添加配置分类',"{:Url('create')}");
    })
    $('.layui-btn-danger').on('click',function(){

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
    $('.add-config').on('click',function (e) {
        $eb.swal({
            title: '请选择数据类型',
            input: 'radio',
            inputOptions: ['文本框','多行文本框','单选框','文件上传','多选框'],
            inputValidator: function(result) {
                return new Promise(function(resolve, reject) {
                    if (result) {
                        resolve();
                    } else {
                        reject('请选择数据类型');
                    }
                });
            }
        }).then(function(result) {
            if (result) {
                $eb.createModalFrame(this.innerText,"{:Url('setting.systemConfig/create')}?type="+result);
            }
        })
    })
</script>
{/block}
