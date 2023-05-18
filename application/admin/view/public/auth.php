<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport"content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no">
        <meta name="browsermode" content="application"/>
        <meta name="renderer" content="webkit">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <!-- 禁止百度转码 -->
        <meta http-equiv="Cache-Control" content="no-siteapp" />
        <!-- uc强制竖屏 -->
        <meta name="screen-orientation" content="portrait">
        <!-- QQ强制竖屏 -->
        <meta name="x5-orientation" content="portrait">
        <link rel="stylesheet" type="text/css" href="{__MODULE_PATH}error/css/reset-2.0.css" />
        <link rel="stylesheet" type="text/css" href="{__MODULE_PATH}error/css/style.css?12" />
        <script src="{__FRAME_PATH}js/jquery.min.js"></script>
    </head>
    <body>
        <div class="link-wrapper">
            <div class="failure">
                <img src="{__MODULE_PATH}error/images/failure-icon.png" />
                <div class="text">
                    <p class="status">{$msg}</p>
                    <p class="failure-btn">
                        <a class="refresh" href="javascript:location.reload();">刷新试试</a>
                        <a class="close" href="javascript:void(0);">关闭</a>
                    </p>
                </div>
            </div>
        </div>
        <script>
            $(document).ready(function(){
                $('.back').on('click',function(){
                    parent.layer.close(parent.layer.getFrameIndex(window.name));parent.$(".J_iframe:visible")[0].contentWindow.location.reload();
                });
            });
        </script>
    </body>
</html>
