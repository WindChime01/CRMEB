<!DOCTYPE html>
<html lang="zh-CN">
<head>
    {include file="public/head"}
    <title>{$title|default=''}</title>
</head>
<body>
<div id="form-add" class="mp-form" v-cloak>
    <form-builder></form-builder>
</div>
<script>
    var _vm ;
    mpFrame.start(function(Vue){
        require(['axios','system/util/mpFormBuilder'],function(axios,mpFormBuilder){
            Vue.use(mpFormBuilder,$eb,<?php echo $rules; ?>,{
                action:'{$action}'
            });
            new Vue({
                el:"#form-add",
                mounted:function(){
                }
            });
        });
    });
</script>
</body>
