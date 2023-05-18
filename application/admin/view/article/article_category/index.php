{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header"></div>
        <div class="layui-card-body">
            <form action="" class="layui-form layui-form-pane">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <select name="status" lay-verify="">
                                <option value=""></option>
                                <option value="1" {eq name="$where.status" value="1"}selected="selected"{/eq}>显示</option>
                                <option value="0" {eq name="$where.status" value="0"}selected="selected"{/eq}>不显示</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <input type="text" placeholder="请输入关键词" class="layui-input" name="title" value="{$where.title}">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <button type="submit" class="layui-btn layui-btn-sm layui-btn-normal">
                                <i class="layui-icon layui-icon-search"></i>搜索
                            </button>
                            <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" style="background-color:#0092DC;" onclick="window.location.reload()">
                                <i class="layui-icon">&#xe669;</i>刷新
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            <div class="layui-btn-group">
                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" data-type="add">
                    <i class="layui-icon">&#xe608;</i>添加新闻分类
                </button>
            </div>
            <table class="layui-table">
                <thead>
                    <tr>
                        <th style="width: 60px;" lay-data="{align:'center'}">编号</th>
                        <th style="text-align: center">分类昵称</th>
                        <th style="text-align: center">状态</th>
                        <th style="text-align: center">查看文章</th>
                        <th style="text-align: center" lay-data="{align:'center'}">操作</th>
                    </tr>
                </thead>
                <tbody>
                    {volist name="list" id="vo"}
                    <tr>
                        <td>
                            {$vo.id}
                        </td>
                        <td class="text-center">
                            {$vo.title}
                        </td>
                        <td class="text-center">
                            {if condition="$vo['status'] eq 1"}
                            <i class="fa fa-check text-navy"></i>
                            {else/}
                            <i class="fa fa-close text-danger"></i>
                            {/if}
                        </td>
                        <td class="text-center">
                            <a href="{:Url('article.article_v1/index',array('cid'=>$vo['id']))}" class="layui-btn layui-btn-normal layui-btn-xs">查看文章</a>
                        </td>
                        <td class="text-center">
                            <button class="layui-btn layui-btn-normal layui-btn-xs" type="button" data-type="edit" data-id="{$vo.id}">
                                <i class="layui-icon">&#xe642;</i>编辑
                            </button>
                            <button class="layui-btn layui-btn-danger layui-btn-xs" type="button" data-type="delete" data-id="{$vo.id}" data-url="{:Url('delete',array('id'=>$vo['id']))}" >
                                <i class="layui-icon">&#xe640;</i>删除
                            </button>
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
    parent.$('.J_menuTab').each(function () {
        if ($(this).hasClass('active')) {
            $('.layui-card-header').text($(this).text());
            return false;
        }
    });
    var form = layui.form;
    form.render();

    var active = {
        add: function () {
            $eb.createModalFrame('添加新闻分类', "{:Url('create')}");
        },
        edit: function () {
            var _this = $(this),
                id =_this.data('id');
            $eb.createModalFrame('编辑','{:Url('edit')}?id='+id);
        },
        delete: function () {
            var _this = $(this),
                url =_this.data('url');
            $eb.$swal('delete',function(){
                $eb.axios.get(url).then(function(res){
                    if (res.status == 200 && res.data.code == 200) {
                        $eb.$swal('success',res.data.msg);
                        _this.parents('tr').remove();
                    } else {
                        return Promise.reject(res.data.msg || '删除失败');
                    }
                }).catch(function(err){
                    $eb.$swal('error',err);
                });
            })
        }
    };

    $('.layui-btn').on('click', function () {
        var type = $(this).data('type');
        active[type] ? active[type].call(this) : '';
    });

    $('.image_info').on('click',function (e) {
        var image_url = $(this).data('image');
        $eb.openImage(image_url);
    })

    $('.add_filed_base').on('click',function (e) {
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
                $eb.createModalFrame(this.innerText,"{:Url('SystemConfig/create')}?type="+result);
            }
        })
    })
</script>
{/block}
