{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header"></div>
        <div class="layui-card-body">
            <form action="" class="layui-form layui-form-pane">
                <div class="layui-form-item">
                    <div class="layui-input-inline">
                        <select name="status">
                            <option value="">是否有效</option>
                            <option value="1" {eq name="where.status" value="1"}selected="selected"{/eq}>开启</option>
                            <option value="0" {eq name="where.status" value="0"}selected="selected"{/eq}>关闭</option>
                        </select>
                    </div>
                    <div class="layui-input-inline">
                        <input type="text" name="name" value="{$where.name}" placeholder="请输入模板名" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-input-inline">
                        <button type="submit" class="layui-btn layui-btn-normal layui-btn-sm"><i class="layui-icon layui-icon-search"></i>搜索</button>
                    </div>
                </div>
            </form>
            <div class="layui-btn-container">
                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="$eb.createModalFrame('添加模板消息','{:Url('create')}')"><i class="layui-icon layui-icon-add-1"></i>添加模板消息</button>
            </div>
            <blockquote class="layui-elem-quote">
                <div>主营行业：<span><?= isset($industry['primary_industry']) ? $industry['primary_industry']['first_class'].' | '.$industry['primary_industry']['second_class'] : '未选择' ?></span></div>
                <div>副营行业：<span><?= isset($industry['secondary_industry']) ? $industry['secondary_industry']['first_class'].' | '.$industry['secondary_industry']['second_class'] : '未选择' ?></span></div>
            </blockquote>
            <table class="layui-table">
                <thead>
                <tr>
                    <th>编号</th>
                    <th>模板编号</th>
                    <th style="width: 300px;">模板ID</th>
                    <th>模板名</th>
                    <th>回复内容</th>
                    <th>状态</th>
                    <th>添加时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                {volist name="list" id="vo"}
                <tr>
                    <td>{$vo.id}</td>
                    <td>{$vo.tempkey}</td>
                    <td>{$vo.tempid}</td>
                    <td>{$vo.name}</td>
                    <td><pre>{$vo.content}</pre></td>
                    <td><i class="layui-icon {eq name='vo.status' value='1'}layui-icon-ok text-navy{else/}layui-icon-close text-danger{/eq}" style="font-size: 20px;"></i></td>
                    <td>{$vo.add_time|date='Y-m-d H:i:s',###}</td>
                    <td>
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="$eb.createModalFrame('编辑','{:Url('edit',array('id'=>$vo['id']))}',{h:400})"><i class="layui-icon layui-icon-edit"></i>编辑</button>
                        <button type="button" class="layui-btn layui-btn-danger layui-btn-xs" data-url="{:Url('delete',array('id'=>$vo['id']))}"><i class="layui-icon layui-icon-delete"></i>删除</button>
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
    layui.form.render();
    $('.layui-btn-danger').on('click',function(){
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
        });
    });
    $(".open_image").on('click',function (e) {
        var image = $(this).data('image');
        $eb.openImage(image);
    })
</script>
{/block}
