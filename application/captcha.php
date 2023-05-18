<?php
declare(strict_types=1);
/**
 * 请将该文件放置于config目录
 */
return [
    'font_file' => '', //自定义字体包路径， 不填使用默认值
    //文字验证码
    'click_world' => [
        'backgrounds' => []
    ],
    //滑动验证码
    'block_puzzle' => [
        'backgrounds' => [
            'system/img/yanzheng1.jpg',
            'system/img/yanzheng2.jpg',
            'system/img/yanzheng3.jpg',
            'system/img/yanzheng4.jpg',
            'system/img/yanzheng5.jpg',
            'system/img/yanzheng6.jpg'
        ], //背景图片路径， 不填使用默认值
        'templates' => [], //模板图
        'offset' => 10, //容错偏移量
    ],
    //水印
    'watermark' => [
        'fontsize' => 12,
        'color' => '#000000',
        'text' => 'ZSFF'
    ],
    'cache' => [
        'constructor' => \think\Cache::class
    ]
];
