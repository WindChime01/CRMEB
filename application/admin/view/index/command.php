{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">Linux 下workerman命令详解</div>
        <div class="layui-card-body">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md12">
                    <div class="alert alert-info" role="alert" style="padding-left:15px; ">
                        <p>进程守护模式启动：php think workerman start  --d</p>
                        <p>进程守护模式停止：php think workerman stop</p>
                        <p>查看运行状态：php think workerman status</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-card">
        <div class="layui-card-header">守护进程命令[消息队列]</div>
        <div class="layui-card-body">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md12">
                    <div class="alert alert-info" role="alert" style="padding-left:15px; ">
                        <p id="biao1">守护进程启动命令：php think queue:listen --queue {$name}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{/block}
