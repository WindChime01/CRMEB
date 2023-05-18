<!--<script type="text/javascript" src="/static/plug/basket.js"></script>-->
<script type="text/javascript" src="{__ADMIN_PATH}/plug/requirejs/require.js"></script>
<?php /*  <script type="text/javascript" src="/static/plug/requirejs/require-basket-load.js"></script>  */ ?>
<script>
    requirejs.config({
        map: {
            '*': {
                'css': '{__PLUG_PATH}requirejs/css.min.js'
            }
        },
        shim:{
            'iview':{
                deps:['css!iviewcss']
            },
            'layer':{
                deps:['css!layercss']
            }
        },
        baseUrl:'//'+location.hostname+'{__PUBLIC_PATH}',
        paths: {
            'static':'static',
            'system':'system',
            'vue':'static/plug/vue/dist/vue.min',
            'axios':'static/plug/axios.min',
            'helper':'static/plug/helper',
            'store':'wap/first/crmeb/module/store',
            'reg-verify':"static/plug/reg-verify",
            'iview':'static/plug/iview/dist/iview.min',
            'iviewcss':'static/plug/iview/dist/styles/iview',
            'lodash':'static/plug/lodash',
            'layer':'static/plug/layer/layer',
            'layercss':'static/plug/layer/theme/default/layer',
            'jquery':'static/plug/jquery-1.10.2.min',
            'moment':'static/plug/moment',
            'sweetalert':'static/plug/sweetalert2/sweetalert2.all.min',
            'ZeroClipboard':'{__ADMIN_PATH}plug/ueditor/third-party/zeroclipboard/ZeroClipboard',
            'ueditor-config':'{__ADMIN_PATH}plug/ueditor/ueditor.config',
            'ueditor-all':'{__ADMIN_PATH}plug/ueditor/ueditor.all.min',
            'zh-cn':'{__ADMIN_PATH}plug/ueditor/lang/zh-cn/zh-cn',
            'aliyun-oss':'{__ADMIN_PATH}js/aliyun-oss-sdk-4.4.4.min',
            'request':'{__ADMIN_PATH}js/request',
            'plupload':'{__MODULE_PATH}widget/lib/plupload-2.1.2/js/plupload.full.min',
            'OssUpload':'{__MODULE_PATH}widget/OssUpload',
            'aliyun-upload-sdk':'{__ADMIN_PATH}plug/aliyun-upload-sdk/aliyun-upload-sdk-1.5.0.min.js',
            'es6-promise':'{__ADMIN_PATH}plug/aliyun-upload-sdk/lib/es6-promise.min.js',
            'aliyun-oss-sdk':'{__ADMIN_PATH}plug/aliyun-upload-sdk/lib/aliyun-oss-sdk-5.3.1.min.js'

        },
        basket: {
            excludes:['system/js/index','system/util/mpVueComponent','system/util/mpVuePackage']
        }
    });

    // require(['jquery'], function ($) {
        $(window).ready(function () {
            $('.upload-image-box').on({
                mouseover:function () {
                    $(this).find('.mask').show();
                },
                mouseout:function () {
                    $(this).find('.mask').hide();
                }
            })
            $('.open_image').on('click',function () {
                $eb.openImage($(this).data('url'));
            });
        })
    // });
</script>
<script type="text/javascript" src="{__ADMIN_PATH}util/mpFrame.js"></script>