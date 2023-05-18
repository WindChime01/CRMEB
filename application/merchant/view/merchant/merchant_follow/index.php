{extend name="public/container"}
{block name="head_top"}{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">用户关注</div>
        <div class="layui-card-body">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md12">
                    <div class="layui-btn-container">
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload()"  style="margin-left: 5px;"><i class="layui-icon">&#xe669;</i>刷新</button>
                    </div>
                </div>
                <div class="layui-col-md12">
                    <table class="layui-table">
                        <thead>
                        <tr>
                            <th>用户UID</th>
                            <th>用户昵称</th>
                            <th>用户头像</th>
                            <th>关注时间</th>
                        </tr>
                        </thead>
                        <tbody class="">
                        {volist name="list" id="vo"}
                        <tr>
                            <td>{$vo.uid}</td>
                            <td>{$vo.nickname}</td>
                            <td><img src="{$vo.avatar}" class="head_image" data-image="{$vo.avatar}" height="50"></td>
                            <td>{$vo.follow_time|date='Y-m-d H:i:s',###}</td>
                        </tr>
                        {/volist}
                        </tbody>
                    </table>
                </div>
                {include file="public/inner_page"}
            </div>
        </div>
    </div>
</div>
{/block}
