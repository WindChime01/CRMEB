<!DOCTYPE html>
<html lang="zh-CN">
<head>
    {include file="public/head"}
    <title>{$title}</title>
    <script src="{__PLUG_PATH}jquery-1.10.2.min.js"></script>
    <script src="{__PLUG_PATH}reg-verify.js"></script>

</head>
<body>
<div id="form-add" class="mp-form" v-cloak>
    <i-Form :model="formData" :label-width="80">
        <Form-Item label="后台账号">
            <i-input v-model="formData.account" placeholder="请输入商户账号(账号前面自动添加总管理员的账号使用@隔开)" readonly></i-input>
        </Form-Item>
        <Form-Item label="管理员名称">
            <i-input v-model="formData.real_name" placeholder="请输入管理员名称"></i-input>
        </Form-Item>
        <Form-Item label="联系电话">
            <i-input v-model="formData.phone" placeholder="请输入联系电话"></i-input>
        </Form-Item>
        <Form-Item label="联系邮箱">
            <i-input v-model="formData.email" placeholder="请输入联系邮箱"></i-input>
        </Form-Item>
        <Form-Item label="状态">
            <Radio-Group v-model="formData.status">
                <Radio :label="1">开启</Radio>
                <Radio :label="0">关闭</Radio>
            </Radio-Group>
        </Form-Item>
        <Form-Item label="可用权限">
            <Tree :data="menus" show-checkbox ref="tree"></Tree>
        </Form-Item>
        <Form-Item :class="'add-submit-item'">
            <i-Button :type="'primary'" :html-type="'submit'" :size="'large'" :long="true" :loading="loading" @click.prevent="submit">提交</i-Button>
        </Form-Item>
    </i-Form>
</div>
<script>

    $eb = parent._mpApi;
    var role = <?php echo $roles; ?> || {};
    var menus = <?php echo $menus; ?> || [];
    mpFrame.start(function(Vue){
        new Vue({
            el:'#form-add',
            data:{
                formData:{
                    account:role.account || '',
                    real_name:role.real_name || '',
                    phone:role.phone || '',
                    email:role.email || '',
                    status:role.status || 0,
                    checked_menus:role.rules
                },
                menus:[],
                loading:false
            },
            methods:{
                tidyRes:function(){
                    var data = [];
                    menus.map((menu)=>{
                        data.push(this.initMenu(menu));
                    });
                    this.$set(this,'menus',data);
                },
                initMenu:function(menu){
                    var data = {},checkMenus = ','+this.formData.checked_menus+',';
                    data.title = menu.menu_name;
                    data.id = menu.id;
                    if(menu.child && menu.child.length >0){
                        data.children = [];
                        menu.child.map((child)=>{
                            data.children.push(this.initMenu(child));
                        })
                    }else{
                        data.checked = checkMenus.indexOf(String(','+data.id+',')) !== -1;
                        data.expand = !data.checked;
                    }
                    return data;
                },
                submit:function(){
                    this.formData.checked_menus = [];
                    this.$refs.tree.getCheckedNodes().map((node)=>{
                        this.formData.checked_menus.push(node.id);
                    });
                    if(this.formData.phone){
                        if(!$reg.isPhone(this.formData.phone)){
                            return $eb.message('error','请输入正确的手机号');
                        }}
                    if(this.formData.email){
                        if(!$reg.isEmail(this.formData.email)){
                            return $eb.message('error','请输入正确的邮箱');
                        }
                    }
                    this.loading = true;
                    $eb.axios.post("{$action}",this.formData).then((res)=>{
                        if(res.status && res.data.code == 200)
                            return Promise.resolve(res.data);
                        else
                            return Promise.reject(res.data.msg || '添加失败,请稍候再试!');
                    }).then((res)=>{
                        $eb.message('success',res.msg || '操作成功!');
                        $eb.closeModalFrame(window.name);
                    }).catch((err)=>{
                        this.loading=false;
                        $eb.message('error',err);
                    });
                }
            },
            mounted:function(){
                t = this;
                this.tidyRes();
            }
        });
    });
</script>
</body>
</html>
