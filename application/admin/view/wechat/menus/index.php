{extend name="public/container"}
{block name="head_top"}
<link rel="stylesheet" type="text/css" href="{__ADMIN_PATH}css/main.css" />
<link href="{__FRAME_PATH}css/plugins/iCheck/custom.css" rel="stylesheet">
{/block}
{block name="content"}
<div v-cloak id="app" class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header"></div>
        <div class="layui-card-body">
            <div class="wechat-reply-wrapper wechat-menu">
                <div class="layui-row layui-col-space10 ibox-content clearfix">
                    <div class="layui-col-md4 view-wrapper">
                        <div class="mobile-header">公众号</div>
                        <section class="view-body">
                            <div class="time-wrapper"><span class="time">9:36</span></div>
                        </section>
                        <div class="menu-footer">
                            <ul class="flex">
                                <li v-for="(menu, index) in menus" :class="{active:menu === checkedMenu}">
                                    <span @click="activeMenu(menu,index,null)"><i class="icon-sub"></i>{{ menu.name || '一级菜单' }}</span>
                                    <div class="sub-menu">
                                        <ul>
                                            <li v-for="(child, cindex) in menu.sub_button" :class="{active:child === checkedMenu}">
                                                <span @click="activeMenu(child,cindex,index)">{{ child.name || '二级菜单' }}</span>
                                            </li>
                                            <li v-if="menu.sub_button.length < 5" @click="addChild(menu,index)"><i class="icon-add"></i></li>
                                        </ul>
                                    </div>
                                </li>
                                <li v-if="menus.length < 3" @click="addMenu()"><i class="icon-add"></i></li>
                            </ul>
                        </div>
                    </div>
                    <div class="layui-col-md8 control-wrapper menu-control" v-show="checkedMenuId !== null">
                        <section>
                            <div class="control-main">
                                <h3 class="popover-title">菜单名称 <a class="fr" href="javascript:void(0);" @click="delMenu">删除</a></h3>
                                <p class="tips-txt">已添加子菜单，仅可设置菜单名称。</p>
                                <div class="menu-content control-body">
                                    <form action="" class="layui-form">
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">菜单名称</label>
                                            <div class="layui-input-block">
                                                <input type="text" placeholder="菜单名称" maxlength="13" class="layui-input" v-model="checkedMenu.name">
                                                <span class="layui-form-mid layui-word-aux">字数不超过13个汉字</span>
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">规则状态</label>
                                            <div class="layui-input-block">
                                                <select lay-filter="type">
                                                    <option value="click">关键字</option>
                                                    <option value="view">跳转网页</option>
                                                    <!-- <option value="miniprogram">小程序</option> -->
                                                </select>
                                            </div>
                                        </div>
                                        <div v-show="checkedMenu.type === 'click'" class="layui-form-item">
                                            <label class="layui-form-label">关键字</label>
                                            <div class="layui-input-block">
                                                <input type="text" placeholder="请输入关键字" class="layui-input" v-model="checkedMenu.key">
                                            </div>
                                        </div>
                                        <div v-show="checkedMenu.type === 'view'" class="layui-form-item">
                                            <label class="layui-form-label">跳转地址</label>
                                            <div class="layui-input-block">
                                                <input type="text" v-model="checkedMenu.url" placeholder="请输入跳转地址" class="layui-input">
                                                <div class="well well-lg">
                                                    <span class="help-block m-b-none">首页：{$Request.domain}{:url('wap/index/index')}</span>
                                                    <span class="help-block m-b-none">个人中心：{$Request.domain}{:url('wap/my/index')}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-show="checkedMenu.type === 'miniprogram'" class="layui-form-item">
                                            <label class="layui-form-label">appid</label>
                                            <div class="layui-input-block">
                                                <input class="layui-input" v-model="checkedMenu.appid" type="text" />
                                            </div>
                                        </div>
                                        <div v-show="checkedMenu.type === 'miniprogram'" class="layui-form-item">
                                            <label class="layui-form-label">备用网页url</label>
                                            <div class="layui-input-block">
                                                <input class="layui-input" v-model="checkedMenu.pagepath" type="text" />
                                            </div>
                                        </div>
                                        <div v-show="checkedMenu.type === 'miniprogram'" class="layui-form-item">
                                            <label class="layui-form-label">小程序路径</label>
                                            <div class="layui-input-block">
                                                <input class="layui-input" v-model="checkedMenu.url" type="text" />
                                            </div>
                                        </div>
                                        <!-- <div class="layui-form-item">
                                            <label class="layui-form-label">回复内容</label>
                                            <div class="layui-input-block">
                                                <textarea class="layui-textarea"></textarea>
                                            </div>
                                        </div> -->
                                        <div class="layui-form-item">
                                            <div class="layui-input-block">
                                                <button class="layui-btn layui-btn-normal" @click="submit">保存发布</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{/block}
{block name="script"}
<script src="{__FRAME_PATH}js/plugins/iCheck/icheck.min.js"></script>
<script src="{__FRAME_PATH}js/bootstrap.min.js"></script>
<script src="{__FRAME_PATH}js/content.min.js"></script>
<script src="{__PLUG_PATH}reg-verify.js"></script>
<script type="text/javascript">
    parent.$('.J_menuTab').each(function () {
        if ($(this).hasClass('active')) {
            $('.layui-card-header').text($(this).text());
            return false;
        }
    });
    $eb = parent._mpApi;
    $eb.mpFrame.start(function(Vue){
        var $http = $eb.axios;
        const vm = new Vue({
            data:{
                menus:<?=$menus?>,
                checkedMenu:{
                    type:'click',
                    name:''
                },
                checkedMenuId:null,
                parentMenuId:null
            },
            methods:{
                defaultMenusData:function(){
                    return {
                        type:'click',
                        name:'',
                        sub_button:[]
                    };
                },
                defaultChildData:function(){
                    return {
                        type:'click',
                        name:''
                    };
                },
                addMenu:function(){
                    if(!this.check()) return false;
                    var data = this.defaultMenusData(),id = this.menus.length;
                    this.menus.push(data);
                    this.checkedMenu = data;
                    this.checkedMenuId = id;
                    this.parentMenuId = null;
                },
                addChild:function(menu,index){
                    if(!this.check()) return false;
                    var data = this.defaultChildData(),id = menu.sub_button.length;
                    menu.sub_button.push(data);
                    this.checkedMenu = data;
                    this.checkedMenuId = id;
                    this.parentMenuId = index;
                },
                delMenu:function(){
                    this.parentMenuId === null ?
                        this.menus.splice(this.checkedMenuId,1) : this.menus[this.parentMenuId].sub_button.splice(this.checkedMenuId,1);
                    this.parentMenuId = null;
                    this.checkedMenu = {};
                    this.checkedMenuId = null;
                },
                activeMenu:function(menu,index,pid){
                    if(!this.check()) return false;
                    pid === null ?
                        (this.checkedMenu = menu) : (this.checkedMenu = this.menus[pid].sub_button[index],this.parentMenuId = pid);
                    this.checkedMenuId=index
                },
                check:function(){
                    if(this.checkedMenuId === null) return true;
                    if(!this.checkedMenu.name){
                        $eb.message('请输入按钮名称!');
                        return false;
                    }
                    if(this.checkedMenu.type == 'click' && !this.checkedMenu.key){
                        $eb.message('请输入关键字!');
                        return false;
                    }
                    if(this.checkedMenu.type == 'view' && !$reg.isHref(this.checkedMenu.url)){
                        $eb.message('请输入正确的跳转地址!');
                        return false;
                    }
                    if(this.checkedMenu.type == 'miniprogram'
                        && (!this.checkedMenu.appid
                            || !this.checkedMenu.pagepath
                            || !this.checkedMenu.url)){
                        $eb.message('请填写完整小程序配置!');
                        return false;
                    }
                    return true;
                },
                submit:function(){
                    if(!this.check()) return false;
                    if(!this.menus.length){
                        $eb.message('error','请添加菜单!');
                        return false;
                    }
                    $http.post("{:url('wechat.menus/save',array('dis'=>1))}",{button:this.menus}).then(function (res) {
                        if(res.status == 200 && res.data.code == 200)
                            $eb.message('success','发布菜单成功!');
                        else
                            return Promise.reject(res.data.msg || '发布菜单失败!');
                    }).catch(function(err){
                        $eb.message('error',err);
                    })
                }
            },
            mounted:function(){
                window.vm = this;
                this.$nextTick(function () {
                    layui.form.render();
                    layui.form.on('select(type)', function (data) {
                        vm.checkedMenu.type = data.value;
                    });
                });
            }
        });
        vm.$mount(document.getElementById('app'));
    });
    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });
</script>
{/block}
