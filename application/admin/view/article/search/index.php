{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card" id="app" v-cloak>
        <div class="layui-card-header">关键词搜索</div>
        <div class="layui-card-body">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md12">
                    <form class="layui-form">
                        <div class="layui-form-item">
                            <label class="layui-form-label">关键词：</label>
                            <div class="layui-input-inline">
                                <input type="text" name="name" autocomplete="off" v-model.trim="name" placeholder="请输入关键词" maxlength="10" class="layui-input">
                            </div>
                            <div class="layui-input-inline">
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm"  @click="add">确认添加</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="layui-col-md12">
                    <button type="button" class="layui-btn layui-btn-primary" v-for="(item,index) in searchList">
                        {{item.name}} <i class="layui-icon layui-icon-close" @click="del(item,index)"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name='script'}
<script>
    var list=<?=count($list) ? json_encode($list) : "[]"?>;
    require(['vue'],function(Vue) {
        new Vue({
            el: "#app",
            data: {
                searchList:list,
                name:'',
            },
            methods:{
                add:function () {
                    var that=this;
                    for (var i = 0; i < this.searchList.length; i++) {
                        if (this.name === this.searchList[i].name) {
                            return layui.layer.msg('请勿重复添加', {icon: 5});
                        }
                    }
                    layList.baseGet(layList.U({a:'save',q:{name:that.name}}),function (res) {
                        that.searchList.push(res.data);
                        that.$set(that,'searchList',that.searchList);
                        layList.msg(res.msg);
                        that.name='';
                    });
                },
                del:function (item,index) {
                    var that=this;
                    layList.baseGet(layList.U({a:'del_search',q:{id:item.id}}),function (res) {
                        that.searchList.splice(index,1);
                        that.$set(that,'searchList',that.searchList);
                        layList.msg(res.msg);
                    });
                }
            }
        })
    })
</script>
{/block}