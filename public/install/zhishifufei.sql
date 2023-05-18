--
-- 表的结构 `eb_article`
--
CREATE TABLE `eb_article` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '文章管理ID',
  `cid` varchar(255) DEFAULT '1' COMMENT '分类id',
  `title` varchar(255) DEFAULT NULL COMMENT '文章标题',
  `author` varchar(255) DEFAULT NULL COMMENT '文章作者',
  `image_input` varchar(255) DEFAULT NULL COMMENT '文章图片',
  `synopsis` varchar(255) DEFAULT NULL COMMENT '文章简介',
  `label` varchar(2000) DEFAULT '[]' COMMENT '标签',
  `share_title` varchar(255) DEFAULT NULL COMMENT '文章分享标题',
  `share_synopsis` varchar(255) DEFAULT NULL COMMENT '文章分享简介',
  `visit` int(25) DEFAULT '0' COMMENT '浏览次数',
  `sort` int(10) UNSIGNED DEFAULT '0' COMMENT '排序',
  `url` varchar(255) DEFAULT NULL COMMENT '原文链接',
  `status` tinyint(1) UNSIGNED DEFAULT NULL COMMENT '状态',
  `add_time` int(12) DEFAULT '0' COMMENT '添加时间',
  `hide` tinyint(1) UNSIGNED DEFAULT '0' COMMENT '是否隐藏',
  `admin_id` int(10) UNSIGNED DEFAULT '0' COMMENT '管理员id',
  `mer_id` int(10) UNSIGNED DEFAULT '0' COMMENT '讲师id',
  `is_hot` tinyint(1) UNSIGNED DEFAULT '0' COMMENT '是否热门(小程序)',
  `is_banner` tinyint(1) UNSIGNED DEFAULT '0' COMMENT '是否轮播图(小程序)',
  `consult_image` varchar(255) DEFAULT NULL COMMENT '活动咨询图片',
  `consult_type` tinyint(1) UNSIGNED DEFAULT '0' COMMENT '活动咨询类型 1 图片 2视频',
  `is_consult` tinyint(1) UNSIGNED DEFAULT '0' COMMENT '是否活动咨询',
  `is_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否显示'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章管理表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_article_category`
--
CREATE TABLE `eb_article_category` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '文章分类id',
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '父级ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '文章分类标题',
  `intr` varchar(255) DEFAULT NULL COMMENT '文章分类简介',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '文章分类图片',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '状态',
  `sort` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '排序',
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1删除0未删除',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `hidden` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否隐藏'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章分类表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_article_content`
--
CREATE TABLE `eb_article_content` (
  `nid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '文章id',
  `content` longtext COMMENT '文章内容'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章内容表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_cache`
--
CREATE TABLE `eb_cache` (
  `key` varchar(32) NOT NULL,
  `result` text COMMENT '缓存数据',
  `add_time` int(12) DEFAULT '0' COMMENT '缓存时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='记录缓存';

-- --------------------------------------------------------

--
-- 表的结构 `eb_certificate`
--
CREATE TABLE `eb_certificate` (
  `id` int(10) UNSIGNED NOT NULL,
  `mer_id` int(12) DEFAULT '0' COMMENT '讲师id',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '证书名称',
  `background` varchar(255) NOT NULL DEFAULT '' COMMENT '证书背景',
  `qr_code` varchar(255) DEFAULT '' COMMENT '引流二维码',
  `obtain` tinyint(1) NOT NULL DEFAULT '0' COMMENT '获取方式1=课程2=考试',
  `number` int(5) DEFAULT '0' COMMENT '获得人数',
  `explain` varchar(32) NOT NULL DEFAULT '' COMMENT '说明',
  `related` varchar(255) DEFAULT NULL COMMENT '关联课程或考试',
  `sort` int(5) DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 -1:未通过 0:待审核 1:审核通过',
  `success_time` int(12) NOT NULL DEFAULT '0' COMMENT '通过时间',
  `fail_message` varchar(256) NOT NULL DEFAULT '' COMMENT '未通过原因',
  `fail_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '未通过时间',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='证书列表';

--
-- 表的结构 `eb_certificate_record`
--
CREATE TABLE `eb_certificate_record` (
  `id` int(10) UNSIGNED NOT NULL,
  `cid` int(10) NOT NULL DEFAULT '0' COMMENT '证书ID',
  `uid` int(10) NOT NULL COMMENT '用户uid',
  `nickname` varchar(255) NOT NULL COMMENT '昵称',
  `source_id` int(10) NOT NULL DEFAULT '0' COMMENT '来源ID（试卷ID/课程ID）',
  `obtain` tinyint(1) NOT NULL DEFAULT '0' COMMENT '获取方式1=课程2=考试',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态1=已获得0=已撤销',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '是否删除',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '获得时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='证书获取记录';

--
-- 表的结构 `eb_certificate_related`
--
CREATE TABLE `eb_certificate_related` (
  `id` int(10) UNSIGNED NOT NULL,
  `cid` int(10) NOT NULL DEFAULT '0' COMMENT '证书ID',
  `obtain` tinyint(1) NOT NULL DEFAULT '0' COMMENT '获取方式1=课程2=考试',
  `related` int(12) NOT NULL DEFAULT '0' COMMENT '试卷ID/专题ID',
  `condition` varchar(255) NOT NULL DEFAULT '' COMMENT '获得证书条件',
  `is_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否使用'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='证书关联记录';

--
-- 表的结构 `eb_classes`
--
CREATE TABLE `eb_classes` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '班级标题',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '班级状态 1=开始 2=结束',
  `upper_limit` int(5) NOT NULL DEFAULT '0' COMMENT '班级人数上限',
  `teacher_id` varchar(255) NOT NULL DEFAULT '' COMMENT '班级老师',
  `relation` varchar(255) DEFAULT NULL COMMENT '关联专题',
  `start_time` int(12) DEFAULT '0' COMMENT '开班时间',
  `end_time` int(12) DEFAULT '0' COMMENT '结班时间',
  `sort` int(5) NOT NULL DEFAULT '0' COMMENT '排序',
  `is_del` int(2) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='班级管理表';

--
-- 表的结构 `eb_contact_phone`
--
CREATE TABLE `eb_contact_phone` (
  `id` int(10) UNSIGNED NOT NULL,
  `sid` int(10) NOT NULL DEFAULT '0' COMMENT '学员ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标示',
  `phone` varchar(12) NOT NULL COMMENT '手机号'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学员联系方式';

--
-- 转存表中的数据 `eb_data_download`
--
CREATE TABLE `eb_data_download` (
  `id` int(12) NOT NULL,
  `mer_id` int(12) DEFAULT '0' COMMENT '讲师id',
  `cate_id` int(10) NOT NULL DEFAULT '0' COMMENT '分类ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '封面图',
  `poster_image` varchar(255) DEFAULT NULL COMMENT '海报',
  `pay_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支付类型',
  `money` decimal(8,2) DEFAULT '0.00' COMMENT '金额',
  `member_pay_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '会员支付类型0：免费，1付费',
  `member_money` decimal(8,2) DEFAULT '0.00' COMMENT '会员价格',
  `abstract` text COMMENT '简介',
  `link` varchar(255) DEFAULT NULL COMMENT '文件链接',
  `network_disk_link` varchar(255) DEFAULT NULL COMMENT '百度网盘链接',
  `network_disk_pwd` varchar(255) DEFAULT NULL COMMENT '下载密码',
  `type` tinyint(1) DEFAULT '0' COMMENT '上传方式:0=全部,1=OSS上传,2=百度网盘',
  `sort` int(5) DEFAULT '0' COMMENT '排序',
  `ficti` int(5) DEFAULT '0' COMMENT '虚拟下载量',
  `sales` int(10) DEFAULT '0' COMMENT '下载量',
  `is_show` tinyint(1) DEFAULT '0' COMMENT '是否显示',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 -1:未通过 0:待审核 1:审核通过',
  `success_time` int(12) NOT NULL DEFAULT '0' COMMENT '通过时间',
  `fail_message` varchar(256) NOT NULL DEFAULT '' COMMENT '未通过原因',
  `fail_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '未通过时间',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '是否删除',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COMMENT='资料表';

--
-- 转存表中的数据 `eb_data_download_buy`
--
CREATE TABLE `eb_data_download_buy` (
  `id` int(11) NOT NULL,
  `order_id` varchar(32) NOT NULL DEFAULT '' COMMENT '订单id',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `data_id` int(11) NOT NULL DEFAULT '0' COMMENT '资料id',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '获得方式:0=支付获得,1=领取,2=赠送',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '是否删除',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='资料获得记录';

--
-- 转存表中的数据 `eb_data_download_categpry`
--
CREATE TABLE `eb_data_download_categpry` (
  `id` int(10) NOT NULL,
  `pid` int(10) NOT NULL DEFAULT '0' COMMENT '父级ID',
  `title` varchar(255) NOT NULL COMMENT '分类名称',
  `sort` int(5) DEFAULT NULL COMMENT '排序',
  `is_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '显示/隐藏',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `add_time` int(12) NOT NULL COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='资料分类表';

--
-- 转存表中的数据 `eb_data_download_categpry`
--

INSERT INTO `eb_data_download_categpry` (`id`, `pid`, `title`, `sort`, `is_show`, `is_del`, `add_time`) VALUES
(1, 0, '知识付费', 0, 1, 0, 1627370435),
(2, 1, '开源下载', 0, 1, 0, 1627370457),
(3, 0, '音乐启蒙', 0, 1, 0, 1627372514),
(4, 3, '儿童音乐', 0, 1, 0, 1627372527);

--
-- 转存表中的数据 `eb_data_download_order`
--
CREATE TABLE `eb_data_download_order` (
  `id` int(12) NOT NULL,
  `trade_no` varchar(64) DEFAULT NULL COMMENT '支付宝支付,支付宝交易订单号',
  `order_id` varchar(64) NOT NULL DEFAULT '' COMMENT '订单号',
  `mer_id` int(12) DEFAULT '0' COMMENT '讲师id',
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT 'uid',
  `data_id` int(10) NOT NULL DEFAULT '0' COMMENT '资料ID',
  `total_num` int(5) NOT NULL DEFAULT '0' COMMENT '数量',
  `total_price` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `pay_price` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '实际支付金额',
  `paid` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支付状态',
  `pay_time` int(12) DEFAULT '0' COMMENT '支付时间',
  `pay_type` varchar(12) DEFAULT NULL COMMENT '支付方式',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态0:正常 -1:申请退款 -2:已退款',
  `refund_status` tinyint(1) DEFAULT '0' COMMENT '0 未退款 1 申请中 2 已退款',
  `refund_reason_time` int(12) DEFAULT '0' COMMENT '退款时间',
  `refund_price` decimal(8,2) DEFAULT '0.00' COMMENT '退款金额',
  `unique` char(32) NOT NULL DEFAULT '' COMMENT '唯一id(md5加密)类似id',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `is_system_del` tinyint(1) DEFAULT '0' COMMENT '后台是否删除',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='资料支付订单表';

--
-- 转存表中的数据 `eb_data_download_records`
--
CREATE TABLE `eb_data_download_records` (
  `id` int(12) NOT NULL,
  `uid` int(12) NOT NULL DEFAULT '0' COMMENT 'uid',
  `data_id` int(10) NOT NULL DEFAULT '0' COMMENT '资料id',
  `number` int(10) DEFAULT '0' COMMENT '下载次数',
  `update_time` int(12) DEFAULT '0' COMMENT '最新下载时间',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='资料下载记录';

--
-- 转存表中的数据 `eb_event_data`
--
CREATE TABLE `eb_event_data` (
  `id` int(10) UNSIGNED NOT NULL,
  `event_id` int(10) DEFAULT '0' COMMENT '活动id',
  `event_name` varchar(255) DEFAULT '' COMMENT '字段名称',
  `event_type` tinyint(1) DEFAULT '0' COMMENT '字段类型 1=文本框2=单选框3=多选框4=手机号5=下拉框',
  `event_value` varchar(255) DEFAULT '' COMMENT '字段内容',
  `sort` int(5) DEFAULT '0' COMMENT '排序',
  `is_required` tinyint(4) DEFAULT '0' COMMENT '是否必填'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='活动资料表单';

--
-- 转存表中的数据 `eb_event_price`
--
CREATE TABLE `eb_event_price` (
  `id` int(10) unsigned NOT NULL,
  `event_id` int(10) DEFAULT '0' COMMENT '活动id',
  `event_number` int(5) DEFAULT '0' COMMENT '活动人数',
  `event_price` decimal(10,2) DEFAULT '0.00' COMMENT '价格',
  `event_mer_price` decimal(10,2) DEFAULT '0.00' COMMENT '会员价格',
  `sort` int(5) DEFAULT '0' COMMENT '排序'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='活动人数及对应价格表';

--
-- 表的结构 `eb_event_registration`
--
CREATE TABLE `eb_event_registration` (
  `id` int(10) UNSIGNED NOT NULL,
  `mer_id` int(12) DEFAULT '0' COMMENT '讲师id',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `phone` varchar(12) DEFAULT '' COMMENT '电话',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '封面图',
  `signup_start_time` int(12) NOT NULL DEFAULT '0' COMMENT '报名开始时间',
  `signup_end_time` int(12) NOT NULL DEFAULT '0' COMMENT '报名结束时间',
  `start_time` int(12) NOT NULL DEFAULT '0' COMMENT '活动开始时间',
  `end_time` int(12) NOT NULL DEFAULT '0' COMMENT '活动结束时间',
  `province` varchar(255) NOT NULL DEFAULT '' COMMENT '省',
  `city` varchar(255) NOT NULL DEFAULT '' COMMENT '市',
  `district` varchar(255) NOT NULL DEFAULT '' COMMENT '区',
  `detail` varchar(255) NOT NULL DEFAULT '' COMMENT '详细地址',
  `latitude` varchar(255) DEFAULT '' COMMENT '纬度',
  `longitude` varchar(255) DEFAULT '' COMMENT '经度',
  `number` int(10) NOT NULL DEFAULT '0' COMMENT '人数',
  `sort` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  `activity_rules` longtext COMMENT '活动规则',
  `content` longtext COMMENT '活动详情',
  `pay_type` int(2) NOT NULL DEFAULT '0' COMMENT '是否免费',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '价格',
  `member_pay_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '会员支付状态（0:免费1:付费）',
  `member_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '会员价格',
  `write_off_code` varchar(255) DEFAULT '' COMMENT '核销码',
  `restrictions` int(5) NOT NULL DEFAULT '0' COMMENT '限购',
  `is_fill` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否填写资料',
  `qrcode_img` varchar(255) DEFAULT NULL COMMENT '群聊二维码',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示',
  `statu` tinyint(1) NOT NULL DEFAULT '0' COMMENT '活动状态 0:报名尚未开始 1:报名开始 2:报名结束 活动尚未开始 3:活动中 4:活动结束',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 -1:未通过 0:待审核 1:审核通过',
  `success_time` int(12) DEFAULT '0' COMMENT '通过时间',
  `fail_message` varchar(256) DEFAULT '' COMMENT '未通过原因',
  `fail_time` int(12) DEFAULT '0' COMMENT '未通过时间',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='活动报名表';

--
-- 表的结构 `eb_event_sign_up`
--
CREATE TABLE `eb_event_sign_up` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` varchar(64) NOT NULL DEFAULT '' COMMENT '订单号',
  `trade_no` varchar(64) NOT NULL DEFAULT '' COMMENT '支付宝支付,支付宝交易订单号',
  `mer_id` int(12) DEFAULT '0' COMMENT '讲师id',
  `uid` int(10) NOT NULL DEFAULT '0',
  `user_info` text COMMENT '报名信息',
  `activity_id` int(10) NOT NULL DEFAULT '0' COMMENT '活动ID',
  `number` int(5) DEFAULT '0' COMMENT '报名人数',
  `pay_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '实际支付价格',
  `paid` int(2) NOT NULL DEFAULT '0' COMMENT '支付状态',
  `pay_time` int(12) DEFAULT '0' COMMENT '支付时间',
  `pay_type` varchar(20) NOT NULL DEFAULT '' COMMENT '支付方式',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态（0:未核销1:已核销）',
  `write_off_code` varchar(255) DEFAULT NULL COMMENT '核销二维码',
  `code` int(8) DEFAULT '0' COMMENT '核销码',
  `refund_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '退款金额',
  `refund_status` tinyint(1) DEFAULT '0' COMMENT '0 未退款 1 申请中 2 已退款',
  `refund_reason_time` int(12) DEFAULT '0' COMMENT '退款时间',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户报名表';

--
-- 表的结构 `eb_event_write_off_user`
--
CREATE TABLE `eb_event_write_off_user` (
  `id` int(10) UNSIGNED NOT NULL,
  `event_id` int(10) DEFAULT '0' COMMENT '活动id',
  `uid` int(12) DEFAULT '0' COMMENT '核销用户',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '是否删除',
  `add_time` int(12) DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='活动核销员表';

--
-- 表的结构 `eb_examination_record`
--
CREATE TABLE `eb_examination_record` (
  `id` int(10) UNSIGNED NOT NULL,
  `test_id` int(10) NOT NULL DEFAULT '0' COMMENT '试卷ID',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '区别1=练习2=试卷',
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT 'uid',
  `yes_questions` int(10) DEFAULT '0' COMMENT '对题数量',
  `wrong_question` int(10) DEFAULT '0' COMMENT '错题数量',
  `is_submit` tinyint(1) DEFAULT '0' COMMENT '是否提交',
  `score` int(10) DEFAULT '0' COMMENT '得分',
  `accuracy` varchar(10) DEFAULT NULL COMMENT '正确率',
  `grade` varchar(10) DEFAULT NULL COMMENT '等级',
  `duration` varchar(255) DEFAULT NULL COMMENT '考试时长',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `start_time` int(12) DEFAULT '0' COMMENT '考试开始时间',
  `end_time` int(12) DEFAULT '0' COMMENT '考试结束时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户考试记录';

--
-- 表的结构 `eb_examination_test_record`
--
CREATE TABLE `eb_examination_test_record` (
  `id` int(10) UNSIGNED NOT NULL,
  `e_id` int(10) NOT NULL DEFAULT '0' COMMENT '考试记录ID',
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT 'uid',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '区别1=练习2考试',
  `questions_id` int(10) NOT NULL DEFAULT '0' COMMENT '试题ID',
  `user_answer` varchar(255) NOT NULL DEFAULT '' COMMENT '用户答案',
  `answer` varchar(255) NOT NULL DEFAULT '' COMMENT '正确答案',
  `is_correct` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=未答 1=答错 2=正确',
  `score` int(5) DEFAULT '0' COMMENT '得分'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='考试试题记录';

--
-- 表的结构 `eb_examination_wrong_bank`
--
CREATE TABLE `eb_examination_wrong_bank` (
  `id` int(10) UNSIGNED NOT NULL,
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT 'uid',
  `test_id` int(10) NOT NULL DEFAULT '0' COMMENT '试卷ID',
  `questions_id` int(10) NOT NULL DEFAULT '0' COMMENT '试题ID',
  `user_answer` varchar(255) DEFAULT NULL COMMENT '用户答案',
  `answer` varchar(255) DEFAULT NULL COMMENT '正确答案',
  `is_master` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否掌握',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '是否删除',
  `add_time` int(12) DEFAULT '0' COMMENT '收录时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='错误题库';

--
-- 表的结构 `eb_express`
--
CREATE TABLE `eb_express` (
  `id` mediumint(11) UNSIGNED NOT NULL COMMENT '快递公司id',
  `code` varchar(50) NOT NULL DEFAULT '' COMMENT '快递公司简称',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '快递公司全称',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='快递公司表';

--
-- 转存表中的数据 `eb_express`
--
INSERT INTO `eb_express` (`id`, `code`, `name`, `sort`, `is_show`) VALUES
(1, 'LIMINWL', '利民物流', 1, 1),
(2, 'XINTIAN', '鑫天顺物流', 1, 1),
(3, 'henglu', '恒路物流', 1, 1),
(4, 'klwl', '康力物流', 1, 1),
(5, 'meiguo', '美国快递', 1, 1),
(6, 'a2u', 'A2U速递', 1, 1),
(7, 'benteng', '奔腾物流', 1, 1),
(8, 'ahdf', '德方物流', 1, 1),
(9, 'timedg', '万家通', 1, 1),
(10, 'ztong', '智通物流', 1, 1),
(11, 'xindan', '新蛋物流', 1, 1),
(12, 'bgpyghx', '挂号信', 1, 1),
(13, 'XFHONG', '鑫飞鸿物流快递', 1, 1),
(14, 'ALP', '阿里物流', 1, 1),
(15, 'BFWL', '滨发物流', 1, 1),
(16, 'SJWL', '宋军物流', 1, 1),
(17, 'SHUNFAWL', '顺发物流', 1, 1),
(18, 'TIANHEWL', '天河物流', 1, 1),
(19, 'YBWL', '邮联物流', 1, 1),
(20, 'SWHY', '盛旺货运', 1, 1),
(21, 'TSWL', '汤氏物流', 1, 1),
(22, 'YUANYUANWL', '圆圆物流', 1, 1),
(23, 'BALIANGWL', '八梁物流', 1, 1),
(24, 'ZGWL', '振刚物流', 1, 1),
(25, 'JIAYU', '佳宇物流', 1, 1),
(26, 'SHHX', '昊昕物流', 1, 1),
(27, 'ande', '安得物流', 1, 1),
(28, 'ppbyb', '贝邮宝', 1, 1),
(29, 'dida', '递达快递', 1, 1),
(30, 'jppost', '日本邮政', 1, 1),
(31, 'intmail', '中国邮政', 96, 1),
(32, 'HENGCHENGWL', '恒诚物流', 1, 1),
(33, 'HENGFENGWL', '恒丰物流', 1, 1),
(34, 'gdems', '广东ems快递', 1, 1),
(35, 'xlyt', '祥龙运通', 1, 1),
(36, 'gjbg', '国际包裹', 1, 1),
(37, 'uex', 'UEX', 1, 1),
(38, 'singpost', '新加坡邮政', 1, 1),
(39, 'guangdongyouzhengwuliu', '广东邮政', 1, 1),
(40, 'bht', 'BHT', 1, 1),
(41, 'cces', 'CCES快递', 1, 1),
(42, 'cloudexpress', 'CE易欧通国际速递', 1, 1),
(43, 'dasu', '达速物流', 1, 1),
(44, 'pfcexpress', '皇家物流', 1, 1),
(45, 'hjs', '猴急送', 1, 1),
(46, 'huilian', '辉联物流', 1, 1),
(47, 'huanqiu', '环球速运', 1, 1),
(48, 'huada', '华达快运', 1, 1),
(49, 'htwd', '华通务达物流', 1, 1),
(50, 'hipito', '海派通', 1, 1),
(51, 'hqtd', '环球通达', 1, 1),
(52, 'airgtc', '航空快递', 1, 1),
(53, 'haoyoukuai', '好又快物流', 1, 1),
(54, 'hanrun', '韩润物流', 1, 1),
(55, 'ccd', '河南次晨达', 1, 1),
(56, 'hfwuxi', '和丰同城', 1, 1),
(57, 'Sky', '荷兰', 1, 1),
(58, 'hongxun', '鸿讯物流', 1, 1),
(59, 'hongjie', '宏捷国际物流', 1, 1),
(60, 'httx56', '汇通天下物流', 1, 1),
(61, 'lqht', '恒通快递', 1, 1),
(62, 'jinguangsudikuaijian', '京广速递快件', 1, 1),
(63, 'junfengguoji', '骏丰国际速递', 1, 1),
(64, 'jiajiatong56', '佳家通', 1, 1),
(65, 'jrypex', '吉日优派', 1, 1),
(66, 'jinchengwuliu', '锦程国际物流', 1, 1),
(67, 'jgwl', '景光物流', 1, 1),
(68, 'pzhjst', '急顺通', 1, 1),
(69, 'ruexp', '捷网俄全通', 1, 1),
(70, 'jmjss', '金马甲', 1, 1),
(71, 'lanhu', '蓝弧快递', 1, 1),
(72, 'ltexp', '乐天速递', 1, 1),
(73, 'lutong', '鲁通快运', 1, 1),
(74, 'ledii', '乐递供应链', 1, 1),
(75, 'lundao', '论道国际物流', 1, 1),
(76, 'mailikuaidi', '麦力快递', 1, 1),
(77, 'mchy', '木春货运', 1, 1),
(78, 'meiquick', '美快国际物流', 1, 1),
(79, 'valueway', '美通快递', 1, 1),
(80, 'nuoyaao', '偌亚奥国际', 1, 1),
(81, 'euasia', '欧亚专线', 1, 1),
(82, 'pca', '澳大利亚PCA快递', 1, 1),
(83, 'pingandatengfei', '平安达腾飞', 1, 1),
(84, 'pjbest', '品骏快递', 1, 1),
(85, 'qbexpress', '秦邦快运', 1, 1),
(86, 'quanxintong', '全信通快递', 1, 1),
(87, 'quansutong', '全速通国际快递', 1, 1),
(88, 'qinyuan', '秦远物流', 1, 1),
(89, 'qichen', '启辰国际物流', 1, 1),
(90, 'quansu', '全速快运', 1, 1),
(91, 'qzx56', '全之鑫物流', 1, 1),
(92, 'qskdyxgs', '千顺快递', 1, 1),
(93, 'runhengfeng', '全时速运', 1, 1),
(94, 'rytsd', '日益通速递', 1, 1),
(95, 'ruidaex', '瑞达国际速递', 1, 1),
(96, 'shiyun', '世运快递', 1, 1),
(97, 'sfift', '十方通物流', 1, 1),
(98, 'stkd', '顺通快递', 1, 1),
(99, 'bgn', '布谷鸟快递', 1, 1),
(100, 'jiahuier', '佳惠尔快递', 1, 1),
(101, 'pingyou', '小包', 1, 1),
(102, 'yumeijie', '誉美捷快递', 1, 1),
(103, 'meilong', '美龙快递', 1, 1),
(104, 'guangtong', '广通速递', 1, 1),
(105, 'STARS', '星晨急便', 1, 1),
(106, 'NANHANG', '中国南方航空股份有限公司', 1, 1),
(107, 'lanbiao', '蓝镖快递', 1, 1),
(109, 'baotongda', '宝通达物流', 1, 1),
(110, 'dashun', '大顺物流', 1, 1),
(111, 'dada', '大达物流', 1, 1),
(112, 'fangfangda', '方方达物流', 1, 1),
(113, 'hebeijianhua', '河北建华物流', 1, 1),
(114, 'haolaiyun', '好来运快递', 1, 1),
(115, 'jinyue', '晋越快递', 1, 1),
(116, 'kuaitao', '快淘快递', 1, 1),
(117, 'peixing', '陪行物流', 1, 1),
(118, 'hkpost', '香港邮政', 1, 1),
(119, 'ytfh', '一统飞鸿快递', 1, 1),
(120, 'zhongxinda', '中信达快递', 1, 1),
(121, 'zhongtian', '中天快运', 1, 1),
(122, 'zuochuan', '佐川急便', 1, 1),
(123, 'chengguang', '程光快递', 1, 1),
(124, 'cszx', '城市之星', 1, 1),
(125, 'chuanzhi', '传志快递', 1, 1),
(126, 'feibao', '飞豹快递', 1, 1),
(127, 'huiqiang', '汇强快递', 1, 1),
(128, 'lejiedi', '乐捷递', 1, 1),
(129, 'lijisong', '成都立即送快递', 1, 1),
(130, 'minbang', '民邦速递', 1, 1),
(131, 'ocs', 'OCS国际快递', 1, 1),
(132, 'santai', '三态速递', 1, 1),
(133, 'saiaodi', '赛澳递', 1, 1),
(134, 'jingdong', '京东快递', 1, 1),
(135, 'zengyi', '增益快递', 1, 1),
(136, 'fanyu', '凡宇速递', 1, 1),
(137, 'fengda', '丰达快递', 1, 1),
(138, 'coe', '东方快递', 1, 1),
(139, 'ees', '百福东方快递', 1, 1),
(140, 'disifang', '递四方速递', 1, 1),
(141, 'rufeng', '如风达快递', 1, 1),
(142, 'changtong', '长通物流', 1, 1),
(143, 'chengshi100', '城市100快递', 1, 1),
(144, 'feibang', '飞邦物流', 1, 1),
(145, 'haosheng', '昊盛物流', 1, 1),
(146, 'yinsu', '音速速运', 1, 1),
(147, 'kuanrong', '宽容物流', 1, 1),
(148, 'tongcheng', '通成物流', 1, 1),
(149, 'tonghe', '通和天下物流', 1, 1),
(150, 'zhima', '芝麻开门', 1, 1),
(151, 'ririshun', '日日顺物流', 1, 1),
(152, 'anxun', '安迅物流', 1, 1),
(153, 'baiqian', '百千诚国际物流', 1, 1),
(154, 'chukouyi', '出口易', 1, 1),
(155, 'diantong', '店通快递', 1, 1),
(156, 'dajin', '大金物流', 1, 1),
(157, 'feite', '飞特物流', 1, 1),
(159, 'gnxb', '国内小包', 1, 1),
(160, 'huacheng', '华诚物流', 1, 1),
(161, 'huahan', '华翰物流', 1, 1),
(162, 'hengyu', '恒宇运通', 1, 1),
(163, 'huahang', '华航快递', 1, 1),
(164, 'jiuyi', '久易快递', 1, 1),
(165, 'jiete', '捷特快递', 1, 1),
(166, 'jingshi', '京世物流', 1, 1),
(167, 'kuayue', '跨越快递', 1, 1),
(168, 'mengsu', '蒙速快递', 1, 1),
(169, 'nanbei', '南北快递', 1, 1),
(171, 'pinganda', '平安达快递', 1, 1),
(172, 'ruifeng', '瑞丰速递', 1, 1),
(173, 'rongqing', '荣庆物流', 1, 1),
(174, 'suijia', '穗佳物流', 1, 1),
(175, 'simai', '思迈快递', 1, 1),
(176, 'suteng', '速腾快递', 1, 1),
(177, 'shengbang', '晟邦物流', 1, 1),
(178, 'suchengzhaipei', '速呈宅配', 1, 1),
(179, 'wuhuan', '五环速递', 1, 1),
(180, 'xingchengzhaipei', '星程宅配', 1, 1),
(181, 'yinjie', '顺捷丰达', 1, 1),
(183, 'yanwen', '燕文物流', 1, 1),
(184, 'zongxing', '纵行物流', 1, 1),
(185, 'aae', 'AAE快递', 1, 1),
(186, 'dhl', 'DHL快递', 1, 1),
(187, 'feihu', '飞狐快递', 1, 1),
(188, 'shunfeng', '顺丰速运', 92, 1),
(189, 'spring', '春风物流', 1, 1),
(190, 'yidatong', '易达通快递', 1, 1),
(191, 'PEWKEE', '彪记快递', 1, 1),
(192, 'PHOENIXEXP', '凤凰快递', 1, 1),
(193, 'CNGLS', 'GLS快递', 1, 1),
(194, 'BHTEXP', '华慧快递', 1, 1),
(195, 'B2B', '卡行天下', 1, 1),
(196, 'PEISI', '配思货运', 1, 1),
(197, 'SUNDAPOST', '上大物流', 1, 1),
(198, 'SUYUE', '苏粤货运', 1, 1),
(199, 'F5XM', '伍圆速递', 1, 1),
(200, 'GZWENJIE', '文捷航空速递', 1, 1),
(201, 'yuancheng', '远成物流', 1, 1),
(202, 'dpex', 'DPEX快递', 1, 1),
(203, 'anjie', '安捷快递', 1, 1),
(204, 'jldt', '嘉里大通', 1, 1),
(205, 'yousu', '优速快递', 1, 1),
(206, 'wanbo', '万博快递', 1, 1),
(207, 'sure', '速尔物流', 1, 1),
(208, 'sutong', '速通物流', 1, 1),
(209, 'JUNCHUANWL', '骏川物流', 1, 1),
(210, 'guada', '冠达快递', 1, 1),
(211, 'dsu', 'D速快递', 1, 1),
(212, 'LONGSHENWL', '龙胜物流', 1, 1),
(213, 'abc', '爱彼西快递', 1, 1),
(214, 'eyoubao', 'E邮宝', 1, 1),
(215, 'aol', 'AOL快递', 1, 1),
(216, 'jixianda', '急先达物流', 1, 1),
(217, 'haihong', '山东海红快递', 1, 1),
(218, 'feiyang', '飞洋快递', 1, 1),
(219, 'rpx', 'RPX保时达', 1, 1),
(220, 'zhaijisong', '宅急送', 1, 1),
(221, 'tiantian', '天天快递', 99, 1),
(222, 'yunwuliu', '云物流', 1, 1),
(223, 'jiuye', '九曳供应链', 1, 1),
(224, 'bsky', '百世快运', 1, 1),
(225, 'higo', '黑狗物流', 1, 1),
(226, 'arke', '方舟速递', 1, 1),
(227, 'zwsy', '中外速运', 1, 1),
(228, 'jxy', '吉祥邮', 1, 1),
(229, 'aramex', 'Aramex', 1, 1),
(230, 'guotong', '国通快递', 1, 1),
(231, 'jiayi', '佳怡物流', 1, 1),
(232, 'longbang', '龙邦快运', 1, 1),
(233, 'minhang', '民航快递', 1, 1),
(234, 'quanyi', '全一快递', 1, 1),
(235, 'quanchen', '全晨快递', 1, 1),
(236, 'usps', 'USPS快递', 1, 1),
(237, 'xinbang', '新邦物流', 1, 1),
(238, 'yuanzhi', '元智捷诚快递', 1, 1),
(239, 'zhongyou', '中邮物流', 1, 1),
(240, 'yuxin', '宇鑫物流', 1, 1),
(241, 'cnpex', '中环快递', 1, 1),
(242, 'shengfeng', '盛丰物流', 1, 1),
(243, 'yuantong', '圆通速递', 97, 1),
(244, 'jiayunmei', '加运美物流', 1, 1),
(245, 'ywfex', '源伟丰快递', 1, 1),
(246, 'xinfeng', '信丰物流', 1, 1),
(247, 'wanxiang', '万象物流', 1, 1),
(248, 'menduimen', '门对门', 1, 1),
(249, 'mingliang', '明亮物流', 1, 1),
(250, 'fengxingtianxia', '风行天下', 1, 1),
(251, 'gongsuda', '共速达物流', 1, 1),
(252, 'zhongtong', '中通快递', 100, 1),
(253, 'quanritong', '全日通快递', 1, 1),
(254, 'ems', 'EMS', 1, 1),
(255, 'wanjia', '万家物流', 1, 1),
(256, 'yuntong', '运通快递', 1, 1),
(257, 'feikuaida', '飞快达物流', 1, 1),
(258, 'haimeng', '海盟速递', 1, 1),
(259, 'zhongsukuaidi', '中速快件', 1, 1),
(260, 'yuefeng', '越丰快递', 1, 1),
(261, 'shenghui', '盛辉物流', 1, 1),
(262, 'datian', '大田物流', 1, 1),
(263, 'quanjitong', '全际通快递', 1, 1),
(264, 'longlangkuaidi', '隆浪快递', 1, 1),
(265, 'neweggozzo', '新蛋奥硕物流', 1, 1),
(266, 'shentong', '申通快递', 95, 1),
(267, 'haiwaihuanqiu', '海外环球', 1, 1),
(268, 'yad', '源安达快递', 1, 1),
(269, 'jindawuliu', '金大物流', 1, 1),
(270, 'sevendays', '七天连锁', 1, 1),
(271, 'tnt', 'TNT快递', 1, 1),
(272, 'huayu', '天地华宇物流', 1, 1),
(273, 'lianhaotong', '联昊通快递', 1, 1),
(274, 'nengda', '港中能达快递', 1, 1),
(275, 'LBWL', '联邦物流', 1, 1),
(276, 'ontrac', 'onTrac', 1, 1),
(277, 'feihang', '原飞航快递', 1, 1),
(278, 'bangsongwuliu', '邦送物流', 1, 1),
(279, 'huaxialong', '华夏龙物流', 1, 1),
(280, 'ztwy', '中天万运快递', 1, 1),
(281, 'fkd', '飞康达物流', 1, 1),
(282, 'anxinda', '安信达快递', 1, 1),
(283, 'quanfeng', '全峰快递', 1, 1),
(284, 'shengan', '圣安物流', 1, 1),
(285, 'jiaji', '佳吉物流', 1, 1),
(286, 'yunda', '韵达快运', 94, 1),
(287, 'ups', 'UPS快递', 1, 1),
(288, 'debang', '德邦物流', 1, 1),
(289, 'yafeng', '亚风速递', 1, 1),
(290, 'kuaijie', '快捷速递', 98, 1),
(291, 'huitong', '百世快递', 93, 1),
(293, 'aolau', 'AOL澳通速递', 1, 1),
(294, 'anneng', '安能物流', 1, 1),
(295, 'auexpress', '澳邮中国快运', 1, 1),
(296, 'exfresh', '安鲜达', 1, 1),
(297, 'bcwelt', 'BCWELT', 1, 1),
(298, 'youzhengguonei', '挂号信', 1, 1),
(299, 'xiaohongmao', '北青小红帽', 1, 1),
(300, 'lbbk', '宝凯物流', 1, 1),
(301, 'byht', '博源恒通', 1, 1),
(302, 'idada', '百成大达物流', 1, 1),
(303, 'baitengwuliu', '百腾物流', 1, 1),
(304, 'birdex', '笨鸟海淘', 1, 1),
(305, 'bsht', '百事亨通', 1, 1),
(306, 'dayang', '大洋物流快递', 1, 1),
(307, 'dechuangwuliu', '德创物流', 1, 1),
(308, 'donghanwl', '东瀚物流', 1, 1),
(309, 'dfpost', '达方物流', 1, 1),
(310, 'dongjun', '东骏快捷物流', 1, 1),
(311, 'dindon', '叮咚澳洲转运', 1, 1),
(312, 'dazhong', '大众佐川急便', 1, 1),
(313, 'decnlh', '德中快递', 1, 1),
(314, 'dekuncn', '德坤供应链', 1, 1),
(315, 'eshunda', '俄顺达', 1, 1),
(316, 'ewe', 'EWE全球快递', 1, 1),
(317, 'fedexuk', 'FedEx英国', 1, 1),
(318, 'fox', 'FOX国际速递', 1, 1),
(319, 'rufengda', '凡客如风达', 1, 1),
(320, 'fandaguoji', '颿达国际快递', 1, 1),
(321, 'hnfy', '飞鹰物流', 1, 1),
(322, 'flysman', '飞力士物流', 1, 1),
(323, 'sccod', '丰程物流', 1, 1),
(324, 'farlogistis', '泛远国际物流', 1, 1),
(325, 'gsm', 'GSM', 1, 1),
(326, 'gaticn', 'GATI快递', 1, 1),
(327, 'gts', 'GTS快递', 1, 1),
(328, 'gangkuai', '港快速递', 1, 1),
(329, 'gtsd', '高铁速递', 1, 1),
(330, 'tiandihuayu', '华宇物流', 1, 1),
(331, 'huangmajia', '黄马甲快递', 1, 1),
(332, 'ucs', '合众速递', 1, 1),
(333, 'huoban', '伙伴物流', 1, 1),
(334, 'nedahm', '红马速递', 1, 1),
(335, 'huiwen', '汇文配送', 1, 1),
(336, 'nmhuahe', '华赫物流', 1, 1),
(337, 'hangyu', '航宇快递', 1, 1),
(338, 'minsheng', '闽盛物流', 1, 1),
(339, 'riyu', '日昱物流', 1, 1),
(340, 'sxhongmajia', '山西红马甲', 1, 1),
(341, 'syjiahuier', '沈阳佳惠尔', 1, 1),
(342, 'shlindao', '上海林道货运', 1, 1),
(343, 'shunjiefengda', '顺捷丰达', 1, 1),
(344, 'subida', '速必达物流', 1, 1),
(345, 'bphchina', '速方国际物流', 1, 1),
(346, 'sendtochina', '速递中国', 1, 1),
(347, 'suning', '苏宁快递', 1, 1),
(348, 'sihaiet', '四海快递', 1, 1),
(349, 'tianzong', '天纵物流', 1, 1),
(350, 'chinatzx', '同舟行物流', 1, 1),
(351, 'nntengda', '腾达速递', 1, 1),
(352, 'sd138', '泰国138', 1, 1),
(353, 'tongdaxing', '通达兴物流', 1, 1),
(354, 'tlky', '天联快运', 1, 1),
(355, 'youshuwuliu', 'UC优速快递', 1, 1),
(356, 'ueq', 'UEQ快递', 1, 1),
(357, 'weitepai', '微特派快递', 1, 1),
(358, 'wtdchina', '威时沛运', 1, 1),
(359, 'wzhaunyun', '微转运', 1, 1),
(360, 'gswtkd', '万通快递', 1, 1),
(361, 'wotu', '渥途国际速运', 1, 1),
(362, 'xiyoute', '希优特快递', 1, 1),
(363, 'xilaikd', '喜来快递', 1, 1),
(364, 'xsrd', '鑫世锐达', 1, 1),
(365, 'xtb', '鑫通宝物流', 1, 1),
(366, 'xintianjie', '信天捷快递', 1, 1),
(367, 'xaetc', '西安胜峰', 1, 1),
(368, 'xianfeng', '先锋快递', 1, 1),
(369, 'sunspeedy', '新速航', 1, 1),
(370, 'xipost', '西邮寄', 1, 1),
(371, 'sinatone', '信联通', 1, 1),
(372, 'sunjex', '新杰物流', 1, 1),
(373, 'yundaexus', '韵达美国件', 1, 1),
(374, 'yxwl', '宇鑫物流', 1, 1),
(375, 'yitongda', '易通达', 1, 1),
(376, 'yiqiguojiwuliu', '一柒物流', 1, 1),
(377, 'yilingsuyun', '亿领速运', 1, 1),
(378, 'yujiawuliu', '煜嘉物流', 1, 1),
(379, 'gml', '英脉物流', 1, 1),
(380, 'leopard', '云豹国际货运', 1, 1),
(381, 'czwlyn', '云南中诚', 1, 1),
(382, 'sdyoupei', '优配速运', 1, 1),
(383, 'yongchang', '永昌物流', 1, 1),
(384, 'yufeng', '御风速运', 1, 1),
(385, 'yamaxunwuliu', '亚马逊物流', 1, 1),
(386, 'yousutongda', '优速通达', 1, 1),
(387, 'yishunhang', '亿顺航', 1, 1),
(388, 'yongwangda', '永旺达快递', 1, 1),
(389, 'ecmscn', '易满客', 1, 1),
(390, 'yingchao', '英超物流', 1, 1),
(391, 'edlogistics', '益递物流', 1, 1),
(392, 'yyexpress', '远洋国际', 1, 1),
(393, 'onehcang', '一号仓', 1, 1),
(394, 'ycgky', '远成快运', 1, 1),
(395, 'lineone', '一号线', 1, 1),
(396, 'ypsd', '壹品速递', 1, 1),
(397, 'vipexpress', '鹰运国际速递', 1, 1),
(398, 'el56', '易联通达物流', 1, 1),
(399, 'yyqc56', '一运全成物流', 1, 1),
(400, 'zhongtie', '中铁快运', 1, 1),
(401, 'ZTKY', '中铁物流', 1, 1),
(402, 'zzjh', '郑州建华快递', 1, 1),
(403, 'zhongruisudi', '中睿速递', 1, 1),
(404, 'zhongwaiyun', '中外运速递', 1, 1),
(405, 'zengyisudi', '增益速递', 1, 1),
(406, 'sujievip', '郑州速捷', 1, 1),
(407, 'zhichengtongda', '至诚通达快递', 1, 1),
(408, 'zhdwl', '众辉达物流', 1, 1),
(409, 'kuachangwuliu', '直邮易', 1, 1),
(410, 'topspeedex', '中运全速', 1, 1),
(411, 'otobv', '中欧快运', 1, 1),
(412, 'zsky123', '准实快运', 1, 1),
(413, 'donghong', '东红物流', 1, 1),
(414, 'kuaiyouda', '快优达速递', 1, 1),
(415, 'balunzhi', '巴伦支快递', 1, 1),
(416, 'hutongwuliu', '户通物流', 1, 1),
(417, 'xianchenglian', '西安城联速递', 1, 1),
(418, 'youbijia', '邮必佳', 1, 1),
(419, 'feiyuan', '飞远物流', 1, 1),
(420, 'chengji', '城际速递', 1, 1),
(421, 'huaqi', '华企快运', 1, 1),
(422, 'yibang', '一邦快递', 1, 1),
(423, 'citylink', 'CityLink快递', 1, 1),
(424, 'meixi', '美西快递', 1, 1),
(425, 'acs', 'ACS', 1, 1);

-- --------------------------------------------------------

--
-- 表的结构 `eb_grade`
--

CREATE TABLE `eb_grade` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '年级名称',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '删除'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='年级列表';

-- --------------------------------------------------------
--
-- 转存表中的数据 `eb_learning_records`
--
CREATE TABLE `eb_learning_records` (
  `id` int(12) UNSIGNED NOT NULL,
  `uid` int(12) NOT NULL DEFAULT '0' COMMENT 'uid',
  `special_id` int(12) NOT NULL DEFAULT '0' COMMENT '专题id',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COMMENT='专题学习记录';

--
-- 表的结构 `eb_lecturer`
--
CREATE TABLE `eb_lecturer` (
  `id` int(10) UNSIGNED NOT NULL,
  `mer_id` int(12) DEFAULT '0' COMMENT '讲师id',
  `lecturer_name` varchar(255) NOT NULL DEFAULT '' COMMENT '讲师名称',
  `lecturer_head` varchar(255) NOT NULL DEFAULT '' COMMENT '讲师头像',
  `phone` varchar(12) DEFAULT NULL COMMENT '讲师手机号',
  `label` varchar(255) NOT NULL DEFAULT '' COMMENT '讲师标签',
  `explain` varchar(255)  DEFAULT '' COMMENT '讲师说明',
  `introduction` text COMMENT '简介',
  `curriculum` int(5) NOT NULL DEFAULT '0' COMMENT '课程数量',
  `study` int(10) NOT NULL DEFAULT '0' COMMENT '学习人数',
  `sort` int(5) NOT NULL DEFAULT '0' COMMENT '排序',
  `is_show` int(2) NOT NULL DEFAULT '1' COMMENT '是否显示',
  `is_del` int(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='讲师表';

--
-- 表的结构 `eb_live_audit`
--
CREATE TABLE `eb_live_audit` (
  `id` int(12) NOT NULL,
  `mer_id` int(12) DEFAULT '0' COMMENT '讲师ID',
  `stream_name` varchar(32) DEFAULT NULL COMMENT '直播间号',
  `live_title` varchar(255) DEFAULT NULL COMMENT '直播间标题',
  `live_image` varchar(255) DEFAULT NULL COMMENT '直播间封面',
  `live_id` int(12) DEFAULT '0' COMMENT '直播间id',
  `special_id` int(12) DEFAULT '0' COMMENT '关联专题id',
  `status` tinyint(1) DEFAULT '0' COMMENT '审核状态',
  `live_strar_time` int(12) DEFAULT '0' COMMENT '直播开始时间',
  `live_end_time` int(12) DEFAULT '0' COMMENT '直播结束时间',
  `success_time` int(12) DEFAULT '0' COMMENT '通过时间',
  `fail_message` varchar(255) DEFAULT NULL COMMENT '未通过原因',
  `fail_time` int(12) DEFAULT '0' COMMENT '未通过时间',
  `add_time` int(12) DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='讲师直播申请表';
--
-- 表的结构 `eb_live_barrage`
--
CREATE TABLE `eb_live_barrage` (
  `id` int(11) UNSIGNED NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户uid',
  `to_uid` int(11) NOT NULL DEFAULT '0' COMMENT '给谁发送',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '消息类型，1=文本，2=图片，3=语音',
  `barrage` varchar(3000) NOT NULL DEFAULT '' COMMENT '评论内容',
  `live_id` int(11) NOT NULL DEFAULT '0' COMMENT '直播间id',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '发送评论时间',
  `is_show` int(11) NOT NULL DEFAULT '1' COMMENT '是否显示 1= 显示，0=隐藏'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='直播间弹幕';

-- --------------------------------------------------------
--
-- 表的结构 `eb_live_gift`
--
CREATE TABLE `eb_live_gift` (
  `id` int(10) UNSIGNED NOT NULL,
  `live_gift_name` varchar(255) NOT NULL DEFAULT '' COMMENT '礼物名称',
  `live_gift_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '礼物价格（虚拟货币）',
  `live_gift_num` varchar(255) NOT NULL DEFAULT '0' COMMENT '赠送数量列表',
  `live_gift_show_img` varchar(255) NOT NULL DEFAULT '' COMMENT '礼物图标',
  `sort` int(5) NOT NULL DEFAULT '0' COMMENT '排序',
  `is_show` int(2) NOT NULL DEFAULT '1' COMMENT '显示',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='礼物列表';

--
-- 表的结构 `eb_live_gift` 添加数据
--
INSERT INTO `eb_live_gift` (`id`, `live_gift_name`, `live_gift_price`, `live_gift_num`, `live_gift_show_img`, `sort`, `is_show`, `add_time`) VALUES
(1, '鲜花', 2.00, '[\"1\",\"5\",\"66\",\"520\",\"999\",\"1314\"]', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/da616202007011009436251.png', 0, 1, 1603850176),
(2, '爱心', 1.00, '[\"1\",\"5\",\"10\",\"20\"]', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/5a2d520200701101025960.png', 0, 1, 1603850176),
(3, '气球', 6.00, '[\"1\",\"5\",\"10\",\"20\",\"66\",\"99\",\"520\",\"999\",\"1314\"]', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/0c648202007011012055342.png', 0, 1, 1603850176),
(4, '皇冠', 20.00, '[\"1\",\"5\",\"10\",\"20\",\"66\",\"99\",\"520\"]', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/82d4c202007011013025359.png', 0, 1, 1603850176),
(5, '奖杯', 15.00, '[\"1\",\"5\",\"10\",\"20\",\"66\",\"99\",\"520\"]', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/1caae202007011013442033.png', 0, 1, 1603850176),
(6, '锦鲤', 30.00, '[\"1\",\"5\",\"10\",\"20\",\"66\",\"99\",\"520\"]', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/226e5202007011015051008.png', 0, 1, 1603850176),
(7, '嘉年华', 50.00, '[\"1\",\"5\",\"10\",\"20\",\"66\",\"99\",\"520\"]', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/22c8e202007011017537720.png', 0, 1, 1603850176),
(8, '粉笔', 5.00, '[\"1\",\"5\",\"10\",\"20\",\"66\",\"99\",\"520\"]', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/96ea420200701101917287.png', 0, 1, 1603850176);

--
-- 表的结构 `eb_live_goods`
--
CREATE TABLE `eb_live_goods` (
  `id` int(11) UNSIGNED NOT NULL,
  `special_id` int(11) NOT NULL DEFAULT '0' COMMENT '专题id',
  `live_id` int(11) NOT NULL DEFAULT '0' COMMENT '直播间id',
  `special_name` varchar(100) NOT NULL DEFAULT '' COMMENT '课程名称',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0:专题，1：商品, 2:活动',
  `sort` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  `sales` int(10) NOT NULL DEFAULT '0' COMMENT '销量',
  `fake_sales` int(10) NOT NULL DEFAULT '0' COMMENT '虚拟销量',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `add_time` int(12) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(12) NOT NULL DEFAULT '0' COMMENT '修改时间'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COMMENT='直播间带货表';

--
-- 表的结构 `eb_live_honoured_guest`
--
CREATE TABLE `eb_live_honoured_guest` (
  `id` int(11) UNSIGNED NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '嘉宾用户id',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '嘉宾类型 0=助教，1=讲师',
  `wechat_name` varchar(64) DEFAULT NULL COMMENT '微信昵称',
  `nickname` varchar(255) NOT NULL DEFAULT '' COMMENT '嘉宾别昵称',
  `live_id` int(11) NOT NULL DEFAULT '0' COMMENT '直播间id',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加事件'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='直播间嘉宾';

-- --------------------------------------------------------
--
-- 表的结构 `eb_live_playback`
--
CREATE TABLE `eb_live_playback` (
  `id` int(10) UNSIGNED NOT NULL,
  `stream_name` varchar(255) NOT NULL DEFAULT '' COMMENT '直播间号',
  `playback_name` varchar(255) DEFAULT '' COMMENT '回放名称',
  `playback_url` varchar(255) NOT NULL DEFAULT '' COMMENT '回放地址',
  `start_time` int(12) NOT NULL DEFAULT '0' COMMENT '开始时间',
  `end_time` int(12) NOT NULL DEFAULT '0' COMMENT '结束时间',
  `is_show` int(2) NOT NULL DEFAULT '1' COMMENT '是否显示',
  `RecordId` varchar (37) NOT NULL DEFAULT '' COMMENT '直播间回放设置ID',
  `is_record` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否设置回放',
  `sort` int(2) DEFAULT '0' COMMENT '排序',
  `is_del` int(2) NOT NULL DEFAULT '0' COMMENT '删除',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='直播间回放';

--
-- 表的结构 `eb_live_reward`
--
CREATE TABLE `eb_live_reward` (
  `id` int(11) UNSIGNED NOT NULL,
  `live_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '直播间id',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户uid',
  `nickname` varchar(100) NOT NULL DEFAULT '' COMMENT '昵称',
  `gift_id` int(11) NOT NULL DEFAULT '0' COMMENT '礼物id',
  `gift_name` varchar(100) NOT NULL DEFAULT '' COMMENT '礼物名称',
  `gift_price` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '虚拟货币价格',
  `gift_num` int(11) NOT NULL DEFAULT '0' COMMENT '礼物数量',
  `total_price` int(11) NOT NULL DEFAULT '0' COMMENT '总虚拟货币价格',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '打赏时间',
  `is_extract` tinyint(1) DEFAULT '0' COMMENT '是否提取',
  `is_show` int(11) NOT NULL DEFAULT '1' COMMENT '是否显示 1= 显示，0=隐藏'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='直播间礼物打赏表';

--
-- 表的结构 `eb_live_studio`
--
CREATE TABLE `eb_live_studio` (
  `id` int(11) UNSIGNED NOT NULL,
  `stream_name` varchar(32) NOT NULL DEFAULT '' COMMENT '直播间号',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户uid',
  `live_title` varchar(255) NOT NULL DEFAULT '' COMMENT '直播间标题',
  `live_image` varchar(255) NOT NULL DEFAULT '' COMMENT '直播间封面',
  `start_play_time` varchar(30) NOT NULL DEFAULT '0' COMMENT '每日开播时间',
  `stop_play_time` varchar(30) NOT NULL DEFAULT '0' COMMENT '每日停播时间',
  `live_introduction` varchar(3000) NOT NULL DEFAULT '' COMMENT '直播间简介',
  `special_id` int(11) NOT NULL DEFAULT '0' COMMENT '关联专题id',
  `online_user_num` int(11) NOT NULL DEFAULT '0' COMMENT '在线人数',
  `online_num` int(11) NOT NULL DEFAULT '0' COMMENT '虚拟在线人数',
  `studio_pwd` varchar(32) NOT NULL DEFAULT '' COMMENT '直播间密码',
  `is_remind` tinyint(1) NOT NULL DEFAULT '0' COMMENT '开播提醒',
  `is_reminded` tinyint(1) DEFAULT '0' COMMENT '是否提醒',
  `remind_time` int(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '开播前提醒时间（分钟）',
  `auto_phrase` varchar(255) NOT NULL DEFAULT '' COMMENT '进入直播间自动回复',
  `add_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '添加时间',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `is_recording` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否录制直播间',
  `is_play` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否正在开播',
  `recording` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否录制进行中',
  `record_time` int(12) NOT NULL DEFAULT '0' COMMENT '录制开始时间',
  `is_playback` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否回放',
  `playback_record_id` varchar(1000) NOT NULL DEFAULT '' COMMENT '回放RecordId',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `is_chat` tinyint(1) DEFAULT '1' COMMENT '是否开启聊天',
  `is_class_chat` tinyint(1) DEFAULT '1' COMMENT '是否开启课堂聊天',
  `is_live_recommend` tinyint(1) DEFAULT '1' COMMENT '是否开启直播间推荐',
  `is_ranking` tinyint(1) DEFAULT '1' COMMENT '是否开启直播间排行'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='直播间';

--
-- 转存表中的数据 `eb_live_studio`
--
INSERT INTO `eb_live_studio`(`id`, `stream_name`, `uid`, `live_title`, `live_image`, `start_play_time`, `stop_play_time`, `live_introduction`, `special_id`, `online_user_num`, `online_num`, `studio_pwd`, `is_remind`,`is_reminded`, `remind_time`, `auto_phrase`, `add_time`, `is_del`, `is_recording`, `is_play`, `recording`, `record_time`, `is_playback`, `playback_record_id`, `sort`) VALUES
(14, '66396889', 0, '直播国学大讲堂', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/ce99d202002201232432554.jpg', '2020-04-28 11:22:57', '2020-04-28 12:22:57', '直播国学大讲堂', 1, 0, 0, '', 0, 0, 0, '国学大讲堂', 1588044359, 0, 0, 0, 0, 0, 0, '', 0);

-- --------------------------------------------------------

--
-- 表的结构 `eb_live_user`
--
CREATE TABLE `eb_live_user` (
  `id` int(11) UNSIGNED NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `live_id` int(11) NOT NULL DEFAULT '0' COMMENT '直播间id',
  `add_ip` varchar(16) NOT NULL DEFAULT '' COMMENT '首次浏览ip',
  `last_ip` varchar(16) NOT NULL DEFAULT '' COMMENT '历史访问id',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '首次访问时间',
  `last_time` int(12) NOT NULL DEFAULT '0' COMMENT '历史访问时间',
  `visit_num` int(11) NOT NULL DEFAULT '0' COMMENT '访问次数',
  `is_online` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否在线 1=在线，0=下线',
  `is_ban` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否禁言，1=禁言，0=自由',
  `is_open_ben` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否禁止进入直播间 1=禁止，0=自由',
  `ban_time` int(12) NOT NULL DEFAULT '0' COMMENT '禁言时间',
  `open_ben_time` int(12) NOT NULL DEFAULT '0' COMMENT '禁止进入直播间时间',
  `is_follow` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否关注'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='直播间用户管理';

-- --------------------------------------------------------

--
-- 表的结构 `eb_member_card`
--
CREATE TABLE `eb_member_card` (
  `id` int(11) UNSIGNED NOT NULL,
  `card_batch_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '卡批次id',
  `card_number` varchar(200) NOT NULL DEFAULT '' COMMENT '卡号',
  `card_password` char(12) NOT NULL DEFAULT '' COMMENT '密码',
  `use_uid` int(11) NOT NULL DEFAULT '0' COMMENT '使用用户',
  `use_time` int(12) NOT NULL DEFAULT '0' COMMENT '使用时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '卡状态：0：冻结；1：激活',
  `create_time` int(12) NOT NULL DEFAULT '0',
  `update_time` int(12) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='会员卡表';

--
-- 表的结构 `eb_member_card_batch`
--
CREATE TABLE `eb_member_card_batch` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '批次名称',
  `total_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '生成卡数量',
  `use_day` int(11) unsigned NOT NULL DEFAULT '7' COMMENT '体验天数',
  `use_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '使用',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否生效,控制此批次所有卡0：不生效；1：生效',
  `qrcode` varchar(255) NOT NULL DEFAULT '' COMMENT '二维码图路径',
  `remark` varchar(502) NOT NULL DEFAULT '' COMMENT '备注',
  `create_time` int(12) NOT NULL DEFAULT '0',
  `update_time` int(12) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='会员卡批次表';
-- --------------------------------------------------------

--
-- 表的结构 `eb_member_record`
--
CREATE TABLE `eb_member_record` (
  `id` int(10) UNSIGNED NOT NULL,
  `oid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单ID',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户uid',
  `type` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '区别 0:购买 1:会员卡',
  `code` varchar(255) DEFAULT NULL COMMENT '卡号',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '支付金额',
  `purchase_time` int(12) unsigned NOT NULL DEFAULT '0' COMMENT '会员购买时间',
  `is_free` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '是否免费',
  `is_permanent` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '是否永久',
  `overdue_time` int(12) unsigned NOT NULL DEFAULT '0' COMMENT '会员过期时间',
  `validity` varchar(10) DEFAULT '0' COMMENT '有效期',
  `add_time` int(12) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='会员购买记录表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_member_ship`
--
CREATE TABLE `eb_member_ship` (
  `id` int(11) UNSIGNED NOT NULL,
  `type` int(2) NOT NULL DEFAULT '1' COMMENT '会员类别(1:普通会员)',
  `title` varchar(200) NOT NULL DEFAULT '' COMMENT '会员名称',
  `img` varchar(255) DEFAULT NULL COMMENT '会员图标',
  `vip_day` int(10) NOT NULL DEFAULT '0' COMMENT '会员时间(天)',
  `original_price` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '原价',
  `price` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '优惠后价格',
  `is_permanent` int(2) NOT NULL DEFAULT '0' COMMENT '是否永久',
  `is_publish` int(2) NOT NULL DEFAULT '1' COMMENT '是否发布',
  `is_free` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '是否免费',
  `sort` int(10) NOT NULL DEFAULT '0' COMMENT '排序倒序',
  `is_alone` tinyint(1) DEFAULT '0' COMMENT '是否开启单独分销',
  `brokerage_ratio` int(3) DEFAULT '0' COMMENT '一级推广人返佣比例',
  `brokerage_two` int(3) DEFAULT '0' COMMENT '二级推广人返佣比例',
  `is_del` int(2) NOT NULL DEFAULT '0' COMMENT '删除',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='会员管理表';
-- --------------------------------------------------------

--
-- 表数据 `eb_member_ship` 添加数据
--
INSERT INTO `eb_member_ship` (`id`, `type`, `title`,`img`, `vip_day`, `original_price`, `price`, `is_permanent`, `is_publish`, `is_free`, `sort`,`is_alone`,`brokerage_ratio`,`brokerage_two`, `is_del`, `add_time`) VALUES
(1, 1, '月卡', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/4092f202108171704439564.png', 30, 33.00, 30.00, 0, 1, 0, 4, 0, 0, 0, 0, 1588129765),
(2, 1, '季卡', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/4092f202108171704439564.png', 90, 90.00, 80.00, 0, 1, 0, 3, 0, 0, 0, 0, 1588129794),
(3, 1, '年卡', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/4092f202108171704439564.png', 365, 360.00, 300.00, 0, 1, 0, 2, 0, 0, 0, 0, 1588129818),
(4, 1, '永久', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/4092f202108171704439564.png', -1, 1200.00, 900.00, 1, 1, 0, 1, 0, 0, 0, 0, 1588129856),
(5, 1, '免费', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/4092f202108171704439564.png', 7, 0.00, 0.00, 0, 1, 1, 5, 0, 0, 0, 0, 1588130680);

--
-- 表的结构 `eb_merchant`
--
CREATE TABLE `eb_merchant` (
  `id` mediumint(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `lecturer_id` int(12) NOT NULL DEFAULT '0' COMMENT '讲师id',
  `mer_name` varchar(32) NOT NULL DEFAULT '' COMMENT '讲师名称',
  `real_name` varchar(32) NOT NULL DEFAULT '' COMMENT '讲师姓名',
  `mer_email` varchar(32) DEFAULT '' COMMENT '讲师邮箱',
  `mer_phone` varchar(13) NOT NULL DEFAULT '' COMMENT '讲师手机号',
  `mer_address` varchar(128) NOT NULL DEFAULT '' COMMENT '讲师地址',
  `mer_avatar` varchar(128) DEFAULT '' COMMENT '讲师头像',
  `mer_special_divide` int(5) DEFAULT '0' COMMENT '专题分成',
  `mer_store_divide` int(5) DEFAULT '0' COMMENT '商品分成',
  `mer_event_divide` int(5) DEFAULT '0' COMMENT '活动分成',
  `mer_data_divide` int(5) DEFAULT '0' COMMENT '资料分成',
  `mer_test_divide` int(5) DEFAULT '0' COMMENT '试卷分成',
  `now_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '余额',
  `gold_divide` int(5) DEFAULT '0' COMMENT '直播收益分成',
  `gold_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '虚拟币余额',
  `mark` varchar(256) DEFAULT NULL COMMENT '讲师备注',
  `rules` text COMMENT '讲师权限 menu_id',
  `reg_admin_id` int(10) unsigned DEFAULT '0' COMMENT '总后台管理员ID',
  `reg_time` int(12) unsigned NOT NULL DEFAULT '0' COMMENT '添加讲师时间',
  `sort` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '-1' COMMENT '讲师是否禁用-1审核中，-2审核失败，0锁定,1正常',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0未删除1删除',
  `is_audit` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '添加的产品是否审核0不审核1审核',
  `estate` tinyint(1) unsigned DEFAULT '0' COMMENT '讲师是否1开启0关闭',
  `mer_info` varchar(512) DEFAULT '' COMMENT '讲师简介',
  `is_source` tinyint(1) DEFAULT '0' COMMENT '来源0=平台 1=入驻',
  `card_id` char(30) DEFAULT '' COMMENT '身份证',
  `bank` varchar(128)  DEFAULT '' COMMENT '银行卡开户行',
  `bank_number` varchar(20)  DEFAULT '' COMMENT '银行卡卡号',
  `bank_name` varchar(64)  DEFAULT '' COMMENT '银行卡持卡人姓名',
  `bank_address` varchar(256)  DEFAULT '' COMMENT '银行卡开户行地址',
  `is_phone_service` tinyint(1) DEFAULT '0' COMMENT '是否手机客服',
  `service_phone` varchar(12) DEFAULT NULL COMMENT '客服电话',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='讲师表';

--
-- 表的结构 `eb_merchant_admin`
--
CREATE TABLE `eb_merchant_admin` (
  `id` smallint(5) unsigned NOT NULL,
  `uid` int(20) NOT NULL DEFAULT '0',
  `mer_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '讲师id',
  `account` varchar(32) NOT NULL DEFAULT '' COMMENT '讲师管理员账号',
  `pwd` char(32) NOT NULL DEFAULT '' COMMENT '讲师管理员密码',
  `real_name` varchar(16) NOT NULL DEFAULT '' COMMENT '讲师管理员姓名',
  `rules` text COMMENT '讲师管理员权限(merchant_menus_id)',
  `phone` varchar(13) NOT NULL DEFAULT '' COMMENT '讲师管理员手机号',
  `email` varchar(32) DEFAULT '' COMMENT '讲师管理员邮箱',
  `last_ip` varchar(16) DEFAULT '' COMMENT '讲师管理员最后一次登录IP地址',
  `last_time` int(50) unsigned DEFAULT '0' COMMENT '讲师管理员最后一次登录时间',
  `login_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '讲师管理员登录次数',
  `level` int(3) unsigned NOT NULL DEFAULT '1' COMMENT '讲师管理员等级(商户添加的为1，等级1添加的为2)',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否有效 1有效 0无效 ',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '是否删除',
  `add_time` int(12) unsigned NOT NULL DEFAULT '0' COMMENT '讲师管理员添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='讲师管理员表';

--
-- 表的结构 `eb_merchant_bill`
--
CREATE TABLE `eb_merchant_bill` (
  `id` int(12) unsigned NOT NULL,
  `link_id` varchar(32) NOT NULL DEFAULT '0' COMMENT '关联id',
  `mer_id` int(12) NOT NULL DEFAULT '0' COMMENT '讲师ID',
  `pm` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 = 支出 1 = 获得',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '账单标题',
  `category` varchar(255) NOT NULL DEFAULT '' COMMENT '明细种类',
  `type` varchar(255) NOT NULL DEFAULT '' COMMENT '明细类型',
  `number` decimal(8,2) DEFAULT '0.00' COMMENT '明细数字',
  `balance` decimal(8,2) DEFAULT '0.00' COMMENT '剩余',
  `mark` varchar(255) DEFAULT '0' COMMENT '备注',
  `add_time` int(12) DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='讲师账单表';

--
-- 表的结构 `eb_merchant_bill`
--
CREATE TABLE `eb_merchant_follow` (
  `id` int(12) NOT NULL,
  `mer_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '讲师id',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'UID',
  `unfollow_time` int(12) unsigned NOT NULL DEFAULT '0' COMMENT '取消关注时间',
  `follow_time` int(12) unsigned NOT NULL DEFAULT '0' COMMENT '关注时间',
  `is_follow` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0 =取消关注  1= 关注'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户关注讲师表';

--
-- 表的结构 `eb_merchant_flowing_water`
--
CREATE TABLE `eb_merchant_flowing_water` (
  `id` int(12) NOT NULL,
  `mer_id` int(12) DEFAULT '0' COMMENT '讲师ID',
  `oid` int(12) DEFAULT '0' COMMENT '订单id',
  `type` tinyint(1) DEFAULT '0' COMMENT '订单类别 0:课程订单 1:会员订单 2:商品订单 3:资料订单 4:报名订单 5:考试订单',
  `total_price` decimal(10,2) DEFAULT '0.00' COMMENT '订单总价',
  `pay_price` decimal(10,2) DEFAULT '0.00' COMMENT '实际支付金额',
  `refund_price` decimal(10,2) DEFAULT '0.00' COMMENT '退款金额',
  `price` decimal(10,2) DEFAULT '0.00' COMMENT '讲师分成金额',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态 1:获得 0:返还',
  `add_time` int(12) DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COMMENT='讲师流水';

--
-- 表的结构 `eb_merchant_menus`
--
CREATE TABLE `eb_merchant_menus` (
  `id` smallint(5) unsigned NOT NULL,
  `pid` int(5) unsigned NOT NULL DEFAULT '0' COMMENT '父级id',
  `icon` varchar(16) NOT NULL DEFAULT '' COMMENT '图标',
  `menu_name` varchar(32) NOT NULL DEFAULT '' COMMENT '按钮名',
  `module` varchar(32) NOT NULL DEFAULT '' COMMENT '模块名',
  `controller` varchar(64) COLLATE utf8mb4_bin NOT NULL COMMENT '控制器名',
  `action` varchar(32) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '方法名',
  `params` varchar(128) COLLATE utf8mb4_bin NOT NULL DEFAULT '[]' COMMENT '参数',
  `sort` int(5) NOT NULL DEFAULT '1' COMMENT '排序',
  `is_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否显示 1显示 0隐藏'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='讲师菜单表';

--
-- 转存表中的数据 `eb_merchant_menus`
--
INSERT INTO `eb_merchant_menus` (`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`) VALUES
(2, 0, 'cog', '系统设置', 'merchant', '', '', '[]', 4, 1),
(3, 2, '', '基本设置', 'merchant', 'system', 'index', '[]', 5, 1),
(5, 0, 'viacoin', '财务管理', 'merchant', '', '', '[]', 5, 1),
(6, 5, '', '提现管理', 'merchant', 'finance.userExtract', 'forward_list', '[]', 4, 1),
(7, 5, '', '分成记录', 'merchant', 'finance.finance', 'merOrderBill', '[]', 5, 1),
(8, 2, '', '权限管理', 'merchant', '', '', '[]', 2, 1),
(9, 8, '', '子管理员列表', 'merchant', 'merchant.merchantAdmin', 'index', '[]', 0, 1),
(12, 31, '', '专题管理', 'merchant', '', '', '[]', 9, 1),
(13, 14, '', '商品管理', 'merchant', '', '', '[]', 7, 1),
(14, 0, 'newspaper-o', '内容管理', 'merchant', '', '', '[]', 9, 1),
(15, 14, '', '资料管理', 'merchant', '', '', '[]', 6, 1),
(16, 0, 'send-o', '营销管理', 'merchant', '', '', '[]', 8, 1),
(17, 0, 'th-list', '订单管理', 'merchant', '', '', '[]', 6, 1),
(18, 12, '', '图文专题', 'merchant', 'special.specialType', 'index', '{\"special_type\":\"1\"}', 5, 1),
(19, 12, '', '音频专题', 'merchant', 'special.specialType', 'index', '{\"special_type\":\"2\"}', 4, 1),
(20, 12, '', '视频专题', 'merchant', 'special.specialType', 'index', '{\"special_type\":\"3\"}', 3, 1),
(21, 12, '', '轻专题', 'merchant', 'special.SpecialType', 'index', '{\"special_type\":\"6\"}', 2, 1),
(22, 12, '', '专栏专题', 'merchant', 'special.SpecialType', 'index', '{\"special_type\":\"5\"}', 1, 1),
(23, 14, '', '直播管理', 'merchant', '', '', '[]', 8, 1),
(24, 23, '', '直播列表', 'merchant', 'live.aliyunLive', 'special_live', '{\"special_type\":\"4\"}', 3, 1),
(25, 23, '', '直播间管理', 'merchant', 'live.aliyunLive', 'index', '{\"special_type\":\"4\",\"type\":\"2\"}', 2, 1),
(26, 23, '', '直播贡献', 'merchant', 'live.aliyunLive', 'live_reward', '[]', 1, 1),
(27, 13, '', '商品列表', 'merchant', 'store.storeProduct', 'index', '{\"type\":\"7\"}', 2, 1),
(28, 13, '', '商品评论', 'merchant', 'store.storeProductReply', 'index', '[]', 1, 1),
(29, 15, '', '资料列表', 'merchant', 'download.dataDownload', 'index', '[]', 0, 1),
(30, 16, '', '活动列表', 'merchant', 'ump.eventRegistration', 'index', '[]', 2, 1),
(31, 14, '', '课程管理', 'merchant', '', '', '[]', 9, 1),
(32, 31, '', '专题评论', 'merchant', 'special.specialReply', 'index', '[]', 8, 1),
(33, 31, '', '素材管理', 'merchant', '', '', '[]', 7, 1),
(34, 33, '', '素材分类', 'merchant', 'special.specialTaskCategory', 'index', '[]', 2, 1),
(35, 33, '', '素材列表', 'merchant', 'special.SpecialType', 'sources_index', '[]', 1, 1),
(36, 0, 'legal', '审核管理', 'merchant', '', '', '[]', 7, 1),
(42, 16, '', '拼团管理', 'merchant', '', '', '[]', 3, 1),
(43, 42, '', '拼团列表', 'merchant', 'ump.storeCombination', 'combina_list', '[]', 3, 1),
(44, 42, '', '拼团专题', 'merchant', 'special.SpecialType', 'groupList', '[]', 2, 1),
(45, 17, '', '课程订单', 'merchant', 'order.storeOrder', 'index', '[]', 5, 1),
(46, 17, '', '商品订单', 'merchant', 'order.storeOrder', 'productOrder', '[]', 4, 1),
(47, 17, '', '报名订单', 'merchant', 'ump.eventRegistration', 'order', '[]', 3, 1),
(48, 17, '', '资料订单', 'merchant', 'order.dataDownloadOrder', 'index', '[]', 2, 1),
(50, 36, '', '直播审核', 'merchant', 'live.aliyunLive', 'live_audit', '[]', 0, 1),
(52, 5, '', '直播收益', 'merchant', 'live.aliyunLive', 'mer_live_reward', '[]', 1, 1),
(53, 5, '', '资金流水', 'merchant', 'finance.finance', 'merbill', '[]', 3, 1),
(54, 5, '', '虚拟币流水', 'merchant', 'finance.finance', 'mer_gold_bill', '[]', 2, 1),
(55, 18, '', '删除专题', 'merchant', 'special.SpecialType', 'delete', '[]', 0, 0),
(56, 18, '', '创建编辑专题', 'merchant', 'special.SpecialType', 'add', '[]', 0, 0),
(57, 18, '', '学习记录', 'merchant', 'special.SpecialType', 'learningRecords', '[]', 0, 0),
(58, 18, '', '关联资料', 'merchant', 'special.SpecialType', 'dataDownloadRelation', '[]', 0, 0),
(59, 18, '', '保存专题', 'merchant', 'special.SpecialType', 'save_special', '[]', 0, 0),
(60, 18, '', '保存拼团设置', 'merchant', 'special.SpecialType', 'save_pink', '[]', 0, 0),
(61, 19, '', '创建编辑专题', 'merchant', 'special.SpecialType', 'add', '[]', 0, 0),
(62, 19, '', '保存专题', 'merchant', 'special.SpecialType', 'save_special', '[]', 0, 0),
(63, 19, '', '关联资料', 'merchant', 'special.SpecialType', 'dataDownloadRelation', '[]', 0, 0),
(64, 19, '', '保存拼团设置', 'merchant', 'special.SpecialType', 'save_pink', '[]', 0, 0),
(65, 19, '', '删除专题', 'merchant', 'special.SpecialType', 'delete', '[]', 0, 0),
(66, 19, '', '学习记录', 'merchant', 'special.SpecialType', 'learningRecords', '[]', 0, 0),
(67, 20, '', '创建编辑专题', 'merchant', 'special.SpecialType', 'add', '[]', 0, 0),
(68, 20, '', '保存专题', 'merchant', 'special.SpecialType', 'save_special', '[]', 0, 0),
(69, 20, '', '关联资料', 'merchant', 'special.SpecialType', 'dataDownloadRelation', '[]', 0, 0),
(70, 20, '', '保存拼团设置', 'merchant', 'special.SpecialType', 'save_pink', '[]', 0, 0),
(71, 20, '', '删除专题', 'merchant', 'special.SpecialType', 'delete', '[]', 0, 0),
(72, 20, '', '学习记录', 'merchant', 'special.SpecialType', 'learningRecords', '[]', 0, 0),
(73, 21, '', '创建编辑专题', 'merchant', 'special.SpecialType', 'add', '[]', 0, 0),
(74, 21, '', '保存专题', 'merchant', 'special.SpecialType', 'save_special', '[]', 0, 0),
(75, 21, '', '关联资料', 'merchant', 'special.SpecialType', 'dataDownloadRelation', '[]', 0, 0),
(76, 21, '', '保存拼团设置', 'merchant', 'special.SpecialType', 'save_pink', '[]', 0, 0),
(77, 21, '', '删除专题', 'merchant', 'special.SpecialType', 'delete', '[]', 0, 0),
(78, 21, '', '学习记录', 'merchant', 'special.SpecialType', 'learningRecords', '[]', 0, 0),
(79, 22, '', '创建编辑专题', 'merchant', 'special.SpecialType', 'add', '[]', 0, 0),
(80, 22, '', '保存专题', 'merchant', 'special.SpecialType', 'save_special', '[]', 0, 0),
(81, 22, '', '关联资料', 'merchant', 'special.SpecialType', 'dataDownloadRelation', '[]', 0, 0),
(82, 22, '', '保存拼团设置', 'merchant', 'special.SpecialType', 'save_pink', '[]', 0, 0),
(83, 22, '', '删除专题', 'merchant', 'special.SpecialType', 'delete', '[]', 0, 0),
(84, 22, '', '学习记录', 'merchant', 'special.SpecialType', 'learningRecords', '[]', 0, 0),
(85, 32, '', '评论删除', 'merchant', 'special.specialReply', 'delete', '[]', 0, 0),
(86, 32, '', '评论回复', 'merchant', 'special.specialReply', 'set_reply', '[]', 0, 0),
(87, 32, '', '加精设置', 'merchant', 'special.specialReply', 'set_value', '[]', 0, 0),
(88, 32, '', '保存虚拟评论', 'merchant', 'special.specialReply', 'save_false', '[]', 0, 0),
(89, 32, '', '创建虚拟评论', 'merchant', 'special.specialReply', 'create_false', '[]', 0, 0),
(90, 32, '', '创建虚拟评论', 'merchant', 'special.specialReply', 'index', '[]', 0, 0),
(91, 34, '', '删除素材分类', 'merchant', 'special.specialTaskCategory', 'delete', '[]', 0, 0),
(92, 34, '', '保存素材分类', 'merchant', 'special.specialTaskCategory', 'save', '[]', 0, 0),
(93, 34, '', '编辑素材分类', 'merchant', 'special.specialTaskCategory', 'create', '[]', 0, 0),
(94, 35, '', '删除素材', 'merchant', 'special.SpecialType', 'delete', '[]', 0, 0),
(95, 35, '', '保存素材', 'merchant', 'special.SpecialType', 'save_source', '[]', 0, 0),
(96, 35, '', '创建编辑素材', 'merchant', 'special.SpecialType', 'addSources', '[]', 0, 0),
(97, 24, '', '保存直播', 'merchant', 'special.SpecialType', 'save_special', '[]', 0, 0),
(98, 24, '', '创建编辑专题', 'merchant', 'special.SpecialType', 'add', '[]', 0, 0),
(99, 24, '', '直播拼团', 'merchant', 'special.SpecialType', 'save_pink', '[]', 0, 0),
(100, 24, '', '删除直播专题', 'merchant', 'live.aliyunLive', 'delete', '[]', 0, 0),
(101, 24, '', '去直播', 'merchant', 'live.aliyunLive', 'go_live', '[]', 0, 0),
(102, 24, '', '直播回放', 'merchant', 'live.aliyunLive', 'download_list', '[]', 0, 0),
(103, 25, '', '编辑直播间', 'merchant', 'live.aliyunLive', 'update_live', '[]', 0, 0),
(104, 25, '', '直播间编辑保存', 'merchant', 'live.aliyunLive', 'save_live', '[]', 0, 0),
(105, 25, '', '直播间禁言', 'merchant', 'live.aliyunLive', 'live_no_speaking', '[]', 0, 0),
(106, 25, '', '直播间禁止进入', 'merchant', 'live.aliyunLive', 'live_no_entry', '[]', 0, 0),
(107, 25, '', '直播间课程', 'merchant', 'live.aliyunLive', 'live_goods', '[]', 0, 0),
(108, 25, '', '直播间商品', 'merchant', 'live.aliyunLive', 'live_store', '[]', 0, 0),
(109, 25, '', '直播提醒', 'merchant', 'live.aliyunLive', 'send_remind', '[]', 0, 0),
(110, 25, '', '嘉宾设置', 'merchant', 'live.aliyunLive', 'guest_list', '[]', 0, 0),
(111, 25, '', '评论查看', 'merchant', 'live.aliyunLive', 'comment_list', '[]', 0, 0),
(112, 27, '', '上架开关', 'merchant', 'store.storeProduct', 'set_show', '[]', 0, 0),
(113, 27, '', '商品保存', 'merchant', 'store.storeProduct', 'save', '[]', 0, 0),
(114, 27, '', '删除商品', 'merchant', 'store.storeProduct', 'delete', '[]', 0, 0),
(115, 27, '', '关联专题', 'merchant', 'store.storeProduct', 'knowledge', '[]', 0, 0),
(116, 27, '', '商品创建编辑', 'merchant', 'store.storeProduct', 'create', '[]', 0, 0),
(117, 28, '', '评论删除', 'merchant', 'store.storeProductReply', 'delete', '[]', 0, 0),
(118, 28, '', '评论回复', 'merchant', 'store.storeProductReply', 'set_reply', '[]', 0, 0),
(119, 28, '', '加精设置', 'merchant', 'store.storeProductReply', 'set_value', '[]', 0, 0),
(120, 28, '', '保存虚拟评论', 'merchant', 'store.storeProductReply', 'save_false', '[]', 0, 0),
(121, 28, '', '创建虚拟评论', 'merchant', 'store.storeProductReply', 'create_false', '[]', 0, 0),
(122, 28, '', '评论列表', 'merchant', 'store.storeProductReply', 'index', '[]', 0, 0),
(123, 29, '', '评论删除', 'merchant', 'download.dataDownload', 'delete', '[]', 0, 0),
(124, 29, '', '下载记录', 'merchant', 'download.dataDownload', 'records', '[]', 0, 0),
(125, 29, '', '状态设置', 'merchant', 'download.dataDownload', 'set_value', '[]', 0, 0),
(126, 29, '', '保存资料', 'merchant', 'download.dataDownload', 'save_data', '[]', 0, 0),
(127, 29, '', '编辑资料', 'merchant', 'download.dataDownload', 'add', '[]', 0, 0),
(128, 29, '', '资料展示列表', 'merchant', 'download.dataDownload', 'index', '[]', 0, 0),
(129, 43, '', '拼团下架', 'merchant', 'ump.storeCombination', 'down_pink', '[]', 0, 0),
(130, 43, '', '拼团助力提交', 'merchant', 'ump.storeCombination', 'save', '[]', 0, 0),
(131, 43, '', '拼团删除', 'merchant', 'ump.storeCombination', 'delete_pink', '[]', 0, 0),
(132, 43, '', '拼团页面展示', 'merchant', 'ump.storeCombination', 'combina_list', '[]', 0, 0),
(133, 44, '', '保存拼团设置', 'merchant', 'special.SpecialType', 'save_pink', '[]', 0, 0),
(134, 30, '', '显示开关', 'merchant', 'ump.eventRegistration', 'set_show', '[]', 0, 0),
(135, 30, '', '活动保存', 'merchant', 'ump.eventRegistration', 'add_new', '[]', 0, 0),
(136, 30, '', '活动核销', 'merchant', 'ump.eventRegistration', 'scanCodeSignIn', '[]', 0, 0),
(137, 30, '', '活动删除', 'merchant', 'ump.eventRegistration', 'delete', '[]', 0, 0),
(138, 30, '', '活动列表展示', 'merchant', 'ump.eventRegistration', 'index', '[]', 0, 0),
(147, 50, '', '编辑审核', 'merchant', 'live.aliyunLive', 'edit_audit', '[]', 0, 0),
(148, 45, '', '删除订单', 'merchant', 'order.storeOrder', 'delete', '[]', 0, 0),
(149, 45, '', '添加订单备注', 'merchant', 'order.storeOrder', 'remark', '[]', 0, 0),
(150, 45, '', '保存不退款信息', 'merchant', 'order.storeOrder', 'updateRefundN', '[]', 0, 0),
(151, 45, '', '不退款保存页面', 'merchant', 'order.storeOrder', 'refund_n', '[]', 0, 0),
(152, 45, '', '订单详情', 'merchant', 'order.storeOrder', 'order_info', '[]', 0, 0),
(153, 45, '', '退款处理', 'merchant', 'order.storeOrder', 'updateRefundY', '[]', 0, 0),
(154, 45, '', '退款页面', 'merchant', 'order.storeOrder', 'refund_y', '[]', 0, 0),
(155, 45, '', '订单展示页面', 'merchant', 'order.storeOrder', 'order_list', '[]', 0, 0),
(156, 46, '', '删除订单', 'merchant', 'order.storeOrder', 'delete', '[]', 0, 0),
(157, 46, '', '添加订单备注', 'merchant', 'order.storeOrder', 'remark', '[]', 0, 0),
(158, 46, '', '保存不退款信息', 'merchant', 'order.storeOrder', 'updateRefundN', '[]', 0, 0),
(159, 46, '', '不退款保存页面', 'merchant', 'order.storeOrder', 'refund_n', '[]', 0, 0),
(160, 46, '', '订单详情', 'merchant', 'order.storeOrder', 'order_info', '[]', 0, 0),
(161, 46, '', '退款处理', 'merchant', 'order.storeOrder', 'updateRefundY', '[]', 0, 0),
(162, 46, '', '退款页面', 'merchant', 'order.storeOrder', 'refund_y', '[]', 0, 0),
(163, 46, '', '保存配送信息', 'merchant', 'order.storeOrder', 'updateDistribution', '[]', 0, 0),
(164, 46, '', '修改配送信息', 'merchant', 'order.storeOrder', 'distribution', '[]', 0, 0),
(165, 46, '', '确认收货', 'merchant', 'order.storeOrder', 'take_delivery', '[]', 0, 0),
(166, 46, '', '发货保存', 'merchant', 'order.storeOrder', 'updateDeliveryGoods', '[]', 0, 0),
(167, 46, '', '发货', 'merchant', 'order.storeOrder', 'deliver_goods', '[]', 0, 0),
(168, 46, '', '保存订单金额', 'merchant', 'order.storeOrder', 'update', '[]', 0, 0),
(169, 46, '', '修改订单金额', 'merchant', 'order.storeOrder', 'edit', '[]', 0, 0),
(170, 46, '', '订单展示页面', 'merchant', 'order.storeOrder', 'order_list', '[]', 0, 0),
(171, 47, '', '删除订单', 'merchant', 'ump.eventRegistration', 'order_delete', '[]', 0, 0),
(172, 47, '', '订单退款', 'merchant', 'ump.eventRegistration', 'refund_y', '[]', 0, 0),
(173, 47, '', '订单展示页面', 'merchant', 'ump.eventRegistration', 'order', '[]', 0, 0),
(174, 48, '', '删除订单', 'merchant', 'order.dataDownloadOrder', 'delete', '[]', 0, 0),
(175, 48, '', '订单退款', 'merchant', 'order.dataDownloadOrder', 'refund_y', '[]', 0, 0),
(176, 48, '', '退款提交', 'merchant', 'order.dataDownloadOrder', 'updateRefundY', '[]', 0, 0),
(177, 48, '', '订单详情', 'merchant', 'order.dataDownloadOrder', 'order_info', '[]', 0, 0),
(178, 48, '', '订单展示页面', 'merchant', 'order.dataDownloadOrder', 'data_download_order_list', '[]', 0, 0),
(179, 6, '', '提现操作', 'merchant', 'finance.userExtract', 'extract', '[]', 0, 0),
(180, 52, '', '收益提现', 'merchant', 'live.aliyunLive', 'rawal', '[]', 0, 0),
(181, 3, '', '设置保存', 'merchant', 'system', 'edit_merchant', '[]', 0, 0),
(182, 9, '', '添加管理员', 'merchant', 'merchant.merchantAdmin', 'create', '[]', 0, 0),
(183, 9, '', '编辑管理员', 'merchant', 'merchant.merchantAdmin', 'edit', '[]', 0, 0),
(184, 9, '', '管理员展示', 'merchant', 'merchant.merchantAdmin', 'index', '[]', 0, 0),
(185, 3, '', '设置展示', 'merchant', 'system', 'index', '[]', 0, 0),
(186, 52, '', '收益展示', 'merchant', 'live.aliyunLive', 'mer_live_reward_list', '[]', 0, 0),
(187, 6, '', '提现展示', 'merchant', 'finance.userExtract', 'get_mer_user_extract', '[]', 0, 0),
(188, 14, '', '题库管理', 'merchant', '', '', '[]', 5, 1),
(189, 188, '', '题库分类', 'merchant', 'questions.questionsCategpry', 'index', '[]', 2, 1),
(190, 188, '', '题库列表', 'merchant', 'questions.questions', 'index', '[]', 1, 1),
(191, 189, '', '题库分类展示', 'merchant', 'questions.questionsCategpry', 'index', '[]', 4, 0),
(192, 189, '', '分类编辑', 'merchant', 'questions.questionsCategpry', 'create', '[]', 3, 0),
(193, 189, '', '分类保存', 'merchant', 'questions.questionsCategpry', 'save', '[]', 2, 0),
(194, 189, '', '分类删除', 'merchant', 'questions.questionsCategpry', 'delete', '[]', 1, 0),
(195, 190, '', '题库列表展示', 'merchant', 'questions.questions', 'index', '[]', 10, 0),
(196, 190, '', '试题编辑', 'merchant', 'questions.questions', 'add', '[]', 9, 0),
(197, 190, '', '试题保存', 'merchant', 'questions.questions', 'save_add', '[]', 8, 0),
(198, 190, '', '试题删除', 'merchant', 'questions.questions', 'delete', '[]', 7, 0),
(199, 190, '', '导入试题', 'merchant', 'questions.questions', 'imports', '[]', 6, 0),
(200, 190, '', '上传文件', 'merchant', 'questions.questions', 'file_import_upload', '[]', 5, 0),
(201, 190, '', '导出试题', 'merchant', 'questions.questions', 'getQuestionsList', '[]', 4, 0),
(202, 190, '', '下载模版', 'merchant', 'questions.questions', 'downloadExcel', '[]', 2, 0),
(203, 190, '', '试题导入', 'merchant', 'questions.questions', 'importTestQuestions', '[]', 3, 0),
(204, 14, '', '证书管理', 'merchant', '', '', '[]', 4, 1),
(205, 14, '', '试卷管理', 'merchant', '', '', '[]', 3, 1),
(206, 204, '', '证书列表', 'merchant', 'questions.certificate', 'index', '[]', 2, 1),
(207, 204, '', '证书记录', 'merchant', 'questions.certificate', 'record', '[]', 1, 1),
(208, 206, '', '证书列表展示', 'merchant', 'questions.certificate', 'index', '[]', 4, 0),
(209, 206, '', '证书编辑', 'merchant', 'questions.certificate', 'add', '[]', 3, 0),
(210, 206, '', '证书保存', 'merchant', 'questions.certificate', 'save_add', '[]', 2, 0),
(211, 206, '', '证书删除', 'merchant', 'questions.certificate', 'delete', '[]', 1, 0),
(212, 207, '', '证书记录展示', 'merchant', 'questions.certificate', 'record', '[]', 3, 0),
(213, 207, '', '证书记录删除', 'merchant', 'questions.certificate', 'deleteRecord', '[]', 2, 0),
(214, 207, '', '证书记录撤销', 'merchant', 'questions.certificate', 'revokeRecord', '[]', 1, 0),
(215, 205, '', '练习管理', 'merchant', '', '', '[]', 2, 1),
(216, 205, '', '考试管理', 'merchant', '', '', '[]', 1, 1),
(217, 215, '', '练习列表', 'merchant', 'questions.testPaper', 'index', '{\"type\":\"1\"}', 2, 1),
(218, 215, '', '练习记录', 'merchant', 'questions.testPaper', 'answerNotes','{\"type\":\"1\"}', 1, 1),
(219, 217, '', '试卷展示', 'merchant', 'questions.testPaper', 'index', '[]', 10, 0),
(220, 217, '', '试卷编辑', 'merchant', 'questions.testPaper', 'add', '[]', 9, 0),
(221, 217, '', '试卷保存', 'merchant', 'questions.testPaper', 'save_add', '[]', 8, 0),
(222, 217, '', '试卷删除', 'merchant', 'questions.testPaper', 'delete', '[]', 7, 0),
(223, 217, '', '关联专题保存', 'merchant', 'questions.testPaper', 'related_courses', '[]', 6, 0),
(224, 216, '', '考试列表', 'merchant', 'questions.testPaper', 'index', '{\"type\":\"2\"}', 2, 1),
(225, 216, '', '考试记录', 'merchant', 'questions.testPaper', 'answerNotes', '{\"type\":\"2\"}', 1, 1),
(226, 224, '', '试卷展示', 'merchant', 'questions.testPaper', 'index', '[]', 9, 0),
(227, 224, '', '试卷编辑', 'merchant', 'questions.testPaper', 'add', '[]', 8, 0),
(228, 224, '', '试卷保存', 'merchant', 'questions.testPaper', 'save_add', '[]', 7, 0),
(229, 224, '', '试卷删除', 'merchant', 'questions.testPaper', 'delete', '[]', 6, 0),
(230, 224, '', '关联专题保存', 'merchant', 'questions.testPaper', 'related_courses', '[]', 5, 0),
(231, 17, '', '考试订单', 'merchant', 'order.testPaperOrder', 'index', '[]', 0, 1),
(232, 231, '', '订单展示页面', 'merchant', 'order.testPaperOrder', 'test_paper_order_list', '[]', 5, 0),
(233, 231, '', '订单详情', 'merchant', 'order.testPaperOrder', 'order_info', '[]', 4, 0),
(234, 231, '', '退款提交', 'merchant', 'order.testPaperOrder', 'updateRefundY', '[]', 3, 0),
(235, 231, '', '订单退款', 'merchant', 'order.testPaperOrder', 'refund_y', '[]', 2, 0),
(236, 231, '', '删除订单', 'merchant', 'order.testPaperOrder', 'delete', '[]', 1, 0),
(237, 2, '', '用户关注', 'merchant', 'merchant.merchantFollow', 'index', '[]', 4, 1),
(238, 2, '', '客服管理', 'merchant', '', '', '[]', 3, 1),
(239, 238, '', '客服列表', 'merchant', 'wechat.storeService', 'index', '[]', 1, 1),
(240, 238, '', '客服配置', 'merchant', 'wechat.storeService', 'service', '[]', 2, 1);

--
-- 表的结构 `eb_phone_user`
--
CREATE TABLE `eb_phone_user` (
  `id` int(10) UNSIGNED NOT NULL,
  `phone` varchar(11) NOT NULL DEFAULT '' COMMENT '手机号码',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '用户头像',
  `nickname` varchar(255) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT 'user用户id,不关联为0',
  `grade_id` int(11) NOT NULL DEFAULT '0' COMMENT '年级id',
  `new_money` decimal(9,2) NOT NULL DEFAULT '0.00' COMMENT '用户余额',
  `level` tinyint(1) NOT NULL DEFAULT '0' COMMENT '用户等级',
  `add_ip` varchar(16) NOT NULL DEFAULT '' COMMENT '添加ip',
  `pwd` varchar(32) NOT NULL DEFAULT '' COMMENT '登录密码',
  `spread_uid` int(11) NOT NULL DEFAULT '0' COMMENT '推广员id',
  `is_promoter` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为推广员',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `last_time` int(12) NOT NULL DEFAULT '0' COMMENT '登录时间',
  `last_ip` varchar(16) NOT NULL DEFAULT '' COMMENT '登录id',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否禁止,0=禁止,1=开启'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='手机号码注册用户表';

--
-- 表的结构 `eb_questions`
--
CREATE TABLE `eb_questions` (
  `id` int(10) UNSIGNED NOT NULL,
  `mer_id` int(12) DEFAULT '0' COMMENT '讲师id',
  `question_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '题型区别1=单选题 2=多选题 3=判断题',
  `pid` int(10) NOT NULL DEFAULT '0' COMMENT '分类ID',
  `stem` text NOT NULL COMMENT '题干',
  `image` varchar(255) DEFAULT NULL COMMENT '配图',
  `is_img` tinyint(1) DEFAULT '0' COMMENT '是否纯图片',
  `option` text COMMENT '选项',
  `answer` varchar(255) NOT NULL DEFAULT '' COMMENT '答案',
  `difficulty` int(2) NOT NULL DEFAULT '0' COMMENT '难度',
  `analysis` longtext COMMENT '答案解析',
  `relation` varchar(255) DEFAULT NULL COMMENT '关联专题',
  `number` int(5) DEFAULT '0' COMMENT '答题人数',
  `sort` int(5) DEFAULT '0' COMMENT '排序',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='试题表';

--
-- 表的结构 `eb_questions_categpry`
--
CREATE TABLE `eb_questions_categpry` (
  `id` int(10) UNSIGNED NOT NULL,
  `mer_id` int(12) DEFAULT '0' COMMENT '讲师id',
  `pid` int(10) NOT NULL DEFAULT '0' COMMENT '父级ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '分类标题',
  `sort` int(5) DEFAULT '0' COMMENT '排序',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COMMENT='试题分类';

--
-- 表的结构 `eb_pc_recommend`
--
CREATE TABLE `eb_web_recommend` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '标题',
  `explain` varchar(255) DEFAULT NULL COMMENT '说明',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=专题,1=直播,2=讲师,3=资料,4=新闻,5=课程推荐,6=自定义跳转,7=练习,8=考试',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `link` varchar(255) DEFAULT NULL COMMENT '自定义跳转路径',
  `is_fixed` tinyint(1) DEFAULT '0' COMMENT '0=前端首页推荐 1=首页导航',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否上架',
  `show_count` int(11) NOT NULL DEFAULT '0' COMMENT '展示多少个',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='首页推荐';

--
-- 表的结构 `eb_web_recommend_relation`
--
CREATE TABLE `eb_web_recommend_relation` (
  `id` int(11) UNSIGNED NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=专题,1=直播,2=讲师,3=资料,4=新闻,5=课程推荐,6=自定义跳转,7=练习,8=考试',
  `recommend_id` int(11) NOT NULL COMMENT '推荐id',
  `link_id` int(11) NOT NULL DEFAULT '0' COMMENT '关联id',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='主页推荐关联表';

--
-- 表的结构 `eb_recommend`
--
CREATE TABLE `eb_recommend` (
  `id` int(11) UNSIGNED NOT NULL,
  `typesetting` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1=大图,2=宫图,3=小图,4=纯图,5=固定',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '标题',
  `link` varchar(255) NOT NULL DEFAULT '' COMMENT '自定义跳转路径',
  `type` int(5) NOT NULL DEFAULT '0' COMMENT '0=专题,1=新闻,2=直播,3=自定义跳转,4=商品,5=热门直播,6=讲师,7=活动,8=拼团,9=菜单,10=素材,11=练习,12=考试,13=首页广告,14=资料',
  `number` int(11) NOT NULL DEFAULT '0' COMMENT '数量',
  `relation_ids` varchar(1000) NOT NULL DEFAULT '' COMMENT '关联id',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '图标',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '封面图',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `is_fixed` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=前端首页推荐 1=首页导航 2=个人中心菜单',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否上架',
  `grade_id` int(11) NOT NULL DEFAULT '0' COMMENT '年级id',
  `show_count` int(11) NOT NULL DEFAULT '0' COMMENT '展示多少个',
  `is_promoter` int(2) NOT NULL DEFAULT '0' COMMENT '是否需要推广权限',
  `is_write_off` tinyint(1) DEFAULT '0' COMMENT '是否核销权限',
  `is_lecturer` tinyint(1) DEFAULT '0' COMMENT '是否是讲师菜单'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='首页推荐';

--
-- 转存表中的数据 `eb_recommend` 添加数据
--
INSERT INTO `eb_recommend` (`id`, `typesetting`, `title`, `link`, `type`, `number`, `relation_ids`, `add_time`, `icon`, `image`, `sort`, `is_fixed`, `is_show`, `grade_id`, `show_count`,`is_promoter`,`is_write_off`,`is_lecturer`) VALUES
(17, 1, '课程推荐', '', 0, 0, '', 1581402048, '', '', 6, 0, 1, 1, 10,0,0,0),
(18, 0, '小学', '', 0, 0, '', 1581415908, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/34176202008191447346762.png', '', 10, 1, 1, 4, 0,0,0,0),
(20, 0, '高中', '', 0, 0, '', 1581416083, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/01ccc202008191450103239.png', '', 8, 1, 1, 2, 0,0,0,0),
(21, 0, '少儿编程', '', 0, 0, '', 1581416143, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/b101120200819145017271.png', '', 7, 1, 1, 1, 0,0,0,0),
(22, 0, '绘画', '', 0, 0, '', 1581416164, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/6f8ce202008191450353008.png', '', 6, 1, 1, 5, 0,0,0,0),
(23, 0, '公考', '', 0, 0, '', 1581416198, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/2593c202008191450273923.png', '', 5, 1, 1, 6, 0,0,0,0),
(24, 0, '小语种', '', 0, 0, '', 1581416219, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/b461d202008191451235187.png', '', 4, 1, 1, 7, 0,0,0,0),
(25, 0, '考研', '', 0, 0, '', 1581416246, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/032d4202008191450437213.png', '', 3, 1, 1, 8, 0,0,0,0),
(26, 2, '精品课程', '', 0, 0, '', 1581417034, '', '', 8, 0, 1, 4, 100,0,0,0),
(27, 0, '我的课程', '/wap/special/grade_special.html', 3, 0, '', 1581502207, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/31685202008190947085097.png', '', 2, 1, 1, 0, 0,0,0,0),
(28, 0, '我的收藏', '/wap/special/grade_list/type/1.html', 3, 0, '', 1581502283, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/d17f620200819094709564.png', '', 1, 1, 1, 0, 0,0,0,0),
(30, 2, '热销商品', '', 4, 0, '', 1581921700, '', '', 7, 0, 1, 4, 10,0,0,0),
(31, 4, '在线直播', '', 0, 0, '', 1582180461, '', '', 9, 0, 1, 4, 10,0,0,0),
(32, 5, '热门直播', '', 5, 0, '', 0, '', '', 5, 0, 1, 0, 0,0,0,0),
(33, 0, '讲师', '', 6, 0, '', 0, '', '', 4, 0, 1, 0, 0,0,0,0),
(34, 5, '线下活动', '', 7, 0, '', 0, '', '', 3, 0, 1, 0, 0,0,0,0),
(35, 5, '首页广告', '', 13, 0, '', 0, '', '', 10, 0, 1, 0, 0,0,0,0),
(36, 0, '签到打卡', '/wap/my/sign_in', 3, 0, '', 1615539490, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/1041b202103121654522618.png', '', 20, 2, 1, 0, 0, 0,0,0),
(37, 0, '会员中心', '/wap/special/member_recharge', 3, 0, '', 1615539569, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/01288202103121654535295.png', '', 19, 2, 1, 0, 0, 0,0,0),
(38, 0, '推广中心', '/wap/spread/spread', 3, 0, '', 1615539616, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/68fe1202103121654539278.png', '', 18, 2, 1, 0, 0, 1,0,0),
(39, 0, '拼团课程', '/wap/my/order_list', 3, 0, '', 1615539658, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/9e80e202103121654505916.png', '', 17, 2, 1, 0, 0, 0,0,0),
(40, 0, '我的报名', '/wap/my/sign_list', 3, 0, '', 1615539688, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/d17f1202103121654537270.png', '', 16, 2, 1, 0, 0, 0,0,0),
(41, 0, '报名核销', '/wap/my/verify_activity.html', 3, 0, '', 1640339102, 'http://lsq123456789.oss-cn-shenzhen.aliyuncs.com/f324b202112241744099150.png', '', 15, 2, 1, 0, 0, 0,1,0),
(42, 0, '我的赠送', '/wap/my/my_gift', 3, 0, '', 1615539728, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/68d0e202103121654545242.png', '', 14, 2, 1, 0, 0, 0,0,0),
(43, 0, '我的余额', '/wap/my/bill_detail', 3, 0, '', 1615539761, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/e3997202103121654544341.png', '', 13, 2, 1, 0, 0, 0,0,0),
(44, 0, '充值金币', '/wap/special/recharge_index', 3, 0, '', 1615539804, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/38d11202103121654528899.png', '', 12, 2, 1, 0, 0, 0,0,0),
(45, 0, '我的证书', '/wap/topic/certificate_list', 3, 0, '', 1621390614, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/cf692202105191016134752.png', '', 11, 2, 1, 0, 0, 0,0,0),
(46, 0, '我的关注', '/wap/my/follow_lecturer', 3, 0, '', 1657610629, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/26aac202105191016159737.png', '', 10, 2, 1, 0, 0, 0, 0, 0),
(47, 0, '我的收藏', '/wap/special/grade_list/type/1', 3, 0, '', 1621390645, 'http://lsq123456789.oss-cn-shenzhen.aliyuncs.com/359e2202207121521453165.png', '', 9, 2, 1, 0, 0, 0,0,0),
(48, 0, '地址管理', '/wap/my/address', 3, 0, '', 1615539836, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/13485202103121654532452.png', '', 9, 2, 1, 0, 0, 0,0,0),
(49, 0, '讲师申请', '/wap/merchant/index', 3, 0, '', 1650425785, 'http://lsq123456789.oss-cn-shenzhen.aliyuncs.com/ca1bc202204201135534439.png', '', 7, 2, 1, 0, 0, 0, 0, 1),
(50, 0, '讲师中心', '/wap/merchant/teacher_detail', 3, 0, '', 1650425999, 'http://lsq123456789.oss-cn-shenzhen.aliyuncs.com/ca1bc202204201135534439.png', '', 6, 2, 1, 0, 0, 0, 0, 2),
(51, 0, '我的资料', '/wap/material/my_material', 3, 0, '', 1627014391, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/29da320210723122536524.png', '', 5, 2, 1, 0, 0, 0,0,0),
(52, 0, '关于我们', '/wap/my/about_us', 3, 0, '', 1615539868, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/d86d1202103121654532734.png', '', 4, 2, 1, 0, 0, 0,0,0),
(53, 0, '咨询客服', '/wap/service/service_list', 3, 0, '', 1615539906, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/9eb63202107231437158177.png', '', 3, 2, 1, 0, 0, 0,0,0);

-- --------------------------------------------------------
--
-- 表的结构 `eb_recommend_banner`
--
CREATE TABLE `eb_recommend_banner` (
  `id` int(11) UNSIGNED NOT NULL,
  `recommend_id` int(11) NOT NULL DEFAULT '0' COMMENT '推荐id',
  `pic` varchar(300) NOT NULL DEFAULT '' COMMENT '图片链接',
  `url` varchar(300) NOT NULL DEFAULT '' COMMENT '跳转地址',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `is_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否显示',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='首页推荐banner';

--
-- 转存表中的数据 `eb_recommend_banner`
--
INSERT INTO `eb_recommend_banner` (`id`, `recommend_id`, `pic`, `url`, `sort`, `is_show`, `add_time`) VALUES
(6, 26, 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/10f3d202002111757429313.jpg', '#', 0, 1, 1581924394);

-- --------------------------------------------------------

--
-- 表的结构 `eb_recommend_relation`
--
CREATE TABLE `eb_recommend_relation` (
  `id` int(11) UNSIGNED NOT NULL,
  `type` int(5) NOT NULL DEFAULT '0' COMMENT '0=专题,1=新闻,2=直播,3=自定义跳转,4=商品,5=热门直播,6=讲师,7=活动,8=拼团,9=菜单,10=素材,11=练习,12=考试,13=首页广告,14=资料',
  `recommend_id` int(11) NOT NULL DEFAULT '0' COMMENT '推荐id',
  `link_id` int(11) NOT NULL DEFAULT '0' COMMENT '关联id',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='主页推荐关联表';

--
-- 转存表中的数据 `eb_recommend_relation`
--
INSERT INTO `eb_recommend_relation` (`id`, `type`, `recommend_id`, `link_id`, `sort`, `add_time`) VALUES
(1, 0, 31, 1, 0, 1588047108),
(2, 0, 26, 2, 0, 1588047166),
(3, 0, 17, 3, 0, 1588047184),
(4, 0, 26, 3, 0, 1588047194),
(5, 0, 17, 4, 0, 1588047207),
(6, 0, 26, 5, 0, 1588047333),
(7, 0, 17, 5, 0, 1588047341);

-- --------------------------------------------------------
CREATE TABLE `eb_relation` (
  `id` int(10) UNSIGNED NOT NULL,
  `relationship` tinyint(1) NOT NULL DEFAULT '0' COMMENT '关系1=专题关联练习2=专题关联考试3=试题关联专题4=专题关联资料5=商品关联专题',
  `relationship_id` int(10) NOT NULL DEFAULT '0' COMMENT '关系ID',
  `relation_id` int(10) NOT NULL DEFAULT '0' COMMENT '被关联ID',
  `sort` int(5) DEFAULT '0' COMMENT '排序',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '是否删除',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='专题、试卷、试题、证书关联表';

--
-- 转存表中的数据 `eb_reply_false`
--
CREATE TABLE `eb_reply_false` (
  `id` int(12) NOT NULL,
  `reply_id` int(12) NOT NULL DEFAULT '0' COMMENT '评论ID',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=专题1=商品',
  `nickname` varchar(255) NOT NULL DEFAULT '' COMMENT '用户名',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='虚拟评论';

--
-- 表的结构 `eb_routine_access_token`
--
CREATE TABLE `eb_routine_access_token` (
  `id` int(11) UNSIGNED NOT NULL  COMMENT '小程序access_token表ID',
  `access_token` varchar(256) NOT NULL DEFAULT '' COMMENT 'openid',
  `stop_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='小程序access_token表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_routine_form_id`
--
CREATE TABLE `eb_routine_form_id` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '表单ID表ID',
  `uid` int(11) DEFAULT '0' COMMENT '用户uid',
  `form_id` varchar(32) NOT NULL DEFAULT '' COMMENT '表单ID',
  `stop_time` int(12) UNSIGNED DEFAULT NULL COMMENT '表单ID失效时间',
  `status` tinyint(1) UNSIGNED DEFAULT '0' COMMENT '状态1 未使用 2不能使用'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='表单id表记录表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_routine_template`
--
CREATE TABLE `eb_routine_template` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '模板id',
  `tempkey` char(50) NOT NULL DEFAULT '' COMMENT '模板编号',
  `name` char(100) NOT NULL DEFAULT '' COMMENT '模板名',
  `content` varchar(1000) NOT NULL DEFAULT '' COMMENT '回复内容',
  `tempid` char(100) DEFAULT NULL COMMENT '模板ID',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订阅消息模版';

-- --------------------------------------------------------

INSERT INTO `eb_routine_template`(`id`,`tempkey`, `name`, `content`, `tempid`, `add_time`, `status`) VALUES
(1,'16172', '直播开播提醒', '直播名称{{thing5.DATA}}\n开播时间{{time2.DATA}}', 'TnX4gJF1WqAxuYxOkhlqWZyhgNGnQtmKlfn8vi_vHR0', '1617846740', 1),
(2,'5275', '拼团进度通知', '商品名称{{thing1.DATA}}\n已拼人数{{number6.DATA}}\n温馨提醒{{thing4.DATA}}', 'r2ehtNIsXchmOKLUuhg5DC96O-2-xKkLyI88q640aus', '1617846791', 1),
(3,'4533', '开团成功提醒', '商品名称{{thing5.DATA}}\n开团价格{{amount4.DATA}}\n成团人数{{thing7.DATA}}\n开团时间{{date1.DATA}}', 'hl-2ZYOmw6mBFW36bXngGuEgPskdLmMAeYEhJvsEIfI', '1617846868', 1),
(4,'6271', '订单支付成功通知', '订单编号{{character_string1.DATA}}\n订单金额{{amount3.DATA}}\n支付时间{{time2.DATA}}\n备注{{thing6.DATA}}', '065lubx3Gg6zkSdQULmA91VmfvC7nd7a5tEmA_5aU5Y', '1617846920', 1),
(5,'4534', '拼团失败通知', '商品名称{{thing1.DATA}}\n商品金额{{amount8.DATA}}\n退款金额{{amount5.DATA}}', 'ndgwreIddk1Bi4p5eJGvkxxy9aBpj1LIU_k3T0Dbj7I', '1617846971', 1),
(6,'3098', '拼团成功通知', '订单号{{character_string10.DATA}}\n商品名称{{thing7.DATA}}\n备注{{thing9.DATA}}', 'y2i2_uYAK8LYc1kCXKXeCIKsnHPjo9MnGuepRrAr7k0', '1617847017', 1),
(7,'4148', '账户余额变动提醒', '变动类型{{thing8.DATA}}\n时间{{date4.DATA}}\n变动金额{{amount1.DATA}}\n账户余额{{amount2.DATA}}\n备注{{thing5.DATA}}', 'jVIfUXIWkdinesHaFsSHDAvkkDgOO1mO9_7PLUM8gm8', '1617847076', 1),
(8,'1451', '退款通知', '订单编号{{character_string7.DATA}}\n下单时间{{time8.DATA}}\n退款金额{{amount3.DATA}}', 'tAYhnrZ3FEH_yOCvLfRRK42DiEh97UQn2P_8qCOlrn8', '1617847129', 1),
(9,'3510', '收货提醒通知', '订单编号{{character_string9.DATA}}\n商品名称{{thing2.DATA}}\n确认收货时间{{date8.DATA}}', 'bZiuqzZv7fyfgQ6_oilWpKp3XZSbs-4ufYsQIxRws64', '1617847219', 1),
(10,'3637', '订单发货通知', '订单号{{character_string2.DATA}}\n快递公司{{thing4.DATA}}\n快递单号{{character_string5.DATA}}', 'PVHY84-y5jGdbL5tPsnVybk9OXDI8F_Jhyyv4Z7X22A', '1617847273', 1),
(11,'13684', '报名成功通知', '活动名称{{thing1.DATA}}\n活动时间{{time2.DATA}}\n活动地点{{thing6.DATA}}', 'pmb3nzn6YPDNUgChb_RgedCSIegqjh1A-8fMUzz1dUc', '1640228237', 1),
(12,'5545', '审核结果通知', '审核结果{{phrase5.DATA}}\n审核时间{{time24.DATA}}\n备注{{thing4.DATA}}', 'Zd6tVG6Ynys1LWIgLKTQVj1gfcnyhMvx4H5VA882VCQ', '1646293339', 1);

--
-- 表的结构 `eb_search`
--
CREATE TABLE `eb_search` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '关键词名',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='关键词搜索设置';

--
-- 转存表中的数据 `eb_search`
--

INSERT INTO `eb_search` (`id`, `name`, `add_time`) VALUES
(29, '语文', 1581476333),
(30, '数学', 1581476338),
(31, '英语', 1581476343);

-- --------------------------------------------------------

--
-- 表的结构 `eb_search_history`
--
CREATE TABLE `eb_search_history` (
  `id` int(11) UNSIGNED NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0',
  `search` varchar(255) NOT NULL DEFAULT '' COMMENT '搜索内容',
  `add_time` int(12) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='搜索历史记录';

--
-- 表的结构 `eb_sign_poster`
--
CREATE TABLE `eb_sign_poster` (
  `id` int(10) UNSIGNED NOT NULL,
  `sign_time` int(12) unsigned NOT NULL DEFAULT '0' COMMENT '签到时间',
  `poster` varchar(255) DEFAULT NULL COMMENT '分享海报',
  `sign_talk` varchar(255) DEFAULT NULL COMMENT '签到语录',
  `sort` int(5) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `add_time` int(12) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='签到海报';

--
-- 表的结构 `eb_sms_code`
--
CREATE TABLE `eb_sms_code` (
  `id` int(5) UNSIGNED NOT NULL COMMENT 'id',
  `tel` varchar(20) NOT NULL DEFAULT '' COMMENT '电话号码',
  `uid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '谁发送的',
  `code` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '验证码',
  `last_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '过期时间',
  `is_use` int(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否已使用'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `eb_special`
--
CREATE TABLE `eb_special` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `subject_id` int(11) NOT NULL DEFAULT '0' COMMENT '分类id',
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '所属管理者id',
  `lecturer_id` int(11) DEFAULT '0' COMMENT '讲师ID',
  `mer_id` int(12) DEFAULT '0' COMMENT '讲师id',
  `type` tinyint(1) NOT NULL DEFAULT '6' COMMENT '专题类型1：图文专题；2：音频专题；3：视频专题；4：直播专题；5：专栏；6：单素材专题',
  `is_light` tinyint(1) DEFAULT '0' COMMENT '是否为轻专题',
  `light_type` tinyint(1) DEFAULT '0' COMMENT '轻专题类型 1：图文 2:音频 3:视频',
  `is_mer_visible` tinyint(1) DEFAULT '0' COMMENT '是否会员可见',
  `abstract` text COMMENT '简介',
  `phrase` varchar(2000) NOT NULL DEFAULT '' COMMENT '短语',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '封面图',
  `label` varchar(1000) NOT NULL DEFAULT '' COMMENT '标签',
  `banner` varchar(2000) NOT NULL DEFAULT '' COMMENT 'banner图',
  `poster_image` varchar(255) NOT NULL DEFAULT '' COMMENT '海报',
  `service_code` varchar(255) NOT NULL DEFAULT '' COMMENT '客服二维码',
  `is_live` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否为直播',
  `money` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '金额',
  `pink_money` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '拼团价格',
  `is_pink` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否开启拼团',
  `pink_number` int(11) NOT NULL DEFAULT '0' COMMENT '拼团人数',
  `pink_strar_time` int(12) NOT NULL DEFAULT '0' COMMENT '拼团开始时间',
  `pink_end_time` int(12) NOT NULL DEFAULT '0' COMMENT '拼团结束时间',
  `pink_time` int(12) NOT NULL DEFAULT '0' COMMENT '拼团时间',
  `is_fake_pink` tinyint(1) NOT NULL DEFAULT '0' COMMENT '虚拟拼团是否开启',
  `fake_pink_number` int(11) NOT NULL DEFAULT '0' COMMENT '虚拟拼团人数',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `score` int(5) DEFAULT '0' COMMENT '评分',
  `sales` int(11) NOT NULL DEFAULT '0' COMMENT '实际销量',
  `validity` int(5) DEFAULT '0' COMMENT '课程有效期(d)',
  `fake_sales` int(11) NOT NULL DEFAULT '0' COMMENT '虚拟销量',
  `browse_count` int(11) NOT NULL DEFAULT '0' COMMENT '浏览量',
  `pay_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支付类型',
  `member_pay_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '会员支付类型0：免费，1付费',
  `member_money` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '会员价格',
  `link` varchar(512) NOT NULL DEFAULT '' COMMENT '音频视频专题链接',
  `is_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否显示',
  `is_alone` tinyint(1) DEFAULT '0' COMMENT '是否开启单独分销',
  `brokerage_ratio` int(3) DEFAULT '0' COMMENT '一级推广人返佣比例',
  `brokerage_two` int(3) DEFAULT '0' COMMENT '二级推广人返佣比例',
  `quantity` int(5) NOT NULL DEFAULT '0' COMMENT '素材数量',
  `sum` int(5) NOT NULL DEFAULT '0' COMMENT '预计素材数量',
  `sort_order` tinyint(1) DEFAULT '0' COMMENT '素材排序方法 0 倒序 1 正序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 -1:未通过 0:待审核 1:审核通过',
  `success_time` int(12) NOT NULL DEFAULT '0' COMMENT '通过时间',
  `fail_message` varchar(256) NOT NULL DEFAULT '' COMMENT '未通过原因',
  `fail_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '未通过时间',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='专题';

--
-- 转存表中的数据 `eb_special` 添加数据
--
INSERT INTO `eb_special` (`id`, `title`, `subject_id`, `admin_id`, `lecturer_id`, `mer_id`, `type`, `is_light`, `light_type`, `is_mer_visible`, `abstract`, `phrase`, `image`, `label`, `banner`, `poster_image`, `service_code`, `is_live`, `money`, `pink_money`, `is_pink`, `pink_number`, `pink_strar_time`, `pink_end_time`, `pink_time`, `is_fake_pink`, `fake_pink_number`, `sort`, `score`, `sales`, `validity`, `fake_sales`, `browse_count`, `pay_type`, `member_pay_type`, `member_money`, `link`, `is_show`, `is_alone`, `brokerage_ratio`, `brokerage_two`, `quantity`, `sum`, `status`, `success_time`, `fail_message`, `fail_time`, `is_del`, `add_time`) VALUES
(1, '直播国学大讲堂', 5, 1, 0, 0, 4, 0, 0, 0, '直播国学大讲堂', '直播国学大讲堂', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/ce99d202002201232432554.jpg', '[\"\\u56fd\\u5b66\\u8bb2\\u5802\"]', '[\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/ce99d202002201232432554.jpg\"]', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/ce99d202002201232432554.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/bd134202109101459407925.png', 0, 3.00, 1.00, 0, 4, 0, 0, 12, 1, 1, 0, 0, 0, 0, 2147483647, 193, 1, 0, 0.00, '', 1, 1, 2, 1, 0, 0, 1, 0, '', 0, 0, 1588044359),
(2, '图文专题', 6, 0, 0, 0, 1, 0, 0, 0, '图文专题简介', '图文专题短语', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/20362202002201412303972.jpg', '[\"\\u56fe\\u6587\\u4e13\\u9898\"]', '[\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/f4313202002201232435004.jpg\"]', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/3ec76202002201232442683.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/32095202002111804187328.jpg', 0, 5.00, 0.00, 0, 0, 0, 0, 0, 1, 0, 2, 0, 0, 0, 0, 105, 1, 1, 3.00, '', 1, 0, 0, 0, 0, 0, 1, 0, '', 0, 0, 1588045424),
(3, '音频专题', 7, 0, 0, 0, 2, 0, 0, 0, '音频专题简介', '音频专题', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/20362202002201412303972.jpg', '[\"\\u97f3\\u9891\\u4e13\\u9898\"]', '[\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/8ab46202002201232152644.jpg\"]', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/8ab46202002201232152644.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/32095202002111804187328.jpg', 0, 0.20, 0.00, 0, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 101, 1, 0, 0.00, '', 1, 0, 0, 0, 0, 0, 1, 0, '', 0, 0, 1588046472),
(4, '视频专题', 9, 0, 0, 0, 3, 0, 0, 0, '视频专题', '视频专题', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/32379202002201232449232.jpg', '[\"\\u89c6\\u9891\\u4e13\\u9898\"]', '[\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/f4313202002201232435004.jpg\",\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/d41c7202002201232431520.jpg\"]', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/20362202002201412303972.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/32095202002111804187328.jpg', 0, 0.00, 2.00, 0, 3, 0, 0, 24, 1, 1, 0, 0, 0, 0, 1234567, 123463, 0, 0, 0.00, '', 1, 0, 0, 0, 6, 6, 1, 0, '', 0, 0, 1588046648),
(5, '我是专栏', 8, 0, 0, 0, 5, 0, 0, 0, '我是专栏（我里面包含专题）', '我是专栏', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/20362202002201412303972.jpg', '[\"\\u4e13\\u680f\\u4e13\\u9898\"]', '[\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/32379202002201232449232.jpg\"]', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/32379202002201232449232.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/32379202002201232449232.jpg', 0, 1.00, 0.30, 0, 2, 0, 0, 100, 1, 50, 0, 0, 0, 10, 12, 233, 1, 1, 0.50, '', 1, 0, 0, 0, 4, 4, 1, 0, '', 0, 0, 1588046788);
--
-- 表的结构 `eb_special_barrage`
--
CREATE TABLE `eb_special_barrage` (
  `id` int(11) UNSIGNED NOT NULL,
  `special_id` int(11) NOT NULL DEFAULT '0' COMMENT '专题id 0=全部可用',
  `nickname` varchar(255)  NOT NULL DEFAULT '' COMMENT '用户昵称',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '用户图像',
  `action` tinyint(1) NOT NULL DEFAULT '0' COMMENT '动作类型 1=开团，2=参团',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示，默认显示',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='专题弹幕';

--
-- 转存表中的数据 `eb_special_barrage` 添加数据
--
INSERT INTO `eb_special_barrage` (`id`, `special_id`, `nickname`, `avatar`, `action`, `sort`, `is_show`, `add_time`) VALUES
(1, 0, '666', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/6b85d202002111757421971.jpg', 1, 0, 1, 1582011509),
(2, 0, '666', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/6b85d202002111757421971.jpg', 1, 0, 1, 1582011520),
(3, 0, 'yy', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/7f0bd202002111757435432.jpg', 1, 0, 1, 1582018672);

-- --------------------------------------------------------

--
-- 转存表中的数据 `eb_special_batch`
--
CREATE TABLE `eb_special_batch` (
  `id` int(11) unsigned NOT NULL,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '批次名称',
  `special_id` int(12) DEFAULT '0' COMMENT '专题ID',
  `total_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '生成码数量',
  `use_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '使用',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否生效,控制此批次所有卡0：不生效；1：生效',
  `qrcode` varchar(255) NOT NULL DEFAULT '' COMMENT '二维码图路径',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='专题兑换码批次表';

--
-- 表的结构 `eb_special_buy`
--
CREATE TABLE `eb_special_buy` (
  `id` int(11) UNSIGNED NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `order_id` varchar(32) NOT NULL DEFAULT '' COMMENT '订单id',
  `column_id` int(11) DEFAULT '0' COMMENT '是专栏下专题时显示专栏ID',
  `special_id` int(11) NOT NULL DEFAULT '0' COMMENT '专题id',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '获得方式:0=支付获得,1=拼团获得,2=领取礼物获得,3=赠送,4=兑换',
  `validity_time` int(12) DEFAULT '0' COMMENT '有效时间',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '是否删除',
  `is_update` tinyint(1) DEFAULT '0' COMMENT '是否需要更新',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='专题获得记录';

-- --------------------------------------------------------

--
-- 表的结构 `eb_special_content`
--
CREATE TABLE `eb_special_content` (
  `id` int(11) UNSIGNED NOT NULL,
  `special_id` int(11) NOT NULL DEFAULT '0' COMMENT '专题id',
  `is_try` tinyint(1) DEFAULT '0' COMMENT '是否开启试看',
  `try_content` longtext COMMENT '图文试看内容',
  `try_time` int(5) DEFAULT '0' COMMENT '音视频试看时间/分',
  `link` varchar(255) DEFAULT NULL COMMENT '链接',
  `videoId` varchar(255) DEFAULT NULL COMMENT '视频ID',
  `file_name` varchar(255) DEFAULT NULL COMMENT '视频/音频名称',
  `file_type` varchar(255) DEFAULT NULL COMMENT '文件分类',
  `content` longtext COMMENT '内容',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='专栏内容关联表';

--
-- 转存表中的数据 `eb_special_content` 添加数据
--
INSERT INTO `eb_special_content` (`id`, `special_id`,`is_try`,`try_content`,`try_time`, `link`, `videoId`, `file_name`, `file_type`, `content`, `add_time`) VALUES
(1, 1,0,'',0, NULL, NULL, NULL, NULL, '&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;div&gt;&lt;video style=&quot;width: 100%&quot; src=&quot;http://cremb-zsff.oss-cn-beijing.aliyuncs.com/W8rykksS4m6ZtYp27E.mp4&quot; class=&quot;video-ue&quot; controls=&quot;controls&quot;&gt;&lt;/video&gt;&lt;/div&gt;&lt;p&gt;直播简介&lt;br/&gt;&lt;/p&gt;', 1588044359),
(2, 2,0,'',0, NULL, NULL, NULL, NULL, '&lt;p&gt;专题内容&lt;/p&gt;&lt;div&gt;&lt;video style=&quot;width: 100%&quot; src=&quot;http://cremb-zsff.oss-cn-beijing.aliyuncs.com/W8rykksS4m6ZtYp27E.mp4&quot; class=&quot;video-ue&quot; controls=&quot;controls&quot;&gt;&lt;/video&gt;&lt;/div&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', 1588045424),
(3, 3,0,'',0, NULL, NULL, NULL, NULL, '&lt;p&gt;内容&lt;/p&gt;&lt;div&gt;&lt;video style=&quot;width: 100%&quot; src=&quot;http://cremb-zsff.oss-cn-beijing.aliyuncs.com/W8rykksS4m6ZtYp27E.mp4&quot; class=&quot;video-ue&quot; controls=&quot;controls&quot;&gt;&lt;/video&gt;&lt;/div&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', 1588046472),
(4, 4,0,'',0, NULL, NULL, NULL, NULL, '&lt;p&gt;内容&lt;/p&gt;&lt;div&gt;&lt;video style=&quot;width: 100%&quot; src=&quot;http://cremb-zsff.oss-cn-beijing.aliyuncs.com/W8rykksS4m6ZtYp27E.mp4&quot; class=&quot;video-ue&quot; controls=&quot;controls&quot;&gt;&lt;/video&gt;&lt;/div&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', 1588046648),
(5, 5,0,'',0, NULL, NULL, NULL, NULL, '&lt;p&gt;专栏内容&lt;/p&gt;&lt;div&gt;&lt;video style=&quot;width: 100%&quot; src=&quot;http://cremb-zsff.oss-cn-beijing.aliyuncs.com/W8rykksS4m6ZtYp27E.mp4&quot; class=&quot;video-ue&quot; controls=&quot;controls&quot;&gt;&lt;/video&gt;&lt;/div&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', 1588046788);
-- --------------------------------------------------------

--
-- 表的结构 `eb_special_course`
--
CREATE TABLE `eb_special_course` (
  `id` int(11) UNSIGNED NOT NULL,
  `special_id` int(11) NOT NULL DEFAULT '0' COMMENT '专题id',
  `course_name` varchar(100) NOT NULL DEFAULT '' COMMENT '课程名称',
  `is_show` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否上架',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `number` int(11) NOT NULL DEFAULT '0' COMMENT '数量',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='课程(1.1版本后废弃)';

-- --------------------------------------------------------

--
-- 转存表中的数据 `eb_special_exchange`
--
CREATE TABLE `eb_special_exchange` (
  `id` int(11) unsigned NOT NULL,
  `special_id` int(12) NOT NULL DEFAULT '0' COMMENT '专题ID',
  `card_batch_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '卡批次id',
  `exchange_code` char(12) NOT NULL DEFAULT '' COMMENT '兑换码',
  `use_uid` int(11) NOT NULL DEFAULT '0' COMMENT '使用用户',
  `use_time` int(12) NOT NULL DEFAULT '0' COMMENT '使用时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '码状态：0：结束；1：开始',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='兑换码表';

--
-- 表的结构 `eb_special_source`
--
CREATE TABLE `eb_special_source` (
    `id` int(11) UNSIGNED NOT NULL,
    `special_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '专题id',
    `type` tinyint(1) DEFAULT '0' COMMENT '专题类型1：图文专题；2：音频专题；3：视频专题；4：直播专题；5：专栏；6：单素材专题',
    `source_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '素材id(task表id)',
    `pay_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0:免费，1：付费',
    `play_count` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '课程光看次数',
    `sort` int(5) DEFAULT 0 COMMENT '排序',
    `add_time` int(12) NOT NULL DEFAULT 0 COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='素材专题关联表';

--
-- 转存表中的数据 `eb_special_source` 添加数据
--
INSERT INTO `eb_special_source` (`id`, `special_id`,`type`, `source_id`, `pay_status`, `play_count`,`sort`, `add_time`) VALUES
(1, 2, 1, 2, 1, 0,0, 1588045424),
(2, 2, 1, 1, 0, 0,0, 1588045424),
(3, 3, 2, 4, 0, 0,0, 1588046472),
(4, 3, 2, 3, 0, 0,0, 1588046472),
(5, 4, 3, 6, 1, 0,0, 1588046648),
(6, 4, 3, 5, 0, 0,0, 1588046648),
(7, 5, 5, 4, 0, 0,0, 1588046788),
(8, 5, 5, 2, 0, 0,0, 1588046788),
(9, 5, 5, 3, 0, 0,0, 1588046788);

-- --------------------------------------------------------
--
-- 表的结构 `eb_special_record`
--
CREATE TABLE `eb_special_record` (
  `id` int(11) UNSIGNED NOT NULL,
  `special_id` int(11) NOT NULL DEFAULT '0' COMMENT '专题id',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `number` int(11) NOT NULL DEFAULT '0' COMMENT '浏览次数',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(12) NOT NULL DEFAULT '0' COMMENT '再次访问时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学习记录';
-- --------------------------------------------------------

-- --------------------------------------------------------

--
-- 表的结构 `eb_special_relation`
--
CREATE TABLE `eb_special_relation` (
  `id` int(11) UNSIGNED NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=专题,1=资料',
  `link_id` int(11) NOT NULL DEFAULT '0' COMMENT '关联id',
  `category` tinyint(1) NOT NULL DEFAULT '0' COMMENT '类型,1=收藏,2=点赞',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='图文专题点赞收藏';

-- --------------------------------------------------------

--
-- 转存表中的数据 `eb_special_reply`
--
CREATE TABLE `eb_special_reply` (
  `id` int(10) UNSIGNED NOT NULL,
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT 'UID',
  `special_id` int(10) NOT NULL DEFAULT '0' COMMENT '专题id',
  `satisfied_score` tinyint(1) NOT NULL DEFAULT '0' COMMENT '满意度',
  `comment` varchar(512) DEFAULT NULL COMMENT '评论内容',
  `pics` text COMMENT '评论图片',
  `merchant_reply_content` varchar(512) DEFAULT NULL COMMENT '管理员回复内容',
  `merchant_reply_time` int(12) DEFAULT '0' COMMENT '管理员回复时间',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '0未删除1已删除',
  `is_selected` tinyint(1) DEFAULT '0' COMMENT '是否精选',
  `is_reply` tinyint(1) DEFAULT '0' COMMENT '0未回复1已回复',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '评论时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='专题评论';

--
-- 表的结构 `eb_special_subject`
--
CREATE TABLE `eb_special_subject` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
  `pic` varchar(255) DEFAULT '' COMMENT '图标',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `is_show` int(11) NOT NULL DEFAULT '0' COMMENT '是否显示,1=显示,0=隐藏',
  `grade_id` int(11) NOT NULL DEFAULT '0' COMMENT '父级id',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '删除'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='课程分类列表';

--
-- 转存表中的数据 `eb_special_subject` 添加数据
--
INSERT INTO `eb_special_subject` (`id`, `name`, `pic`, `sort`, `is_show`, `grade_id`, `add_time`, `is_del`) VALUES
(1, '小学', '', 0, 1, 0, 1618470289, 0),
(2, '初中', '', 0, 1, 0, 1618470299, 0),
(3, '高中', '', 0, 1, 0, 1618470306, 0),
(4, '大学', '', 0, 1, 0, 1618470313, 0),
(5, '语文', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/15c59202104151509555157.jpeg', 0, 1, 1, 1618470617, 0),
(6, '数学', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/4230e202104151510076554.jpeg', 0, 1, 1, 1618470638, 0),
(7, '初中语文', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/15c59202104151509555157.jpeg', 0, 1, 2, 1618470672, 0),
(8, '初中数学', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/4230e202104151510076554.jpeg', 0, 1, 2, 1618470687, 0),
(9, '初中化学', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/24f0b202104151509382120.jpeg', 0, 1, 2, 1618470703, 0),
(10, '高中语文', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/15c59202104151509555157.jpeg', 0, 1, 3, 1618470727, 0),
(11, '高中数学', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/4230e202104151510076554.jpeg', 0, 1, 3, 1618470741, 0),
(12, '高中生物', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/f7e8e202104151509483381.jpeg', 0, 1, 3, 1618470762, 0),
(13, '高中化学', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/24f0b202104151509382120.jpeg', 0, 1, 3, 1618470777, 0),
(14, '高等数学', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/4230e202104151510076554.jpeg', 0, 1, 4, 1618470800, 0),
(15, '大学语文', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/15c59202104151509555157.jpeg', 0, 1, 4, 1618470820, 0),
(16, '编程', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/6132f202104151510019168.jpeg', 0, 1, 4, 1618470836, 0);

-- --------------------------------------------------------

--
-- 表的结构 `eb_special_task`
--
CREATE TABLE `eb_special_task` (
  `id` int(11) UNSIGNED NOT NULL,
  `mer_id` int(12) DEFAULT '0' COMMENT '讲师id',
  `coures_id` int(11) NOT NULL DEFAULT '0' COMMENT '课程id（1.1版本后废弃）',
  `special_id` int(11) NOT NULL DEFAULT '0' COMMENT '专题id',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '素材标题',
  `content` longtext COMMENT '内容',
  `detail` text COMMENT '简介',
  `is_try` tinyint(1) DEFAULT '0' COMMENT '是否开启试看',
  `try_content` longtext COMMENT '图文试看内容',
  `try_time` int(5) DEFAULT '0' COMMENT '音视频试看时间/分',
  `type` tinyint(1) NOT NULL DEFAULT '6' COMMENT '素材类型',
  `pid` int(12) DEFAULT '0' COMMENT '素材分类',
  `is_pay` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否付费,0=免费,1=付费',
  `link` varchar(1024) NOT NULL DEFAULT '' COMMENT '链接',
  `image` varchar(200) NOT NULL DEFAULT '0' COMMENT '封面图',
  `abstract` varchar(2048) NOT NULL DEFAULT '' COMMENT '摘要',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `play_count` int(11) NOT NULL DEFAULT '0' COMMENT '播放量',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '删除',
  `is_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=下架,1=上架',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `edit_time` int(12) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `live_id` int(11) NOT NULL DEFAULT '0' COMMENT '直播ID，0=普通（1.1版本后废弃）',
  `videoId` varchar(512) DEFAULT NULL COMMENT '视频ID',
  `file_name` varchar(512) DEFAULT NULL COMMENT '视频/音频名称',
  `file_type` varchar(255) DEFAULT NULL COMMENT '文件分类'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='专题-素材';

--
-- 转存表中的数据 `eb_special_task`
--
INSERT INTO `eb_special_task`(`id`,`mer_id`, `coures_id`, `special_id`, `title`, `content`, `detail`, `type`,`pid`, `is_pay`, `link`, `image`, `abstract`, `sort`, `play_count`, `is_del`, `is_show`, `add_time`, `edit_time`, `live_id`, `videoId`, `file_name`, `file_type`) VALUES
(1, 0, 0, 0, '图文素材1', '<p>我是图文素材1内容</p>', '<p>我是图文素材1简介</p>', 1,1,0, '', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/32379202002201232449232.jpg', '', 1, 0, 0, 1, 1588042168, 0, 0,'','',''),
(2, 0, 0, 0, '图文素材2', '<p>我是图文素材2内容</p>', '<p>我是图文素材2简介</p>', 1,1,0, '', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/32379202002201232449232.jpg', '', 2, 0, 0, 1, 1588042210, 0, 0,'','',''),
(3, 0, 0, 0, '音频素材一', '<p><br/></p><div><audio style=\"width: 100%\" src=\"http://cremb-zsff.oss-cn-beijing.aliyuncs.com/W8rykksS4m6ZtYp27E.mp4\" class=\"video-ue\" controls=\"controls\"></audio></div><p>音频</p>', '<p>我是音频素材1简介</p>', 2,2,0, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/W8rykksS4m6ZtYp27E.mp4', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/0620e202002201232446695.jpg', '', 1, 0,0, 1, 1588043711, 0, 0,'','',''),
(4, 0, 0, 0, '音频素材二', '<p><br/></p><audio style=\"width: 100%\" src=\"http://cremb-zsff.oss-cn-beijing.aliyuncs.com/W8rykksS4m6ZtYp27E.mp4\" class=\"video-ue\" controls=\"controls\"></audio><p>我是音频素材内容</p>', '<p>我是音频素材二简介</p>', 2,2,0, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/W8rykksS4m6ZtYp27E.mp4', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/e56f5202002201232449146.jpg', '', 2, 0,0, 1, 1588043762, 0, 0,'','',''),
(5, 0, 0, 0, '视频素材一', '<p><br/></p><div><video style=\"width: 100%\" src=\"http://cremb-zsff.oss-cn-beijing.aliyuncs.com/W8rykksS4m6ZtYp27E.mp4\" class=\"video-ue\" controls=\"controls\"></video></div><p>我是视频素材一内容</p>', '<p>我是视频素材一简介</p>', 3,3,0, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/W8rykksS4m6ZtYp27E.mp4', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/3ec76202002201232442683.jpg', '', 1, 0, 0, 1, 1588043857, 0, 0,'','',''),
(6, 0, 0, 0, '视频素材二', '<p><br/></p><div><video style=\"width: 100%\" src=\"http://cremb-zsff.oss-cn-beijing.aliyuncs.com/W8rykksS4m6ZtYp27E.mp4\" class=\"video-ue\" controls=\"controls\"></video></div><p>我是视频素材2内容</p>', '<p>我是视频素材2简介</p>', 3,3,0, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/W8rykksS4m6ZtYp27E.mp4', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/78cdd20200220123244525.jpg', '', 2, 0,0, 1, 1588043914, 0, 0,'','','');

--
-- 转存表中的数据 `eb_special_task_category`
--
CREATE TABLE `eb_special_task_category` (
  `id` int(10) UNSIGNED NOT NULL,
  `mer_id` int(12) DEFAULT '0' COMMENT '讲师id',
  `pid` int(10) DEFAULT '0' COMMENT '上级ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '分类标题',
  `sort` int(10) DEFAULT '0' COMMENT '排序',
  `is_del` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `add_time` int(12) DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COMMENT='素材分类';

--
-- 转存表中的数据 `eb_special_task_category` 添加数据
--
INSERT INTO `eb_special_task_category` (`id`,`mer_id`, `pid`, `title`, `sort`, `is_del`,`add_time`) VALUES
(1, 0, 0, '图文', 0, 0, 1612496006),
(2, 0, 0, '音频', 0, 0, 1612496017),
(3, 0, 0, '视频', 0, 0, 1612496023);

--
-- 表的结构 `eb_special_watch`
--
CREATE TABLE `eb_special_watch` (
  `id` int(11) UNSIGNED NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT 'uid',
  `special_id` int(11) NOT NULL DEFAULT '0' COMMENT '专题ID',
  `task_id` int(11) NOT NULL DEFAULT '0' COMMENT '素材ID',
  `viewing_time` decimal(15,5) DEFAULT '0.00000' COMMENT '观看部位',
  `percentage` int(5) DEFAULT '0' COMMENT '百分比',
  `total` decimal(15,5) DEFAULT '0.00000' COMMENT '总数',
  `add_time` int(12) DEFAULT '0' COMMENT '时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户观看素材时间';

--
-- 表的结构 `eb_store_bargain`
--
CREATE TABLE `eb_store_bargain` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '砍价产品ID',
  `product_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '关联产品ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '砍价活动名称',
  `image` varchar(150) NOT NULL DEFAULT '' COMMENT '砍价活动图片',
  `unit_name` varchar(16) DEFAULT NULL COMMENT '单位名称',
  `stock` int(11) UNSIGNED DEFAULT NULL COMMENT '库存',
  `sales` int(11) UNSIGNED DEFAULT NULL COMMENT '销量',
  `images` varchar(1000) NOT NULL DEFAULT '' COMMENT '砍价产品轮播图',
  `start_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '砍价开启时间',
  `stop_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '砍价结束时间',
  `store_name` varchar(255) DEFAULT NULL COMMENT '砍价产品名称',
  `price` decimal(8,2) UNSIGNED DEFAULT NULL COMMENT '砍价金额',
  `min_price` decimal(8,2) UNSIGNED DEFAULT NULL COMMENT '砍价商品最低价',
  `num` int(11) UNSIGNED DEFAULT NULL COMMENT '每次购买的砍价产品数量',
  `bargain_max_price` decimal(8,2) UNSIGNED DEFAULT NULL COMMENT '用户每次砍价的最大金额',
  `bargain_min_price` decimal(8,2) UNSIGNED DEFAULT NULL COMMENT '用户每次砍价的最小金额',
  `bargain_num` int(11) UNSIGNED NOT NULL DEFAULT '1' COMMENT '用户每次砍价的次数',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '砍价状态 0(到砍价时间不自动开启)  1(到砍价时间自动开启时间)',
  `description` text COMMENT '砍价详情',
  `give_integral` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '反多少积分',
  `info` varchar(255) DEFAULT NULL COMMENT '砍价活动简介',
  `cost` decimal(8,2) UNSIGNED DEFAULT NULL COMMENT '成本价',
  `sort` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '排序',
  `is_hot` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否推荐0不推荐1推荐',
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否删除 0未删除 1删除',
  `add_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '添加时间',
  `is_postage` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '是否包邮 0不包邮 1包邮',
  `postage` decimal(10,2) UNSIGNED DEFAULT NULL COMMENT '邮费',
  `rule` text COMMENT '砍价规则',
  `look` int(11) UNSIGNED DEFAULT '0' COMMENT '砍价产品浏览量',
  `share` int(11) UNSIGNED DEFAULT '0' COMMENT '砍价产品分享量'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='砍价表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_bargain_user`
--
CREATE TABLE `eb_store_bargain_user` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '用户参与砍价表ID',
  `uid` int(11) UNSIGNED DEFAULT NULL COMMENT '用户ID',
  `bargain_id` int(11) UNSIGNED DEFAULT NULL COMMENT '砍价产品id',
  `bargain_price_min` decimal(8,2) UNSIGNED DEFAULT NULL COMMENT '砍价的最低价',
  `bargain_price` decimal(8,2) DEFAULT NULL COMMENT '砍价金额',
  `price` decimal(8,2) UNSIGNED DEFAULT NULL COMMENT '砍掉的价格',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '状态 1参与中 2 活动结束参与失败 3活动结束参与成功',
  `add_time` int(12) UNSIGNED DEFAULT NULL COMMENT '参与时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户参与砍价表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_bargain_user_help`
--
CREATE TABLE `eb_store_bargain_user_help` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '砍价用户帮助表ID',
  `uid` int(11) UNSIGNED DEFAULT NULL COMMENT '帮助的用户id',
  `bargain_id` int(11) UNSIGNED DEFAULT NULL COMMENT '砍价产品ID',
  `bargain_user_id` int(11) UNSIGNED DEFAULT NULL COMMENT '用户参与砍价表id',
  `price` decimal(8,2) UNSIGNED DEFAULT NULL COMMENT '帮助砍价多少金额',
  `add_time` int(12) UNSIGNED DEFAULT NULL COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='砍价用户帮助表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_cart`
--
CREATE TABLE `eb_store_cart` (
  `id` bigint(8) UNSIGNED NOT NULL COMMENT '购物车表ID',
  `uid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
  `type` varchar(32) NOT NULL DEFAULT '' COMMENT '类型',
  `product_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商品ID',
  `product_attr_unique` varchar(16) NOT NULL DEFAULT '' COMMENT '商品属性',
  `cart_num` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商品数量',
  `add_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '添加时间',
  `is_pay` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 = 未购买 1 = 已购买',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `is_new` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为立即购买',
  `combination_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '拼团id',
  `seckill_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '秒杀产品ID',
  `bargain_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '砍价id',
  `integral_id` int(11) DEFAULT '0' COMMENT '积分id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='购物车表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_category`
--
CREATE TABLE `eb_store_category` (
  `id` mediumint(11) UNSIGNED NOT NULL COMMENT '商品分类表ID',
  `mer_id` int(11) NOT NULL DEFAULT '0' COMMENT '讲师id',
  `pid` mediumint(11) NOT NULL DEFAULT '0' COMMENT '父id',
  `cate_name` varchar(100) NOT NULL DEFAULT '' COMMENT '分类名称',
  `sort` mediumint(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `pic` varchar(128) NOT NULL DEFAULT '' COMMENT '图标',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否推荐',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品分类表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_combination`
--
CREATE TABLE `eb_store_combination` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商品id',
  `mer_id` int(10) UNSIGNED DEFAULT '0' COMMENT '讲师id',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '推荐图',
  `images` varchar(1000) NOT NULL DEFAULT '' COMMENT '轮播图',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '活动标题',
  `attr` varchar(255) NOT NULL DEFAULT '' COMMENT '活动属性',
  `people` int(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '参团人数',
  `info` varchar(255) NOT NULL DEFAULT '' COMMENT '简介',
  `price` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '价格',
  `sort` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '排序',
  `sales` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '销量',
  `stock` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '库存',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `is_host` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '推荐',
  `is_show` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '产品状态',
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `combination` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `mer_use` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '讲师是否可用1可用0不可用',
  `is_postage` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否包邮1是0否',
  `postage` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '邮费',
  `description` text COMMENT '拼团内容',
  `start_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '拼团开始时间',
  `stop_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '拼团结束时间',
  `cost` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '拼图产品成本',
  `browse` int(11) DEFAULT '0' COMMENT '浏览量'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='拼团产品表' ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_combination_attr`
--
CREATE TABLE `eb_store_combination_attr` (
  `product_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商品ID',
  `attr_name` varchar(32) NOT NULL DEFAULT '' COMMENT '属性名',
  `attr_values` varchar(256) NOT NULL DEFAULT '' COMMENT '属性值'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品属性表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_combination_attr_result`
--
CREATE TABLE `eb_store_combination_attr_result` (
  `product_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商品ID',
  `result` text COMMENT '商品属性参数',
  `change_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '上次修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品属性详情表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_combination_attr_value`
--
CREATE TABLE `eb_store_combination_attr_value` (
  `product_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商品ID',
  `suk` varchar(128) NOT NULL DEFAULT '' COMMENT '商品属性索引值 (attr_value|attr_value[|....])',
  `stock` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '属性对应的库存',
  `sales` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '销量',
  `price` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '属性金额',
  `image` varchar(128) DEFAULT NULL COMMENT '图片',
  `unique` char(8) NOT NULL DEFAULT '' COMMENT '唯一值',
  `cost` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '成本价'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品属性值表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_coupon`
--
CREATE TABLE `eb_store_coupon` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '优惠券表ID',
  `title` varchar(64) NOT NULL DEFAULT '' COMMENT '优惠券名称',
  `integral` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '兑换消耗积分值',
  `coupon_price` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '兑换的优惠券面值',
  `use_min_price` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '最低消费多少金额可用优惠券',
  `coupon_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '优惠券有效期限（单位：天）',
  `sort` int(11) UNSIGNED NOT NULL DEFAULT '1' COMMENT '排序',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '状态（0：关闭，1：开启）',
  `add_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '兑换项目添加时间',
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否删除'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='优惠券表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_coupon_issue`
--
CREATE TABLE `eb_store_coupon_issue` (
  `id` int(10) UNSIGNED NOT NULL,
  `cid` int(10) DEFAULT NULL COMMENT '优惠券ID',
  `start_time` int(12) DEFAULT NULL COMMENT '优惠券领取开启时间',
  `end_time` int(12) DEFAULT NULL COMMENT '优惠券领取结束时间',
  `total_count` int(10) DEFAULT NULL COMMENT '优惠券领取数量',
  `remain_count` int(10) DEFAULT NULL COMMENT '优惠券剩余领取数量',
  `is_permanent` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否无限张数',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 正常 0 未开启 -1 已无效',
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `add_time` int(12) DEFAULT NULL COMMENT '优惠券添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='优惠券前台领取表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_coupon_issue_user`
--
CREATE TABLE `eb_store_coupon_issue_user` (
  `uid` int(10) DEFAULT NULL COMMENT '领取优惠券用户ID',
  `issue_coupon_id` int(10) DEFAULT NULL COMMENT '优惠券前台领取ID',
  `add_time` int(12) DEFAULT NULL COMMENT '领取时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='优惠券前台用户领取记录表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_coupon_user`
--
CREATE TABLE `eb_store_coupon_user` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '优惠券发放记录id',
  `cid` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '兑换的项目id',
  `uid` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '优惠券所属用户',
  `coupon_title` varchar(32) NOT NULL DEFAULT '' COMMENT '优惠券名称',
  `coupon_price` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '优惠券的面值',
  `use_min_price` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '最低消费多少金额可用优惠券',
  `add_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '优惠券创建时间',
  `end_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '优惠券结束时间',
  `use_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '使用时间',
  `type` varchar(32) NOT NULL DEFAULT 'send' COMMENT '获取方式',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态（0：未使用，1：已使用, 2:已过期）',
  `is_fail` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否有效'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='优惠券发放记录表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_order`
--
CREATE TABLE `eb_store_order` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '订单ID',
  `trade_no` varchar(50) NOT NULL DEFAULT '' COMMENT '支付宝支付,支付宝交易订单号',
  `order_id` varchar(32) NOT NULL DEFAULT '' COMMENT '订单号',
  `uid` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户id',
  `real_name` varchar(32) NOT NULL DEFAULT '' COMMENT '用户姓名',
  `user_phone` varchar(18) NOT NULL DEFAULT '' COMMENT '用户电话',
  `user_address` varchar(100) NOT NULL DEFAULT '' COMMENT '详细地址',
  `cart_id` varchar(256) NOT NULL DEFAULT '[]' COMMENT '购物车id',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '订单类别(0:课程订单 1:会员订单 2:商品订单)',
  `member_id` int(11) NOT NULL DEFAULT '0' COMMENT '会员期限ID',
  `total_num` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '订单商品总数',
  `total_price` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '订单总价',
  `total_postage` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '邮费',
  `pay_price` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '实际支付金额',
  `deduction_price` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '抵扣金额',
  `coupon_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '优惠券id',
  `coupon_price` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '优惠券金额',
  `paid` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '支付状态',
  `pay_time` int(12) UNSIGNED DEFAULT '0' COMMENT '支付时间',
  `pay_type` varchar(32) NOT NULL DEFAULT 'weixin' COMMENT '支付方式',
  `add_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '订单状态(-1 : 申请退款 -2 : 退货成功 0：待发货；1：待收货；2：待评价；3：已完成)',
  `refund_status` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0 未退款 1 申请中 2 已退款',
  `refund_reason_wap_img` varchar(255) DEFAULT NULL COMMENT '退款图片',
  `refund_reason_wap_explain` varchar(255) DEFAULT NULL COMMENT '退款用户说明',
  `refund_reason_time` int(12) UNSIGNED DEFAULT '0' COMMENT '退款时间',
  `refund_reason_wap` varchar(255) DEFAULT NULL COMMENT '前台退款原因',
  `refund_reason` varchar(255) DEFAULT NULL COMMENT '不退款的理由',
  `refund_price` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '退款金额',
  `refund_application_time` int(12) DEFAULT '0' COMMENT '退款申请时间',
  `mark` varchar(512) NOT NULL DEFAULT '' COMMENT '备注',
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否删除',
  `unique` char(32) NOT NULL DEFAULT '' COMMENT '唯一id(md5加密)类似id',
  `remark` varchar(512) DEFAULT NULL COMMENT '管理员备注',
  `mer_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '讲师ID',
  `cost` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '成本价',
  `is_channel` tinyint(1) UNSIGNED DEFAULT '0' COMMENT '支付渠道(0微信公众号1微信小程序)',
  `pink_time` int(12) NOT NULL DEFAULT '0' COMMENT '拼团有效时间',
  `combination_id` int(11) UNSIGNED DEFAULT '0' COMMENT '拼团产品id',
  `pink_id` int(11) UNSIGNED DEFAULT '0' COMMENT '开团id',
  `seckill_id` int(11) UNSIGNED DEFAULT NULL COMMENT '秒杀产品id',
  `bargain_id` int(11) UNSIGNED DEFAULT NULL COMMENT '砍价产品id',
  `integral_id` int(11) UNSIGNED DEFAULT NULL,
  `pay_postage` decimal(8,2) DEFAULT '0.00' COMMENT '邮费',
  `delivery_name` varchar(255) DEFAULT NULL COMMENT '快递公司',
  `delivery_type` varchar(255) DEFAULT NULL COMMENT '物流类型',
  `delivery_id` varchar(64) DEFAULT '' COMMENT '快递单号',
  `gain_integral` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '消费赚取积分',
  `use_integral` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '使用积分',
  `back_integral` decimal(8,2) UNSIGNED DEFAULT '0.00' COMMENT '给用户退了多少积分',
  `is_gift` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为礼物订单',
  `gift_count` int(11) NOT NULL DEFAULT '0' COMMENT '领取次数',
  `is_receive_gift` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为领取的礼物订单',
  `gift_order_id` varchar(32) NOT NULL DEFAULT '' COMMENT '购买礼物order_id',
  `link_pay_uid` int(11) NOT NULL DEFAULT '0' COMMENT '通过连接购买所属用户',
  `gain_gold_num` int(11) NOT NULL DEFAULT '0' COMMENT '消费赚取虚拟币',
  `use_gold` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '使用虚拟币',
  `back_gold` decimal(8,2) UNSIGNED DEFAULT '0.00' COMMENT '给用户退了多少虚拟币',
  `spread_uid` int(11) NOT NULL DEFAULT '0' COMMENT '推广人uid',
  `is_system_del` tinyint(1) DEFAULT '0' COMMENT '后台是否删除'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_order_cart_info`
--
CREATE TABLE `eb_store_order_cart_info` (
  `oid` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '订单id',
  `cart_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '购物车id',
  `product_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商品ID',
  `cart_info` text COMMENT '购买东西的详细信息',
  `unique` char(32) NOT NULL DEFAULT '' COMMENT '唯一id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单购物详情表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_order_status`
--
CREATE TABLE `eb_store_order_status` (
  `oid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '订单id',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=外面订单,1=跑腿订单',
  `change_type` varchar(32) NOT NULL DEFAULT '' COMMENT '操作类型',
  `change_message` varchar(256) NOT NULL DEFAULT '' COMMENT '操作备注',
  `change_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '操作时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单操作记录表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_pink`
--
CREATE TABLE `eb_store_pink` (
  `id` int(10) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户id',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '拼团产品类型0=商品,1=专题',
  `order_id` varchar(32) NOT NULL DEFAULT '' COMMENT '订单id 生成',
  `order_id_key` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '订单id  数据库',
  `total_num` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '购买商品个数',
  `total_price` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '购买总金额',
  `cid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '拼团产品id',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '产品id',
  `people` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '拼图总人数',
  `price` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '拼团产品单价',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '开始时间',
  `stop_time` int(12) NOT NULL DEFAULT '0',
  `k_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '团长id 0为团长',
  `is_tpl` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否发送模板消息0未发送1已发送',
  `is_refund` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否退款 0未退款 1已退款',
  `is_false` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为虚拟拼团',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '状态1进行中2已完成3未完成'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='拼团表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_pink_false`
--
CREATE TABLE `eb_store_pink_false` (
  `id` int(11) UNSIGNED NOT NULL,
  `pink_id` int(11) NOT NULL DEFAULT '0' COMMENT '拼团id',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名',
  `avatar` varchar(225) NOT NULL DEFAULT '' COMMENT '头像',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='虚拟参团人员';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_product`
--
CREATE TABLE `eb_store_product` (
  `id` mediumint(11) UNSIGNED NOT NULL COMMENT '商品id',
  `mer_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '讲师Id(0为总后台管理员创建,不为0的时候是讲师后台创建)',
  `image` varchar(128) NOT NULL DEFAULT '' COMMENT '商品图片',
  `slider_image` varchar(512) NOT NULL DEFAULT '' COMMENT '轮播图',
  `store_name` varchar(128) NOT NULL DEFAULT '' COMMENT '商品名称',
  `store_info` varchar(256) NOT NULL DEFAULT '' COMMENT '商品简介',
  `keyword` varchar(256) NOT NULL DEFAULT '' COMMENT '关键字',
  `cate_id` varchar(64) NOT NULL DEFAULT '0' COMMENT '分类id',
  `price` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '商品价格',
  `vip_price` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '会员价格',
  `ot_price` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '市场价',
  `postage` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '邮费',
  `unit_name` varchar(32) NOT NULL DEFAULT '' COMMENT '单位名',
  `give_gold_num` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '获得虚拟币',
  `free_shipping` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '满几件包邮',
  `sort` smallint(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `sales` mediumint(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '销量',
  `stock` mediumint(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '库存',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态（0：未上架，1：上架）',
  `is_hot` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否热卖',
  `is_benefit` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否优惠',
  `is_best` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否精品',
  `is_new` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否新品',
  `member_pay_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '会员是否免费0:免费 1:付费',
  `description` text COMMENT '产品描述',
  `add_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '添加时间',
  `is_postage` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否包邮',
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否删除',
  `mer_use` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '讲师是否代理 0不可代理1可代理',
  `give_integral` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '获得积分',
  `cost` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '成本价',
  `is_seckill` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '秒杀状态 0 未开启 1已开启',
  `is_bargain` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '砍价状态 0未开启 1开启',
  `is_alone` tinyint(1) DEFAULT '0' COMMENT '是否开启单独分销',
  `brokerage_ratio` int(3) DEFAULT '0' COMMENT '一级推广人返佣比例',
  `brokerage_two` int(3) DEFAULT '0' COMMENT '二级推广人返佣比例',
  `ficti` mediumint(11) DEFAULT '100' COMMENT '虚拟销量',
  `browse` int(11) DEFAULT '0' COMMENT '浏览量',
  `code_path` varchar(70) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 -1:未通过 0:待审核 1:审核通过',
  `success_time` int(12) NOT NULL DEFAULT '0' COMMENT '通过时间',
  `fail_message` varchar(256) NOT NULL DEFAULT '' COMMENT '未通过原因',
  `fail_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '未通过时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_product_attr`
--
CREATE TABLE `eb_store_product_attr` (
  `product_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商品ID',
  `attr_name` varchar(32) NOT NULL DEFAULT '' COMMENT '属性名',
  `attr_values` varchar(256) NOT NULL DEFAULT '' COMMENT '属性值'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品属性表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_product_attr_result`
--
CREATE TABLE `eb_store_product_attr_result` (
  `product_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商品ID',
  `result` text COMMENT '商品属性参数',
  `change_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '上次修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品属性详情表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_product_attr_value`
--
CREATE TABLE `eb_store_product_attr_value` (
  `product_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商品ID',
  `suk` varchar(128) NOT NULL  DEFAULT '' COMMENT '商品属性索引值 (attr_value|attr_value[|....])',
  `stock` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '属性对应的库存',
  `sales` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '销量',
  `price` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '属性金额',
  `vip_price` decimal(9,2) NOT NULL DEFAULT '0.00' COMMENT '会员金额',
  `image` varchar(128) DEFAULT NULL COMMENT '图片',
  `unique` char(8) NOT NULL DEFAULT '' COMMENT '唯一值',
  `cost` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '成本价'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品属性值表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_product_relation`
--
CREATE TABLE `eb_store_product_relation` (
  `uid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
  `product_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商品ID',
  `type` varchar(32) NOT NULL DEFAULT '' COMMENT '类型(收藏(collect）、点赞(like))',
  `category` varchar(32) NOT NULL DEFAULT '' COMMENT '某种类型的商品(普通商品、秒杀商品)',
  `add_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品点赞和收藏表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_product_reply`
--
CREATE TABLE `eb_store_product_reply` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '评论ID',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `oid` int(11) NOT NULL DEFAULT '0' COMMENT '订单ID',
  `unique` char(32) NOT NULL DEFAULT '' COMMENT '唯一id',
  `product_id` int(11) NOT NULL DEFAULT '0' COMMENT '产品id',
  `reply_type` varchar(32) NOT NULL DEFAULT 'product' COMMENT '某种商品类型(普通商品）',
  `product_score` tinyint(1) NOT NULL DEFAULT '0' COMMENT '商品分数',
  `service_score` tinyint(1) NOT NULL DEFAULT '0' COMMENT '服务分数',
  `delivery_score` tinyint(1) NOT NULL DEFAULT '0' COMMENT '物流分数',
  `comment` varchar(512) NOT NULL DEFAULT '' COMMENT '评论内容',
  `pics` text COMMENT '评论图片',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '评论时间',
  `merchant_reply_content` varchar(300) NOT NULL DEFAULT '' COMMENT '管理员回复内容',
  `merchant_reply_time` int(12) NOT NULL DEFAULT '0' COMMENT '管理员回复时间',
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0未删除1已删除',
  `is_selected` tinyint(1) DEFAULT '0' COMMENT '是否精选',
  `is_reply` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未回复1已回复'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='评论表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_seckill`
--
CREATE TABLE `eb_store_seckill` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '商品秒杀产品表id',
  `product_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商品id',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '推荐图',
  `images` varchar(1000) NOT NULL DEFAULT '' COMMENT '轮播图',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '活动标题',
  `info` varchar(255) NOT NULL DEFAULT '' COMMENT '简介',
  `price` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '价格',
  `cost` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '成本',
  `ot_price` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '原价',
  `give_integral` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '返多少积分',
  `sort` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '排序',
  `stock` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '库存',
  `sales` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '销量',
  `unit_name` varchar(16) NOT NULL DEFAULT '' COMMENT '单位名',
  `postage` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '邮费',
  `description` text COMMENT '内容',
  `start_time` int(12) NOT NULL DEFAULT '0' COMMENT '开始时间',
  `stop_time` int(12) NOT NULL DEFAULT '0' COMMENT '结束时间',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '产品状态',
  `is_postage` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否包邮',
  `is_hot` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '热门推荐',
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除 0未删除1已删除',
  `num` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '最多秒杀几个',
  `is_show` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '显示'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品秒杀产品表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_seckill_attr`
--
CREATE TABLE `eb_store_seckill_attr` (
  `product_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商品ID',
  `attr_name` varchar(32) NOT NULL DEFAULT '' COMMENT '属性名',
  `attr_values` varchar(256) NOT NULL DEFAULT '' COMMENT '属性值'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='秒杀商品属性表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_seckill_attr_result`
--
CREATE TABLE `eb_store_seckill_attr_result` (
  `product_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商品ID',
  `result` text COMMENT '商品属性参数',
  `change_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '上次修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='秒杀商品属性详情表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_seckill_attr_value`
--
CREATE TABLE `eb_store_seckill_attr_value` (
  `product_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商品ID',
  `suk` varchar(128) NOT NULL DEFAULT '' COMMENT '商品属性索引值 (attr_value|attr_value[|....])',
  `stock` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '属性对应的库存',
  `sales` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '销量',
  `price` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '属性金额',
  `image` varchar(128) DEFAULT NULL COMMENT '图片',
  `unique` char(8) NOT NULL DEFAULT '' COMMENT '唯一值',
  `cost` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '成本价'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='秒杀商品属性值表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_service`
--
CREATE TABLE `eb_store_service` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '客服id',
  `mer_id` int(11) NOT NULL DEFAULT '0' COMMENT '讲师id',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '客服uid',
  `kefu_id` int(12) DEFAULT '0' COMMENT '客服ID',
  `avatar` varchar(250) NOT NULL DEFAULT '' COMMENT '客服头像',
  `notify` int(11) NOT NULL DEFAULT '0',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '代理名称',
  `sort` int(5) DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '0隐藏1显示',
  `is_h5user` tinyint(1) DEFAULT '0' COMMENT '是否h5用户',
  `subscribe` tinyint(1) DEFAULT '0' COMMENT '用户是否订阅该公众号标识',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='客服表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_service_log`
--
CREATE TABLE `eb_store_service_log` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '客服用户对话记录表ID',
  `mer_id` int(11) NOT NULL DEFAULT '0' COMMENT '讲师id',
  `msn` text COMMENT '消息内容',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '发送人uid',
  `to_uid` int(11) NOT NULL DEFAULT '0' COMMENT '接收人uid',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '发送时间',
  `type` tinyint(1) DEFAULT '0' COMMENT '是否已读（0：否；1：是；）',
  `remind` tinyint(1) DEFAULT '0' COMMENT '是否提醒过'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='客服用户对话记录表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_visit`
--
CREATE TABLE `eb_store_visit` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(11) DEFAULT NULL COMMENT '产品ID',
  `product_type` varchar(32) DEFAULT NULL COMMENT '产品类型',
  `cate_id` int(11) DEFAULT NULL COMMENT '产品分类ID',
  `type` char(50) DEFAULT NULL COMMENT '产品类型',
  `uid` int(11) DEFAULT NULL COMMENT '用户ID',
  `count` int(11) DEFAULT NULL COMMENT '访问次数',
  `content` varchar(255) DEFAULT NULL COMMENT '备注描述',
  `add_time` int(12) DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='产品浏览分析表';

-- --------------------------------------------------------
--
-- 表的结构 `eb_student`
--
CREATE TABLE `eb_student` (
  `id` int(10) UNSIGNED NOT NULL,
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT 'uid',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
  `sex` tinyint(1) NOT NULL DEFAULT '0' COMMENT '用户的性别，值为1时是男性，值为2时是女性，值为0时是未知',
  `nickname` varchar(255) DEFAULT NULL COMMENT '昵称',
  `classes_id` int(10) NOT NULL DEFAULT '0' COMMENT '班级ID',
  `province` varchar(255) DEFAULT NULL COMMENT '所在省',
  `city` varchar(255) DEFAULT NULL COMMENT '市',
  `district` varchar(255) DEFAULT NULL COMMENT '区',
  `detail` varchar(255) DEFAULT NULL COMMENT '详细地址',
  `sort` int(5) DEFAULT '0' COMMENT '排序',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '是否删除',
  `add_time` int(12) DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学员表';

--
-- 表的结构 `eb_system_admin`
--
CREATE TABLE `eb_system_admin` (
  `id` smallint(5) UNSIGNED NOT NULL COMMENT '后台管理员表ID',
  `account` varchar(32) NOT NULL DEFAULT '' COMMENT '后台管理员账号',
  `pwd` char(32) NOT NULL DEFAULT '' COMMENT '后台管理员密码',
  `real_name` varchar(16) NOT NULL DEFAULT '' COMMENT '后台管理员姓名',
  `roles` varchar(128) NOT NULL DEFAULT '' COMMENT '后台管理员权限(menus_id)',
  `phone` varchar(11) NOT NULL DEFAULT '' COMMENT '前端登录电话号码',
  `last_ip` varchar(16) DEFAULT NULL COMMENT '后台管理员最后一次登录ip',
  `last_time` int(12) UNSIGNED DEFAULT '0' COMMENT '后台管理员最后一次登录时间',
  `add_time` int(12) UNSIGNED NOT NULL  DEFAULT '0'COMMENT '后台管理员添加时间',
  `login_count` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '登录次数',
  `level` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '后台管理员级别',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '后台管理员状态 1有效0无效',
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='后台管理员表';

--
-- 转存表中的数据 `eb_system_admin`
--
INSERT INTO `eb_system_admin` (`id`, `account`, `pwd`, `real_name`, `roles`, `last_ip`, `last_time`, `add_time`, `login_count`, `level`, `status`, `is_del`) VALUES
(1, 'admin', 'e10adc3949ba59abbe56e057f20f883e', 'admin', '1', '111.18.84.139', 1582432279, 1581341252, 0, 0, 1, 0);

-- --------------------------------------------------------

--
-- 表的结构 `eb_system_attachment`
--
CREATE TABLE `eb_system_attachment` (
  `att_id` int(11) UNSIGNED NOT NULL,
  `mer_id` int(12) DEFAULT '0' COMMENT '讲师id',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '附件名称',
  `title` varchar(255) DEFAULT NULL COMMENT '图片名称',
  `att_dir` varchar(200) NOT NULL DEFAULT '' COMMENT '附件路径',
  `satt_dir` varchar(200) DEFAULT NULL COMMENT '压缩图片路径',
  `att_size` char(30) NOT NULL DEFAULT '' COMMENT '附件大小',
  `att_type` char(30) NOT NULL DEFAULT '' COMMENT '附件类型',
  `pid` int(10) NOT NULL DEFAULT '0' COMMENT '分类id',
  `time` int(11) NOT NULL DEFAULT '0' COMMENT '上传时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='附件管理表';

--
-- 转存表中的数据 `eb_system_attachment`
--
INSERT INTO `eb_system_attachment` (`att_id`,`mer_id`,`title`, `name`, `att_dir`, `satt_dir`, `att_size`, `att_type`, `pid`, `time`) VALUES
(23, 0, 'dff8b202002111344407490.png','dff8b202002111344407490.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/dff8b202002111344407490.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/dff8b202002111344407490.png', '0', 'image/jpg', 4, 1581399880),
(24, 0, '4e0a9202002111413415852.jpg','4e0a9202002111413415852.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/4e0a9202002111413415852.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/4e0a9202002111413415852.jpg', '0', 'image/jpg', 5, 1581401621),
(25, 0, '5ab3f202002111454479193.jpg','5ab3f202002111454479193.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/5ab3f202002111454479193.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/5ab3f202002111454479193.jpg', '0', 'image/jpg', 0, 1581404087),
(26, 0, 'bd2ef202002111519022660.jpg','bd2ef202002111519022660.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/bd2ef202002111519022660.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/bd2ef202002111519022660.jpg', '0', 'image/jpg', 6, 1581405542),
(27, 0, '12017202002111757416996.png','12017202002111757416996.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/12017202002111757416996.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/12017202002111757416996.png', '0', 'image/jpg', 0, 1581415061),
(28, 0, 'fb05a202002111757415258.png','fb05a202002111757415258.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/fb05a202002111757415258.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/fb05a202002111757415258.png', '0', 'image/jpg', 0, 1581415061),
(29, 0, '022b1202002111757413298.png','022b1202002111757413298.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/022b1202002111757413298.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/022b1202002111757413298.png', '0', 'image/jpg', 0, 1581415061),
(30, 0, '8cf7c202002111757418051.png','8cf7c202002111757418051.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/8cf7c202002111757418051.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/8cf7c202002111757418051.png', '0', 'image/jpg', 0, 1581415061),
(31, 0, 'a3248202002111757415453.png','a3248202002111757415453.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/a3248202002111757415453.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/a3248202002111757415453.png', '0', 'image/jpg', 0, 1581415061),
(32, 0, '37e3a202002111757417776.png','37e3a202002111757417776.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/37e3a202002111757417776.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/37e3a202002111757417776.png', '0', 'image/jpg', 0, 1581415062),
(33, 0, 'ccb10202002111757423753.jpg','ccb10202002111757423753.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/ccb10202002111757423753.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/ccb10202002111757423753.jpg', '0', 'image/jpg', 0, 1581415062),
(34, 0, 'abbe8202002111757429842.png','abbe8202002111757429842.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/abbe8202002111757429842.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/abbe8202002111757429842.png', '0', 'image/jpg', 0, 1581415062),
(35, 0, '10f3d202002111757429313.jpg','10f3d202002111757429313.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/10f3d202002111757429313.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/10f3d202002111757429313.jpg', '0', 'image/jpg', 0, 1581415062),
(36, 0, 'e20b0202002111757424191.png','e20b0202002111757424191.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/e20b0202002111757424191.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/e20b0202002111757424191.png', '0', 'image/jpg', 0, 1581415062),
(37, 0, '55bd0202002111757421468.png','55bd0202002111757421468.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/55bd0202002111757421468.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/55bd0202002111757421468.png', '0', 'image/jpg', 0, 1581415062),
(38, 0, '6b85d202002111757421971.jpg','6b85d202002111757421971.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/6b85d202002111757421971.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/6b85d202002111757421971.jpg', '0', 'image/jpg', 0, 1581415062),
(39, 0, '7d68920200211175742320.png','7d68920200211175742320.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/7d68920200211175742320.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/7d68920200211175742320.png', '0', 'image/jpg', 0, 1581415062),
(40, 0, '09bd3202002111757426798.png','09bd3202002111757426798.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/09bd3202002111757426798.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/09bd3202002111757426798.png', '0', 'image/jpg', 0, 1581415063),
(41, 0, '7f0bd202002111757435432.jpg','7f0bd202002111757435432.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/7f0bd202002111757435432.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/7f0bd202002111757435432.jpg', '0', 'image/jpg', 0, 1581415063),
(42, 0, '99cb7202002111757434673.jpg','99cb7202002111757434673.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/99cb7202002111757434673.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/99cb7202002111757434673.jpg', '0', 'image/jpg', 0, 1581415063),
(43, 0, '7c54d202002111757589056.png','7c54d202002111757589056.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/7c54d202002111757589056.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/7c54d202002111757589056.png', '0', 'image/jpg', 0, 1581415078),
(44, 0, '8b0be202002111757582367.jpg','8b0be202002111757582367.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/8b0be202002111757582367.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/8b0be202002111757582367.jpg', '0', 'image/jpg', 0, 1581415078),
(45, 0, '5ae6f202002111757583267.jpg','5ae6f202002111757583267.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/5ae6f202002111757583267.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/5ae6f202002111757583267.jpg', '0', 'image/jpg', 0, 1581415078),
(46, 0, '67c04202002111757584709.jpg','67c04202002111757584709.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/67c04202002111757584709.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/67c04202002111757584709.jpg', '0', 'image/jpg', 0, 1581415079),
(47, 0, '7809f202002111801111378.jpg','7809f202002111801111378.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/7809f202002111801111378.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/7809f202002111801111378.jpg', '0', 'image/jpg', 0, 1581415271),
(48, 0, '32095202002111804187328.jpg','32095202002111804187328.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/32095202002111804187328.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/32095202002111804187328.jpg', '0', 'image/jpg', 0, 1581415458),
(49, 0, '9ec16202002121807113099.png','9ec16202002121807113099.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/9ec16202002121807113099.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/9ec16202002121807113099.png', '0', 'image/jpg', 0, 1581502031),
(50, 0, '9d76920200212180711743.png','9d76920200212180711743.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/9d76920200212180711743.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/9d76920200212180711743.png', '0', 'image/jpg', 0, 1581502031),
(52, 0, 'd6c57202002171744271971.png','d6c57202002171744271971.png', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/d6c57202002171744271971.png', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/d6c57202002171744271971.png', '0', 'image/jpg', 6, 1581932667),
(53, 0, '68244202002171745063469.jpg','68244202002171745063469.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/68244202002171745063469.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/68244202002171745063469.jpg', '0', 'image/jpg', 6, 1581932714),
(54, 0, 'f939b202002201114298263.png','f939b202002201114298263.png', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/f939b202002201114298263.png', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/f939b202002201114298263.png', '0', 'image/jpg', 0, 1582168469),
(74, 0, '267f3202002201232144167.jpg','267f3202002201232144167.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/267f3202002201232144167.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/267f3202002201232144167.jpg', '0', 'image/jpg', 0, 1582173134),
(75, 0, '1b0cb202002201232145135.jpg','1b0cb202002201232145135.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/1b0cb202002201232145135.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/1b0cb202002201232145135.jpg', '0', 'image/jpg', 0, 1582173134),
(76, 0, '9f41f202002201232146791.jpg','9f41f202002201232146791.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/9f41f202002201232146791.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/9f41f202002201232146791.jpg', '0', 'image/jpg', 0, 1582173134),
(77, 0, '44910202002201232149124.jpg','44910202002201232149124.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/44910202002201232149124.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/44910202002201232149124.jpg', '0', 'image/jpg', 0, 1582173135),
(78, 0, '3e9f4202002201232159295.jpg','3e9f4202002201232159295.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/3e9f4202002201232159295.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/3e9f4202002201232159295.jpg', '0', 'image/jpg', 0, 1582173135),
(79, 0, '8ab46202002201232152644.jpg','8ab46202002201232152644.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/8ab46202002201232152644.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/8ab46202002201232152644.jpg', '0', 'image/jpg', 0, 1582173135),
(80, 0, '887d9202002201232157812.jpg','887d9202002201232157812.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/887d9202002201232157812.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/887d9202002201232157812.jpg', '0', 'image/jpg', 0, 1582173135),
(81, 0, '9547920200220123215506.jpg','9547920200220123215506.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/9547920200220123215506.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/9547920200220123215506.jpg', '0', 'image/jpg', 0, 1582173135),
(82, 0, '85d12202002201232152250.jpg','85d12202002201232152250.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/85d12202002201232152250.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/85d12202002201232152250.jpg', '0', 'image/jpg', 0, 1582173136),
(83, 0, '9a0d1202002201232324402.jpg','9a0d1202002201232324402.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/9a0d1202002201232324402.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/9a0d1202002201232324402.jpg', '0', 'image/jpg', 0, 1582173154),
(84, 0, 'd41c7202002201232431520.jpg','d41c7202002201232431520.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/d41c7202002201232431520.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/d41c7202002201232431520.jpg', '0', 'image/jpg', 0, 1582173163),
(85, 0, 'f4313202002201232435004.jpg','f4313202002201232435004.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/f4313202002201232435004.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/f4313202002201232435004.jpg', '0', 'image/jpg', 0, 1582173163),
(86, 0, 'ce99d202002201232432554.jpg','ce99d202002201232432554.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/ce99d202002201232432554.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/ce99d202002201232432554.jpg', '0', 'image/jpg', 0, 1582173164),
(87, 0, '78cdd20200220123244525.jpg','78cdd20200220123244525.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/78cdd20200220123244525.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/78cdd20200220123244525.jpg', '0', 'image/jpg', 0, 1582173164),
(88, 0, '3ec76202002201232442683.jpg','3ec76202002201232442683.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/3ec76202002201232442683.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/3ec76202002201232442683.jpg', '0', 'image/jpg', 0, 1582173164),
(89, 0, 'e56f5202002201232449146.jpg','e56f5202002201232449146.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/e56f5202002201232449146.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/e56f5202002201232449146.jpg', '0', 'image/jpg', 0, 1582173164),
(90, 0, '0620e202002201232446695.jpg','0620e202002201232446695.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/0620e202002201232446695.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/0620e202002201232446695.jpg', '0', 'image/jpg', 0, 1582173164),
(91, 0, '32379202002201232449232.jpg','32379202002201232449232.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/32379202002201232449232.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/32379202002201232449232.jpg', '0', 'image/jpg', 0, 1582173165),
(92, 0, '744f3202002201232457195.jpg','744f3202002201232457195.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/744f3202002201232457195.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/744f3202002201232457195.jpg', '0', 'image/jpg', 0, 1582173165),
(94, 0, '20362202002201412303972.jpg','20362202002201412303972.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/20362202002201412303972.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/20362202002201412303972.jpg', '0', 'image/jpg', 0, 1582179150);

-- --------------------------------------------------------

--
-- 表的结构 `eb_system_attachment_category`
--
CREATE TABLE `eb_system_attachment_category` (
  `id` int(11) UNSIGNED NOT NULL,
  `mer_id` int(12) DEFAULT '0' COMMENT '讲师id',
  `pid` int(11) DEFAULT '0' COMMENT '父级ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '分类名称',
  `enname` varchar(50) NOT NULL DEFAULT '' COMMENT '分类目录'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='附件分类表';

--
-- 转存表中的数据 `eb_system_attachment_category`
--
INSERT INTO `eb_system_attachment_category` (`id`, `mer_id`, `pid`, `name`, `enname`) VALUES
(4, 0, 0, '导航图标', ''),
(5, 0, 0, '专题主图', ''),
(6, 0, 0, '公众号图', '');

-- --------------------------------------------------------
--
-- 转存表中的数据 `eb_system_broadcast`
--
CREATE TABLE `eb_system_broadcast` (
  `id` int(10) UNSIGNED NOT NULL,
  `domain_name` varchar(255) NOT NULL DEFAULT '' COMMENT '域名',
  `cname` varchar(255) DEFAULT NULL COMMENT 'CNAME',
  `live_domain_type` varchar(255) NOT NULL DEFAULT '' COMMENT 'liveVideo：播流域名 liveEdge：边缘推流域名\n',
  `region` varchar(255) NOT NULL DEFAULT '' COMMENT '直播域名单元化信息',
  `scope` varchar(255) NOT NULL DEFAULT '' COMMENT '加速域名 domestic（默认值）：国内 overseas：海外 global：全球\n',
  `domain_status` varchar(255) NOT NULL DEFAULT '' COMMENT '状态 offline:停止 online:正常运行 configuring:配置中',
  `auth_key1` varchar(255) DEFAULT NULL COMMENT '主KEY',
  `push_domain` varchar(255) DEFAULT NULL COMMENT '绑定的推流域名',
  `bucket_name` varchar(255) DEFAULT NULL COMMENT '对象存储oss桶名称',
  `is_use` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否使用',
  `is_del` int(2) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='直播配置表';

--
-- 转存表中的数据 `eb_system_bucket`
--
CREATE TABLE `eb_system_bucket` (
  `id` int(10) UNSIGNED NOT NULL,
  `bucket_name` varchar(255) NOT NULL DEFAULT '' COMMENT '存储空间名称',
  `endpoint` varchar(255) NOT NULL DEFAULT '' COMMENT '区域',
  `domain_name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Bucket域名',
  `creation_time` varchar(255) NOT NULL DEFAULT '' COMMENT '创建时间',
  `is_use` int(2) NOT NULL DEFAULT '0' COMMENT '是否使用0:未使用 1:上传使用 2:直播使用',
  `is_del` int(5) NOT NULL DEFAULT '0' COMMENT '删除',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='储存空间';

--
-- 表的结构 `eb_system_config`
--
CREATE TABLE `eb_system_config` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '配置id',
  `menu_name` varchar(255) NOT NULL DEFAULT '' COMMENT '字段名称',
  `type` varchar(255) NOT NULL DEFAULT '' COMMENT '类型(文本框,单选按钮...)',
  `input_type` varchar(20) DEFAULT 'input' COMMENT '表单类型',
  `config_tab_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '配置分类id',
  `parameter` varchar(255) DEFAULT NULL COMMENT '规则 单选框和多选框',
  `upload_type` tinyint(1) UNSIGNED DEFAULT NULL COMMENT '上传文件格式1单图2多图3文件',
  `required` varchar(255) DEFAULT NULL COMMENT '规则',
  `width` int(10) UNSIGNED DEFAULT NULL COMMENT '多行文本框的宽度',
  `high` int(10) UNSIGNED DEFAULT NULL COMMENT '多行文框的高度',
  `value` varchar(5000) DEFAULT NULL COMMENT '默认值',
  `info` varchar(255) NOT NULL DEFAULT '' COMMENT '配置名称',
  `desc` varchar(255) DEFAULT NULL COMMENT '配置简介',
  `sort` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否隐藏'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='配置表';

--
-- 转存表中的数据 `eb_system_config`
--
INSERT INTO `eb_system_config` (`id`, `menu_name`, `type`, `input_type`, `config_tab_id`, `parameter`, `upload_type`, `required`, `width`, `high`, `value`, `info`, `desc`, `sort`, `status`) VALUES
(1, 'site_name', 'text', 'input', 1, '', 0, 'required:true', 100, 0, '\"\\u77e5\\u8bc6\\u4ed8\\u8d39\"', '网站名称', '网站名称', 20, 1),
(2, 'site_url', 'text', 'input', 1, '', 0, 'required:true,url:true', 100, 0, '\"\"', '网站地址', '网站地址', 19, 1),
(3, 'site_logo', 'upload', 'input', 1, '', 1, '', 0, 0, '\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/d6c57202002171744271971.png\"', '后台LOGO', '左上角logo,建议尺寸[170*50]', 18, 1),
(4, 'site_phone', 'text', 'input', 27, '', 0, '', 100, 0, '\"\"', '联系电话', '联系电话', 4, 1),
(5, 'seo_title', 'text', 'input', 27, '', 0, 'required:true', 100, 0, '\"\"', 'SEO标题', 'SEO标题', 10, 1),
(6, 'site_email', 'text', 'input', 22, '', 0, 'email:true', 100, 0, '\"\"', '联系邮箱', '联系邮箱', 0, 1),
(7, 'site_qq', 'text', 'input', 22, '', 0, 'qq:true', 100, 0, '\"\"', '联系QQ', '联系QQ', 0, 1),
(9, 'close_system', 'radio', 'input', 1, '0=开启\n1=关闭', 0, '', 0, 0, '\"0\"', '关闭后台', '关闭后台', 0, 2),
(13, 'wechat_appid', 'text', 'input', 2, '', 0, 'required:true', 100, 0, '\"\"', 'AppID', 'AppID', 0, 1),
(14, 'wechat_appsecret', 'text', 'input', 2, '', 0, 'required:true', 100, 0, '\"\"', 'AppSecret', 'AppSecret', 0, 1),
(15, 'wechat_token', 'text', 'input', 2, '', 0, 'required:true', 100, 0, '\"\"', '微信验证TOKEN', '微信验证TOKEN', 0, 1),
(16, 'wechat_encode', 'radio', 'input', 2, '0=明文模式\n1=兼容模式\n2=安全模式', 0, '', 0, 0, '\"0\"', '消息加解密方式', '如需使用安全模式请在管理中心修改，仅限服务号和认证订阅号', 0, 1),
(17, 'wechat_encodingaeskey', 'text', 'input', 2, '', 0, 'required:true', 100, 0, '\"\"', 'EncodingAESKey', '公众号消息加解密Key,在使用安全模式情况下要填写该值，请先在管理中心修改，然后填写该值，仅限服务号和认证订阅号', 0, 1),
(18, 'wechat_share_img', 'upload', 'input', 3, '', 1, '', 0, 0, '\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/68244202002171745063469.jpg\"', '微信分享图片', '若填写此图片地址，则分享网页出去时会分享此图片。可有效防止分享图片变形', 0, 1),
(19, 'wechat_qrcode', 'upload', 'input', 2, '', 1, '', 0, 0, '\"http:\\/\\/testcrmeb.oss-cn-beijing.aliyuncs.com\\/bd2ef202002111519022660.jpg\"', '公众号二维码', '您的公众号二维码', 0, 1),
(21, 'wechat_share_title', 'text', 'input', 3, '', 0, 'required:true', 100, 0, '\"\\u77e5\\u8bc6\\u4ed8\\u8d39\"', '微信分享标题', '微信分享标题', 0, 1),
(22, 'wechat_share_synopsis', 'textarea', 'input', 3, '', 0, '', 100, 5, '\"\\u77e5\\u8bc6\\u4ed8\\u8d39\"', '微信分享简介', '微信分享简介', 0, 1),
(25, 'pay_weixin_mchid', 'text', 'input', 4, '', 0, '', 100, 0, '\"\"', 'Mchid', '受理商ID，身份标识', 0, 1),
(26, 'pay_weixin_client_cert', 'upload', 'input', 4, '', 3, '', 0, 0, '\"\"', '微信支付证书', '微信支付证书，在微信商家平台中可以下载！文件名一般为apiclient_cert.pem', 0, 1),
(27, 'pay_weixin_client_key', 'upload', 'input', 4, '', 3, '', 0, 0, '\"\"', '微信支付证书密钥', '微信支付证书密钥，在微信商家平台中可以下载！文件名一般为apiclient_key.pem', 0, 1),
(28, 'pay_weixin_key', 'text', 'input', 4, '', 0, '', 100, 0, '\"\"', 'Key', '讲师支付api密钥Key', 0, 1),
(29, 'pay_weixin_open', 'radio', 'input', 4, '1=开启\n0=关闭', NULL, NULL, 0, 0, '1', '开启', '是否启用微信支付', 0, 2),
(31, 'store_postage', 'text', 'input', 10, '', 0, 'number:true,min:0', 100, 0, '\"12\"', '邮费基础价', '商品邮费基础价格,最终金额为(基础价 + 商品1邮费 + 商品2邮费)', 0, 1),
(33, 'offline_postage', 'radio', 'input', 10, '0=不包邮\n1=包邮', 0, '', 0, 0, '\"1\"', '线下支付是否包邮', '用户选择线下支付时是否包邮', 0, 1),
(34, 'integral_ratio', 'text', 'input', 11, '', 0, 'number:true', 100, 0, '\"0.01\"', '积分抵用比例', '积分抵用比例(1积分抵多少金额)', 0, 1),
(35, 'site_service_phone', 'text', 'input', 26, '', 0, '', 100, 0, '', '客服电话', '客服联系电话', 0, 1),
(46, 'system_express_app_code', 'text', 'input', 10, '', 0, '', 100, 0, '\"dbc3c1426efe44fdb6c0aa04c52a4ca9\"', '快递查询密钥', '阿里云快递查询接口密钥购买地址：https://market.aliyun.com/products/57126001/cmapi011120.html', 0, 1),
(47, 'main_business', 'text', 'input', 2, '', 0, 'required:true', 100, 0, '\" IT\\u79d1\\u6280 \\u4e92\\u8054\\u7f51|\\u7535\\u5b50\\u5546\\u52a1\"', '微信模板消息_主营行业', '微信公众号模板消息中选择开通的主营行业', 0, 0),
(48, 'vice_business', 'text', 'input', 2, '', 0, 'required:true', 100, 0, '\"IT\\u79d1\\u6280 IT\\u8f6f\\u4ef6\\u4e0e\\u670d\\u52a1 \"', '微信模板消息_副营行业', '微信公众号模板消息中选择开通的副营行业', 0, 0),
(49, 'store_brokerage_ratio', 'text', 'input', 9, NULL, NULL, 'required:true,min:0,max:100,number:true', 100, 0, '\"1\"', '一级推广人返佣比例', '订单交易成功后给上级返佣的比例0 - 100,例:5 = 返订单金额的5%', 18, 1),
(50, 'wechat_first_sub_give_coupon', 'text', 'input', 12, '', 0, 'requred:true,digits:true,min:0', 100, 0, '\"\"', '首次关注赠送优惠券ID', '首次关注赠送优惠券ID,0为不赠送', 0, 1),
(51, 'store_give_con_min_price', 'text', 'input', 12, '', 0, 'requred:true,digits:true,min:0', 100, 0, '\"0.01\"', '消费满多少赠送优惠券', '消费满多少赠送优惠券,0为不赠送', 0, 1),
(52, 'store_order_give_coupon', 'text', 'input', 12, '', 0, 'requred:true,digits:true,min:0', 100, 0, '\"\"', '消费赠送优惠劵ID', '消费赠送优惠劵ID,0为不赠送', 0, 1),
(54, 'sx_sign_min_int', 'text', 'input', 11, '', 0, 'required:true,number:true,min:0', 100, 0, '\"1\"', '签到奖励最低积分', '签到奖励最低积分', 0, 1),
(55, 'sx_sign_max_int', 'text', 'input', 11, '', 0, 'required:true,number:true,min:0', 100, 0, '\"5\"', '签到奖励最高积分', '签到奖励最高积分', 0, 1),
(57, 'about_us', 'upload', 'input', 22, '', 1, '', 0, 0, '\"\"', '关于我们', '系统的标识', 0, 0),
(58, 'replenishment_num', 'text', 'input', 5, '', 0, 'required:true,number:true,min:0', 100, 0, '\"20\"', '待补货数量', '产品待补货数量低于多少时，提示补货', 9, 0),
(59, 'routine_appId', 'text', 'input', 7, '', 0, '', 100, 0, '\"\"', 'appId', '小程序appID', 0, 1),
(60, 'routine_appsecret', 'text', 'input', 7, '', 0, '', 100, 0, '\"\"', 'AppSecret', '小程序AppSecret', 0, 1),
(61, 'api', 'text', 'input', 2, '', 0, '', 100, 0, '\"\\/wap\\/wechat\\/serve\"', '接口地址', '微信接口例如：http://www.abc.com/wap/wechat/serve', 0, 1),
(62, 'paydir', 'textarea', 'input', 4, '', 0, '', 100, 5,  '\"\\/wap\\/special\\/\\n\"', '配置目录', '支付目录配置系统不调用提示作用', 0, 1),
(73, 'routine_logo', 'upload', 'input', 7, '', 1, '', 0, 0, '\"\"', '小程序logo', '小程序logo', 0, 1),
(74, 'routine_name', 'text', 'input', 7, '', 0, '', 100, 0, '\"\\u8f66\\u8054\\u76df\\u65b0\\u80fd\\u6e90\"', '小程序名称', '小程序名称', 0, 1),
(76, 'routine_style', 'text', 'input', 7, '', 0, '', 100, 0, '\"#FFFFFF\"', '小程序风格', '小程序颜色', 0, 1),
(77, 'store_stock', 'text', 'input', 5, '', 0, '', 100, 0, '\"1\"', '警戒库存', '警戒库存提醒值', 8, 1),
(78, 'order_clear_time', 'text', 'input', 5, NULL, NULL, '', 100, NULL, '24', '订单清除时间(h)', '未支付订单清除时间', 7, 1),
(79, 'automatic_receiving_time', 'text', 'input', 5, NULL, NULL, '', 100, NULL, '7', '自动收货时间(天)', '自动收货时间', 6, 1),
(80, 'store_switch', 'radio', 'input', 5, '1=开启\n0=关闭', NULL, NULL, NULL, NULL, '\"1\"', '商城开关', '商城订单列表开关', 0, 1),
(87, 'store_brokerage_two', 'text', 'input', 9, NULL, NULL, 'required:true,min:0,max:100,number:true', 100, 0, '\"0\"', '二级推广人返佣比例', '订单交易成功后给上级返佣的比例0 - 100,例:5 = 返订单金额的5%', 17, 1),
(88, 'store_brokerage_statu', 'radio', 'input', 9, '1=指定分销\n2=人人分销', 0, '', 0, 0, '\"2\"', '分销模式', '人人分销默认每个人都可以分销，制定人分销后台制定人开启分销', 20, 1),
(89, 'pay_routine_appid', 'text', 'input', 14, '', 0, 'required:true', 100, 0, '\"\"', 'Appid', '小程序Appid', 0, 1),
(90, 'pay_routine_appsecret', 'text', 'input', 14, '', 0, 'required:true', 100, 0, '\"\"', 'Appsecret', '小程序Appsecret', 0, 1),
(91, 'pay_routine_mchid', 'text', 'input', 14, '', 0, 'required:true', 100, 0, '\"\"', 'Mchid', '讲师号', 0, 1),
(92, 'pay_routine_key', 'text', 'input', 14, '', 0, 'required:true', 100, 0, '\"\"', 'Key', '讲师key', 0, 1),
(93, 'pay_routine_client_cert', 'upload', 'input', 14, '', 3, '', 0, 0, '[\"\\/public\\/uploads\\/config\\/file\\/5c12fb7b9ebc5.pem\"]', '小程序支付证书', '小程序支付证书', 0, 1),
(94, 'pay_routine_client_key', 'upload', 'input', 14, '', 3, '', 0, 0, '[\"\\/public\\/uploads\\/config\\/file\\/5c12fb7eb8897.pem\"]', '小程序支付证书密钥', '小程序支付证书密钥', 0, 1),
(95, 'log_cycle', 'text', 'input', 22, '', 0, '', 100, 0, '\"\"', '系统日志', '系统日志保存时间单位天', 0, 1),
(96, 'sign_in_integral', 'radio', 'input', 11, '1=开启\n0=关闭', NULL, NULL, NULL, NULL, '\"0\"', '签到送积分', '签到送积分显示开关', 0, 1),
(97, 'give_integral', 'radio', 'input', 11, '1=开启\n0=关闭', NULL, NULL, NULL, NULL, '\"0\"', '邀请好友赠送积分', '邀请好友赠送积分显示开关', 0, 1),
(98, 'share_integral', 'radio', 'input', 11, '1=开启\n0=关闭', NULL, NULL, NULL, NULL, '\"0\"', '分享商品送积分', '分享商品送积分开关', 0, 1),
(116, 'index_recommend_url', 'upload', 'input', 15, NULL, 1, NULL, NULL, NULL, '[\"\\/public\\/uploads\\/config\\/image\\/5c1ef9f2594ea.png\"]', '主页推荐图', '主页推荐图', 0, 1),
(117, 'hot_food', 'upload', 'input', 15, NULL, 1, NULL, NULL, NULL, '\"\"', '热销菜品', '热销菜品', 0, 1),
(118, 'welcome_url', 'upload', 'input', 15, NULL, 1, NULL, NULL, NULL, '[\"\\/public\\/uploads\\/config\\/image\\/5c29e37db77e8.jpg\"]', '最受欢迎', '热销菜品', 0, 1),
(119, 'praise_part', 'text', 'input', 7, NULL, NULL, '', 100, NULL, '\"5\"', '用户给讲师好评加分', '用户给讲师好评加分', 0, 1),
(120, 'praise_bad_part', 'text', 'input', 7, NULL, NULL, '', 100, NULL, '\"8\"', '用户给讲师差评减分分', '用户给讲师好评加分', 0, 1),
(130, 'extract_min_money', 'text', 'input', 9, NULL, NULL, NULL, 100, NULL, '\"1\"', '提现最低金额', '提现最低金额', 10, 1),
(132, 'smsSignName', 'text', 'input', 17, NULL, NULL, '', 100, NULL, '\"CEMEB\\u77e5\\u8bc6\\u4ed8\\u8d39\"', '短信签名', '短信签名', 5, 1),
(133, 'accessKeyId', 'text', 'input', 18, NULL, NULL, NULL, 100, NULL, '\"\"', '阿里云AccessKeyId', '阿里云AccessKeyId', 0, 1),
(134, 'accessKeySecret', 'text', 'input', 18, NULL, NULL, NULL, 100, NULL, '\"\"', '阿里云AccessKeySecret', '阿里云AccessKeySecret', 0, 1),
(135, 'smsTemplateCode', 'text', 'input', 17, NULL, NULL, '', 100, NULL, '\"\"', '短信模板ID', '短信模板ID', 4, 1),
(136, 'approved_template_id', 'text', 'input', 17, NULL, NULL, '', 100, NULL, '\"\"', '审核通过模版ID', '审核通过模版ID', 3, 1),
(137, 'approval_failed_template_id', 'text', 'input',17, NULL, NULL, '', 100, NULL, '\"\"', '审核未通过模版ID', '审核未通过模版ID', 2, 1),
(140, 'spread_poster_url', 'upload', 'input', 9, NULL, 1, NULL, NULL, NULL, '\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/20362202002201412303972.jpg\"', '推广人海报', '推广人海报[600*740px]', 9, 1),
(143, 'alipay_app_id', 'text', 'input', 16, NULL, NULL, '', 100, NULL, '\"\"', '蚂蚁金服appid', '蚂蚁金服appid', 0, 1),
(144, 'alipay_private_key', 'text', 'input', 16, NULL, NULL, '', 100, NULL, '\"\"', '蚂蚁金服key', '蚂蚁金服key', 0, 1),
(145, 'alipay_public_key', 'text', 'input', 16, NULL, NULL, '', 100, NULL, '\"\"', '蚂蚁金服公钥', '蚂蚁金服公钥', 0, 1),
(146, 'promoter_content', 'textarea', 'input', 9, NULL, NULL, NULL, 100, 8, '\"\"', '推广细则', '推广细则', 0, 2),
(147, 'promoter_guide', 'textarea', 'input', 9, NULL, NULL, NULL, 100, 8, '\"\"', '推荐指引', '推荐指引', 8, 1),
(154, 'open_barrage', 'radio', 'input', 1, '1=开启\n0=关闭', NULL, NULL, 100, NULL, '\"1\"', '专题虚拟弹幕开关', '专题虚拟弹幕开关', 14, 1),
(155, 'barrage_show_time', 'text', 'input', 22, NULL, NULL, NULL, 100, NULL, '\"5\"', '专题弹幕停留时间', '专题弹幕停留时间 单位秒计算', 0, 1),
(164, 'uploadUrl', 'text', 'input', 19, NULL, NULL, '', 100, NULL, '\"\"', '空间域名 Domain', '空间域名 Domain', 0, 1),
(165, 'OssBucket', 'text', 'input', 19, NULL, NULL, '', 100, NULL, '\"\"', '存储空间名称', '存储空间名称,又称桶名', 0, 1),
(166, 'end_point', 'text', 'input', 19, NULL, NULL, '', 100, NULL, '\"oss-cn-beijing.aliyuncs.com\"', 'EndPoint（地域节点）', 'EndPoint（地域节点）', 0, 1),
(168, 'home_logo', 'upload', 'input', 1, NULL, 1, NULL, NULL, NULL, '\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/34285202002171340371181.gif\"', '移动端课程分类LOGO[200*200]', '移动端课程分类LOGO[200*200]', 13, 1),
(169, 'aliyun_live_rtmpLink', 'text', 'input', 21, NULL, NULL, '', 100, NULL, '\"\"', '推流地址', '推流地址', 0, 1),
(170, 'aliyun_live_playLike', 'text', 'input', 21, NULL, NULL, '', 100, NULL, '\"\"', '播放地址', '播放地址', 0, 1),
(171, 'aliyun_live_push_key', 'text', 'input', 21, NULL, NULL, '', 100, NULL, '\"\"', '推流主key', '推流主key', 0, 1),
(172, 'aliyun_live_play_key', 'text', 'input', 21, NULL, NULL, '', 100, NULL, '\"\"', '播放主key', '播放主key', 0, 1),
(173, 'aliyun_live_appName', 'text', 'input', 21, NULL, NULL, '', 100, NULL, '\"\"', '直播应用名', '直播应用名(只能为英文数字组合)', 0, 1),
(174, 'aliyun_live_oss_bucket', 'text', 'input', 21, NULL, NULL, '', 100, NULL, '\"\"', '直播录制OSS桶名', '直播录制OSS桶名', 0, 1),
(175, 'aliyun_live_end_point', 'text', 'input', 21, NULL, NULL, NULL, 100, NULL, '\"oss-cn-shenzhen.aliyuncs.com\"', '直播录制OssEndpoint', '直播录制OssEndpoint地域节点', 0, 1),
(176, 'gold_rate', 'text', 'input', 23, NULL, NULL, 'number:rue', 100, NULL, '\"10\"', '人民币与金币换算率', '充值人民币和金币的换算概率，默认：1元人民币=10系统虚拟金币', 0, 1),
(177, 'gold_name', 'text', 'input', 23, NULL, NULL, NULL, 100, NULL, '\"\\u91d1\\u5e01\"', '虚拟货币名称', '虚拟货币名称（如，金币，水滴，鲜花等）', 0, 1),
(178, 'gold_image', 'upload', 'input', 23, NULL, 1, NULL, NULL, NULL, '\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/61d9d202006181439471781.png\"', '虚拟货币图标', '虚拟货币图标', 0, 1),
(179, 'single_gold_coin', 'text', 'input', 25, NULL, NULL, '', 100, NULL, '1', '单次签到虚拟币数', '每次签到用户可以获得的虚拟币数（默认为金币）', 0, 1),
(180, 'sign_default_poster', 'upload', 'input', 25, NULL, 1, NULL, NULL, NULL, '\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/20362202002201412303972.jpg\"', '签到分享海报[690*590]', '签到默认海报（没有签到海报时默认显示此图）', 0, 1),
(181, 'balance_switch', 'radio', 'input', 1, '1=开启\n0=关闭', NULL, NULL, NULL, NULL, '\"1\"', '余额开关', '余额开关', 12, 1),
(182, 'alicloud_account_id', 'text', 'input', 24, NULL, NULL, '', 100, NULL, '\"\"', '阿里云账号ID', '阿里云账号ID', 0, 1),
(183, 'configuration_item_region', 'text', 'input', 24, NULL, NULL, '', 100, NULL, '\"cn-shanghai\"', '配置项region', '配置项region', 0, 1),
(184, 'demand_switch', 'radio', 'input', 24, '1=开启\n2=关闭', NULL, NULL, NULL, NULL, '\"2\"', '点播开关', '点播开关', 0, 1),
(185, 'sms_account', 'text', 'input', 17, NULL, NULL, NULL, 100, NULL, '', '短信账号', 'crmeb短信平台账号', 0, 0),
(186, 'sms_token', 'text', 'input', 17, NULL, NULL, '', 100, NULL, '', '短信密码', 'crmeb短信平台的密码', 0, 0),
(187, 'sms_platform_selection', 'radio', 'input', 17, '1=阿里云短信平台\n2=crmeb短信平台', NULL, NULL, NULL, NULL, '\"2\"', '短信平台选择', '短信平台选择', 0, 1),
(188, 'alipay_switch', 'radio', 'input', 16, '1=开启\n0=关闭', NULL, NULL, NULL, NULL, '\"1\"', '支付宝开关', '支付宝开关', 0, 1),
(189, 'is_show_or_hide', 'radio', 'input', 22, '1=显示\n2=隐藏', NULL, NULL, NULL, NULL, '\"1\"', '首页活动显示开关', '首页活动显示开关', 0, 0),
(190, 'member_brokerage_ratio', 'text', 'input', 9, NULL, NULL, 'required:true,min:0,max:100,number:true', 100, NULL, '1', '一级推广人返佣比例(会员)', '订单交易成功后给上级返佣的比例0 - 100,例:5 = 返订单金额的5%', 15, 1),
(191, 'member_brokerage_two', 'text', 'input', 9, NULL, NULL, 'required:true,min:0,max:100,number:true', 100, NULL, '0', '二级推广人返佣比例(会员)', '订单交易成功后给上级返佣的比例0 - 100,例:5 = 返订单金额的5%', 14, 1),
(192, 'force_binding', 'radio', 'input', 1, '1=绑定\n2=不绑定', NULL, NULL, NULL, NULL, '\"1\"', '微信端是否强制绑定手机号', '微信端是否强制绑定手机号', 11, 1),
(193, 'login_logo', 'upload', 'input', 1, NULL, 1, NULL, NULL, NULL, '\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/f93a2202103161033588457.png\"', '后台登录框LOGO', '后台登录框LOGO[建议尺寸180*180]', 17, 1),
(194, 'group_background', 'upload', 'input', 1, NULL, 1, NULL, NULL, NULL, '\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/d751f202103241832163764.png\"', '拼团列表背景图(750*480)', '前端拼团列表背景图(750*480)', 15, 1),
(195, 'wechat_notification_message', 'radio', 'input', 1, '1=模版消息\n2=订阅消息', NULL, NULL, NULL, NULL, '1', '微信通知消息', '微信端通知消息的选择', 10, 1),
(196, 'h5_wechat_payment_switch', 'radio', 'input', 4, '1=开启\n0=关闭', NULL, NULL, NULL, NULL, '0', 'h5微信支付开关', 'h5端微信支付开关', 0, 1),
(197, 'official_account_switch', 'radio', 'input', 1, '1=显示\n0=隐藏', NULL, NULL, NULL, NULL, '\"1\"', '关注公众号开关', '关注公众号开关', 9, 1),
(198, 'course_distribution_switch', 'radio', 'input', 9, '1=开启\n0=关闭', NULL, NULL, NULL, NULL, '\"1\"', '课程分销开关', '课程分销开关', 19, 1),
(199, 'member_distribution_switch', 'radio', 'input', 9, '1=开启\n0=关闭', NULL, NULL, NULL, NULL, '\"1\"', '会员分销开关', '会员分销开关', 16, 1),
(200, 'customer_service_configuration', 'radio', 'input', 26, '1=微信客服\n2=CRMchat客服\n3=拨打电话', NULL, NULL, NULL, NULL, '\"3\"', '客服配置', '客服配置', 3, 1),
(201, 'service_url', 'text', 'input', 26, NULL, NULL, NULL, 100, NULL, '', 'CRMchat客服网址', 'CRMchat客服网址', 2, 1),
(202, 'kefu_token', 'textarea', 'input', 26, NULL, NULL, NULL, 100, 5, '', 'CRMchat客服token', 'CRMchat客服token', 1, 1),
(203, 'website_statistics', 'textarea', 'input', 22, NULL, NULL, NULL, 100, 5, '', '网站统计', '网站统计', 0, 1),
(204, 'special_comment_switch', 'radio', 'input', 1, '1=开启\n0=关闭', NULL, NULL, NULL, NULL, '\"1\"', '专题评论开关', '专题评论开关', 8, 1),
(205, 'goods_distribution_switch', 'radio', 'input', 9, '1=开启\n0=关闭', NULL, NULL, NULL, NULL, '\"1\"', '商品分销开关', '商品分销开关', 13, 1),
(206, 'goods_brokerage_ratio', 'text', 'input', 9, NULL, NULL, 'required:true,min:0,max:100,number:true', 100, NULL, '0', '一级推广人返佣比例(商品)', '订单交易成功后给上级返佣的比例0 - 100,例:5 = 返订单金额的5%', 12, 1),
(207, 'goods_brokerage_two', 'text', 'input', 9, NULL, NULL, 'required:true,min:0,max:100,number:true', 100, NULL, '0', '二级推广人返佣比例(商品)', '订单交易成功后给上级返佣的比例0 - 100,例:5 = 返订单金额的5%', 11, 1),
(208, 'cate_id', 'text', 'input', 24, NULL, NULL, NULL, 100, NULL, '', '视频分类ID[非必填]', '视频分类ID', 3, 1),
(209, 'share_display_switch', 'radio', 'input', 1, '1=开启\n0=关闭', NULL, NULL, NULL, NULL, '\"1\"', '分享显示开关', '课程、商品、会员详情分享显示开关', 7, 1),
(210, 'yd_keep_on_record', 'text', 'input', 22, NULL, NULL, '', 100, NULL, '', '移动端备案信息', '移动端备案信息', 0, 1),
(211, 'extract_price', 'text', 'input', 27, NULL, NULL, '', 100, NULL, '\"0.1\"', '讲师后台提现最低金额', '讲师后台提现最低金额', 9, 1),
(212, 'gold_coin_ratio', 'text', 'input', 23, NULL, NULL, NULL, 100, NULL, '\"10\"', '虚拟币提现比例', '虚拟币提现比例5=5%(若100，人民币和虚拟币1:1)', 0, 1),
(213, 'deduction_proportion_ratio', 'text', 'input', 23, NULL, NULL, '', 100, NULL, '\"10\"', '虚拟币抵扣比例', '虚拟币抵扣比例5=5%(若100，人民币和虚拟币1:1)', 0, 1),
(214, 'login_image_general_platform', 'upload', 'input', 1, NULL, 1, NULL, NULL, NULL, '\"http:\\/\\/lsq123456789.oss-cn-shenzhen.aliyuncs.com\\/369e0202209071633271475.png\"', '总平台登录框左侧图片', '总平台登录框左侧图片【286*400】', 16, 1);
-- --------------------------------------------------------

--
-- 表的结构 `eb_system_config_content`
--
CREATE TABLE `eb_system_config_content` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '中文标题',
  `config_name` varchar(100) NOT NULL DEFAULT '' COMMENT '配置名',
  `content` longtext COMMENT '内容',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `is_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否隐藏',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='配置文章表' ROW_FORMAT=COMPACT;

--
-- 转存表中的数据 `eb_system_config_content`
--
INSERT INTO `eb_system_config_content` (`id`, `title`, `config_name`, `content`, `add_time`, `is_show`, `is_del`) VALUES
(1, '关于我们', 'about_us', '&lt;p&gt;关于公司的信息介绍&lt;/p&gt;', 0, 1, 0),
(2, '注册登录协议', 'user_agreement', '', 0, 1, 0),
(3, '讲师入驻协议', 'lecturer_entry', '', 0, 1, 0);

-- --------------------------------------------------------

--
-- 表的结构 `eb_system_config_tab`
--
CREATE TABLE `eb_system_config_tab` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '配置分类id',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '配置分类名称',
  `eng_title` varchar(255) NOT NULL DEFAULT '' COMMENT '配置分类英文名称',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '配置分类状态',
  `info` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '配置分类是否显示',
  `icon` varchar(30) DEFAULT NULL COMMENT '图标',
  `type` int(2) DEFAULT '0' COMMENT '配置类型'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='配置分类表';

--
-- 转存表中的数据 `eb_system_config_tab`
--
INSERT INTO `eb_system_config_tab` (`id`, `title`, `eng_title`, `status`, `info`, `icon`, `type`) VALUES
(1, '基础配置', 'basics', 1, 0, 'cog', 0),
(2, '公众号配置', 'wechat', 1, 0, 'weixin', 1),
(3, '公众号分享配置', 'wechat_share', 1, 0, 'whatsapp', 1),
(4, '公众号支付配置', 'pay', 1, 0, 'jpy', 4),
(5, '商城配置', 'store_x', 1, 0, 'shopping-cart', 0),
(9, '分销配置', 'fenxiao', 1, 0, 'sitemap', 2),
(16, '支付宝支付配置', 'pay_system', 1, 0, 'yen', 4),
(17, '短信配置', 'sms_system', 1, 0, 'paper-plane', 5),
(18, '阿里云key配置', 'aliyun_system', 1, 0, 'sun-o', 5),
(19, 'OSS上传配置', 'oss_sytem', 1, 0, 'cloud-upload', 5),
(21, '阿里云直播', 'aliyun_live', 1, 0, 'arrow-circle-o-left', 5),
(22, '其他配置', 'shop_home', 1, 0, 'buysellads', 0),
(23, '充值金币', 'recharge', 1, 0, 'jpy', 7),
(24, '视频点播上传', 'vod_upload', 1, 0, 'plus-square-o', 5),
(25, '签到配置', 'check_configuration', 1, 0, 'check-square', 3),
(26, '客服配置', 'service configuration', 1, 0, 'facebook-official', 3),
(27, '讲师后台配置', 'instructor_backstage', 1, 0, 'mortar-board', 0);
-- --------------------------------------------------------

--
-- 表的结构 `eb_system_file`
--
CREATE TABLE `eb_system_file` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '文件对比ID',
  `cthash` char(32) NOT NULL DEFAULT '' COMMENT '文件内容',
  `filename` varchar(255) NOT NULL DEFAULT '' COMMENT '文价名称',
  `atime` char(12) NOT NULL DEFAULT '0' COMMENT '上次访问时间',
  `mtime` char(12) NOT NULL DEFAULT '0' COMMENT '上次修改时间',
  `ctime` char(12) NOT NULL DEFAULT '0' COMMENT '上次改变时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文件对比表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_system_group`
--
CREATE TABLE `eb_system_group` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '组合数据ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '数据组名称',
  `info` varchar(256) NOT NULL DEFAULT '' COMMENT '数据提示',
  `config_name` varchar(50) NOT NULL DEFAULT '' COMMENT '数据字段',
  `fields` text COMMENT '数据组字段以及类型（json数据）'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='组合数据表';

--
-- 转存表中的数据 `eb_system_group`
--
INSERT INTO `eb_system_group` (`id`, `name`, `info`, `config_name`, `fields`) VALUES
(34, '首页轮播图', '首页轮播图设置', 'store_home_banner', '[{\"name\":\"\\u6807\\u9898\",\"title\":\"title\",\"type\":\"input\",\"param\":\"\"},{\"name\":\"\\u94fe\\u63a5\",\"title\":\"url\",\"type\":\"input\",\"param\":\"\"},{\"name\":\"\\u56fe\\u7247\",\"title\":\"pic\",\"type\":\"upload\",\"param\":\"\"}]'),
(52, '提现银行', '提现银行', 'extract_bank', '[{\"name\":\"\\u94f6\\u884c\\u540d\\u79f0\",\"title\":\"bank_name\",\"type\":\"input\",\"param\":\"\"}]'),
(53, '提现规则', '提现规则', 'extract_rule', '[{\"name\":\"\\u89c4\\u5219\",\"title\":\"content\",\"type\":\"textarea\",\"param\":\"\"}]'),
(54, '分享页面背景图', '分享页面背景图', 'share_background', '[{\"name\":\"\\u80cc\\u666f\\u56fe\",\"title\":\"pic\",\"type\":\"upload\",\"param\":\"\"}]'),
(56, '首页活动区域图片', '首页活动区域图片', 'home_activity', '[{\"name\":\"\\u56fe\\u7247(200*200)\",\"title\":\"pic\",\"type\":\"upload\",\"param\":\"\"},{\"name\":\"\\u6807\\u9898\",\"title\":\"title\",\"type\":\"input\",\"param\":\"\"},{\"name\":\"\\u7b80\\u4ecb\",\"title\":\"info\",\"type\":\"input\",\"param\":\"\"},{\"name\":\"\\u94fe\\u63a5\",\"title\":\"wap_link\",\"type\":\"input\",\"param\":\"\"}]'),
(57, '会员权益', '会员权益', 'membership_interests', '[{\"name\":\"\\u6743\\u76ca\\u540d\\u79f0\",\"title\":\"name\",\"type\":\"input\",\"param\":\"\"},{\"name\":\"\\u56fe\\u6807\",\"title\":\"pic\",\"type\":\"upload\",\"param\":\"\"},{\"name\":\"\\u8bf4\\u660e\",\"title\":\"explain\",\"type\":\"input\",\"param\":\"\"},{\"name\":\"\\u6392\\u5e8f\",\"title\":\"sort\",\"type\":\"input\",\"param\":\"\"}]'),
(58, '会员说明', '会员说明', 'member_description', '[{\"name\":\"\\u5185\\u5bb9\",\"title\":\"text\",\"type\":\"input\",\"param\":\"\"},{\"name\":\"\\u6392\\u5e8f\",\"title\":\"sort\",\"type\":\"input\",\"param\":\"\"}]'),
(62, '商品列表轮播', '商品列表轮播设置', 'product_list_carousel', '[{\"name\":\"\\u6807\\u9898\",\"title\":\"title\",\"type\":\"input\",\"param\":\"\"},{\"name\":\"\\u94fe\\u63a5\",\"title\":\"url\",\"type\":\"input\",\"param\":\"\"},{\"name\":\"\\u56fe\\u7247\",\"title\":\"pic\",\"type\":\"upload\",\"param\":\"\"}]'),
(63, '订单详情状态图 ', '订单详情状态图 ', 'order_details_images', '[{\"name\":\"\\u8ba2\\u5355\\u72b6\\u6001\",\"title\":\"order_status\",\"type\":\"select\",\"param\":\"0=>\\u672a\\u652f\\u4ed8\\n1=>\\u5f85\\u53d1\\u8d27\\n2=>\\u5f85\\u6536\\u8d27\\n3=>\\u5f85\\u8bc4\\u4ef7\\n4=>\\u5df2\\u5b8c\\u6210\"},{\"name\":\"\\u56fe\\u6807\",\"title\":\"pic\",\"type\":\"upload\",\"param\":\"\"}]'),
(64, '商品订单退款原因', '商品订单退款原因', 'goods_order_refund_reason', '[{\"name\":\"\\u9000\\u6b3e\\u539f\\u56e0\",\"title\":\"reason_refund\",\"type\":\"input\",\"param\":\"\"}]'),
(65, '前端底部导航', '前端底部导航', 'bottom_navigation', '[{\"name\":\"\\u79fb\\u52a8\\u7aef\\u9875\\u9762\\u8fde\\u63a5\",\"title\":\"url\",\"type\":\"input\",\"param\":\"\"},{\"name\":\"\\u9009\\u4e2d\\u65f6\\u56fe\\u7247\",\"title\":\"class\",\"type\":\"upload\",\"param\":\"\"},{\"name\":\"\\u672a\\u9009\\u4e2d\\u65f6\\u56fe\\u7247\",\"title\":\"hove_class\",\"type\":\"upload\",\"param\":\"\"},{\"name\":\"\\u540d\\u79f0\",\"title\":\"name\",\"type\":\"input\",\"param\":\"\"}]'),
(66, '新闻简报', '新闻简报', 'news_bulletin', '[{\"name\":\"\\u6807\\u9898\",\"title\":\"title\",\"type\":\"input\",\"param\":\"\"},{\"name\":\"\\u94fe\\u63a5\",\"title\":\"url\",\"type\":\"input\",\"param\":\"\"}]'),
(67, '悬浮按钮', '前端页面悬浮按钮', 'suspension_button', '[{\"name\":\"\\u540d\\u79f0\",\"title\":\"name\",\"type\":\"input\",\"param\":\"\"},{\"name\":\"\\u94fe\\u63a5\",\"title\":\"url\",\"type\":\"input\",\"param\":\"\"},{\"name\":\"\\u56fe\\u6807\",\"title\":\"icon\",\"type\":\"upload\",\"param\":\"\"}]'),
(68, '个人中心题库模块', '个人中心题库模块', 'question_bank_module', '[{\"name\":\"\\u6807\\u9898\",\"title\":\"title\",\"type\":\"input\",\"param\":\"\"},{\"name\":\"\\u8bf4\\u660e\",\"title\":\"explain\",\"type\":\"input\",\"param\":\"\"},{\"name\":\"\\u56fe\\u6807\",\"title\":\"icon\",\"type\":\"upload\",\"param\":\"\"},{\"name\":\"\\u94fe\\u63a5\",\"title\":\"url\",\"type\":\"input\",\"param\":\"\"},{\"name\":\"\\u6392\\u5e8f\",\"title\":\"sort\",\"type\":\"input\",\"param\":\"\"}]'),
(69, '首页广告', '移动端首页轮播广告bannar', 'homepage_ads', '[{\"name\":\"\\u6807\\u9898\",\"title\":\"title\",\"type\":\"input\",\"param\":\"\"},{\"name\":\"\\u94fe\\u63a5\",\"title\":\"url\",\"type\":\"input\",\"param\":\"\"},{\"name\":\"banner\\u56fe\",\"title\":\"banner\",\"type\":\"upload\",\"param\":\"\"},{\"name\":\"\\u6392\\u5e8f\",\"title\":\"sort\",\"type\":\"input\",\"param\":\"\"}]');

-- --------------------------------------------------------

--
-- 表的结构 `eb_system_group_data`
--
CREATE TABLE `eb_system_group_data` (
  `id` int(11)  UNSIGNED NOT NULL,
  `gid` int(11) NOT NULL DEFAULT '0' COMMENT '对应的数据组id',
  `value` text COMMENT '数据组对应的数据值（json数据）',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加数据时间',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '数据排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态（1：开启；2：关闭；)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='组合数据详情表';

--
-- 转存表中的数据 `eb_system_group_data`
--
INSERT INTO `eb_system_group_data` (`id`,`gid`, `value`, `add_time`, `sort`, `status`) VALUES
(1,38, '{\"banner\":{\"type\":\"upload\",\"value\":\"http:\\/\\/shop.crmeb.net\\/public\\/uploads\\/editor\\/20180601\\/5b10b2436876e.jpg\"}}', 1527153599, 1, 1),
(2,52, '{\"bank_name\":{\"type\":\"input\",\"value\":\"\\u62db\\u5546\\u94f6\\u884c\"}}', 1545116190, 1, 1),
(3,52, '{\"bank_name\":{\"type\":\"input\",\"value\":\"\\u5efa\\u8bbe\\u94f6\\u884c\"}}', 1545116196, 1, 1),
(4,52, '{\"bank_name\":{\"type\":\"input\",\"value\":\"\\u519c\\u4e1a\\u94f6\\u884c\"}}', 1545116201, 1, 1),
(5,52, '{\"bank_name\":{\"type\":\"input\",\"value\":\"\\u4e2d\\u56fd\\u94f6\\u884c\"}}', 1545116213, 1, 1),
(6,52, '{\"bank_name\":{\"type\":\"input\",\"value\":\"\\u5de5\\u5546\\u94f6\\u884c\"}}', 1545116248, 1, 1),
(7,53, '{\"content\":{\"type\":\"textarea\",\"value\":\"\\u5230\\u8d26\\u65f6\\u95f4T+1\"}}', 1545117378, 0, 1),
(8,53, '{\"content\":{\"type\":\"textarea\",\"value\":\"\\u63d0\\u73b0\\u624b\\u7eed\\u8d39\\u6263\\u966410%\\uff08\\u542b\\u4e2a\\u4eba\\u6240\\u5f97\\u7a0e\\uff09\"}}', 1545117383, 1, 1),
(9,54, '{\"pic\":{\"type\":\"upload\",\"value\":\"https:\\/\\/huochemeng.crmeb.net\\/public\\/uploads\\/25\\/20181224\\/s_5c2055fb368ab.jpg\"}}', 1545623151, 1, 1),
(10,34, '{\"title\":{\"type\":\"input\",\"value\":\"1\"},\"url\":{\"type\":\"input\",\"value\":\"\\/wap\\/special\\/special_cate.html\"},\"pic\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/f4313202002201232435004.jpg\"}}', 1548224930, 1, 1),
(11,34, '{\"title\":{\"type\":\"input\",\"value\":\"2\"},\"url\":{\"type\":\"input\",\"value\":\"\\/wap\\/special\\/special_cate.html\"},\"pic\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/d41c7202002201232431520.jpg\"}}', 1581416351, 1, 1),
(12,34, '{\"title\":{\"type\":\"input\",\"value\":\"3\"},\"url\":{\"type\":\"input\",\"value\":\"\\/wap\\/special\\/special_cate.html\"},\"pic\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/ce99d202002201232432554.jpg\"}}', 1581502059, 1, 1),
(13,56, '{\"pic\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/e56f5202002201232449146.jpg\"},\"title\":{\"type\":\"input\",\"value\":\"\\u96f6\\u5143\\u8bfe\\u7a0b\\u514d\\u8d39\\u9886\"},\"info\":{\"type\":\"input\",\"value\":\"\\u673a\\u4f1a\\u4e0d\\u80fd\\u9519\\u8fc7\"},\"wap_link\":{\"type\":\"select\",\"value\":\"\\/wap\\/special\\/details?id=6\"},\"select_id\":{\"value\":\"6\",\"type\":\"select\"},\"type\":{\"type\":\"radio\",\"value\":\"0\"}}', 1581416634, 0, 1),
(14,56, '{\"pic\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/85d12202002201232152250.jpg\"},\"title\":{\"type\":\"input\",\"value\":\"\\u96c5\\u601d\\u6258\\u798f\\u7ebf\\u4e0a\\u57f9\\u8bad\"},\"info\":{\"type\":\"input\",\"value\":\"\\u51fa\\u56fd\\u6df1\\u9020\\u5fc5\\u5907\\u8bfe\\u7a0b\"},\"wap_link\":{\"type\":\"select\",\"value\":\"\\/wap\\/special\\/details?id=6\"},\"select_id\":{\"value\":\"6\",\"type\":\"select\"},\"type\":{\"type\":\"radio\",\"value\":\"0\"}}', 1581416745, 0, 1),
(15,56, '{\"pic\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/32379202002201232449232.jpg\"},\"title\":{\"type\":\"input\",\"value\":\"\\u8003\\u7814\\u5347\\u5b66\\u6b63\\u5f53\\u65f6\"},\"info\":{\"type\":\"input\",\"value\":\"\\u8003\\u7814\\u5f3a\\u52a9\\u529b\"},\"wap_link\":{\"type\":\"select\",\"value\":\"\\/wap\\/special\\/special_cate?cate_id=8\"},\"select_id\":{\"value\":\"8\",\"type\":\"select\"},\"type\":{\"type\":\"radio\",\"value\":\"1\"}}', 1581416759, 0, 1),
(16,57, '{\"name\":{\"type\":\"input\",\"value\":\"\\u4f1a\\u5458\\u4f18\\u60e0\\u4ef7\"},\"pic\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/39472202004291128217988.png\"},\"explain\":{\"type\":\"input\",\"value\":\"\\u8d2d\\u4e70\\u4e13\\u9898\\u4eab\\u4f1a\\u5458\\u4ef7\"},\"sort\":{\"type\":\"input\",\"value\":\"2\"}}', 1588130969, 2, 1),
(17,57, '{\"name\":{\"type\":\"input\",\"value\":\"\\u514d\\u8d39\\u8bfe\\u7a0b\"},\"pic\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/59285202004291128212590.png\"},\"explain\":{\"type\":\"input\",\"value\":\"\\u90e8\\u5206\\u8bfe\\u7a0b\\u4f1a\\u5458\\u514d\\u8d39\"},\"sort\":{\"type\":\"input\",\"value\":\"3\"}}', 1588130996, 3, 1),
(18,57, '{\"name\":{\"type\":\"input\",\"value\":\"\\u66f4\\u591a\\u6743\\u76ca\"},\"pic\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/11009202004291128212348.png\"},\"explain\":{\"type\":\"input\",\"value\":\"\\u66f4\\u591a\\u6743\\u76ca\\u589e\\u52a0\\u4e2d\"},\"sort\":{\"type\":\"input\",\"value\":\"1\"}}', 1588131020, 1, 1),
(19,58, '{"text":{"type":"input","value":"\\u4f1a\\u5458\\u8d2d\\u4e70\\u90e8\\u5206\\u8bfe\\u7a0b\\u53ef\\u4eab\\u53d7\\u4f18\\u60e0\\u4ef7"},"sort":{"type":"input","value":"1"}}', '1588131048', '1', '1'),
(20,58, '{"text":{"type":"input","value":"\\u4f1a\\u5458\\u5230\\u671f\\u540e\\u6743\\u76ca\\u5373\\u5931\\u6548\\uff0c\\u9700\\u7ee7\\u7eed\\u4eab\\u53d7\\u6743\\u76ca\\u8bf7\\u53ca\\u65f6\\u7eed\\u8d39"},"sort":{"type":"input","value":"2"}}', '1588131059', '2', '1'),
(21,58, '{"text":{"type":"input","value":"\\u62fc\\u56e2\\u6d3b\\u52a8\\u4ef7\\u65e0\\u4f1a\\u5458\\u4f18\\u60e0"},"sort":{"type":"input","value":"3"}}', '1588131073', '3', '1'),
(22,62, '{\"title\":{\"type\":\"input\",\"value\":\"\\u739b\\u838e\\u62c9\\u8482\\u5f1f\"},\"url\":{\"type\":\"input\",\"value\":\"\\/wap\\/store\\/detail.html?id=1\"},\"pic\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/71b5e20200819165009881.jpg\"}}', 1595058954, 1, 1),
(23,63, '{\"order_status\":{\"type\":\"select\",\"value\":\"0\"},\"pic\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/85bee202008100935589979.gif\"}}', 1597023369, 5, 1),
(24,63, '{\"order_status\":{\"type\":\"select\",\"value\":\"1\"},\"pic\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/b8f8b202008100936333641.gif\"}}', 1597023411, 4, 1),
(25,63, '{\"order_status\":{\"type\":\"select\",\"value\":\"2\"},\"pic\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/1f6d8202008100937514239.gif\"}}', 1597023479, 3, 1),
(26,63, '{\"order_status\":{\"type\":\"select\",\"value\":\"4\"},\"pic\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/55c49202008100938248247.gif\"}}', 1597023511, 1, 1),
(27,63, '{\"order_status\":{\"type\":\"select\",\"value\":\"3\"},\"pic\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/dba0e202107011040593620.gif\"}}', 1625107266, 2, 1),
(28,64, '{\"reason_refund\":{\"type\":\"input\",\"value\":\"\\u6536\\u8d27\\u5730\\u5740\\u586b\\u9519\\u4e86\"}}', 1596525090, 1, 1),
(29,64, '{\"reason_refund\":{\"type\":\"input\",\"value\":\"\\u4e0e\\u63cf\\u8ff0\\u4e0d\\u7b26\"}}', 1596525119, 1, 1),
(30,64, '{\"reason_refund\":{\"type\":\"input\",\"value\":\"\\u4fe1\\u606f\\u586b\\u9519\\u4e86\\uff0c\\u91cd\\u65b0\\u62cd\"}}', 1596525129, 1, 1),
(31,64, '{\"reason_refund\":{\"type\":\"input\",\"value\":\"\\u6536\\u5230\\u5546\\u54c1\\u635f\\u574f\\u4e86\"}}', 1596525139, 1, 1),
(32,64, '{\"reason_refund\":{\"type\":\"input\",\"value\":\"\\u672a\\u6309\\u9884\\u5b9a\\u65f6\\u95f4\\u53d1\\u8d27\"}}', 1596525149, 1, 1),
(33,64, '{\"reason_refund\":{\"type\":\"input\",\"value\":\"\\u5176\\u5b83\\u539f\\u56e0\"}}', 1596525158, 1, 1),
(34,62, '{\"title\":{\"type\":\"input\",\"value\":\"\\u8fc8\\u51ef\\u4f26\\u7406\"},\"url\":{\"type\":\"input\",\"value\":\"#\"},\"pic\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/9a908202008191650033943.jpg\"}}', 1597827237, 1, 1),
(35,65, '{\"url\":{\"type\":\"input\",\"value\":\"\\/wap\\/index\\/index.html\"},\"class\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/03645202102221211256015.png\"},\"hove_class\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/8d6c7202102221211183819.png\"},\"name\":{\"type\":\"input\",\"value\":\"\\u9996\\u9875\"}}', 1614741868, 4, 1),
(36,65, '{\"url\":{\"type\":\"input\",\"value\":\"\\/wap\\/special\\/special_cate.html\"},\"class\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/7e5eb202102221210586855.png\"},\"hove_class\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/1881a20210222121051213.png\"},\"name\":{\"type\":\"input\",\"value\":\"\\u8bfe\\u7a0b\"}}', 1614743135, 3, 1),
(37,65, '{\"url\":{\"type\":\"input\",\"value\":\"\\/wap\\/store\\/index.html\"},\"class\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/eec38202102221210327209.png\"},\"hove_class\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/4b7a920210222121007108.png\"},\"name\":{\"type\":\"input\",\"value\":\"\\u5546\\u57ce\"}}', 1614743246, 2, 1),
(38,65, '{\"url\":{\"type\":\"input\",\"value\":\"\\/wap\\/my\\/index.html\"},\"class\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/4533d202102221209576961.png\"},\"hove_class\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/50710202102221209424541.png\"},\"name\":{\"type\":\"input\",\"value\":\"\\u6211\\u7684\"}}', 1614743277, 1, 1),
(39,66, '{\"title\":{\"type\":\"input\",\"value\":\"\\u7b80\\u62a51\"},\"url\":{\"type\":\"input\",\"value\":\"\\/wap\\/article\\/news_list.html\"}}', 1615371134, 1, 1),
(40,66, '{\"title\":{\"type\":\"input\",\"value\":\"\\u7b80\\u62a52\"},\"url\":{\"type\":\"input\",\"value\":\"\\/wap\\/special\\/special_cate.html\"}}', 1615371147, 2, 1),
(41,67, '{\"name\":{\"type\":\"input\",\"value\":\"\\u9996\\u9875\"},\"url\":{\"type\":\"input\",\"value\":\"\\/wap\\/index\\/index\"},\"icon\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/fe36a202103131049193502.png\"}}', 1615603842, 3, 1),
(42,67, '{\"name\":{\"type\":\"input\",\"value\":\"\\u5ba2\\u670d\"},\"url\":{\"type\":\"input\",\"value\":\"\\/wap\\/service\\/service_list\"},\"icon\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/722d9202103131051525833.png\"}}', 1615603890, 2, 1),
(43,67, '{\"name\":{\"type\":\"input\",\"value\":\"\\u6211\\u7684\"},\"url\":{\"type\":\"input\",\"value\":\"\\/wap\\/my\\/index\"},\"icon\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/8f1fb202103131051255873.png\"}}', 1615603915, 1, 1),
(44,68, '{\"title\":{\"type\":\"input\",\"value\":\"\\u8003\\u9898\\u6d4b\\u8bc4\"},\"explain\":{\"type\":\"input\",\"value\":\"\\u67e5\\u770b\\u81ea\\u5df1\\u7684\\u4e0d\\u8db3\"},\"icon\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/b34cf202105281553232765.png\"},\"url\":{\"type\":\"input\",\"value\":\"\\/wap\\/topic\\/question_user\"},\"sort\":{\"type\":\"input\",\"value\":\"1\"}}', 1622193584, 1, 1),
(45,68, '{\"title\":{\"type\":\"input\",\"value\":\"\\u6211\\u7684\\u9519\\u9898\"},\"explain\":{\"type\":\"input\",\"value\":\"\\u53ca\\u65f6\\u4fee\\u6539\\u81ea\\u5df1\\u7684\\u9519\\u9898\"},\"icon\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/cdd0b202105281553348877.png\"},\"url\":{\"type\":\"input\",\"value\":\"\\/wap\\/topic\\/question_wrong\"},\"sort\":{\"type\":\"input\",\"value\":\"2\"}}', 1622193610, 2, 1),
(46,69, '{\"title\":{\"type\":\"input\",\"value\":\"\\u5e7f\\u544a1\"},\"url\":{\"type\":\"input\",\"value\":\"#\"},\"banner\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/a7b39202106021133542689.png\"},\"sort\":{\"type\":\"input\",\"value\":\"1\"}}', 1622604930, 1, 1);


-- --------------------------------------------------------

--
-- 表的结构 `eb_system_log`
--
CREATE TABLE `eb_system_log` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '管理员操作记录ID',
  `admin_id` int(10) UNSIGNED NOT NULL  DEFAULT '0'COMMENT '管理员id',
  `admin_name` varchar(64) NOT NULL DEFAULT '' COMMENT '管理员姓名',
  `path` varchar(128) NOT NULL DEFAULT '' COMMENT '链接',
  `page` varchar(64) NOT NULL DEFAULT '' COMMENT '行为',
  `method` varchar(12) NOT NULL DEFAULT '' COMMENT '访问类型',
  `ip` varchar(16) NOT NULL DEFAULT '' COMMENT '登录IP',
  `type` varchar(32) NOT NULL DEFAULT '' COMMENT '类型',
  `add_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '操作时间',
  `merchant_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '讲师id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理员操作记录表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_system_menus`
--
CREATE TABLE `eb_system_menus` (
  `id` smallint(5) UNSIGNED NOT NULL COMMENT '菜单ID',
  `pid` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父级id',
  `icon` varchar(16) NOT NULL DEFAULT '' COMMENT '图标',
  `menu_name` varchar(32) NOT NULL DEFAULT '' COMMENT '按钮名',
  `module` varchar(32) NOT NULL DEFAULT '' COMMENT '模块名',
  `controller` varchar(64)  COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '控制器',
  `action` varchar(32)  COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '方法名',
  `params` varchar(128)  COLLATE utf8mb4_bin NOT NULL DEFAULT '[]' COMMENT '参数',
  `sort` int(5) NOT NULL DEFAULT '1' COMMENT '排序',
  `is_show` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '是否显示',
  `access` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '子管理员是否可用'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='菜单表';

--
-- 转存表中的数据 `eb_system_menus`
--
INSERT INTO `eb_system_menus` (`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES
(1, 289, '', '系统设置', 'admin', '', '', '[]', 4, 1, 1),
(2, 153, '', '权限规则', 'admin', 'setting.systemMenus', 'index', '{\"cate\":\"12\"}', 7, 1, 1),
(4, 153, '', '管理员列表', 'admin', 'setting.systemAdmin', 'index', '[]', 9, 1, 1),
(6, 675, '', '基础配置', 'admin', 'setting.systemConfig', 'index', '{\"tab_id\":\"1\",\"type\":\"0\"}', 4, 1, 1),
(7, 497, '', '配置分类', 'admin', 'setting.systemConfigTab', 'index', '[]', 277, 1, 1),
(8, 153, '', '身份管理', 'admin', 'setting.systemRole', 'index', '[]', 10, 1, 1),
(9, 497, '', '组合数据', 'admin', 'setting.systemGroup', 'index', '[]', 276, 1, 1),
(11, 0, 'wechat', '公众号', 'admin', '', '', '[]', 12, 1, 1),
(12, 354, '', '微信关注回复', 'admin', 'wechat.reply', 'index', '{\"key\":\"subscribe\",\"title\":\"\\u7f16\\u8f91\\u65e0\\u914d\\u7f6e\\u9ed8\\u8ba4\\u56de\\u590d\"}', 3, 1, 1),
(17, 360, '', '微信菜单', 'admin', 'wechat.menus', 'index', '[]', 95, 1, 1),
(19, 11, '', '图文管理', 'admin', 'wechat.wechatNewsCategory', 'index', '[]', 60, 1, 1),
(21, 0, 'magic', '系统维护', 'admin', '', '', '[]', 1, 1, 1),
(22, 21, '', '物流公司', 'admin', 'system.express', 'index', '[]', 0, 1, 1),
(30, 354, '', '关键字回复', 'admin', 'wechat.reply', 'keyword', '[]', 2, 1, 1),
(31, 354, '', '无效关键词回复', 'admin', 'wechat.reply', 'index', '{\"key\":\"default\",\"title\":\"\\u7f16\\u8f91\\u65e0\\u6548\\u5173\\u952e\\u5b57\\u9ed8\\u8ba4\\u56de\\u590d\"}', 1, 1, 1),
(37, 31, '', '附加权限', 'admin', 'wechat.reply', '', '[]', 0, 0, 1),
(42, 19, '', '附加权限', 'admin', 'wechat.wechatNewsCategory', '', '[]', 0, 0, 1),
(43, 42, '', '添加图文消息', 'admin', 'wechat.wechatNewsCategory', 'create', '[]', 0, 0, 1),
(44, 42, '', '编辑图文消息', 'admin', 'wechat.wechatNewsCategory', 'edit', '[]', 0, 0, 1),
(45, 42, '', '删除图文消息', 'admin', 'wechat.wechatNewsCategory', 'delete', '[]', 0, 0, 1),
(46, 7, '', '配置分类附加权限', 'admin', 'setting.systemConfigTab', '', '[]', 0, 0, 1),
(47, 46, '', '添加配置分类', 'admin', 'setting.systemConfigTab', 'create', '[]', 0, 0, 1),
(48, 46, '', '添加配置', 'admin', 'setting.systemConfig', 'create', '[]', 0, 0, 1),
(49, 46, '', '编辑配置分类', 'admin', 'setting.systemConfigTab', 'edit', '[]', 0, 0, 1),
(50, 46, '', '删除配置分类', 'admin', 'setting.systemConfigTab', 'delete', '[]', 0, 0, 1),
(51, 46, '', '查看子字段', 'admin', 'setting.systemConfigTab', 'sonConfigTab', '[]', 0, 0, 1),
(52, 9, '', '组合数据附加权限', 'admin', 'setting.systemGroup', '', '[]', 0, 0, 1),
(53, 52, '', '添加数据', 'admin', 'setting.systemGroupData', 'create', '[]', 0, 0, 1),
(54, 52, '', '编辑数据', 'admin', 'setting.systemGroupData', 'edit', '[]', 0, 0, 1),
(55, 52, '', '删除数据', 'admin', 'setting.systemGroupData', 'delete', '[]', 0, 0, 1),
(56, 52, '', '数据列表', 'admin', 'setting.systemGroupData', 'index', '[]', 0, 0, 1),
(57, 52, '', '添加数据组', 'admin', 'setting.systemGroup', 'create', '[]', 0, 0, 1),
(58, 52, '', '删除数据组', 'admin', 'setting.systemGroup', 'delete', '[]', 0, 0, 1),
(59, 4, '', '管理员列表附加权限', 'admin', 'setting.systemAdmin', '', '[]', 0, 0, 1),
(60, 59, '', '添加管理员', 'admin', 'setting.systemAdmin', 'create', '[]', 0, 0, 1),
(61, 59, '', '编辑管理员', 'admin', 'setting.systemAdmin', 'edit', '[]', 0, 0, 1),
(62, 59, '', '删除管理员', 'admin', 'setting.systemAdmin', 'delete', '[]', 0, 0, 1),
(63, 8, '', '身份管理附加权限', 'admin', 'setting.systemRole', '', '[]', 0, 0, 1),
(64, 63, '', '添加身份', 'admin', 'setting.systemRole', 'create', '[]', 0, 0, 1),
(65, 63, '', '修改身份', 'admin', 'setting.systemRole', 'edit', '[]', 0, 0, 1),
(66, 63, '', '删除身份', 'admin', 'setting.systemRole', 'delete', '[]', 0, 0, 1),
(67, 8, '', '身份管理展示页', 'admin', 'setting.systemRole', 'index', '[]', 0, 0, 1),
(68, 4, '', '管理员列表展示页', 'admin', 'setting.systemAdmin', 'index', '[]', 0, 0, 1),
(69, 7, '', '配置分类展示页', 'admin', 'setting.systemConfigTab', 'index', '[]', 0, 0, 1),
(70, 9, '', '组合数据展示页', 'admin', 'setting.systemGroup', 'index', '[]', 0, 0, 1),
(73, 19, '', '图文消息展示页', 'admin', 'wechat.wechatNewsCategory', 'index', '[]', 0, 0, 1),
(74, 2, '', '菜单管理附加权限', 'admin', 'setting.systemMenus', '', '[]', 0, 0, 1),
(75, 74, '', '添加菜单', 'admin', 'setting.systemMenus', 'create', '[]', 0, 0, 1),
(76, 74, '', '编辑菜单', 'admin', 'setting.systemMenus', 'edit', '[]', 0, 0, 1),
(77, 74, '', '删除菜单', 'admin', 'setting.systemMenus', 'delete', '[]', 0, 0, 1),
(78, 2, '', '菜单管理展示页', 'admin', 'setting.systemMenus', 'index', '[]', 0, 0, 1),
(89, 30, '', '关键字回复附加权限', 'admin', 'wechat.reply', '', '[]', 0, 0, 1),
(90, 89, '', '添加关键字', 'admin', 'wechat.reply', 'add_keyword', '[]', 0, 0, 1),
(91, 89, '', '修改关键字', 'admin', 'wechat.reply', 'info_keyword', '[]', 0, 0, 1),
(92, 89, '', '删除关键字', 'admin', 'wechat.reply', 'delete', '[]', 0, 0, 1),
(93, 30, '', '关键字回复展示页', 'admin', 'wechat.reply', 'keyword', '[]', 0, 0, 1),
(94, 31, '', '无效关键词回复展示页', 'admin', 'wechat.reply', 'index', '[]', 0, 0, 1),
(95, 31, '', '无效关键词回复附加权限', 'admin', 'wechat.reply', '', '[]', 0, 0, 1),
(96, 95, '', '无效关键词回复提交按钮', 'admin', 'wechat.reply', 'save', '{\"key\":\"default\",\"title\":\"编辑无效关键字默认回复\"}', 0, 0, 1),
(97, 12, '', '微信关注回复展示页', 'admin', 'wechat.reply', 'index', '[]', 0, 0, 1),
(98, 12, '', '微信关注回复附加权限', 'admin', 'wechat.reply', '', '[]', 0, 0, 1),
(99, 98, '', '微信关注回复提交按钮', 'admin', 'wechat.reply', 'save', '{\"key\":\"subscribe\",\"title\":\"编辑无配置默认回复\"}', 0, 0, 1),
(100, 74, '', '添加提交菜单', 'admin', 'setting.systemMenus', 'save', '[]', 0, 0, 1),
(101, 74, '', '编辑提交菜单', 'admin', 'setting.systemMenus', 'update', '[]', 0, 1, 1),
(102, 59, '', '提交添加管理员', 'admin', 'setting.systemAdmin', 'save', '[]', 0, 0, 1),
(103, 59, '', '提交修改管理员', 'admin', 'setting.systemAdmin', 'update', '[]', 0, 0, 1),
(104, 63, '', '提交添加身份', 'admin', 'setting.systemRole', 'save', '[]', 0, 0, 1),
(105, 63, '', '提交修改身份', 'admin', 'setting.systemRole', 'update', '[]', 0, 0, 1),
(106, 46, '', '提交添加配置分类', 'admin', 'setting.systemConfigTab', 'save', '[]', 0, 0, 1),
(107, 46, '', '提交修改配置分类', 'admin', 'setting.systemConfigTab', 'update', '[]', 0, 0, 1),
(108, 46, '', '提交添加配置列表', 'admin', 'setting.systemConfig', 'save', '[]', 0, 0, 1),
(109, 52, '', '提交添加数据组', 'admin', 'setting.systemGroup', 'save', '[]', 0, 0, 1),
(110, 52, '', '提交修改数据组', 'admin', 'setting.systemGroup', 'update', '[]', 0, 0, 1),
(111, 52, '', '提交添加数据', 'admin', 'setting.systemGroupData', 'save', '[]', 0, 0, 1),
(112, 52, '', '提交修改数据', 'admin', 'setting.systemGroupData', 'update', '[]', 0, 0, 1),
(115, 42, '', '提交添加图文消息', 'admin', 'wechat.wechatNewsCategory', 'save', '[]', 0, 0, 1),
(116, 42, '', '提交编辑图文消息', 'admin', 'wechat.wechatNewsCategory', 'update', '[]', 0, 0, 1),
(117, 6, '', '配置列表附加权限', 'admin', 'setting.systemConfig', '', '[]', 0, 0, 1),
(118, 6, '', '配置列表展示页', 'admin', 'setting.systemConfig', 'index', '[]', 0, 0, 1),
(119, 117, '', '提交保存配置列表', 'admin', 'setting.systemConfig', 'save_basics', '[]', 0, 0, 1),
(123, 89, '', '提交添加关键字', 'admin', 'wechat.reply', 'save_keyword', '{\"dis\":\"1\"}', 0, 0, 1),
(124, 89, '', '提交修改关键字', 'admin', 'wechat.reply', 'save_keyword', '{\"dis\":\"2\"}', 0, 0, 1),
(126, 17, '', '微信菜单展示页', 'admin', 'wechat.menus', 'index', '[]', 0, 0, 1),
(127, 17, '', '微信菜单附加权限', 'admin', 'wechat.menus', '', '[]', 0, 0, 1),
(128, 127, '', '提交微信菜单按钮', 'admin', 'wechat.menus', 'save', '{\"dis\":\"1\"}', 0, 0, 1),
(130, 498, '', '系统日志', 'admin', 'system.systemLog', 'index', '[]', 274, 1, 1),
(131, 130, '', '管理员操作记录展示页', 'admin', 'system.systemLog', 'index', '[]', 0, 0, 1),
(137, 135, '', '添加通知模板', 'admin', 'setting.systemNotice', 'create', '[]', 0, 0, 1),
(138, 135, '', '编辑通知模板', 'admin', 'setting.systemNotice', 'edit', '[]', 0, 0, 1),
(139, 135, '', '删除辑通知模板', 'admin', 'setting.systemNotice', 'delete', '[]', 0, 0, 1),
(140, 135, '', '提交编辑辑通知模板', 'admin', 'setting.systemNotice', 'update', '[]', 0, 0, 1),
(141, 135, '', '提交添加辑通知模板', 'admin', 'setting.systemNotice', 'save', '[]', 0, 0, 1),
(144, 117, '', '获取配置列表上传文件的名称', 'admin', 'setting.systemConfig', 'getimagename', '[]', 0, 0, 1),
(145, 117, '', '配置列表上传文件', 'admin', 'setting.systemConfig', 'view_upload', '[]', 0, 0, 1),
(151, 0, 'user', '用户管理', 'admin', '', '', '[]', 16, 1, 1),
(153, 289, '', '权限管理', 'admin', 'setting.systemAdmin', '', '[]', 3, 1, 1),
(155, 154, '', '讲师产品展示页', 'admin', 'store.storeMerchant', 'index', '[]', 0, 0, 1),
(156, 154, '', '讲师产品附加权限', 'admin', 'store.storeMerchant', '', '[]', 0, 0, 1),
(158, 157, '', '讲师文章管理展示页', 'admin', 'wechat.wechatNews', 'merchantIndex', '[]', 0, 0, 1),
(159, 157, '', '讲师文章管理附加权限', 'admin', 'wechat.wechatNews', '', '[]', 0, 0, 1),
(173, 498, '', '文件校验', 'admin', 'system.systemFile', 'index', '[]', 273, 1, 1),
(174, 360, '', '模板消息', 'admin', 'wechat.wechatTemplate', 'index', '[]', 1, 1, 1),
(175, 675, '', '客服管理', 'admin', 'wechat.storeService', 'index', '[]', 1, 1, 1),
(177, 151, '', '用户管理', 'admin', 'user.user', 'index', '[]', 2, 1, 1),
(178, 177, '', '查看手机号', 'admin', 'is_phone', '', '[]', 0, 0, 1),
(179, 399, '', '充值订单', 'admin', 'finance.userRecharge', 'index', '[]', 3, 1, 1),
(200, 177, '', '赠送试卷提交', 'admin', 'user.user', 'sendTestPaper', '[]', 0, 0, 1),
(201, 177, '', '清除会员', 'admin', 'user.user', 'del_vip', '[]', 0, 0, 1),
(209, 175, '', '客服管理展示页', 'admin', 'wechat.storeService', 'index', '[]', 0, 0, 1),
(210, 175, '', '客服管理附加权限', 'admin', 'wechat.storeService', '', '[]', 0, 0, 1),
(211, 210, '', '客服管理添加', 'admin', 'wechat.storeService', 'create', '[]', 0, 0, 1),
(212, 210, '', '客服管理添加提交', 'admin', 'wechat.storeService', 'save', '[]', 0, 0, 1),
(213, 210, '', '客服管理编辑', 'admin', 'wechat.storeService', 'edit', '[]', 0, 0, 1),
(214, 210, '', '客服管理编辑提交', 'admin', 'wechat.storeService', 'update', '[]', 0, 0, 1),
(215, 210, '', '客服管理删除', 'admin', 'wechat.storeService', 'delete', '[]', 0, 0, 1),
(216, 179, '', '用户充值记录展示页', 'admin', 'finance.userRecharge', 'index', '[]', 3, 0, 1),
(217, 179, '', '用户充值记录附加权限', 'admin', 'finance.userRecharge', '', '[]', 2, 0, 1),
(218, 217, '', '用户充值记录退款', 'admin', 'finance.userRecharge', 'edit', '[]', 0, 0, 1),
(219, 217, '', '用户充值记录退款提交', 'admin', 'finance.userRecharge', 'updaterefundy', '[]', 0, 0, 1),
(220, 180, '', '预售卡管理批量修改预售卡金额', 'admin', 'presell.presellCard', 'batch_price', '[]', 0, 0, 1),
(221, 180, '', '预售卡管理批量修改预售卡金额提交', 'admin', 'presell.presellCard', 'savebatch', '[]', 0, 0, 1),
(222, 210, '', '客服管理聊天记录查询', 'admin', 'wechat.storeService', 'chat_user', '[]', 0, 0, 1),
(223, 210, '', '客服管理聊天记录查询详情', 'admin', 'wechat.storeService', 'chat_list', '[]', 0, 0, 1),
(237, 21, '', '刷新缓存', 'admin', 'system.clear', 'index', '[]', 0, 1, 1),
(239, 306, '', '用户提现', 'admin', 'finance.userExtract', 'index', '[]', 0, 1, 1),
(244, 294, '', '财务报表', 'admin', 'record.storeStatistics', 'index', '[]', 0, 1, 1),
(245, 293, '', '商品统计', 'admin', 'store.storeProduct', 'statistics', '[]', 0, 0, 1),
(246, 295, '', '用户统计', 'admin', 'user.user', 'user_analysis', '[]', 0, 1, 1),
(247, 153, '', '个人资料', 'admin', 'setting.systemAdmin', 'admininfo', '[]', 0, 0, 1),
(248, 247, '', '个人资料附加权限', 'admin', 'setting.systemAdmin', '', '[]', 0, 0, 1),
(249, 248, '', '个人资料提交保存', 'admin', 'setting.systemAdmin', 'setAdminInfo', '[]', 0, 0, 1),
(250, 247, '', '个人资料展示页', 'admin', 'setting.systemAdmin', 'admininfo', '[]', 0, 0, 1),
(251, 293, '', '订单统计', 'admin', 'order.storeOrder', 'orderchart', '[]', 0, 1, 1),
(269, 0, 'newspaper-o', '内容管理', 'admin', '', '', '[]', 20, 1, 1),
(276, 21, '', '附件管理', 'admin', '', '', '[]', 0, 0, 1),
(286, 0, 'paper-plane', '营销中心', 'admin', '', '', '[]', 15, 1, 1),
(287, 0, 'money', '财务管理', 'admin', '', '', '[]', 13, 1, 1),
(289, 0, 'gear', '系统设置', 'admin', '', '', '[]', 2, 1, 1),
(290, 289, '', '宝塔命令', 'admin', 'index', 'command', '[]', 0, 1, 1),
(306, 287, '', '财务操作', 'admin', '', '', '[]', 4, 1, 1),
(307, 287, '', '财务记录', 'admin', '', '', '[]', 3, 1, 1),
(308, 287, '', '佣金记录', 'admin', '', '', '[]', 2, 1, 1),
(312, 307, '', '余额监控', 'admin', 'finance.finance', 'bill', '{\"category\":\"now_money\"}', 0, 1, 1),
(313, 308, '', '佣金统计', 'admin', 'finance.finance', 'commission_list', '[]', 0, 1, 1),
(314, 308, '', '佣金监控', 'admin', 'finance.finance', 'bill', '{\"category\":\"brokerage_price\"}', 0, 1, 1),
(337, 286, '', '分销管理', 'admin', '', '', '[]', 4, 1, 1),
(353, 337, '', '分销配置', 'admin', 'setting.systemConfig', 'index', '{\"type\":\"2\",\"tab_id\":\"9\"}', 282, 1, 1),
(354, 11, '', '自动回复', '', '', '', '[]', 80, 1, 1),
(357, 507, '', '首页轮播图', 'admin', 'setting.systemGroupData', 'index', '{\"gid\":\"34\"}', 463, 1, 1),
(360, 11, '', '公众号配置', '', '', '', '[]', 100, 1, 1),
(362, 276, '', '附加权限', '', '', '', '[]', 0, 0, 1),
(363, 362, '', '上传图片', 'admin', 'widget.images', 'upload', '[]', 0, 0, 1),
(364, 362, '', '删除图片', 'admin', 'widget.images', 'delete', '[]', 0, 0, 1),
(365, 362, '', '附件管理', 'admin', 'widget.images', 'index', '[]', 0, 0, 1),
(374, 295, '', '分销会员业务', 'admin', 'record.record', 'userDistributionChart', '[]', 0, 1, 1),
(377, 498, '', '数据库维护', 'admin', 'system.systemDatabackup', 'index', '[]', 272, 1, 1),
(379, 673, '', '拼团列表', 'admin', 'ump.storeCombination', 'combina_list', '[]', 2, 1, 1),
(399, 0, 'th-list', '订单中心', 'admin', '', '', '[]', 17, 1, 1),
(400, 399, '', '课程订单', 'admin', 'order.storeOrder', 'index', '[]', 9, 1, 1),
(409, 507, '', '首页推荐', 'admin', 'setting.systemGroupData', 'recommend', '[]', 261, 1, 1),
(417, 269, '', '新闻资讯', 'admin', '', '', '[]', 5, 1, 1),
(418, 417, '', '新闻列表', 'admin', 'article.articleV1', 'index', '[]', 0, 1, 1),
(420, 502, '', '关键词搜索', 'admin', 'article.search', 'index', '[]', 258, 1, 1),
(421, 337, '', '分销列表', 'admin', 'user.userSpread', 'index', '[]', 287, 1, 1),
(423, 673, '', '拼团弹幕', 'admin', 'special.specialType', 'special_barrage', '{\"type\":\"3\"}', 1, 1, 1),
(429, 482, '', '课程分类', 'admin', 'special.subject', 'index', '[]', 299, 1, 1),
(431, 507, '', '导航配置', 'admin', 'setting.systemGroupData', 'navigation', '[]', 264, 1, 1),
(432, 507, '', '首页活动', 'admin', 'setting.systemGroupData', 'index_v1', '{\"gid\":\"56\"}', 262, 1, 1),
(433, 674, '', '提现银行配置', 'admin', 'setting.systemGroupData', 'index', '{\"gid\":\"52\"}', 2, 1, 1),
(434, 502, '', '关于我们', 'admin', 'setting.systemConfigContent', 'index', '{\"id\":\"1\"}', 4, 1, 1),
(435, 502, '', '用户付费协议', 'admin', 'setting.systemConfigContent', 'index', '{\"id\":\"2\"}', 5, 1, 1),
(436, 502, '', '讲师入驻协议', 'admin', 'setting.systemConfigContent', 'index', '{\"id\":\"3\"}', 3, 1, 1),
(454, 429, '', '删除分类', 'admin', 'special.subject', 'delete', '[]', 0, 0, 1),
(455, 429, '', '保存分类', 'admin', 'special.subject', 'save', '[]', 0, 0, 1),
(456, 429, '', '快速编辑分类', 'admin', 'special.subject', 'set_value', '[]', 0, 0, 1),
(457, 429, '', '创建分类', 'admin', 'special.subject', 'create', '[]', 0, 0, 1),
(461, 400, '', '订单展示页面', 'admin', 'order.storeOrder', 'order_list', '[]', 0, 0, 1),
(468, 400, '', '退款页面', 'admin', 'order.storeOrder', 'refund_y', '[]', 0, 0, 1),
(469, 400, '', '退款处理', 'admin', 'order.storeOrder', 'updateRefundY', '[]', 0, 0, 1),
(470, 400, '', '订单详情', 'admin', 'order.storeOrder', 'order_info', '[]', 0, 0, 1),
(474, 400, '', '不退款保存页面', 'admin', 'order.storeOrder', 'refund_n', '[]', 0, 0, 1),
(475, 400, '', '保存不退款信息', 'admin', 'order.storeOrder', 'updateRefundN', '[]', 0, 0, 1),
(482, 269, '', '课程管理', 'admin', '', '', '[]', 10, 1, 1),
(485, 482, '', '专题管理', 'admin', 'special.specialType', 'index', '{\"special_type\":\"3\"}', 2, 1, 1),
(487, 513, '', '直播列表', 'admin', 'live.aliyunLive', 'special_live', '{\"special_type\":\"4\"}', 292, 1, 1),
(488, 485, '', '专栏专题', 'admin', 'special.specialType', 'index', '{\"special_type\":\"5\"}', 1, 1, 1),
(490, 493, '', '卡密会员', 'admin', 'user.memberCard', 'index', '[]', 4, 1, 1),
(491, 493, '', '付费会员', 'admin', 'user.memberShip', 'index', '[]', 5, 1, 1),
(492, 493, '', '会员记录', 'admin', 'user.memberRecord', 'index', '[]', 3, 1, 1),
(493, 151, '', '会员管理', 'admin', '', '', '[]', 1, 1, 1),
(496, 674, '', '提现银行规则', 'admin', 'setting.systemGroupData', 'index', '{\"gid\":\"53\"}', 1, 1, 1),
(497, 21, '', '开发配置', 'admin', '', '', '[]', 278, 1, 1),
(498, 21, '', '安全维护', 'admin', '', '', '[]', 275, 1, 1),
(499, 1, '', '公众号配置', 'admin', 'setting.systemConfig', 'index', '{\"type\":\"1\",\"tab_id\":\"2\"}', 269, 1, 1),
(500, 1, '', '支付配置', 'admin', 'setting.systemConfig', 'index', '{\"type\":\"4\",\"tab_id\":\"4\"}', 266, 1, 1),
(501, 1, '', '阿里云配置', 'admin', 'setting.systemConfig', 'index', '{\"tab_id\":\"17\",\"type\":\"5\"}', 265, 1, 1),
(502, 675, '', '其他设置', 'admin', '', '', '[]', 2, 1, 1),
(505, 493, '', '会员权益', 'admin', 'setting.systemGroupData', 'index', '{\"gid\":\"57\"}', 2, 1, 1),
(506, 493, '', '会员说明', 'admin', 'setting.systemGroupData', 'index', '{\"gid\":\"58\"}', 1, 1, 1),
(507, 675, '', '移动端配置', 'admin', '', '', '[]', 3, 1, 1),
(508, 502, '', '其他配置', 'admin', 'setting.systemConfig', 'index', '{\"tab_id\":\"22\",\"type\":\"6\"}', 260, 0, 1),
(512, 287, '', '充值配置', 'admin', 'setting.systemConfig', 'index', '{\"tab_id\":\"23\",\"type\":\"7\"}', 1, 1, 1),
(513, 269, '', '直播管理', 'admin', '', '', '[]', 9, 1, 1),
(515, 513, '', '直播间管理', 'admin', 'live.aliyunLive', 'index', '{\"special_type\":\"4\",\"type\":\"2\"}', 290, 1, 1),
(517, 513, '', '直播贡献', 'admin', 'live.aliyunLive', 'live_reward', '[]', 288, 1, 1),
(518, 307, '', '虚拟币监控', 'admin', 'finance.finance', 'bill', '{\"category\":\"gold_num\"}', 0, 1, 1),
(519, 286, '', '活动管理', 'admin', 'ump.eventRegistration', 'index', '[]', 1, 1, 1),
(520, 485, '', '视频专题', 'admin', 'special.SpecialType', 'index', '{\"special_type\":\"3\"}', 2, 1, 1),
(522, 485, '', '音频专题', 'admin', 'special.SpecialType', 'index', '{\"special_type\":\"2\"}', 3, 1, 1),
(524, 485, '', '图文专题', 'admin', 'special.SpecialType', 'index', '{\"special_type\":\"1\"}', 4, 1, 1),
(526, 417, '', '新闻分类', 'admin', 'article.articleCategory', 'index', '[]', 1, 1, 1),
(527, 286, '', '签到管理', 'admin', '', '', '[]', 2, 1, 1),
(528, 527, '', '签到海报', 'admin', 'user.signPoster', 'index', '[]', 2, 1, 1),
(529, 527, '', '签到记录', 'admin', 'user.userSign', 'index', '[]', 1, 1, 1),
(530, 0, 'cart-plus', '实物商品', 'admin', '', '', '[]', 18, 1, 1),
(531, 530, '', '商品管理', 'admin', 'store.storeProduct', 'index', '{\"type\":\"1\"}', 1, 1, 1),
(532, 530, '', '商品分类', 'admin', 'store.storeCategory', 'index', '[]', 2, 1, 1),
(533, 21, '', '商业授权', 'admin', 'index', 'auth', '[]', 0, 1, 1),
(534, 507, '', '商品轮播图', 'admin', 'setting.systemGroupData', 'index', '{\"gid\":\"62\"}', 0, 1, 1),
(536, 513, '', '礼物设置', 'admin', 'live.aliyunLive', 'live_gift', '[]', 0, 1, 1),
(537, 289, '', '阿里云管理', 'admin', '', '', '[]', 1, 1, 1),
(538, 537, '', '对象存储', 'admin', 'setting.systemBucket', 'index', '[]', 1, 1, 1),
(539, 537, '', '直播配置', 'admin', 'setting.systemBroadcast', 'index', '[]', 0, 1, 1),
(540, 289, '', '一号通', 'admin', '', '', '[]', 2, 1, 1),
(541, 540, '', '账户管理', 'admin', 'setting.systemPlat', 'index', '[]', 0, 1, 1),
(542, 540, '', '服务管理', 'admin', 'setting.systemPlat', 'meal', '[]', 0, 1, 1),
(543, 540, '', '短信模版', 'admin', 'setting.systemPlat', 'sms_temp', '[]', 0, 1, 1),
(544, 0, 'mortar-board', '讲师管理', 'admin', '', '', '[]', 19, 1, 1),
(545, 544, '', '讲师列表', 'admin', 'special.lecturer', 'index', '[]', 1, 1, 1),
(546, 545, '', '生成讲师后台', 'admin', 'special.lecturer', 'mercreate', '[]', 0, 0, 1),
(550, 520, '', '删除专题', 'admin', 'special.SpecialType', 'delete', '[]', 0, 0, 1),
(551, 520, '', '保存拼团设置', 'admin', 'special.SpecialType', 'save_pink', '[]', 0, 0, 1),
(552, 520, '', '保存专题', 'admin', 'special.SpecialType', 'save_special', '[]', 0, 0, 1),
(553, 520, '', '创建编辑专题', 'admin', 'special.SpecialType', 'add', '[]', 0, 0, 1),
(554, 522, '', '删除专题', 'admin', 'special.SpecialType', 'delete', '[]', 0, 0, 1),
(555, 522, '', '保存拼团设置	', 'admin', 'special.SpecialType', 'save_pink', '[]', 0, 0, 1),
(556, 522, '', '保存专题', 'admin', 'special.SpecialType', 'save_special', '[]', 0, 0, 1),
(557, 522, '', '创建编辑专题', 'admin', 'special.SpecialType', 'add', '[]', 0, 0, 1),
(562, 545, '', '创建编辑讲师', 'admin', 'special.lecturer', 'create', '[]', 0, 0, 1),
(563, 545, '', '讲师保存', 'admin', 'special.lecturer', 'save_lecturer', '[]', 0, 0, 1),
(564, 545, '', '删除讲师', 'admin', 'special.lecturer', 'delete', '[]', 0, 0, 1),
(565, 532, '', '创建商品分类', 'admin', 'store.storeCategory', 'create', '[]', 0, 0, 1),
(566, 532, '', '删除商品分类', 'admin', 'store.storeCategory', 'delete', '[]', 0, 0, 1),
(567, 532, '', '商品分类保存', 'admin', 'store.storeCategory', 'save', '[]', 0, 0, 1),
(568, 531, '', '商品创建编辑', 'admin', 'store.storeProduct', 'create', '[]', 0, 0, 1),
(569, 531, '', '删除商品', 'admin', 'store.storeProduct', 'delete', '[]', 0, 0, 1),
(570, 531, '', '商品保存', 'admin', 'store.storeProduct', 'save', '[]', 0, 0, 1),
(571, 487, '', '直播回放', 'admin', 'live.aliyunLive', 'download_list', '[]', 0, 0, 1),
(572, 487, '', '直播拼团', 'admin', 'special.SpecialType', 'save_pink', '[]', 0, 0, 1),
(573, 487, '', '直播间转让', 'admin', 'live.aliyunLive', 'move_live_admin', '[]', 0, 0, 1),
(574, 487, '', '去直播', 'admin', 'live.aliyunLive', 'go_live', '[]', 0, 0, 1),
(575, 487, '', '删除直播专题', 'admin', 'live.aliyunLive', 'delete', '[]', 0, 0, 1),
(576, 487, '', '创建编辑专题', 'admin', 'special.SpecialType', 'add', '[]', 0, 0, 1),
(577, 515, '', '评论查看', 'admin', 'live.aliyunLive', 'comment_list', '[]', 0, 0, 1),
(578, 515, '', '嘉宾设置', 'admin', 'live.aliyunLive', 'guest_list', '[]', 0, 0, 1),
(579, 515, '', '直播提醒', 'admin', 'live.aliyunLive', 'send_remind', '[]', 0, 0, 1),
(580, 515, '', '直播间商品', 'admin', 'live.aliyunLive', 'live_store', '[]', 0, 0, 1),
(581, 515, '', '直播间课程', 'admin', 'live.aliyunLive', 'live_goods', '[]', 0, 0, 1),
(582, 515, '', '直播间禁止进入', 'admin', 'live.aliyunLive', 'live_no_entry', '[]', 0, 0, 1),
(583, 515, '', '直播间禁言', 'admin', 'live.aliyunLive', 'live_no_speaking', '[]', 0, 0, 1),
(584, 515, '', '直播间编辑保存', 'admin', 'live.aliyunLive', 'save_live', '[]', 0, 0, 1),
(585, 515, '', '编辑直播间', 'admin', 'live.aliyunLive', 'update_live', '[]', 0, 0, 1),
(586, 536, '', '礼物创建', 'admin', 'live.aliyunLive', 'create', '[]', 0, 0, 1),
(587, 536, '', '礼物提交保存', 'admin', 'live.aliyunLive', 'save', '[]', 0, 0, 1),
(588, 524, '', '创建编辑专题', 'admin', 'special.SpecialType', 'add', '[]', 0, 0, 1),
(589, 524, '', '保存专题', 'admin', 'special.SpecialType', 'save_special', '[]', 0, 0, 1),
(590, 524, '', '保存拼团设置', 'admin', 'special.SpecialType', 'save_pink', '[]', 0, 0, 1),
(591, 524, '', '删除专题', 'admin', 'special.SpecialType', 'delete', '[]', 0, 0, 1),
(596, 488, '', '创建编辑专题', 'admin', 'special.SpecialType', 'add', '[]', 0, 0, 1),
(597, 488, '', '保存专题', 'admin', 'special.SpecialType', 'save_special', '[]', 0, 0, 1),
(598, 488, '', '保存拼团设置', 'admin', 'special.SpecialType', 'save_pink', '[]', 0, 0, 1),
(599, 488, '', '删除专题', 'admin', 'special.SpecialType', 'delete', '[]', 0, 0, 1),
(600, 423, '', '弹幕页面展示', 'admin', 'special.SpecialType', 'special_barrage', '[]', 0, 0, 1),
(601, 423, '', '删除弹幕', 'admin', 'special.SpecialType', 'del_barrage', '[]', 0, 0, 1),
(602, 423, '', '添加弹幕', 'admin', 'special.SpecialType', 'save_barrage', '[]', 0, 0, 1),
(603, 400, '', '添加订单备注', 'admin', 'order.storeOrder', 'remark', '[]', 0, 0, 1),
(604, 400, '', '删除订单', 'admin', 'order.storeOrder', 'delete', '[]', 0, 0, 1),
(605, 418, '', '删除新闻', 'admin', 'article.articleV1', 'delete', '[]', 0, 0, 1),
(606, 418, '', '保存新闻', 'admin', 'article.articleV1', 'save_article', '[]', 0, 0, 1),
(607, 418, '', '添加编辑新闻', 'admin', 'article.articleV1', 'add_article', '[]', 0, 0, 1),
(608, 526, '', '编辑新闻分类', 'admin', 'article.articleCategory', 'edit', '[]', 0, 0, 1),
(609, 526, '', '添加新闻分类', 'admin', 'article.articleCategory', 'create', '[]', 0, 0, 1),
(610, 526, '', '编辑新闻分类保存', 'admin', 'article.articleCategory', 'update', '[]', 0, 0, 1),
(611, 526, '', '添加新闻分类保存', 'admin', 'article.articleCategory', 'save', '[]', 0, 0, 1),
(612, 177, '', '用户信息编辑', 'admin', 'user.user', 'edit', '[]', 0, 0, 1),
(613, 177, '', '用户页面展示', 'admin', 'user.user', 'index', '[]', 0, 0, 1),
(614, 177, '', '赠送会员提交', 'admin', 'user.user', 'gift_members', '[]', 0, 0, 1),
(615, 177, '', '赠送专题提交', 'admin', 'user.user', 'save_give', '[]', 0, 0, 1),
(616, 491, '', '会员页面展示', 'admin', 'user.memberShip', 'index', '[]', 0, 0, 1),
(617, 491, '', '会员等级编辑提交', 'admin', 'user.memberShip', 'save_sytem_vip', '[]', 0, 0, 1),
(618, 490, '', '会员卡创建', 'admin', 'user.memberCard', 'add_batch', '[]', 0, 0, 1),
(619, 490, '', '会员卡导出', 'admin', 'user.memberCard', 'card_list', '[]', 0, 0, 1),
(620, 490, '', '会员批次保存', 'admin', 'user.memberCard', 'save_batch', '[]', 0, 0, 1),
(621, 379, '', '拼团页面展示', 'admin', 'ump.storeCombination', 'combina_list', '[]', 0, 0, 1),
(622, 379, '', '拼团删除', 'admin', 'ump.storeCombination', 'delete_pink', '[]', 0, 0, 1),
(623, 379, '', '拼团助力提交', 'admin', 'ump.storeCombination', 'save', '[]', 0, 0, 1),
(624, 379, '', '拼团下架', 'admin', 'ump.storeCombination', 'down_pink', '[]', 0, 0, 1),
(625, 528, '', '海报列表展示', 'admin', 'user.signPoster', 'index', '[]', 0, 0, 1),
(626, 528, '', '海报删除', 'admin', 'user.signPoster', 'delete', '[]', 0, 0, 1),
(627, 528, '', '编辑保存', 'admin', 'user.signPoster', 'update', '[]', 0, 0, 1),
(628, 528, '', '新增保存', 'admin', 'user.signPoster', 'save', '[]', 0, 0, 1),
(629, 519, '', '活动列表展示', 'admin', 'ump.eventRegistration', 'index', '[]', 0, 0, 1),
(630, 519, '', '活动删除', 'admin', 'ump.eventRegistration', 'delete', '[]', 0, 0, 1),
(631, 519, '', '活动核销', 'admin', 'ump.eventRegistration', 'scanCodeSignIn', '[]', 0, 0, 1),
(632, 519, '', '活动保存', 'admin', 'ump.eventRegistration', 'add_new', '[]', 0, 0, 1),
(633, 239, '', '提现不通过', 'admin', 'finance.userExtract', 'fail', '[]', 0, 0, 1),
(634, 239, '', '提现通过', 'admin', 'finance.userExtract', 'succ', '[]', 0, 0, 1),
(635, 46, '', '修改配置', 'admin', 'setting.systemConfig', 'update_config', '[]', 0, 0, 1),
(636, 46, '', '删除配置', 'admin', 'setting.systemConfig', 'delete_cinfig', '[]', 0, 0, 1),
(637, 538, '', '添加存储空间', 'admin', 'setting.systemBucket', 'create', '[]', 0, 0, 1),
(638, 538, '', '保存存储空间', 'admin', 'setting.systemBucket', 'save', '[]', 0, 0, 1),
(639, 538, '', '删除储存空间', 'admin', 'setting.systemBucket', 'delete', '[]', 0, 0, 1),
(640, 538, '', '使用储存空间', 'admin', 'setting.systemBucket', 'userUse', '[]', 0, 0, 1),
(641, 539, '', '添加直播域名', 'admin', 'setting.systemBroadcast', 'create', '[]', 0, 0, 1),
(642, 539, '', '保存直播域名', 'admin', 'setting.systemBroadcast', 'save', '[]', 0, 0, 1),
(643, 539, '', '域名停用', 'admin', 'setting.systemBroadcast', 'offlines', '[]', 0, 0, 1),
(644, 539, '', '域名启用', 'admin', 'setting.systemBroadcast', 'onlines', '[]', 0, 0, 1),
(645, 539, '', '添加推流域名', 'admin', 'setting.systemBroadcast', 'addStreaming', '[]', 0, 0, 1),
(646, 539, '', '录制设置', 'admin', 'setting.systemBroadcast', 'toConfigure', '[]', 0, 0, 1),
(647, 539, '', '删除推流域名', 'admin', 'setting.systemBroadcast', 'delStreaming', '[]', 0, 0, 1),
(648, 539, '', '删除录制oss', 'admin', 'setting.systemBroadcast', 'delLiveAppRecordConfig', '[]', 0, 0, 1),
(649, 539, '', '使用直播域名', 'admin', 'setting.systemBroadcast', 'userLiveUse', '[]', 0, 0, 1),
(650, 526, '', '删除分类', 'admin', 'article.articleCategory', 'delete', '[]', 0, 0, 1),
(651, 430, '', '显示开关', 'admin', 'special.subject', 'set_show', '[]', 0, 0, 1),
(652, 545, '', '显示开关', 'admin', 'special.lecturer', 'set_show', '[]', 0, 0, 1),
(653, 482, '', '专题/直播显示开关', 'admin', 'special.SpecialType', 'set_value', '[]', 0, 0, 1),
(654, 532, '', '显示开关', 'admin', 'store.storeCategory', 'set_show', '[]', 0, 0, 1),
(655, 531, '', '上架开关', 'admin', 'store.storeProduct', 'set_show', '[]', 0, 0, 1),
(656, 418, '', '显示开关', 'admin', 'article.articleV1', 'set_show', '[]', 0, 0, 1),
(657, 491, '', '显示开关', 'admin', 'user.memberShip', 'set_publish', '[]', 0, 0, 1),
(658, 490, '', '激活开关', 'admin', 'user.memberCard', 'set_value', '[]', 0, 0, 1),
(659, 519, '', '显示开关', 'admin', 'ump.eventRegistration', 'set_show', '[]', 0, 0, 1),
(660, 429, '', '显示开关', 'admin', 'special.subject', 'set_show', '[]', 0, 0, 1),
(661, 482, '', '素材管理', 'admin', '', '', '[]', 0, 1, 1),
(662, 661, '', '素材分类', 'admin', 'special.specialTaskCategory', 'index', '[]', 1, 1, 1),
(663, 661, '', '素材列表', 'admin', 'special.SpecialType', 'sources_index', '[]', 0, 1, 1),
(664, 663, '', '创建编辑素材', 'admin', 'special.SpecialType', 'addSources', '[]', 0, 0, 1),
(665, 663, '', '保存素材', 'admin', 'special.SpecialType', 'save_source', '[]', 0, 0, 1),
(666, 663, '', '删除素材', 'admin', 'special.SpecialType', 'delete', '[]', 0, 0, 1),
(667, 662, '', '编辑素材分类', 'admin', 'special.specialTaskCategory', 'create', '[]', 0, 0, 1),
(668, 662, '', '保存素材分类', 'admin', 'special.specialTaskCategory', 'save', '[]', 0, 0, 1),
(669, 662, '', '删除素材分类', 'admin', 'special.specialTaskCategory', 'delete', '[]', 0, 0, 1),
(670, 399, '', '商品订单', 'admin', 'order.storeOrder', 'productOrder', '[]', 8, 1, 1),
(671, 399, '', '会员订单', 'admin', 'order.storeOrder', 'memberOrder', '[]', 7, 1, 1),
(672, 507, '', '底部菜单', 'admin', 'setting.systemGroupData', 'index', '{\"gid\":\"65\"}', 2, 1, 1),
(673, 286, '', '拼团管理', 'admin', '', '', '[]', 3, 1, 1),
(674, 337, '', '提现配置', 'admin', '', '', '[]', 1, 1, 1),
(675, 0, 'th', '系统管理', 'admin', '', '', '[]', 3, 1, 1),
(676, 673, '', '拼团专题', 'admin', 'special.SpecialType', 'groupList', '[]', 0, 1, 1),
(677, 507, '', '新闻简报', 'admin', 'setting.systemGroupData', 'index', '{\"gid\":\"66\"}', 5, 1, 1),
(678, 507, '', '我的-服务菜单', 'admin', 'setting.systemGroupData', 'center_menu', '', 0, 1, 1),
(679, 360, '', '订阅消息', 'admin', 'wechat.routineTemplate', 'index', '[]', 0, 1, 1),
(680, 675, '', '消息管理', 'admin', 'setting.systemMessage', 'index', '[]', 0, 1, 1),
(681, 269, '', '题库管理', 'admin', '', '', '[]', 8, 1, 1),
(682, 681, '', '题库分类', 'admin', 'questions.questionsCategpry', 'index', '[]', 2, 1, 1),
(683, 681, '', '题库列表', 'admin', 'questions.questions', 'index', '[]', 1, 1, 1),
(684, 0, 'leanpub', '教务管理', 'admin', '', '', '[]', 14, 1, 1),
(685, 684, '', '老师管理', 'admin', '', '', '[]', 1, 1, 1),
(686, 685, '', '老师分类', 'admin', 'educational.teacherCategpry', 'index', '[]', 2, 1, 1),
(687, 685, '', '老师列表', 'admin', 'educational.teacher', 'index', '[]', 1, 1, 1),
(688, 684, '', '班级管理', 'admin', '', '', '[]', 0, 1, 1),
(689, 688, '', '班级列表', 'admin', 'educational.classes', 'index', '[]', 1, 1, 1),
(690, 688, '', '学员列表', 'admin', 'educational.student', 'index', '[]', 0, 1, 1),
(691, 269, '', '试卷管理', 'admin', '', '', '[]', 6, 1, 1),
(692, 693, '', '练习分类', 'admin', 'questions.testPaperCategory', 'index', '{\"type\":\"1\"}', 3, 1, 1),
(693, 691, '', '练习管理', 'admin', '', '', '[]', 1, 1, 1),
(694, 691, '', '考试管理', 'admin', '', '', '[]', 0, 1, 1),
(695, 694, '', '考试分类', 'admin', 'questions.testPaperCategory', 'index', '{\"type\":\"2\"}', 3, 1, 1),
(696, 693, '', '练习列表', 'admin', 'questions.testPaper', 'index', '{\"type\":\"1\"}', 2, 1, 1),
(697, 694, '', '考试列表', 'admin', 'questions.testPaper', 'index', '{\"type\":\"2\"}', 2, 1, 1),
(698, 527, '', '签到设置', 'admin', 'setting.systemConfig', 'index', '{\"tab_id\":\"25\",\"type\":\"3\"}', 0, 1, 1),
(699, 693, '', '练习记录', 'admin', 'questions.testPaper', 'answerNotes', '{\"type\":\"1\"}', 1, 1, 1),
(700, 694, '', '考试记录', 'admin', 'questions.testPaper', 'answerNotes', '{\"type\":\"2\"}', 1, 1, 1),
(701, 269, '', '证书管理', 'admin', '', '', '[]', 7, 1, 1),
(702, 701, '', '证书列表', 'admin', 'questions.certificate', 'index', '[]', 1, 1, 1),
(703, 701, '', '证书记录', 'admin', 'questions.certificate', 'record', '[]', 0, 1, 1),
(704, 399, '', '考试订单', 'admin', 'order.testPaperOrder', 'index', '[]', 4, 1, 1),
(705, 686, '', '老师分类展示页', 'admin', 'educational.teacherCategpry', 'index', '[]', 0, 0, 1),
(706, 686, '', '编辑分类', 'admin', 'educational.teacherCategpry', 'create', '[]', 0, 0, 1),
(707, 686, '', '保存分类', 'admin', 'educational.teacherCategpry', 'save', '[]', 0, 0, 1),
(708, 686, '', '分类删除', 'admin', 'educational.teacherCategpry', 'delete', '[]', 0, 0, 1),
(709, 687, '', '老师列表展示页', 'admin', 'educational.teacher', 'index', '[]', 0, 0, 1),
(710, 687, '', '老师编辑', 'admin', 'educational.teacher', 'create', '[]', 0, 0, 1),
(711, 687, '', '老师保存', 'admin', 'educational.teacher', 'save_add', '[]', 0, 0, 1),
(712, 687, '', '老师删除', 'admin', 'educational.teacher', 'delete', '[]', 0, 0, 1),
(713, 689, '', '班级列表展示', 'admin', 'educational.classes', 'index', '[]', 0, 0, 1),
(714, 689, '', '班级编辑', 'admin', 'educational.classes', 'create', '[]', 0, 0, 1),
(715, 689, '', '班级保存', 'admin', 'educational.classes', 'save_add', '[]', 0, 0, 1),
(716, 689, '', '班级删除', 'admin', 'educational.classes', 'delete', '[]', 0, 0, 1),
(717, 690, '', '学员列表展示', 'admin', 'educational.student', 'index', '[]', 0, 0, 1),
(718, 690, '', '学员编辑', 'admin', 'educational.student', 'create', '[]', 0, 0, 1),
(719, 690, '', '学员保存', 'admin', 'educational.student', 'save_add', '[]', 0, 0, 1),
(720, 690, '', '删除学员', 'admin', 'educational.student', 'delete', '[]', 0, 0, 1),
(721, 690, '', '发送课程', 'admin', 'educational.student', 'special', '[]', 0, 0, 1),
(722, 690, '', '课程发送提交', 'admin', 'educational.student', 'sendSpecial', '[]', 0, 0, 1),
(723, 690, '', '发送试卷', 'admin', 'educational.student', 'send', '[]', 0, 0, 1),
(724, 690, '', '试卷发送保存', 'admin', 'educational.student', 'sendTestPaper', '[]', 0, 0, 1),
(725, 682, '', '题库分类展示', 'admin', 'questions.questionsCategpry', 'index', '[]', 0, 0, 1),
(726, 682, '', '分类编辑', 'admin', 'questions.questionsCategpry', 'create', '[]', 0, 0, 1),
(727, 682, '', '分类保存', 'admin', 'questions.questionsCategpry', 'save', '[]', 0, 0, 1),
(728, 682, '', '分类删除', 'admin', 'questions.questionsCategpry', 'delete', '[]', 0, 0, 1),
(729, 683, '', '题库列表展示', 'admin', 'questions.questions', 'index', '[]', 0, 0, 1),
(730, 683, '', '试题编辑', 'admin', 'questions.questions', 'add', '[]', 0, 0, 1),
(731, 683, '', '试题保存', 'admin', 'questions.questions', 'save_add', '[]', 0, 0, 1),
(732, 683, '', '试题删除', 'admin', 'questions.questions', 'delete', '[]', 0, 0, 1),
(733, 692, '', '分类展示', 'admin', 'questions.testPaperCategory', 'index', '[]', 0, 0, 1),
(734, 692, '', '分类编辑', 'admin', 'questions.testPaperCategory', 'create', '[]', 0, 0, 1),
(735, 692, '', '分类保存', 'admin', 'questions.testPaperCategory', 'save', '[]', 0, 0, 1),
(736, 692, '', '删除分类', 'admin', 'questions.testPaperCategory', 'delete', '[]', 0, 0, 1),
(737, 696, '', '试卷展示', 'admin', 'questions.testPaper', 'index', '[]', 0, 0, 1),
(738, 696, '', '试卷编辑', 'admin', 'questions.testPaper', 'add', '[]', 0, 0, 1),
(739, 696, '', '试卷保存', 'admin', 'questions.testPaper', 'save_add', '[]', 0, 0, 1),
(740, 696, '', '试卷删除', 'admin', 'questions.testPaper', 'delete', '[]', 0, 0, 1),
(742, 696, '', '推荐首页', 'admin', 'questions.testPaper', 'save_recommend', '[]', 0, 0, 1),
(743, 696, '', '取消推荐', 'admin', 'questions.testPaper', 'cancel_recommendation', '[]', 0, 0, 1),
(744, 695, '', '分类展示', 'admin', 'questions.testPaperCategory', 'index', '[]', 0, 0, 1),
(745, 695, '', '分类编辑', 'admin', 'questions.testPaperCategory', 'create', '[]', 0, 0, 1),
(746, 695, '', '分类保存', 'admin', 'questions.testPaperCategory', 'save', '[]', 0, 0, 1),
(747, 695, '', '删除分类', 'admin', 'questions.testPaperCategory', 'delete', '[]', 0, 0, 1),
(748, 697, '', '试卷展示', 'admin', 'questions.testPaper', 'index', '[]', 0, 0, 1),
(749, 697, '', '试卷编辑', 'admin', 'questions.testPaper', 'add', '[]', 0, 0, 1),
(750, 697, '', '试卷保存', 'admin', 'questions.testPaper', 'save_add', '[]', 0, 0, 1),
(751, 697, '', '试卷删除', 'admin', 'questions.testPaper', 'delete', '[]', 0, 0, 1),
(753, 697, '', '推荐首页', 'admin', 'questions.testPaper', 'save_recommend', '[]', 0, 0, 1),
(754, 697, '', '取消推荐', 'admin', 'questions.testPaper', 'cancel_recommendation', '[]', 0, 0, 1),
(755, 702, '', '证书列表展示', 'admin', 'questions.certificate', 'index', '[]', 0, 0, 1),
(756, 702, '', '证书编辑', 'admin', 'questions.certificate', 'add', '[]', 0, 0, 1),
(757, 702, '', '证书保存', 'admin', 'questions.certificate', 'save_add', '[]', 0, 0, 1),
(758, 702, '', '证书删除', 'admin', 'questions.certificate', 'delete', '[]', 0, 0, 1),
(759, 703, '', '证书记录撤销', 'admin', 'questions.certificate', 'revokeRecord', '[]', 0, 0, 1),
(760, 703, '', '证书记录删除', 'admin', 'questions.certificate', 'deleteRecord', '[]', 0, 0, 1),
(761, 703, '', '证书记录展示', 'admin', 'questions.certificate', 'record', '[]', 0, 0, 1),
(762, 704, '', '订单退款', 'admin', 'order.testPaperOrder', 'refund_y', '[]', 0, 0, 1),
(763, 704, '', '退款提交', 'admin', 'order.testPaperOrder', 'updateRefundY', '[]', 0, 0, 1),
(764, 704, '', '订单详情', 'admin', 'order.testPaperOrder', 'order_info', '[]', 0, 0, 1),
(765, 431, '', '添加/编辑固定导航', 'admin', 'setting.systemGroupData', 'create_recemmend', '[]', 0, 0, 1),
(766, 431, '', '添加/编辑自定义导航', 'admin', 'setting.systemGroupData', 'create_recemmend_custom', '[]', 0, 0, 1),
(767, 431, '', '保存固定导航', 'admin', 'setting.systemGroupData', 'save_recemmend', '[]', 0, 0, 1),
(768, 431, '', '保存自定义导航', 'admin', 'setting.systemGroupData', 'save_recemmend_custom', '[]', 0, 0, 1),
(769, 431, '', '删除导航', 'admin', 'setting.systemGroupData', 'delete_recomm', '[]', 0, 0, 1),
(770, 431, '', '导航状态设置', 'admin', 'setting.systemGroupData', 'set_show', '[]', 0, 0, 1),
(771, 432, '', '活动展示', 'admin', 'setting.systemGroupData', 'index_v1', '[]', 0, 0, 1),
(772, 432, '', '活动编辑', 'admin', 'setting.systemGroupData', 'create_v1', '[]', 0, 0, 1),
(773, 432, '', '活动保存', 'admin', 'setting.systemGroupData', 'save_group_data', '[]', 0, 0, 1),
(774, 432, '', '活动状态', 'admin', 'setting.systemGroupData', 'set_group_data', '[]', 0, 0, 1),
(775, 432, '', '活动开关', 'admin', 'setting.systemGroupData', 'is_show_or_hide', '[]', 0, 0, 1),
(776, 409, '', '推荐展示页', 'admin', 'setting.systemGroupData', 'recommend', '[]', 0, 0, 1),
(777, 409, '', '添加/编辑推荐', 'admin', 'setting.systemGroupData', 'create_recemmend_v1', '[]', 0, 0, 1),
(778, 409, '', '推荐状态', 'admin', 'setting.systemGroupData', 'set_show', '[]', 0, 0, 1),
(779, 409, '', '新闻/素材内容展示', 'admin', 'setting.systemGroupData', 'recemmend_article_content', '[]', 0, 0, 1),
(780, 409, '', '练习/考试内容展示', 'admin', 'setting.systemGroupData', 'recemmend_test_content', '[]', 0, 0, 1),
(781, 409, '', '其他内容展示', 'admin', 'setting.systemGroupData', 'recemmend_content', '[]', 0, 0, 1),
(782, 409, '', 'banner图展示', 'admin', 'setting.systemGroupData', 'recemmend_banner', '[]', 0, 0, 1),
(783, 409, '', '推荐删除', 'admin', 'setting.systemGroupData', 'delete_recomm', '[]', 0, 0, 1),
(784, 409, '', '推荐内容移除', 'admin', 'setting.systemGroupData', 'recemmed_delete', '[]', 0, 0, 1),
(785, 409, '', '推荐内容页排序', 'admin', 'setting.systemGroupData', 'set_recemmend_value', '[]', 0, 0, 1),
(786, 409, '', 'banner图添加/编辑', 'admin', 'setting.systemGroupData', 'create_recemmend_banner', '[]', 0, 0, 1),
(787, 409, '', 'banner保存', 'admin', 'setting.systemGroupData', 'save_recemmend_banner', '[]', 0, 0, 1),
(788, 409, '', 'banner删除', 'admin', 'setting.systemGroupData', 'delete_banner', '[]', 0, 0, 1),
(789, 409, '', '推荐排序', 'admin', 'setting.systemGroupData', 'set_value', '[]', 0, 0, 1),
(790, 434, '', '内容展示', 'admin', 'setting.systemConfigContent', 'index', '[]', 0, 0, 1),
(791, 434, '', '内容提交', 'admin', 'setting.systemConfigContent', 'save', '[]', 0, 0, 1),
(792, 435, '', '内容展示', 'admin', 'setting.systemConfigContent', 'index', '[]', 0, 0, 1),
(793, 435, '', '内容提交', 'admin', 'setting.systemConfigContent', 'save', '[]', 0, 0, 1),
(794, 420, '', '关键词展示', 'admin', 'article.search', 'index', '[]', 0, 0, 1),
(795, 420, '', '关键词添加', 'admin', 'article.search', 'save', '[]', 0, 0, 1),
(796, 420, '', '关键词删除', 'admin', 'article.search', 'del_search', '[]', 0, 0, 1),
(797, 678, '', '菜单展示', 'admin', 'setting.systemGroupData', 'center_menu', '[]', 0, 0, 1),
(798, 678, '', '菜单添加/编辑', 'admin', 'setting.systemGroupData', 'create_recemmend_custom', '[]', 0, 0, 1),
(799, 678, '', '菜单保存', 'admin', 'setting.systemGroupData', 'save_recemmend_custom', '[]', 0, 0, 1),
(800, 678, '', '菜单排序设置', 'admin', 'setting.systemGroupData', 'set_value', '[]', 0, 0, 1),
(801, 678, '', '菜单状态设置', 'admin', 'setting.systemGroupData', 'set_show', '[]', 0, 0, 1),
(802, 678, '', '菜单删除', 'admin', 'setting.systemGroupData', 'delete_recomm', '[]', 0, 0, 1),
(803, 507, '', '首页广告', 'admin', 'setting.systemGroupData', 'index', '{\"gid\":\"69\"}', 4, 1, 1),
(804, 362, '', '修改图片名称', 'admin', 'widget.images', 'updateImageTitle', '[]', 0, 0, 1),
(805, 362, '', '移动图片分类', 'admin', 'widget.images', 'moveImgCecate', '[]', 0, 0, 1),
(806, 362, '', '添加分类', 'admin', 'widget.images', 'saveCate', '[]', 0, 0, 1),
(807, 362, '', '更新分类', 'admin', 'widget.images', 'updateCate', '[]', 0, 0, 1),
(808, 362, '', '删除分类', 'admin', 'widget.images', 'deletecate', '[]', 0, 0, 1),
(809, 524, '', '专题关联证书', 'admin', 'special.SpecialType', 'certificate', '[]', 0, 0, 1),
(810, 524, '', '关联证书保存', 'admin', 'special.SpecialType', 'certificateRecord', '[]', 0, 0, 1),
(811, 524, '', '学习记录', 'admin', 'special.SpecialType', 'learningRecords', '[]', 0, 0, 1),
(812, 524, '', '关联练习/考试', 'admin', 'special.SpecialType', 'testPaperRelation', '[]', 0, 0, 1),
(813, 524, '', '获取试卷', 'admin', 'special.SpecialType', 'relation', '[]', 0, 0, 1),
(814, 524, '', '试卷保存关联', 'admin', 'special.SpecialType', 'addRelation', '[]', 0, 0, 1),
(815, 524, '', '关联试卷排序', 'admin', 'special.SpecialType', 'upRelationSort', '[]', 0, 0, 1),
(816, 524, '', '移除关联试卷', 'admin', 'special.SpecialType', 'delRelation', '[]', 0, 0, 1),
(817, 522, '', '专题关联证书', 'admin', 'special.SpecialType', 'certificate', '[]', 0, 0, 1),
(818, 522, '', '关联证书保存', 'admin', 'special.SpecialType', 'certificateRecord', '[]', 0, 0, 1),
(819, 522, '', '学习记录', 'admin', 'special.SpecialType', 'learningRecords', '[]', 0, 0, 1),
(820, 522, '', '关联练习/考试', 'admin', 'special.SpecialType', 'testPaperRelation', '[]', 0, 0, 1),
(821, 522, '', '获取试卷', 'admin', 'special.SpecialType', 'relation', '[]', 0, 0, 1),
(822, 522, '', '试卷保存关联', 'admin', 'special.SpecialType', 'addRelation', '[]', 0, 0, 1),
(823, 522, '', '关联试卷排序', 'admin', 'special.SpecialType', 'upRelationSort', '[]', 0, 0, 1),
(824, 522, '', '移除关联试卷', 'admin', 'special.SpecialType', 'delRelation', '[]', 0, 0, 1),
(825, 520, '', '专题关联证书', 'admin', 'special.SpecialType', 'certificate', '[]', 0, 0, 1),
(826, 520, '', '关联证书保存', 'admin', 'special.SpecialType', 'certificateRecord', '[]', 0, 0, 1),
(827, 520, '', '学习记录', 'admin', 'special.SpecialType', 'learningRecords', '[]', 0, 0, 1),
(828, 520, '', '关联练习/考试', 'admin', 'special.SpecialType', 'testPaperRelation', '[]', 0, 0, 1),
(829, 520, '', '获取试卷', 'admin', 'special.SpecialType', 'relation', '[]', 0, 0, 1),
(830, 520, '', '试卷保存关联', 'admin', 'special.SpecialType', 'addRelation', '[]', 0, 0, 1),
(831, 520, '', '关联试卷排序', 'admin', 'special.SpecialType', 'upRelationSort', '[]', 0, 0, 1),
(832, 520, '', '移除关联试卷', 'admin', 'special.SpecialType', 'delRelation', '[]', 0, 0, 1),
(833, 488, '', '学习记录', 'admin', 'special.SpecialType', 'learningRecords', '[]', 0, 0, 1),
(834, 488, '', '关联练习/考试', 'admin', 'special.SpecialType', 'testPaperRelation', '[]', 0, 0, 1),
(835, 488, '', '获取试卷', 'admin', 'special.SpecialType', 'relation', '[]', 0, 0, 1),
(836, 488, '', '试卷保存关联', 'admin', 'special.SpecialType', 'addRelation', '[]', 0, 0, 1),
(837, 488, '', '关联试卷排序', 'admin', 'special.SpecialType', 'upRelationSort', '[]', 0, 0, 1),
(838, 488, '', '移除关联试卷', 'admin', 'special.SpecialType', 'delRelation', '[]', 0, 0, 1),
(839, 485, '', '轻专题', 'admin', 'special.SpecialType', 'index', '{\"special_type\":\"6\"}', 1, 1, 1),
(840, 482, '', '专题评论', 'admin', 'special.specialReply', 'index', '[]', 1, 1, 1),
(841, 530, '', '商品评论', 'admin', 'store.storeProductReply', 'index', '[]', 0, 1, 1),
(842, 286, '', '兑换码', 'admin', 'ump.specialBatch', 'index', '[]', 0, 1, 1),
(843, 269, '', '资料管理', 'admin', '', '', '[]', 4, 1, 1),
(844, 843, '', '资料分类', 'admin', 'download.dataDownloadCategpry', 'index', '[]', 1, 1, 1),
(845, 843, '', '资料列表', 'admin', 'download.dataDownload', 'index', '[]', 0, 1, 1),
(846, 399, '', '资料订单', 'admin', 'order.dataDownloadOrder', 'index', '[]', 5, 1, 1),
(847, 839, '', '页面展示', 'admin', 'special.specialType', 'index', '[]', 0, 0, 1),
(848, 839, '', '创建编辑专题', 'admin', 'special.specialType', 'single_add', '[]', 0, 0, 1),
(849, 839, '', '保存专题', 'admin', 'special.specialType', 'save_single_special', '[]', 0, 0, 1),
(850, 839, '', '保存拼团设置', 'admin', 'special.specialType', 'save_pink', '[]', 0, 0, 1),
(851, 839, '', '删除专题', 'admin', 'special.specialType', 'delete', '[]', 0, 0, 1),
(852, 839, '', '移除关联试卷', 'admin', 'special.specialType', 'delRelation', '[]', 0, 0, 1),
(853, 839, '', '专题关联证书', 'admin', 'special.specialType', 'certificate', '[]', 0, 0, 1),
(854, 839, '', '关联证书保存', 'admin', 'special.specialType', 'certificateRecord', '[]', 0, 0, 1),
(855, 839, '', '学习记录', 'admin', 'special.specialType', 'learningRecords', '[]', 0, 0, 1),
(856, 839, '', '关联练习/考试', 'admin', 'special.specialType', 'testPaperRelation', '[]', 0, 0, 1),
(857, 839, '', '获取试卷', 'admin', 'special.specialType', 'relation', '[]', 0, 0, 1),
(858, 839, '', '试卷保存关联', 'admin', 'special.specialType', 'addRelationTestPaper', '[]', 0, 0, 1),
(859, 839, '', '关联试卷排序', 'admin', 'special.specialType', 'upTestPaperSort', '[]', 0, 0, 1),
(860, 840, '', '创建虚拟评论', 'admin', 'special.specialReply', 'index', '[]', 0, 0, 1),
(861, 840, '', '创建虚拟评论', 'admin', 'special.specialReply', 'create_false', '[]', 0, 0, 1),
(862, 840, '', '保存虚拟评论', 'admin', 'special.specialReply', 'save_false', '[]', 0, 0, 1),
(863, 840, '', '加精设置', 'admin', 'special.specialReply', 'set_value', '[]', 0, 0, 1),
(864, 840, '', '评论回复', 'admin', 'special.specialReply', 'set_reply', '[]', 0, 0, 1),
(865, 840, '', '评论删除', 'admin', 'special.specialReply', 'delete', '[]', 0, 0, 1),
(866, 683, '', '导入试题', 'admin', 'questions.questions', 'imports', '[]', 0, 0, 1),
(867, 683, '', '上传文件', 'admin', 'questions.questions', 'file_import_upload', '[]', 0, 0, 1),
(868, 683, '', '导出试题', 'admin', 'questions.questions', 'getQuestionsList', '[]', 0, 0, 1),
(869, 683, '', '下载模版', 'admin', 'questions.questions', 'downloadExcel', '[]', 0, 0, 1),
(870, 683, '', '试题导入', 'admin', 'questions.questions', 'importTestQuestions', '[]', 0, 0, 1),
(871, 844, '', '分类页面展示', 'admin', 'download.dataDownloadCategpry', 'index', '[]', 0, 0, 1),
(872, 844, '', '编辑分类', 'admin', 'download.dataDownloadCategpry', 'create', '[]', 0, 0, 1),
(873, 844, '', '保存分类', 'admin', 'download.dataDownloadCategpry', 'save', '[]', 0, 0, 1),
(874, 844, '', '状态设置', 'admin', 'download.dataDownloadCategpry', 'set_show', '[]', 0, 0, 1),
(875, 844, '', '分类删除', 'admin', 'download.dataDownloadCategpry', 'delete', '[]', 0, 0, 1),
(876, 845, '', '资料展示列表', 'admin', 'download.dataDownload', 'index', '[]', 0, 0, 1),
(877, 845, '', '编辑资料', 'admin', 'download.dataDownload', 'add', '[]', 0, 0, 1),
(878, 845, '', '保存资料', 'admin', 'download.dataDownload', 'save_data', '[]', 0, 0, 1),
(879, 845, '', '状态设置', 'admin', 'download.dataDownload', 'set_value', '[]', 0, 0, 1),
(880, 845, '', '推荐管理', 'admin', 'download.dataDownload', 'recommend', '[]', 0, 0, 1),
(881, 845, '', '推荐移动端', 'admin', 'download.dataDownload', 'save_recommend', '[]', 0, 0, 1),
(882, 845, '', '移动端取消推荐', 'admin', 'download.dataDownload', 'cancel_recommendation', '[]', 0, 0, 1),
(883, 845, '', '下载记录', 'admin', 'download.dataDownload', 'records', '[]', 0, 0, 1),
(884, 845, '', '删除资料', 'admin', 'download.dataDownload', 'delete', '[]', 0, 0, 1),
(885, 418, '', '推荐管理', 'admin', 'article.articleV1', 'recommend', '[]', 0, 0, 1),
(886, 418, '', '推荐移动端', 'admin', 'article.articleV1', 'save_recommend', '[]', 0, 0, 1),
(887, 418, '', '移动端取消推荐', 'admin', 'article.articleV1', 'cancel_recommendation', '[]', 0, 0, 1),
(888, 531, '', '推荐管理', 'admin', 'store.storeProduct', 'recommend', '[]', 0, 0, 1),
(889, 531, '', '推荐管理', 'admin', 'store.storeProduct', 'save_recommend', '[]', 0, 0, 1),
(890, 531, '', '取消推荐', 'admin', 'store.storeProduct', 'cancel_recommendation', '[]', 0, 0, 1),
(891, 841, '', '评论列表', 'admin', 'store.storeProductReply', 'index', '[]', 0, 0, 1),
(892, 841, '', '创建虚拟评论', 'admin', 'store.storeProductReply', 'create_false', '[]', 0, 0, 1),
(893, 841, '', '保存虚拟评论', 'admin', 'store.storeProductReply', 'save_false', '[]', 0, 0, 1),
(894, 841, '', '加精设置', 'admin', 'store.storeProductReply', 'set_value', '[]', 0, 0, 1),
(895, 841, '', '评论回复', 'admin', 'store.storeProductReply', 'set_reply', '[]', 0, 0, 1),
(896, 841, '', '评论删除', 'admin', 'store.storeProductReply', 'delete', '[]', 0, 0, 1),
(897, 846, '', '订单展示页面', 'admin', 'order.dataDownloadOrder', 'data_download_order_list', '[]', 0, 0, 1),
(898, 846, '', '订单详情', 'admin', 'order.dataDownloadOrder', 'order_info', '[]', 0, 0, 1),
(899, 846, '', '退款提交', 'admin', 'order.dataDownloadOrder', 'updateRefundY', '[]', 0, 0, 1),
(900, 846, '', '订单退款', 'admin', 'order.dataDownloadOrder', 'refund_y', '[]', 0, 0, 1),
(901, 846, '', '订单删除', 'admin', 'order.dataDownloadOrder', 'delete', '[]', 0, 0, 1),
(902, 704, '', '订单展示页面', 'admin', 'order.testPaperOrder', 'test_paper_order_list', '[]', 0, 0, 1),
(903, 704, '', '订单删除', 'admin', 'order.testPaperOrder', 'delete', '[]', 0, 0, 1),
(904, 842, '', '活动列表', 'admin', 'ump.specialBatch', 'index', '[]', 0, 0, 1),
(905, 842, '', '活动编辑', 'admin', 'ump.specialBatch', 'add_batch', '[]', 0, 0, 1),
(906, 842, '', '保存活动', 'admin', 'ump.specialBatch', 'save_batch', '[]', 0, 0, 1),
(907, 842, '', '活动设置', 'admin', 'ump.specialBatch', 'set_value', '[]', 0, 0, 1),
(908, 842, '', '活动删除', 'admin', 'ump.specialBatch', 'delete', '[]', 0, 0, 1),
(909, 842, '', '查看兑换码', 'admin', 'ump.specialBatch', 'card_index', '[]', 0, 0, 1),
(910, 842, '', '兑换码导出', 'admin', 'ump.specialBatch', 'card_list', '[]', 0, 0, 1),
(911, 842, '', '兑换码设置', 'admin', 'ump.specialBatch', 'set_value', '[]', 0, 0, 1),
(912, 175, '', '客服配置', 'admin', 'setting.systemConfig', 'index', '{\"tab_id\":\"26\",\"type\":\"3\"}', 1, 1, 1),
(913, 175, '', '微信客服', 'admin', 'wechat.storeService', 'index', '[]', 0, 1, 1),
(914, 670, '', '订单展示页面', 'admin', 'order.storeOrder', 'order_list', '[]', 0, 0, 1),
(915, 670, '', '修改订单金额', 'admin', 'order.storeOrder', 'edit', '[]', 0, 0, 1),
(916, 670, '', '保存订单金额', 'admin', 'order.storeOrder', 'update', '[]', 0, 0, 1),
(917, 670, '', '发货', 'admin', 'order.storeOrder', 'deliver_goods', '[]', 0, 0, 1),
(918, 670, '', '发货保存', 'admin', 'order.storeOrder', 'updateDeliveryGoods', '[]', 0, 0, 1),
(919, 670, '', '确认收货', 'admin', 'order.storeOrder', 'take_delivery', '[]', 0, 0, 1),
(920, 670, '', '退款页面', 'admin', 'order.storeOrder', 'refund_y', '[]', 0, 0, 1),
(921, 670, '', '退款处理', 'admin', 'order.storeOrder', 'updateRefundY', '[]', 0, 0, 1),
(922, 670, '', '订单详情', 'admin', 'order.storeOrder', 'order_info', '[]', 0, 0, 1),
(923, 670, '', '修改配送信息', 'admin', 'order.storeOrder', 'distribution', '[]', 0, 0, 1),
(924, 670, '', '保存配送信息', 'admin', 'order.storeOrder', 'updateDistribution', '[]', 0, 0, 1),
(925, 670, '', '不退款保存页面', 'admin', 'order.storeOrder', 'refund_n', '[]', 0, 0, 1),
(926, 670, '', '保存不退款信息', 'admin', 'order.storeOrder', 'updateRefundN', '[]', 0, 0, 1),
(927, 670, '', '立即支付', 'admin', 'order.storeOrder', 'offline', '[]', 0, 0, 1),
(928, 670, '', '添加订单备注', 'admin', 'order.storeOrder', 'remark', '[]', 0, 0, 1),
(929, 670, '', '删除订单', 'admin', 'order.storeOrder', 'delete', '[]', 0, 0, 1),
(930, 671, '', '订单展示页面', 'admin', 'order.storeOrder', 'order_list', '[]', 0, 0, 1),
(931, 671, '', '退款页面', 'admin', 'order.storeOrder', 'refund_y', '[]', 0, 0, 1),
(932, 671, '', '退款处理', 'admin', 'order.storeOrder', 'updateRefundY', '[]', 0, 0, 1),
(933, 671, '', '订单详情', 'admin', 'order.storeOrder', 'order_info', '[]', 0, 0, 1),
(934, 671, '', '添加订单备注', 'admin', 'order.storeOrder', 'remark', '[]', 0, 0, 1),
(935, 671, '', '删除订单', 'admin', 'order.storeOrder', 'delete', '[]', 0, 0, 1),
(936, 307, '', '微信监控', 'admin', 'finance.finance', 'bill', '{\"category\":\"weixin\"}', 0, 1, 1),
(937, 307, '', '支付宝监控', 'admin', 'finance.finance', 'bill', '{\"category\":\"zhifubao\"}', 0, 1, 1),
(938, 179, '', '删除订单', 'admin', 'finance.userRecharge', 'delete', '[]', 1, 0, 1),
(939, 399, '', '报名订单', 'admin', 'ump.eventRegistration', 'order', '[]', 6, 1, 1),
(940, 939, '', '订单展示页面', 'admin', 'ump.eventRegistration', 'order', '[]', 3, 0, 1),
(941, 939, '', '订单退款', 'admin', 'ump.eventRegistration', 'refund_y', '[]', 2, 0, 1),
(942, 939, '', '删除订单', 'admin', 'ump.eventRegistration', 'order_delete', '[]', 1, 0, 1),
(944, 485, '', '推荐移动端', 'admin', 'special.specialType', 'save_recommend', '[]', 0, 0, 1),
(945, 485, '', '移动端取消推荐', 'admin', 'special.SpecialType', 'cancel_recommendation', '[]', 0, 0, 1),
(946, 485, '', '推荐pc端', 'admin', 'special.SpecialType', 'save_web_recommend', '[]', 0, 0, 1),
(947, 485, '', 'PC端取消推荐', 'admin', 'special.SpecialType', 'cancel_web_recommendation', '[]', 0, 0, 1),
(948, 487, '', '推荐移动端', 'admin', 'special.specialType', 'save_recommend', '[]', 0, 0, 1),
(949, 487, '', '移动端取消推荐', 'admin', 'special.SpecialType', 'cancel_recommendation', '[]', 0, 0, 1),
(950, 487, '', '推荐pc端', 'admin', 'special.SpecialType', 'save_web_recommend', '[]', 0, 0, 1),
(951, 487, '', 'PC端取消推荐', 'admin', 'special.SpecialType', 'cancel_web_recommendation', '[]', 0, 0, 1),
(952, 845, '', '推荐pc端', 'admin', 'download.dataDownload', 'save_web_recommend', '[]', 0, 0, 1),
(953, 845, '', 'PC端取消推荐', 'admin', 'download.dataDownload', 'cancel_web_recommendation', '[]', 0, 0, 1),
(954, 418, '', '推荐pc端', 'admin', 'article.articleV1', 'save_web_recommend', '[]', 0, 0, 1),
(955, 418, '', 'PC端取消推荐', 'admin', 'article.articleV1', 'cancel_web_recommendation', '[]', 0, 0, 1),
(956, 418, '', '新闻展示页', 'admin', 'article.articleV1', 'index', '[]', 0, 0, 1),
(957, 545, '', '推荐pc端', 'admin', 'special.lecturer', 'save_web_recommend', '[]', 0, 0, 1),
(958, 545, '', 'PC端取消推荐', 'admin', 'special.lecturer', 'cancel_web_recommendation', '[]', 0, 0, 1),
(959, 545, '', '推荐移动端', 'admin', 'special.lecturer', 'save_recommend', '[]', 0, 0, 1),
(960, 545, '', '移动端取消推荐', 'admin', 'special.lecturer', 'cancel_recommendation', '[]', 0, 0, 1),
(961, 544, '', '入驻申请', 'admin', 'merchant.userEnter', 'index', '[]', 5, 1, 1),
(963, 544, '', '后台菜单', 'admin', 'merchant.merchantMenus', 'index', '[]', 0, 1, 1),
(964, 0, 'legal', '审核管理', 'admin', '', '', '[]', 18, 1, 1),
(965, 964, '', '专题审核', 'admin', 'special.specialType', 'examine', '[]', 5, 1, 1),
(966, 964, '', '商品审核', 'admin', 'store.storeProduct', 'examine', '[]', 4, 1, 1),
(967, 964, '', '资料审核', 'admin', 'download.dataDownload', 'examine', '[]', 3, 1, 1),
(968, 964, '', '活动审核', 'admin', 'ump.eventRegistration', 'examine', '[]', 2, 1, 1),
(969, 964, '', '直播审核', 'admin', 'live.aliyunLive', 'live_audit', '[]', 1, 1, 1),
(970, 287, '', '讲师财务', 'admin', '', '', '[]', 5, 1, 1),
(971, 970, '', '分成记录', 'admin', 'finance.finance', 'merOrderBill', '[]', 5, 1, 1),
(972, 306, '', '讲师提现', 'admin', 'finance.userExtract', 'merIndex', '[]', 4, 1, 1),
(973, 970, '', '资金流水', 'admin', 'finance.finance', 'merbill', '[]', 3, 1, 1),
(974, 970, '', '虚拟币流水', 'admin', 'finance.finance', 'mer_gold_bill', '[]', 2, 1, 1),
(975, 524, '', '关联资料', 'admin', 'special.SpecialType', 'dataDownloadRelation', '[]', 0, 0, 1),
(976, 522, '', '关联资料', 'admin', 'special.SpecialType', 'dataDownloadRelation', '[]', 0, 0, 1),
(977, 520, '', '关联资料', 'admin', 'special.SpecialType', 'dataDownloadRelation', '[]', 0, 0, 1),
(978, 839, '', '关联资料', 'admin', 'special.SpecialType', 'dataDownloadRelation', '[]', 0, 0, 1),
(979, 487, '', '保存直播', 'admin', 'special.SpecialType', 'save_special', '[]', 0, 0, 1),
(980, 531, '', '关联专题', 'admin', 'store.storeProduct', 'knowledge', '[]', 0, 0, 1),
(981, 676, '', '保存拼团设置', 'admin', 'special.SpecialType', 'save_pink', '[]', 0, 0, 1),
(982, 239, '', '提现编辑', 'admin', 'finance.userExtract', 'edit', '[]', 0, 0, 1),
(983, 239, '', '提现展示', 'admin', 'finance.userExtract', 'get_user_extract', '[]', 0, 0, 1),
(984, 961, '', '申请展示', 'admin', 'merchant.userEnter', 'index', '[]', 0, 0, 1),
(985, 961, '', '删除申请', 'admin', 'merchant.userEnter', 'delete', '[]', 0, 0, 1),
(986, 961, '', '查看申请', 'admin', 'merchant.userEnter', 'see', '[]', 0, 0, 1),
(987, 961, '', '审核不通过', 'admin', 'merchant.userEnter', 'fail', '[]', 0, 0, 1),
(988, 961, '', '审核通过', 'admin', 'merchant.userEnter', 'succ', '[]', 0, 0, 1),
(989, 961, '', '生成讲师后台', 'admin', 'merchant.userEnter', 'create', '[]', 0, 0, 1),
(990, 545, '', '后台状态', 'admin', 'merchant.merchant', 'modify', '[]', 0, 0, 1),
(992, 545, '', '重置密码', 'admin', 'merchant.merchant', 'reset_pwd', '[]', 0, 0, 1),
(993, 545, '', '编辑后台', 'admin', 'merchant.merchant', 'edit', '[]', 0, 0, 1),
(994, 545, '', '访问后台', 'admin', 'merchant.merchant', 'login', '[]', 0, 0, 1),
(995, 545, '', '讲师后台展示', 'admin', 'special.lecturer', 'lecturer_list', '[]', 0, 0, 1),
(996, 963, '', '菜单管理展示页', 'admin', 'merchant.merchantMenus', 'index', '[]', 0, 0, 1),
(997, 963, '', '编辑提交菜单', 'admin', 'merchant.merchantMenus', 'update', '[]', 0, 0, 1),
(998, 963, '', '添加提交菜单', 'admin', 'merchant.merchantMenus', 'save', '[]', 0, 0, 1),
(999, 963, '', '删除菜单', 'admin', 'merchant.merchantMenus', 'delete', '[]', 0, 0, 1),
(1000, 963, '', '添加菜单', 'admin', 'merchant.merchantMenus', 'create', '[]', 0, 0, 1),
(1001, 963, '', '编辑菜单', 'admin', 'merchant.merchantMenus', 'edit', '[]', 0, 0, 1),
(1002, 965, '', '审核内容', 'admin', 'special.specialType', 'examineDetails', '[]', 0, 0, 1),
(1003, 965, '', '通过审核', 'admin', 'special.specialType', 'succ', '[]', 0, 0, 1),
(1004, 965, '', '不通过审核', 'admin', 'special.specialType', 'fail', '[]', 0, 0, 1),
(1005, 965, '', '审核展示', 'admin', 'special.specialType', 'specialExamineList', '[]', 0, 0, 1),
(1006, 966, '', '审核内容', 'admin', 'store.storeProduct', 'examineDetails', '[]', 0, 0, 1),
(1007, 966, '', '通过审核', 'admin', 'store.storeProduct', 'succ', '[]', 0, 0, 1),
(1008, 966, '', '不通过审核', 'admin', 'store.storeProduct', 'fail', '[]', 0, 0, 1),
(1009, 966, '', '审核展示', 'admin', 'store.storeProduct', 'product_examine_ist', '[]', 0, 0, 1),
(1010, 967, '', '审核内容', 'admin', 'download.dataDownload', 'examineDetails', '[]', 0, 0, 1),
(1011, 967, '', '通过审核', 'admin', 'download.dataDownload', 'succ', '[]', 0, 0, 1),
(1012, 967, '', '不通过审核', 'admin', 'download.dataDownload', 'fail', '[]', 0, 0, 1),
(1013, 967, '', '审核展示', 'admin', 'download.dataDownload', 'data_download_examine_list', '[]', 0, 0, 1),
(1014, 968, '', '审核内容', 'admin', 'ump.eventRegistration', 'examineDetails', '[]', 0, 0, 1),
(1015, 968, '', '通过审核', 'admin', 'ump.eventRegistration', 'succ', '[]', 0, 0, 1),
(1016, 968, '', '不通过审核', 'admin', 'ump.eventRegistration', 'fail', '[]', 0, 0, 1),
(1017, 968, '', '审核展示', 'admin', 'ump.eventRegistration', 'event_examine_ist', '[]', 0, 0, 1),
(1018, 969, '', '通过审核', 'admin', 'live.aliyunLive', 'succ', '[]', 0, 0, 1),
(1019, 969, '', '不通过审核', 'admin', 'live.aliyunLive', 'fail', '[]', 0, 0, 1),
(1020, 969, '', '审核展示', 'admin', 'live.aliyunLive', 'get_live_audit_list', '[]', 0, 0, 1),
(1021, 972, '', '提现编辑', 'admin', 'finance.userExtract', 'edit', '[]', 0, 0, 1),
(1022, 972, '', '提现通过', 'admin', 'finance.userExtract', 'merSucc', '[]', 0, 0, 1),
(1023, 972, '', '提现不通过', 'admin', 'finance.userExtract', 'merFail', '[]', 0, 0, 1),
(1024, 972, '', '提现展示', 'admin', 'finance.userExtract', 'get_mer_user_extract', '[]', 0, 0, 1),
(1025, 507, '', '我的-题库菜单', 'admin', 'setting.systemGroupData', 'center_menu_topic','{\"gid\":\"68\"}', 0, 1, 1),
(1026, 964, '', '证书审核', 'admin', 'questions.certificate', 'examine', '[]', 0, 1, 1),
(1027, 1026, '', '审核内容', 'admin', 'questions.certificate', 'examineDetails', '[]', 0, 0, 1),
(1028, 1026, '', '通过审核', 'admin', 'questions.certificate', 'succ', '[]', 0, 0, 1),
(1029, 1026, '', '不通过审核', 'admin', 'questions.certificate', 'fail', '[]', 0, 0, 1),
(1030, 1026, '', '审核展示', 'admin', 'questions.certificate', 'get_certificate_examine_list', '[]', 0, 0, 1),
(1031, 964, '', '练习审核', 'admin', 'questions.testPaper', 'examine', '{\"type\":\"1\"}', 0, 1, 1),
(1032, 1031, '', '审核内容', 'admin', 'questions.testPaper', 'examineDetails', '[]', 0, 0, 1),
(1033, 1031, '', '通过审核', 'admin', 'questions.testPaper', 'succ', '[]', 0, 0, 1),
(1034, 1031, '', '不通过审核', 'admin', 'questions.testPaper', 'fail', '[]', 0, 0, 1),
(1035, 1031, '', '审核展示', 'admin', 'questions.testPaper', 'get_test_paper_examine_list', '[]', 0, 0, 1),
(1036, 964, '', '考试审核', 'admin', 'questions.testPaper', 'examine', '{\"type\":\"2\"}', 0, 1, 1),
(1037, 1036, '', '审核内容', 'admin', 'questions.testPaper', 'examineDetails', '[]', 0, 0, 1),
(1038, 1036, '', '通过审核', 'admin', 'questions.testPaper', 'succ', '[]', 0, 0, 1),
(1039, 1036, '', '不通过审核', 'admin', 'questions.testPaper', 'fail', '[]', 0, 0, 1),
(1040, 1036, '', '审核展示', 'admin', 'questions.testPaper', 'get_test_paper_examine_list', '[]', 0, 0, 1);

-- --------------------------------------------------------

--
-- 转存表中的数据 `eb_system_message` 新建数据表
--
CREATE TABLE `eb_system_message` (
  `id` int(10) UNSIGNED NOT NULL,
  `template_const` varchar(255) NOT NULL DEFAULT '' COMMENT '模版常数',
  `tempkey` char(50) NOT NULL COMMENT '模板编号',
  `temp_id` int(50) DEFAULT '0' COMMENT '短信模板ID',
  `name` char(100) NOT NULL DEFAULT '' COMMENT '模板名',
  `is_wechat` tinyint(1) DEFAULT '0' COMMENT '是否开启微信模版消息',
  `is_sms` tinyint(1) DEFAULT '0' COMMENT '是否开启短信模版',
  `sms_content` varchar(1000) DEFAULT NULL COMMENT '回复内容',
  `add_time` int(12) DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='消息管理';

--
-- 转存表中的数据 `eb_system_message` 增加数据
--
INSERT INTO `eb_system_message` (`id`,`template_const`, `tempkey`, `temp_id`, `name`, `is_wechat`, `is_sms`, `sms_content`, `add_time`) VALUES
(1,'ORDER_POSTAGE_SUCCESS', 'OPENTM408918196', 441596, '订单发货提醒', 1, 0, '【CRMEB】亲爱的用户{$nickname}您的商品{$store_name}，订单号{$order_id}已发货，请注意查收！', 1636949626),
(2,'ORDER_TAKE_SUCCESS', 'OPENTM418528119', 440402, '订单收货通知', 1, 0, '【CRMEB】亲，您的订单{$order_id},商品{$store_name}已确认收货,感谢您的光临！', 1636964597),
(3,'ORDER_REFUND_STATUS', 'OPENTM411627204', 0, '退款成功通知', 1, 0, '', 1636965318),
(4,'USER_BALANCE_CHANGE', 'OPENTM405575524', 0, '资金变动通知', 1, 0, '', 1636965349),
(5,'ORDER_USER_GROUPS_SUCCESS', 'OPENTM407456411', 0, '拼团成功通知', 1, 0, '', 1636965379),
(6,'ORDER_USER_GROUPS_LOSE', 'OPENTM401113750', 0, '拼团失败通知', 1, 0, '', 1636965410),
(7,'ORDER_CREATE', 'OPENTM205213550', 0, '订单生成通知', 1, 0, '', 1636965436),
(8,'ORDER_PAY_SUCCESS', 'OPENTM201752540', 440396, '订单支付成功通知', 1, 0, '【CRMEB】您购买的商品已支付成功，支付金额{$pay_price}元，订单号{$order_id},感谢您的光临！', 1636965477),
(10,'PINK_ORDER_REMIND', 'OPENTM415198906', 0, '拼单进度提醒', 1, 0, '', 1636965542),
(11,'LIVE_START_NOTICE', 'OPENTM405456204', 554242, '开播提醒', 1, 0, '【CRMEB】亲，你购买的直播课{$title}即将开播，请准时观看哦。', 1636965571),
(12,'ORDER_USER_SIGN_UP_SUCCESS', 'OPENTM406210305', 555397, '活动报名成功通知', 1, 0, '【CRMEB】您已成功报名了{$title}活动，核销码是{$code}，请在活动开始前在核销点核销！', 1640328799),
(13,'EXAMINE_RESULT', 'OPENTM412612226', 0, '审核结果通知', 1, 0, '', 1646291875),
(14,'ENTRY_FAILED', 'OPENTM412612226', 559749, '讲师入驻未通过通知', 1, 0, '【CRMEB】您好，您提交的讲师入驻申请审核未通过，请前往{$site_name}—>我的—>讲师申请查看。', 1652497670),
(15,'SETTLED_THROUGH', 'OPENTM412612226', 558621, '讲师入驻审核通过通知', 1, 0, '【CRMEB】您好，您提交的讲师入驻申请已通过，讲师后台账号：{$phone}, 密码：{$pwd}，请前往{$site_name}->我的->讲师中心查看。', 1652497751);

--
-- 表的结构 `eb_system_notice`
--
CREATE TABLE `eb_system_notice` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '通知模板id',
  `title` varchar(64) NOT NULL DEFAULT '' COMMENT '通知标题',
  `type` varchar(64) NOT NULL DEFAULT '' COMMENT '通知类型',
  `icon` varchar(16) NOT NULL DEFAULT '' COMMENT '图标',
  `url` varchar(64) NOT NULL DEFAULT '' COMMENT '链接',
  `table_title` varchar(256) NOT NULL DEFAULT '' COMMENT '通知数据',
  `template` varchar(64) NOT NULL DEFAULT '' COMMENT '通知模板',
  `push_admin` varchar(128) NOT NULL DEFAULT '' COMMENT '通知管理员id',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='通知模板表' ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- 表的结构 `eb_system_notice_admin`
--
CREATE TABLE `eb_system_notice_admin` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '通知记录ID',
  `notice_type` varchar(64) NOT NULL DEFAULT '' COMMENT '通知类型',
  `admin_id` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '通知的管理员',
  `link_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '关联ID',
  `table_data` text COMMENT '通知的数据',
  `is_click` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '点击次数',
  `is_visit` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '访问次数',
  `visit_time` int(12) NOT NULL DEFAULT '0' COMMENT '访问时间',
  `add_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '通知时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='通知记录表' ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- 表的结构 `eb_system_role`
--
CREATE TABLE `eb_system_role` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '身份管理id',
  `role_name` varchar(32) NOT NULL DEFAULT '' COMMENT '身份管理名称',
  `sign` varchar(50) NOT NULL DEFAULT '' COMMENT '身份标识',
  `rules` text COMMENT '身份管理权限(menus_id)',
  `level` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='身份管理表';

--
-- 转存表中的数据 `eb_system_role`
--
INSERT INTO `eb_system_role` (`id`, `role_name`, `sign`, `rules`, `level`, `status`) VALUES
(1, '超级管理员','admin', '37,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,73,74,75,76,77,78,89,90,91,92,93,94,95,96,97,98,99,100,102,103,104,105,106,107,108,109,110,111,112,115,116,117,118,119,123,124,126,127,128,131,137,138,139,140,141,144,145,148,155,156,158,159,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,229,230,231,232,233,234,245,247,248,249,250,269,273,276,288,349,378,419,436,437,438,439,440,441,442,443,444,445,446,447,448,449,450,451,452,453,454,455,456,457,458,459,460,461,462,463,464,465,466,467,468,469,470,471,472,473,474,475,476,477,478,1,2,4,6,7,8,9,11,12,17,19,21,30,31,101,130,149,150,151,153,173,174,175,177,179,235,237,239,241,244,246,251,252,267,270,278,286,287,289,293,306,307,308,312,313,337,350,351,352,353,354,355,357,360,361,362,363,364,365,372,373,374,375,376,377,379,380,391,392,399,400,409,411,412,415,417,418,420,421,423,428,429,430,431,432,433,434,435,479,480,481', 0, 1),
(2, '主播', 'anchor','', 1, 1),
(3, '核销员', 'verification', '518,286', 1, 1);
-- --------------------------------------------------------

--
-- 表的结构 `eb_teacher`
--
CREATE TABLE `eb_teacher` (
  `id` int(10) UNSIGNED NOT NULL,
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT 'UID',
  `name` varchar(255)  NOT NULL COMMENT '老师名称',
  `image` varchar(255)  NOT NULL COMMENT '老师头像',
  `nickname` varchar(255)  NOT NULL COMMENT '用户昵称',
  `pid` int(5) NOT NULL DEFAULT '0' COMMENT '老师分类',
  `position` varchar(255)  NOT NULL COMMENT '职位',
  `phone` varchar(12)  NOT NULL COMMENT '手机号',
  `sort` int(5) DEFAULT '0' COMMENT '排序',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='老师表';

--
-- 表的结构 `eb_teacher_categpry`
--
CREATE TABLE `eb_teacher_categpry` (
  `id` int(10) UNSIGNED NOT NULL,
  `pid` int(10) NOT NULL DEFAULT '0' COMMENT '父级ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '分类名称',
  `sort` int(5) DEFAULT '0' COMMENT '排序',
  `is_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '显示/隐藏',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='老师分类表';

--
-- 表的结构 `eb_test_paper`
--
CREATE TABLE `eb_test_paper` (
  `id` int(10) UNSIGNED NOT NULL,
  `mer_id` int(12) DEFAULT '0' COMMENT '讲师id',
  `tid` int(10) NOT NULL DEFAULT '0' COMMENT '试题分类ID',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '区别 1=练习 2=试卷',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '试卷名称',
  `image` varchar(255) DEFAULT NULL COMMENT '试卷封面图',
  `item_number` int(5) NOT NULL DEFAULT '0' COMMENT '题目数',
  `answer` int(5) NOT NULL DEFAULT '0' COMMENT '答题人数',
  `single_number` int(5) NOT NULL DEFAULT '0' COMMENT '单选题数量',
  `single_score` int(5) DEFAULT '0' COMMENT '单选题分数',
  `single_sort` int(5) DEFAULT '0' COMMENT '单选题型排序',
  `many_number` int(5) NOT NULL DEFAULT '0' COMMENT '多选题数量',
  `many_score` int(5) DEFAULT '0' COMMENT '多选题分数',
  `many_sort` int(5) DEFAULT '0' COMMENT '多选题型排序',
  `judge_number` int(5) NOT NULL DEFAULT '0' COMMENT '判断题数量',
  `judge_score` int(5) DEFAULT '0' COMMENT '判断题分数',
  `judge_sort` int(5) DEFAULT '0' COMMENT '判断题型排序',
  `total_score` int(5) DEFAULT '0' COMMENT '试卷总分',
  `difficulty` tinyint(1) DEFAULT '0' COMMENT '难度',
  `txamination_time` int(3) DEFAULT '0' COMMENT '考试时间/分',
  `pay_type` tinyint(1) DEFAULT '0' COMMENT '支付类型',
  `money` decimal(8,2) DEFAULT '0.00' COMMENT '金额',
  `member_pay_type` tinyint(1) DEFAULT '0' COMMENT '会员支付类型0：免费，1付费',
  `member_money` decimal(8,2) DEFAULT '0.00' COMMENT '会员价格',
  `is_score` tinyint(1) DEFAULT '0' COMMENT '是否显示分数',
  `frequency` int(5) DEFAULT '0' COMMENT '考试次数',
  `cate_id` int(5) DEFAULT '0' COMMENT '随机组题时选择的试题ID',
  `is_group` tinyint(1) DEFAULT '0' COMMENT '1=手动2=随机',
  `sort` int(3) DEFAULT '0' COMMENT '排序',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否上架',
  `fake_sales` int(10) NOT NULL DEFAULT '0' COMMENT '虚拟答题人数',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 -1:未通过 0:待审核 1:审核通过',
  `success_time` int(12) NOT NULL DEFAULT '0' COMMENT '通过时间',
  `fail_message` varchar(256) NOT NULL DEFAULT '' COMMENT '未通过原因',
  `fail_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '未通过时间',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COMMENT='试卷列表';

--
-- 表的结构 `eb_test_paper_category`
--
CREATE TABLE `eb_test_paper_category` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '分类名称',
  `pid` int(10) NOT NULL DEFAULT '0' COMMENT '父级ID',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '区别 1=练习 2=试卷',
  `sort` int(5) DEFAULT '0' COMMENT '排序',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='试卷分类';

--
-- 表的结构 `eb_test_paper_obtain`
--
CREATE TABLE `eb_test_paper_obtain` (
  `id` int(10) UNSIGNED NOT NULL,
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT 'uid',
  `order_id` varchar(64) DEFAULT NULL COMMENT '订单号',
  `test_id` int(10) NOT NULL COMMENT '试卷ID',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '区别1=练习2=试卷',
  `source` tinyint(1) NOT NULL DEFAULT '0' COMMENT '获得方式:1=支付获得,2=领取,3=赠送',
  `number` int(5) DEFAULT '0' COMMENT '答题次数',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户获得试卷';

--
-- 表的结构 `eb_test_paper_order`
--
CREATE TABLE `eb_test_paper_order` (
  `id` int(10) UNSIGNED NOT NULL,
  `mer_id` int(12) DEFAULT '0' COMMENT '讲师id',
  `trade_no` varchar(64) DEFAULT NULL COMMENT '支付宝支付,支付宝交易订单号',
  `order_id` varchar(64) NOT NULL DEFAULT '' COMMENT '订单号',
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT 'uid',
  `test_id` int(10) NOT NULL DEFAULT '0' COMMENT '试卷ID',
  `total_num` int(5) NOT NULL DEFAULT '0' COMMENT '数量',
  `total_price` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `pay_price` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '实际支付金额',
  `paid` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支付状态',
  `pay_time` int(12) DEFAULT '0' COMMENT '支付时间',
  `pay_type` varchar(12) DEFAULT NULL COMMENT '支付方式',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态0:正常 -1:申请退款 -2:已退款',
  `refund_status` tinyint(1) DEFAULT '0' COMMENT '0 未退款 1 申请中 2 已退款',
  `refund_reason_time` int(12) DEFAULT '0' COMMENT '退款时间',
  `refund_price` decimal(8,2) DEFAULT '0.00' COMMENT '退款金额',
  `unique` char(32) NOT NULL DEFAULT '' COMMENT '唯一id(md5加密)类似id',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `is_system_del` tinyint(1) DEFAULT '0' COMMENT '后台是否删除',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='试卷支付订单表';

--
-- 表的结构 `eb_test_paper_questions`
--
CREATE TABLE `eb_test_paper_questions` (
  `id` int(10) UNSIGNED NOT NULL,
  `question_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '题型区别1=单选题 2=多选题 3=判断题',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '区别 1=练习 2=试卷',
  `test_id` int(10) NOT NULL DEFAULT '0' COMMENT '试卷ID',
  `questions_id` int(10) NOT NULL DEFAULT '0' COMMENT '试题ID',
  `score` int(3) DEFAULT '0' COMMENT '分数',
  `sort` int(5) DEFAULT '0' COMMENT '排序'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='试卷里的试题';

--
-- 表的结构 `eb_test_paper_score_grade`
--
CREATE TABLE `eb_test_paper_score_grade` (
  `id` int(10) UNSIGNED NOT NULL,
  `test_id` int(10) NOT NULL DEFAULT '0' COMMENT '试卷ID',
  `grade_name` varchar(255) DEFAULT NULL COMMENT '等级名称',
  `grade_standard` varchar(255) DEFAULT NULL COMMENT '等级标准'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='得分等级';

--
-- 表的结构 `eb_user`
--
CREATE TABLE `eb_user`(
  `uid` int(10) UNSIGNED NOT NULL COMMENT '用户id',
  `account` varchar(32)  NOT NULL DEFAULT '' COMMENT '用户账号',
  `pwd` varchar(32)  NOT NULL DEFAULT '' COMMENT '用户密码',
  `nickname` varchar(16) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `avatar` varchar(256) NOT NULL DEFAULT '' COMMENT '用户头像',
  `name` varchar(255)  DEFAULT NULL COMMENT '姓名',
  `phone` char(15)  NOT NULL DEFAULT '' COMMENT '手机号码',
  `grade_id` int(11) NOT NULL DEFAULT '0' COMMENT '年级id',
  `full_name` varchar(35)  NOT NULL DEFAULT '' COMMENT '真实姓名',
  `bank_card` varchar(50)  NOT NULL DEFAULT '' COMMENT '银行卡号码',
  `add_time` int(12) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `add_ip` varchar(16)  NOT NULL DEFAULT '' COMMENT '添加ip',
  `last_time` int(12) unsigned NOT NULL DEFAULT '0' COMMENT '最后一次登录时间',
  `last_ip` varchar(16)  NOT NULL DEFAULT '' COMMENT '最后一次登录ip',
  `now_money` decimal(8,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '用户余额',
  `gold_num` decimal(8,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '虚拟币余额',
  `brokerage_price` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '可提现金额',
  `integral` decimal(8,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '用户剩余积分',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1为正常，0为禁止',
  `level` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '等级',
  `spread_uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '推广元id',
  `spread_time` int(12) DEFAULT '0',
  `valid_time` int(12) NOT NULL DEFAULT '0' COMMENT '客户关系解除时间',
  `user_type` varchar(32)  NOT NULL DEFAULT '' COMMENT '用户类型',
  `is_promoter` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否为推广员',
  `is_write_off` tinyint(1) DEFAULT '0' COMMENT '是否为核销员',
  `pay_count` int(11) unsigned DEFAULT '0' COMMENT '用户购买次数',
  `is_binding` int(11) NOT NULL DEFAULT '0',
  `is_senior` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为高级推广人',
  `is_h5user` tinyint(1) DEFAULT '0' COMMENT '用户来源 0=微信 1=h5 2=pc端',
  `is_permanent` tinyint(2) DEFAULT '0' COMMENT '会员是否永久',
  `member_time` int(12) unsigned DEFAULT '0' COMMENT '成为会员时间',
  `overdue_time` int(12) unsigned DEFAULT '0' COMMENT '会员过期时间',
  `client_id` varchar(50)  NOT NULL DEFAULT '0' COMMENT 'client_id',
  `identitys` tinyint(1) DEFAULT '0' COMMENT '用户身份1=学员2=老师',
  `business` tinyint(1) DEFAULT '0' COMMENT '是否是讲师'
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户表';
-- --------------------------------------------------------

--
-- 表的结构 `eb_user_address`
--
CREATE TABLE `eb_user_address` (
  `id` mediumint(8) UNSIGNED NOT NULL COMMENT '用户地址id',
  `uid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户id',
  `real_name` varchar(32) NOT NULL DEFAULT '' COMMENT '收货人姓名',
  `phone` varchar(16) NOT NULL DEFAULT '' COMMENT '收货人电话',
  `province` varchar(64) NOT NULL DEFAULT '' COMMENT '收货人所在省',
  `city` varchar(64) NOT NULL DEFAULT '' COMMENT '收货人所在市',
  `district` varchar(64) NOT NULL DEFAULT '' COMMENT '收货人所在区',
  `detail` varchar(256) NOT NULL DEFAULT '' COMMENT '收货人详细地址',
  `post_code` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '邮编',
  `longitude` varchar(16) NOT NULL DEFAULT '0' COMMENT '经度',
  `latitude` varchar(16) NOT NULL DEFAULT '0' COMMENT '纬度',
  `is_default` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否默认',
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否删除',
  `add_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户地址表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_user_bill`
--
CREATE TABLE `eb_user_bill` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '用户账单id',
  `uid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户uid',
  `get_uid` int(11) NOT NULL DEFAULT '0' COMMENT '由谁获得',
  `link_id` varchar(32) NOT NULL DEFAULT '0' COMMENT '关联id',
  `pm` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0 = 支出 1 = 获得',
  `title` varchar(64) NOT NULL DEFAULT '' COMMENT '账单标题',
  `category` varchar(64) NOT NULL DEFAULT '' COMMENT '明细种类',
  `type` varchar(64) NOT NULL DEFAULT '' COMMENT '明细类型',
  `number` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '明细数字',
  `balance` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '剩余',
  `mark` varchar(512) NOT NULL DEFAULT '' COMMENT '备注',
  `add_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '添加时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 = 带确定 1 = 有效 -1 = 无效'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户账单表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_user_enter`
--
CREATE TABLE `eb_user_enter` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '讲师申请ID',
  `uid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
  `province` varchar(32) NOT NULL DEFAULT '' COMMENT '讲师所在省',
  `city` varchar(32) NOT NULL DEFAULT '' COMMENT '讲师所在市',
  `district` varchar(32) NOT NULL DEFAULT '' COMMENT '讲师所在区',
  `address` varchar(256) NOT NULL DEFAULT '' COMMENT '讲师详细地址',
  `merchant_name` varchar(256) NOT NULL DEFAULT '' COMMENT '讲师名称',
  `merchant_head` varchar(256) NOT NULL DEFAULT '' COMMENT '讲师头像',
  `label` varchar(255) NOT NULL DEFAULT '' COMMENT '讲师标签',
  `explain` varchar(255)  DEFAULT '' COMMENT '讲师说明',
  `introduction` text COMMENT '简介',
  `link_user` varchar(32) DEFAULT '',
  `link_tel` varchar(16) NOT NULL DEFAULT '' COMMENT '讲师电话',
  `charter` varchar(512) DEFAULT '' COMMENT '讲师证书',
  `add_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '添加时间',
  `apply_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '审核时间',
  `success_time` int(12) NOT NULL DEFAULT '0' COMMENT '通过时间',
  `fail_message` varchar(256) NOT NULL DEFAULT '' COMMENT '未通过原因',
  `fail_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '未通过时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '-1 审核未通过 0未审核 1审核通过',
  `is_lock` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0 = 开启 1= 关闭',
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否删除'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='讲师申请表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_user_extract`
--
CREATE TABLE `eb_user_extract` (
  `id` int(10) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED DEFAULT NULL,
  `mer_id` int(11) NOT NULL DEFAULT '0' COMMENT '讲师id',
  `real_name` varchar(64) DEFAULT NULL COMMENT '名称',
  `partner_trade_no` varchar(32) NOT NULL DEFAULT '' COMMENT '讲师订单号',
  `extract_type` varchar(32) DEFAULT 'bank' COMMENT 'bank = 银行卡 alipay = 支付宝wx=微信',
  `bank_code` varchar(32) DEFAULT '0' COMMENT '银行卡',
  `bank_address` varchar(256) DEFAULT '' COMMENT '开户地址',
  `alipay_code` varchar(64) DEFAULT '' COMMENT '支付宝账号',
  `extract_price` decimal(8,2) UNSIGNED DEFAULT '0.00' COMMENT '提现金额',
  `mark` varchar(512) DEFAULT NULL,
  `balance` decimal(8,2) UNSIGNED DEFAULT '0.00',
  `fail_msg` varchar(128) DEFAULT NULL COMMENT '无效原因',
  `fail_time` int(12) UNSIGNED DEFAULT '0',
  `add_time` int(12) UNSIGNED DEFAULT '0' COMMENT '添加时间',
  `status` tinyint(2) DEFAULT '0' COMMENT '-1 未通过 0 审核中 1 已提现',
  `wechat` varchar(15) DEFAULT NULL COMMENT '微信号'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户提现表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_user_group`
--
CREATE TABLE `eb_user_group` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `group_name` varchar(64) DEFAULT NULL COMMENT '用户分组名称'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户分组表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_user_notice`
--
CREATE TABLE `eb_user_notice` (
  `id` int(11) UNSIGNED NOT NULL,
  `uid` text COMMENT '接收消息的用户id（类型：json数据）',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '消息通知类型（1：系统消息；2：用户通知）',
  `user` varchar(20) NOT NULL DEFAULT '' COMMENT '发送人',
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '通知消息的标题信息',
  `content` varchar(500) NOT NULL DEFAULT '' COMMENT '通知消息的内容',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '通知消息发送的时间',
  `is_send` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否发送（0：未发送；1：已发送）',
  `send_time` int(12) NOT NULL DEFAULT '0' COMMENT '发送时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户通知表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_user_notice_see`
--
CREATE TABLE `eb_user_notice_see` (
  `id` int(11) UNSIGNED NOT NULL,
  `nid` int(11) NOT NULL DEFAULT '0' COMMENT '查看的通知id',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '查看通知的用户id',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '查看通知的时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户通知发送记录表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_user_recharge`
--
CREATE TABLE `eb_user_recharge` (
  `id` int(10) UNSIGNED NOT NULL,
  `uid` int(10) DEFAULT NULL COMMENT '充值用户UID',
  `vip_id` int(11) NOT NULL DEFAULT '0' COMMENT '0为充值金额,',
  `order_id` varchar(32) DEFAULT NULL COMMENT '订单号',
  `trade_no` varchar(64) DEFAULT NULL COMMENT '支付宝支付,支付宝交易订单号',
  `price` decimal(8,2) DEFAULT '0.00' COMMENT '充值金额',
  `gold_num` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '虚拟币充值数量',
  `recharge_type` varchar(32) DEFAULT NULL COMMENT '充值类型',
  `paid` tinyint(1) DEFAULT NULL COMMENT '是否充值',
  `pay_time` int(12) DEFAULT '0' COMMENT '充值支付时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态0:正常 -1:申请退款 -2:已退款',
  `refund_status` tinyint(1) DEFAULT '0' COMMENT '0 未退款 1 申请中 2 已退款',
  `refund_reason_time` int(12) DEFAULT '0' COMMENT '退款时间',
  `refund_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '退款金额',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '是否删除',
  `add_time` int(12) DEFAULT '0' COMMENT '充值时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户充值表';

-- --------------------------------------------------------
--
-- 表的结构 `eb_user_sign`
--
CREATE TABLE `eb_user_sign` (
  `id` int(11) UNSIGNED NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户uid',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '签到说明',
  `number` int(11) NOT NULL DEFAULT '0' COMMENT '获得金币',
  `balance` int(11) NOT NULL DEFAULT '0' COMMENT '剩余金币',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='签到记录表';

--
-- 表的结构 `eb_wechat_media`
--
CREATE TABLE `eb_wechat_media` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '微信视频音频id',
  `type` varchar(16) NOT NULL DEFAULT '' COMMENT '回复类型',
  `path` varchar(128) NOT NULL DEFAULT '' COMMENT '文件路径',
  `media_id` varchar(64) NOT NULL DEFAULT '' COMMENT '微信服务器返回的id',
  `url` varchar(256) NOT NULL DEFAULT '' COMMENT '地址',
  `temporary` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否永久或者临时 0永久1临时',
  `add_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='微信回复表';

--
-- 转存表中的数据 `eb_wechat_media`
--

INSERT INTO `eb_wechat_media` (`id`, `type`, `path`, `media_id`, `url`, `temporary`, `add_time`) VALUES
(12, 'image', '/public/uploads/wechat/image/5b042ca618139.jpg', '6sFx6PzPF2v_Lv4FGOMzzwcwmM2wuoA63ZMSxiN-7DY', 'http://mmbiz.qpic.cn/mmbiz_jpg/xVkDhuiaGm78WOdUXuPE1oYLnU4J0LCEiaSuLhwwSrfdyINspibXsllaj8rOMSs5estAv0qhGuGniaqhb6HftecPuw/0?wx_fmt=jpeg', 0, 1527000231);

-- --------------------------------------------------------

--
-- 表的结构 `eb_wechat_message`
--
CREATE TABLE `eb_wechat_message` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '用户行为记录id',
  `openid` varchar(32) NOT NULL DEFAULT '' COMMENT '用户openid',
  `type` varchar(32) NOT NULL DEFAULT '' COMMENT '操作类型',
  `result` varchar(512) NOT NULL DEFAULT '' COMMENT '操作详细记录',
  `add_time` int(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '操作时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户行为记录表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_wechat_news_category`
--
CREATE TABLE `eb_wechat_news_category` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '图文消息管理ID',
  `cate_name` varchar(255) NOT NULL DEFAULT '' COMMENT '图文名称',
  `sort` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '状态',
  `new_id` varchar(255) NOT NULL DEFAULT '' COMMENT '文章id',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='图文消息管理表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_wechat_qrcode`
--
CREATE TABLE `eb_wechat_qrcode` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '微信二维码ID',
  `third_type` varchar(32) NOT NULL DEFAULT '' COMMENT '二维码类型',
  `third_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户id',
  `scan_id` int(10) NOT NULL DEFAULT '0' COMMENT '扫描用户uid',
  `ticket` varchar(255) NOT NULL DEFAULT '' COMMENT '二维码参数',
  `expire_seconds` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '二维码有效时间',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '状态',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '微信访问url',
  `qrcode_url` varchar(255) NOT NULL DEFAULT '' COMMENT '微信二维码url',
  `scan` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '被扫的次数'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='微信二维码管理表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_wechat_reply`
--
CREATE TABLE `eb_wechat_reply` (
  `id` mediumint(8) UNSIGNED NOT NULL COMMENT '微信关键字回复id',
  `key` varchar(64) NOT NULL DEFAULT '' COMMENT '关键字',
  `type` varchar(32) NOT NULL DEFAULT '' COMMENT '回复类型',
  `data` text COMMENT '回复数据',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '0=不可用  1 =可用',
  `hide` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否隐藏'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='微信关键字回复表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_wechat_template`
--
CREATE TABLE `eb_wechat_template` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '模板id',
  `tempkey` char(50) NOT NULL DEFAULT '' COMMENT '模板编号',
  `name` char(100) NOT NULL DEFAULT '' COMMENT '模板名',
  `content` varchar(1000) NOT NULL DEFAULT '' COMMENT '回复内容',
  `tempid` char(100) DEFAULT NULL COMMENT '模板ID',
  `add_time` int(12) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='微信模板';

--
-- 转存表中的数据 `eb_wechat_template`
--
INSERT INTO `eb_wechat_template` (`id`, `tempkey`, `name`, `content`, `tempid`, `add_time`, `status`) VALUES
(1, 'OPENTM415198906', '拼单进度提醒', '\n{{first.DATA}}\n商品名称：{{keyword1.DATA}}\n拼单进度：{{keyword2.DATA}}\n{{remark.DATA}}', 'g7V9Stq44br1oXcygrGoHNLFyecAthGy_92qf6AxQEE', '1562752076', 1),
(2, 'OPENTM405456204', '开播提醒', '{{first.DATA}}\r\n课程名称：{{keyword1.DATA}}\r\n开始时间：{{keyword2.DATA}}\r\n{{remark.DATA}}', 'toaV-6qweYrIGgg6lHd0-KqSzNuIMfnsaIbMn07Po3c', '1585197241', 1),
(3, 'OPENTM408918196', '订单发货提醒', '{{first.DATA}}\n订单编号：{{keyword1.DATA}}\n物流公司：{{keyword2.DATA}}\n物流单号：{{keyword3.DATA}}\n{{remark.DATA}}', 'vSjFIISkCrZ6EfIhetgrg0KmdWzx2vVDe99qR_gqP_Y', '1664246715', 1),
(4, 'OPENTM418528119', '订单收货通知', '{{first.DATA}}\n订单编号：{{keyword1.DATA}}\n商品名称：{{keyword2.DATA}}\n金额：{{keyword3.DATA}}\n时间：{{keyword4.DATA}}\n{{remark.DATA}}', 'V9HhM2iOkyoYPLMPJsI5sDlPKMN2M86JlIIHZSZ9C48', '1664247016', 1),
(5, 'OPENTM407456411', '拼团成功通知', '{{first.DATA}}\n订单编号：{{keyword1.DATA}}\n团购商品：{{keyword2.DATA}}\n{{remark.DATA}}', 'prOQv5_TTa8crdgpREBzJx5HCiYM9-Oq3-nHoM2UNRk', '1664247320', 1),
(6, 'OPENTM401113750', '拼团失败通知', '{{first.DATA}}\n拼团商品：{{keyword1.DATA}}\n商品金额：{{keyword2.DATA}}\n退款金额：{{keyword3.DATA}}\n{{remark.DATA}}', 'YAevLU7SBgz4wg6OY2-IMGGy1wOwcQjjSRnjaguVQws', '1664247439', 1),
(7, 'OPENTM201682460', '订单生成通知', '\n{{first.DATA}}\n时间：{{keyword1.DATA}}\n商品名称：{{keyword2.DATA}}\n订单号：{{keyword3.DATA}}\n{{remark.DATA}}', 'QpWSRj3EwqduA99V2Dx2yfKD6X_atFIIR9u5KL5ms3M', '1664247610', 1),
(8, 'OPENTM201752540', '订单支付成功通知', '{{first.DATA}}\n订单商品：{{keyword1.DATA}}\n订单编号：{{keyword2.DATA}}\n支付金额：{{keyword3.DATA}}\n支付时间：{{keyword4.DATA}}\n{{remark.DATA}}', 't3m3WJ7hTcmuNvPitbj-CG1rCAan3lA0PORK4R2w2Xc', '1664247837', 1),
(9, 'OPENTM406210305', '活动报名成功通知', '{{first.DATA}}\n活动时间：{{keyword1.DATA}}\n活动地点：{{keyword2.DATA}}\n{{remark.DATA}}', 'SabsA48wBpZZrJ0qJyAIDEqwtZLPKZ652qd5ididfjk', '1664248521', 1),
(10, 'OPENTM412612226', '审核结果通知', '{{first.DATA}}\n审核状态：{{keyword1.DATA}}\n审核原因：{{keyword2.DATA}}\n审核时间：{{keyword3.DATA}}\n{{remark.DATA}}', 'RtZZhIl5kCfpplAjYVjMdGmp2pzjRuYZKDr8-lZDz0M', '1664249092', 1),
(11, 'OPENTM411627204', '退款成功通知', '{{first.DATA}}\n订单号：{{keyword1.DATA}}\n退款金额：{{keyword2.DATA}}\n{{remark.DATA}}', 'FcrN9-pGZopN5OZfhUOFv_slVjZ--ytU2KFUl-5Z7JE', '1664249567', 1),
(12, 'OPENTM405575524', '资金变动通知', '{{first.DATA}}\n操作类型：{{keyword1.DATA}}\n当前总额：{{keyword2.DATA}}\n{{remark.DATA}}', 'zDCuPqhTkvwmVgTpln6bZKwDFbdzOaqKLc7wSCf9C_E', '1664250310', 1);
-- --------------------------------------------------------


--
-- 表的结构 `eb_wechat_user`
--
CREATE TABLE `eb_wechat_user`(
  `id` int(10) UNSIGNED NOT NULL,
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '微信用户id',
  `unionid` varchar(30)  DEFAULT NULL COMMENT '只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段',
  `openid` varchar(30)  DEFAULT NULL COMMENT '用户的标识，对当前公众号唯一',
  `routine_openid` varchar(32)  DEFAULT NULL COMMENT '小程序唯一身份ID',
  `nickname` varchar(64) NOT NULL DEFAULT '' COMMENT '用户的昵称',
  `headimgurl` varchar(256) NOT NULL DEFAULT '' COMMENT '用户头像',
  `sex` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '用户的性别，值为1时是男性，值为2时是女性，值为0时是未知',
  `city` varchar(64)  NOT NULL DEFAULT '' COMMENT '用户所在城市',
  `language` varchar(64)  NOT NULL DEFAULT '' COMMENT '用户的语言，简体中文为zh_CN',
  `province` varchar(64)  NOT NULL DEFAULT '' COMMENT '用户所在省份',
  `country` varchar(64)  NOT NULL DEFAULT '' COMMENT '用户所在国家',
  `remark` varchar(256)  DEFAULT NULL COMMENT '公众号运营者对粉丝的备注，公众号运营者可在微信公众平台用户管理界面对粉丝添加备注',
  `groupid` smallint(5) unsigned DEFAULT '0' COMMENT '用户所在的分组ID（兼容旧的用户分组接口）',
  `tagid_list` varchar(256)  DEFAULT NULL COMMENT '用户被打上的标签ID列表',
  `subscribe` tinyint(3) unsigned DEFAULT '1' COMMENT '用户是否订阅该公众号标识',
  `subscribe_time` int(12) unsigned DEFAULT '0' COMMENT '关注公众号时间',
  `add_time` int(12) unsigned DEFAULT '0' COMMENT '添加时间',
  `stair` int(11) unsigned DEFAULT NULL COMMENT '一级推荐人',
  `second` int(11) unsigned DEFAULT NULL COMMENT '二级推荐人',
  `order_stair` int(11) DEFAULT NULL COMMENT '一级推荐人订单',
  `order_second` int(11) unsigned DEFAULT NULL COMMENT '二级推荐人订单',
  `now_money` int(11) unsigned DEFAULT NULL COMMENT '佣金',
  `session_key` varchar(32)  DEFAULT NULL COMMENT '小程序用户会话密匙',
  `user_type` varchar(32)  DEFAULT 'wechat' COMMENT '用户类型',
  `scan_key` varchar(255) DEFAULT NULL COMMENT 'pc端扫码唯一值'
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='微信用户表';

-- --------------------------------------------------------

--
-- 转储表的索引
--

--
-- 表的索引 `eb_article`
--
ALTER TABLE `eb_article`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_article_category`
--
ALTER TABLE `eb_article_category`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_article_content`
--
ALTER TABLE `eb_article_content`
  ADD UNIQUE KEY `nid` (`nid`) USING BTREE;

--
-- 表的索引 `eb_cache`
--
ALTER TABLE `eb_cache`
  ADD PRIMARY KEY (`key`);

--
-- 表的索引 `eb_certificate`
--
ALTER TABLE `eb_certificate`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_certificate_record`
--
ALTER TABLE `eb_certificate_record`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_certificate_related`
--
ALTER TABLE `eb_certificate_related`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_classes`
--
ALTER TABLE `eb_classes`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_contact_phone`
--
ALTER TABLE `eb_contact_phone`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_data_download`
--
ALTER TABLE `eb_data_download`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cate_id` (`cate_id`) USING BTREE;
--
-- 表的索引 `eb_data_download_buy`
--
ALTER TABLE `eb_data_download_buy`
  ADD PRIMARY KEY (`id`);
--
-- 表的索引 `eb_data_download_categpry`
--
ALTER TABLE `eb_data_download_categpry`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pid` (`pid`) USING BTREE;
--
-- 表的索引 `eb_data_download_order`
--
ALTER TABLE `eb_data_download_order`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_data_download_records`
--
ALTER TABLE `eb_data_download_records`
  ADD PRIMARY KEY (`id`),
  ADD  KEY `uid` (`uid`) USING BTREE,
  ADD  KEY `data_id` (`data_id`) USING BTREE;

--
-- 表的索引 `eb_event_data`
--
ALTER TABLE `eb_event_data`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_event_price`
--
ALTER TABLE `eb_event_price`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_event_write_off_user`
--
ALTER TABLE `eb_event_write_off_user`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_examination_record`
--
ALTER TABLE `eb_examination_record`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_examination_test_record`
--
ALTER TABLE `eb_examination_test_record`
  ADD PRIMARY KEY (`id`),
  ADD KEY `e_id` (`e_id`) USING BTREE,
  ADD KEY `type` (`type`) USING BTREE;


--
-- 表的索引 `eb_examination_wrong_bank`
--
ALTER TABLE `eb_examination_wrong_bank`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_express`
--
ALTER TABLE `eb_express`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`) USING BTREE,
  ADD KEY `is_show` (`is_show`) USING BTREE;

--
-- 表的索引 `eb_grade`
--
ALTER TABLE `eb_grade`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_lecturer`
--
ALTER TABLE `eb_lecturer`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_live_gift`
--
ALTER TABLE `eb_live_gift`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_member_ship`
--
ALTER TABLE `eb_member_ship`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_merchant`
--
ALTER TABLE `eb_merchant`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_merchant_admin`
--
ALTER TABLE `eb_merchant_admin`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_merchant_bill`
--
ALTER TABLE `eb_merchant_bill`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_merchant_follow`
--
ALTER TABLE `eb_merchant_follow`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mer_id` (`mer_id`) USING BTREE,
  ADD KEY `uid` (`uid`) USING BTREE,
  ADD KEY `unfollow_time` (`unfollow_time`) USING BTREE,
  ADD KEY `follow_time` (`follow_time`) USING BTREE,
  ADD KEY `follow` (`is_follow`) USING BTREE;

--
-- 表的索引 `eb_merchant_flowing_water`
--
ALTER TABLE `eb_merchant_flowing_water`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_merchant_menus`
--
ALTER TABLE `eb_merchant_menus`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_questions`
--
ALTER TABLE `eb_questions`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_questions_categpry`
--
ALTER TABLE `eb_questions_categpry`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_special_task_category`
--
ALTER TABLE `eb_special_task_category`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_student`
--
ALTER TABLE `eb_student`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_system_broadcast`
--
ALTER TABLE `eb_system_broadcast`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_system_bucket`
--
ALTER TABLE `eb_system_bucket`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_system_group_data`
--
ALTER TABLE `eb_system_group_data`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_teacher`
--
ALTER TABLE `eb_teacher`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_teacher_categpry`
--
ALTER TABLE `eb_teacher_categpry`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_test_paper`
--
ALTER TABLE `eb_test_paper`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_test_paper_category`
--
ALTER TABLE `eb_test_paper_category`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_test_paper_obtain`
--
ALTER TABLE `eb_test_paper_obtain`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_test_paper_order`
--
ALTER TABLE `eb_test_paper_order`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_test_paper_score_grade`
--
ALTER TABLE `eb_test_paper_score_grade`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_test_paper_questions`
--
ALTER TABLE `eb_test_paper_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `test_id` (`test_id`) USING BTREE,
  ADD KEY `questions_id` (`questions_id`) USING BTREE,
  ADD KEY `type` (`type`) USING BTREE;

--
-- 表的索引 `eb_learning_records`
--
ALTER TABLE `eb_learning_records`
 ADD PRIMARY KEY (`id`),
 ADD KEY `uid` (`uid`) USING BTREE,
 ADD KEY `special_id` (`special_id`) USING BTREE;

--
-- 表的索引 `eb_live_audit`
--
ALTER TABLE `eb_live_audit`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_live_barrage`
--
ALTER TABLE `eb_live_barrage`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `uid` (`uid`) USING BTREE,
  ADD KEY `live_id` (`live_id`) USING BTREE,
  ADD KEY `to_uid` (`to_uid`) USING BTREE;


--
-- 表的索引 `eb_live_honoured_guest`
--
ALTER TABLE `eb_live_honoured_guest`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `live_id` (`live_id`);

--
-- 表的索引 `eb_live_studio`
--
ALTER TABLE `eb_live_studio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `special_id` (`special_id`),
  ADD KEY `stream_name` (`stream_name`);

--
-- 表的索引 `eb_live_user`
--
ALTER TABLE `eb_live_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `live_id` (`live_id`);

--
-- 表的索引 `eb_phone_user`
--
ALTER TABLE `eb_phone_user`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_web_recommend`
--
ALTER TABLE `eb_web_recommend`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_web_recommend_relation`
--
ALTER TABLE `eb_web_recommend_relation`
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `id_2` (`id`);

--
-- 表的索引 `eb_recommend`
--
ALTER TABLE `eb_recommend`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_recommend_banner`
--
ALTER TABLE `eb_recommend_banner`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_recommend_relation`
--
ALTER TABLE `eb_recommend_relation`
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `id_2` (`id`);

--
-- 表的索引 `eb_relation`
--
ALTER TABLE `eb_relation`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_reply_false`
--
ALTER TABLE `eb_reply_false`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_routine_access_token`
--
ALTER TABLE `eb_routine_access_token`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_routine_form_id`
--
ALTER TABLE `eb_routine_form_id`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_routine_template`
--
ALTER TABLE `eb_routine_template`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tempkey` (`tempkey`) USING BTREE;

--
-- 表的索引 `eb_search`
--
ALTER TABLE `eb_search`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_sms_code`
--
ALTER TABLE `eb_sms_code`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `id_2` (`id`);

--
-- 表的索引 `eb_special`
--
ALTER TABLE `eb_special`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`subject_id`) USING BTREE;

--
-- 表的索引 `eb_special_barrage`
--
ALTER TABLE `eb_special_barrage`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_special_batch`
--
ALTER TABLE `eb_special_batch`
  ADD PRIMARY KEY (`id`),
  ADD KEY `special_id` (`special_id`) USING BTREE;


--
-- 表的索引 `eb_special_buy`
--
ALTER TABLE `eb_special_buy`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_special_content`
--
ALTER TABLE `eb_special_content`
  ADD UNIQUE KEY `id` (`id`);

--
-- 表的索引 `eb_special_course`
--
ALTER TABLE `eb_special_course`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_special_source`
--
ALTER TABLE `eb_special_exchange`
  ADD PRIMARY KEY (`id`),
  ADD KEY `card_batch_id` (`card_batch_id`) USING BTREE,
  ADD KEY `special_id` (`special_id`) USING BTREE;

--
-- 表的索引 `eb_special_source`
--
ALTER TABLE `eb_special_source`
  ADD PRIMARY KEY (`id`),
  ADD KEY `special_ids` (`special_id`) USING BTREE,
  ADD KEY `source_ids` (`source_id`) USING BTREE;

--
-- 表的索引 `eb_special_record`
--
ALTER TABLE `eb_special_record`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_special_relation`
--
ALTER TABLE `eb_special_relation`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_special_reply`
--
ALTER TABLE `eb_special_reply`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_special_subject`
--
ALTER TABLE `eb_special_subject`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_special_task`
--
ALTER TABLE `eb_special_task`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- 表的索引 `eb_store_bargain`
--
ALTER TABLE `eb_store_bargain`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_store_bargain_user`
--
ALTER TABLE `eb_store_bargain_user`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_store_bargain_user_help`
--
ALTER TABLE `eb_store_bargain_user_help`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_store_cart`
--
ALTER TABLE `eb_store_cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`uid`) USING BTREE,
  ADD KEY `goods_id` (`product_id`) USING BTREE,
  ADD KEY `uid` (`uid`,`is_pay`) USING BTREE,
  ADD KEY `uid_2` (`uid`,`is_del`) USING BTREE,
  ADD KEY `uid_3` (`uid`,`is_new`) USING BTREE,
  ADD KEY `type` (`type`) USING BTREE;

--
-- 表的索引 `eb_store_category`
--
ALTER TABLE `eb_store_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pid` (`pid`) USING BTREE,
  ADD KEY `is_base` (`is_show`) USING BTREE,
  ADD KEY `sort` (`sort`) USING BTREE,
  ADD KEY `add_time` (`add_time`) USING BTREE;

--
-- 表的索引 `eb_store_combination`
--
ALTER TABLE `eb_store_combination`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_store_combination_attr`
--
ALTER TABLE `eb_store_combination_attr`
  ADD KEY `store_id` (`product_id`) USING BTREE;

--
-- 表的索引 `eb_store_combination_attr_result`
--
ALTER TABLE `eb_store_combination_attr_result`
  ADD UNIQUE KEY `product_id` (`product_id`) USING BTREE;

--
-- 表的索引 `eb_store_combination_attr_value`
--
ALTER TABLE `eb_store_combination_attr_value`
  ADD UNIQUE KEY `unique` (`unique`,`suk`) USING BTREE,
  ADD KEY `store_id` (`product_id`,`suk`) USING BTREE;

--
-- 表的索引 `eb_store_coupon`
--
ALTER TABLE `eb_store_coupon`
  ADD PRIMARY KEY (`id`),
  ADD KEY `state` (`status`) USING BTREE,
  ADD KEY `add_time` (`add_time`) USING BTREE,
  ADD KEY `coupon_time` (`coupon_time`) USING BTREE,
  ADD KEY `is_del` (`is_del`) USING BTREE;

--
-- 表的索引 `eb_store_coupon_issue`
--
ALTER TABLE `eb_store_coupon_issue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cid` (`cid`) USING BTREE,
  ADD KEY `start_time` (`start_time`,`end_time`) USING BTREE,
  ADD KEY `remain_count` (`remain_count`) USING BTREE,
  ADD KEY `status` (`status`) USING BTREE,
  ADD KEY `is_del` (`is_del`) USING BTREE;

--
-- 表的索引 `eb_store_coupon_issue_user`
--
ALTER TABLE `eb_store_coupon_issue_user`
  ADD UNIQUE KEY `uid` (`uid`,`issue_coupon_id`) USING BTREE;

--
-- 表的索引 `eb_store_coupon_user`
--
ALTER TABLE `eb_store_coupon_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cid` (`cid`) USING BTREE,
  ADD KEY `uid` (`uid`) USING BTREE,
  ADD KEY `add_time` (`add_time`) USING BTREE,
  ADD KEY `end_time` (`end_time`) USING BTREE,
  ADD KEY `status` (`status`) USING BTREE,
  ADD KEY `is_fail` (`is_fail`) USING BTREE;

--
-- 表的索引 `eb_store_order`
--
ALTER TABLE `eb_store_order`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id_2` (`order_id`,`uid`) USING BTREE,
  ADD UNIQUE KEY `unique` (`unique`) USING BTREE,
  ADD KEY `uid` (`uid`) USING BTREE,
  ADD KEY `add_time` (`add_time`) USING BTREE,
  ADD KEY `pay_price` (`pay_price`) USING BTREE,
  ADD KEY `paid` (`paid`) USING BTREE,
  ADD KEY `pay_time` (`pay_time`) USING BTREE,
  ADD KEY `pay_type` (`pay_type`) USING BTREE,
  ADD KEY `status` (`status`) USING BTREE,
  ADD KEY `is_del` (`is_del`) USING BTREE,
  ADD KEY `coupon_id` (`coupon_id`) USING BTREE;

--
-- 表的索引 `eb_store_order_cart_info`
--
ALTER TABLE `eb_store_order_cart_info`
  ADD UNIQUE KEY `oid` (`oid`,`unique`) USING BTREE,
  ADD KEY `cart_id` (`cart_id`) USING BTREE,
  ADD KEY `product_id` (`product_id`) USING BTREE;

--
-- 表的索引 `eb_store_order_status`
--
ALTER TABLE `eb_store_order_status`
  ADD KEY `oid` (`oid`) USING BTREE,
  ADD KEY `change_type` (`change_type`) USING BTREE;

--
-- 表的索引 `eb_store_pink`
--
ALTER TABLE `eb_store_pink`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_store_pink_false`
--
ALTER TABLE `eb_store_pink_false`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_store_product`
--
ALTER TABLE `eb_store_product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cate_id` (`cate_id`) USING BTREE,
  ADD KEY `is_hot` (`is_hot`) USING BTREE,
  ADD KEY `is_benefit` (`is_benefit`) USING BTREE,
  ADD KEY `is_best` (`is_best`) USING BTREE,
  ADD KEY `is_new` (`is_new`) USING BTREE,
  ADD KEY `toggle_on_sale, is_del` (`is_del`) USING BTREE,
  ADD KEY `price` (`price`) USING BTREE,
  ADD KEY `is_show` (`is_show`) USING BTREE,
  ADD KEY `sort` (`sort`) USING BTREE,
  ADD KEY `sales` (`sales`) USING BTREE,
  ADD KEY `add_time` (`add_time`) USING BTREE,
  ADD KEY `is_postage` (`is_postage`) USING BTREE;

--
-- 表的索引 `eb_store_product_attr`
--
ALTER TABLE `eb_store_product_attr`
  ADD KEY `store_id` (`product_id`) USING BTREE;

--
-- 表的索引 `eb_store_product_attr_result`
--
ALTER TABLE `eb_store_product_attr_result`
  ADD UNIQUE KEY `product_id` (`product_id`) USING BTREE;

--
-- 表的索引 `eb_store_product_attr_value`
--
ALTER TABLE `eb_store_product_attr_value`
  ADD UNIQUE KEY `unique` (`unique`,`suk`) USING BTREE,
  ADD KEY `store_id` (`product_id`,`suk`) USING BTREE;

--
-- 表的索引 `eb_store_product_relation`
--
ALTER TABLE `eb_store_product_relation`
  ADD UNIQUE KEY `uid` (`uid`,`product_id`,`type`) USING BTREE,
  ADD KEY `type` (`type`) USING BTREE,
  ADD KEY `category` (`category`) USING BTREE;

--
-- 表的索引 `eb_store_product_reply`
--
ALTER TABLE `eb_store_product_reply`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `order_id_2` (`oid`,`unique`) USING BTREE,
  ADD KEY `add_time` (`add_time`) USING BTREE,
  ADD KEY `parent_id` (`reply_type`) USING BTREE,
  ADD KEY `is_del` (`is_del`) USING BTREE,
  ADD KEY `product_score` (`product_score`) USING BTREE,
  ADD KEY `service_score` (`service_score`) USING BTREE;

--
-- 表的索引 `eb_store_seckill`
--
ALTER TABLE `eb_store_seckill`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `start_time` (`start_time`,`stop_time`),
  ADD KEY `is_del` (`is_del`),
  ADD KEY `is_hot` (`is_hot`),
  ADD KEY `is_show` (`status`),
  ADD KEY `add_time` (`add_time`),
  ADD KEY `sort` (`sort`),
  ADD KEY `is_postage` (`is_postage`);

--
-- 表的索引 `eb_store_seckill_attr`
--
ALTER TABLE `eb_store_seckill_attr`
  ADD KEY `store_id` (`product_id`) USING BTREE;

--
-- 表的索引 `eb_store_seckill_attr_result`
--
ALTER TABLE `eb_store_seckill_attr_result`
  ADD UNIQUE KEY `product_id` (`product_id`) USING BTREE;

--
-- 表的索引 `eb_store_seckill_attr_value`
--
ALTER TABLE `eb_store_seckill_attr_value`
  ADD UNIQUE KEY `unique` (`unique`,`suk`) USING BTREE,
  ADD KEY `store_id` (`product_id`,`suk`) USING BTREE;

--
-- 表的索引 `eb_store_service`
--
ALTER TABLE `eb_store_service`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_store_service_log`
--
ALTER TABLE `eb_store_service_log`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_store_visit`
--
ALTER TABLE `eb_store_visit`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_system_admin`
--
ALTER TABLE `eb_system_admin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account` (`account`) USING BTREE,
  ADD KEY `status` (`status`) USING BTREE;

--
-- 表的索引 `eb_system_attachment`
--
ALTER TABLE `eb_system_attachment`
  ADD PRIMARY KEY (`att_id`);

--
-- 表的索引 `eb_system_attachment_category`
--
ALTER TABLE `eb_system_attachment_category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- 表的索引 `eb_system_config`
--
ALTER TABLE `eb_system_config`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_system_config_content`
--
ALTER TABLE `eb_system_config_content`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_system_config_tab`
--
ALTER TABLE `eb_system_config_tab`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_system_file`
--
ALTER TABLE `eb_system_file`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_system_group`
--
ALTER TABLE `eb_system_group`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `config_name` (`config_name`) USING BTREE;

--
-- 表的索引 `eb_system_log`
--
ALTER TABLE `eb_system_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`) USING BTREE,
  ADD KEY `add_time` (`add_time`) USING BTREE,
  ADD KEY `type` (`type`) USING BTREE;

--
-- 表的索引 `eb_system_menus`
--
ALTER TABLE `eb_system_menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pid` (`pid`) USING BTREE,
  ADD KEY `is_show` (`is_show`) USING BTREE,
  ADD KEY `access` (`access`) USING BTREE;

--
-- 表的索引 `eb_system_message`
--
ALTER TABLE `eb_system_message`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_system_notice`
--
ALTER TABLE `eb_system_notice`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type` (`type`) USING BTREE,
  ADD KEY `status` (`status`) USING BTREE;

--
-- 表的索引 `eb_system_notice_admin`
--
ALTER TABLE `eb_system_notice_admin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`,`notice_type`) USING BTREE,
  ADD KEY `add_time` (`add_time`) USING BTREE,
  ADD KEY `is_visit` (`is_visit`) USING BTREE,
  ADD KEY `is_click` (`is_click`) USING BTREE;

--
-- 表的索引 `eb_system_role`
--
ALTER TABLE `eb_system_role`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status` (`status`) USING BTREE,
  ADD UNIQUE KEY `sign` (`sign`) USING BTREE;

--
-- 表的索引 `eb_user`
--
ALTER TABLE `eb_user`
  ADD PRIMARY KEY (`uid`),
  ADD KEY `account` (`account`) USING BTREE,
  ADD KEY `spreaduid` (`spread_uid`) USING BTREE,
  ADD KEY `level` (`level`) USING BTREE,
  ADD KEY `status` (`status`) USING BTREE,
  ADD KEY `is_promoter` (`is_promoter`) USING BTREE;

--
-- 表的索引 `eb_user_address`
--
ALTER TABLE `eb_user_address`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`) USING BTREE,
  ADD KEY `is_default` (`is_default`) USING BTREE,
  ADD KEY `is_del` (`is_del`) USING BTREE;

--
-- 表的索引 `eb_user_bill`
--
ALTER TABLE `eb_user_bill`
  ADD PRIMARY KEY (`id`),
  ADD KEY `openid` (`uid`) USING BTREE,
  ADD KEY `status` (`status`) USING BTREE,
  ADD KEY `add_time` (`add_time`) USING BTREE,
  ADD KEY `pm` (`pm`) USING BTREE,
  ADD KEY `type` (`category`,`type`,`link_id`) USING BTREE;

--
-- 表的索引 `eb_user_enter`
--
ALTER TABLE `eb_user_enter`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uid` (`uid`) USING BTREE,
  ADD KEY `province` (`province`,`city`,`district`) USING BTREE,
  ADD KEY `is_lock` (`is_lock`) USING BTREE,
  ADD KEY `is_del` (`is_del`) USING BTREE,
  ADD KEY `status` (`status`) USING BTREE;

--
-- 表的索引 `eb_user_extract`
--
ALTER TABLE `eb_user_extract`
  ADD PRIMARY KEY (`id`),
  ADD KEY `extract_type` (`extract_type`) USING BTREE,
  ADD KEY `status` (`status`) USING BTREE,
  ADD KEY `add_time` (`add_time`) USING BTREE,
  ADD KEY `openid` (`uid`) USING BTREE,
  ADD KEY `fail_time` (`fail_time`);

--
-- 表的索引 `eb_user_group`
--
ALTER TABLE `eb_user_group`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_user_notice`
--
ALTER TABLE `eb_user_notice`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_user_notice_see`
--
ALTER TABLE `eb_user_notice_see`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_user_recharge`
--
ALTER TABLE `eb_user_recharge`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`) USING BTREE,
  ADD KEY `uid` (`uid`) USING BTREE,
  ADD KEY `recharge_type` (`recharge_type`) USING BTREE,
  ADD KEY `paid` (`paid`) USING BTREE;

--
-- 表的索引 `eb_wechat_media`
--
ALTER TABLE `eb_wechat_media`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type` (`type`,`media_id`) USING BTREE,
  ADD KEY `type_2` (`type`) USING BTREE;

--
-- 表的索引 `eb_wechat_message`
--
ALTER TABLE `eb_wechat_message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `openid` (`openid`) USING BTREE,
  ADD KEY `type` (`type`) USING BTREE,
  ADD KEY `add_time` (`add_time`) USING BTREE;

--
-- 表的索引 `eb_wechat_news_category`
--
ALTER TABLE `eb_wechat_news_category`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_wechat_qrcode`
--
ALTER TABLE `eb_wechat_qrcode`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `third_type` (`third_type`,`third_id`) USING BTREE,
  ADD KEY `ticket` (`ticket`) USING BTREE;

--
-- 表的索引 `eb_wechat_reply`
--
ALTER TABLE `eb_wechat_reply`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key` (`key`) USING BTREE,
  ADD KEY `type` (`type`) USING BTREE,
  ADD KEY `status` (`status`) USING BTREE,
  ADD KEY `hide` (`hide`) USING BTREE;

--
-- 表的索引 `eb_wechat_template`
--
ALTER TABLE `eb_wechat_template`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tempkey` (`tempkey`) USING BTREE;

--
-- 表的索引 `eb_wechat_user`
--
ALTER TABLE `eb_wechat_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `openid` (`openid`) USING BTREE,
  ADD KEY `groupid` (`groupid`) USING BTREE,
  ADD KEY `subscribe_time` (`subscribe_time`) USING BTREE,
  ADD KEY `add_time` (`add_time`) USING BTREE,
  ADD KEY `subscribe` (`subscribe`) USING BTREE,
  ADD KEY `unionid` (`unionid`) USING BTREE,
  ADD KEY `uid` (`uid`);

--
-- 表的索引 `eb_member_card_batch`
--
ALTER TABLE `eb_member_card_batch`
 ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_member_card`
--
ALTER TABLE `eb_member_card`
 ADD PRIMARY KEY (`id`),
 ADD KEY `card_batch_id` (`card_batch_id`) USING BTREE;
--
-- 表的索引 `eb_member_record`
--
ALTER TABLE `eb_member_record`
 ADD PRIMARY KEY (`id`);
 --
-- 表的索引 `eb_live_reward`
--
ALTER TABLE `eb_live_reward`
 ADD PRIMARY KEY (`id`),
 ADD KEY `uid` (`uid`) USING BTREE;
--
-- 表的索引 `eb_live_goods`
--
ALTER TABLE `eb_live_goods`
 ADD PRIMARY KEY (`id`);
--
-- 表的索引 `eb_event_registration`
--
ALTER TABLE `eb_event_registration`
 ADD PRIMARY KEY (`id`);
--
-- 表的索引 `eb_event_sign_up`
--
ALTER TABLE `eb_event_sign_up`
 ADD PRIMARY KEY (`id`);
--
-- 表的索引 `eb_user_sign`
--
ALTER TABLE `eb_user_sign`
 ADD PRIMARY KEY (`id`),
 ADD KEY `uid` (`uid`) USING BTREE;
--
-- 表的索引 `eb_sign_poster`
--
ALTER TABLE `eb_sign_poster`
 ADD PRIMARY KEY (`id`);
--
-- 表的索引 `eb_search_history`
--
ALTER TABLE `eb_search_history`
 ADD PRIMARY KEY (`id`);
--
-- 表的索引 `eb_special_watch`
--
ALTER TABLE `eb_special_watch`
 ADD PRIMARY KEY (`id`),
 ADD KEY `uid` (`uid`) USING BTREE,
 ADD KEY `special_id` (`special_id`) USING BTREE,
 ADD KEY `task_id` (`task_id`) USING BTREE;
--
-- 表的索引 `eb_live_playback`
--
ALTER TABLE `eb_live_playback`
 ADD PRIMARY KEY (`id`);
--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `eb_article`
--
ALTER TABLE `eb_article`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '文章管理ID';

--
-- 使用表AUTO_INCREMENT `eb_article_category`
--
ALTER TABLE `eb_article_category`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '文章分类id';

--
-- 使用表AUTO_INCREMENT `eb_certificate`
--
ALTER TABLE `eb_certificate`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_certificate_record`
--
ALTER TABLE `eb_certificate_record`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_certificate_related`
--
ALTER TABLE `eb_certificate_related`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_classes`
--
ALTER TABLE `eb_classes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_contact_phone`
--
ALTER TABLE `eb_contact_phone`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_data_download`
--
ALTER TABLE `eb_data_download`
  MODIFY `id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_data_download_buy`
--
ALTER TABLE `eb_data_download_buy`
  MODIFY `id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_data_download_categpry`
--
ALTER TABLE `eb_data_download_categpry`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_data_download_order`
--
ALTER TABLE `eb_data_download_order`
  MODIFY `id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_data_download_records`
--
ALTER TABLE `eb_data_download_records`
  MODIFY `id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_event_data`
--
ALTER TABLE `eb_event_data`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_event_price`
--
ALTER TABLE `eb_event_price`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_event_write_off_user`
--
ALTER TABLE `eb_event_write_off_user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_examination_record`
--
ALTER TABLE `eb_examination_record`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_examination_test_record`
--
ALTER TABLE `eb_examination_test_record`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_examination_wrong_bank`
--
ALTER TABLE `eb_examination_wrong_bank`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_express`
--
ALTER TABLE `eb_express`
  MODIFY `id` mediumint(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '快递公司id';

--
-- 使用表AUTO_INCREMENT `eb_grade`
--
ALTER TABLE `eb_grade`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_learning_records`
--
ALTER TABLE `eb_learning_records`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_lecturer`
--
ALTER TABLE `eb_lecturer`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_live_gift`
--
ALTER TABLE `eb_live_gift`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_member_ship`
--
ALTER TABLE `eb_member_ship`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_merchant`
--
ALTER TABLE `eb_merchant`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '讲师id';


--
-- 使用表AUTO_INCREMENT `eb_merchant_admin`
--
ALTER TABLE `eb_merchant_admin`
  MODIFY `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '讲师管理员表ID';

--
-- 使用表AUTO_INCREMENT `eb_merchant_bill`
--
ALTER TABLE `eb_merchant_bill`
  MODIFY `id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_merchant_follow`
--
ALTER TABLE `eb_merchant_follow`
  MODIFY `id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_merchant_flowing_water`
--
ALTER TABLE `eb_merchant_flowing_water`
  MODIFY `id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_merchant_menus`
--
ALTER TABLE `eb_merchant_menus`
  MODIFY `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '讲师菜单表ID';

--
-- 使用表AUTO_INCREMENT `eb_questions`
--
ALTER TABLE `eb_questions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_questions_categpry`
--
ALTER TABLE `eb_questions_categpry`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_special_task_category`
--
ALTER TABLE `eb_special_task_category`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_student`
--
ALTER TABLE `eb_student`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_system_broadcast`
--
ALTER TABLE `eb_system_broadcast`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_system_bucket`
--
ALTER TABLE `eb_system_bucket`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_system_group_data`
--
ALTER TABLE `eb_system_group_data`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '组合数据详情ID';

--
-- 使用表AUTO_INCREMENT `eb_teacher`
--
ALTER TABLE `eb_teacher`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_teacher_categpry`
--
ALTER TABLE `eb_teacher_categpry`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_test_paper`
--
ALTER TABLE `eb_test_paper`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_test_paper_category`
--
ALTER TABLE `eb_test_paper_category`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_test_paper_obtain`
--
ALTER TABLE `eb_test_paper_obtain`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_test_paper_order`
--
ALTER TABLE `eb_test_paper_order`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_test_paper_questions`
--
ALTER TABLE `eb_test_paper_questions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_test_paper_score_grade`
--
ALTER TABLE `eb_test_paper_score_grade`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_live_audit`
--
ALTER TABLE `eb_live_audit`
  MODIFY `id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_live_barrage`
--
ALTER TABLE `eb_live_barrage`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_live_honoured_guest`
--
ALTER TABLE `eb_live_honoured_guest`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_live_studio`
--
ALTER TABLE `eb_live_studio`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_live_user`
--
ALTER TABLE `eb_live_user`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_phone_user`
--
ALTER TABLE `eb_phone_user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_web_recommend`
--
ALTER TABLE `eb_web_recommend`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_recommend`
--
ALTER TABLE `eb_recommend`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_web_recommend_relation`
--
ALTER TABLE `eb_web_recommend_relation`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_recommend_banner`
--
ALTER TABLE `eb_recommend_banner`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_recommend_relation`
--
ALTER TABLE `eb_recommend_relation`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_relation`
--
ALTER TABLE `eb_relation`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_reply_false`
--
ALTER TABLE `eb_reply_false`
  MODIFY `id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_routine_access_token`
--
ALTER TABLE `eb_routine_access_token`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '小程序access_token表ID';

--
-- 使用表AUTO_INCREMENT `eb_routine_form_id`
--
ALTER TABLE `eb_routine_form_id`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '表单ID表ID';

--
-- 使用表AUTO_INCREMENT `eb_routine_template`
--
ALTER TABLE `eb_routine_template`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '模板id';

--
-- 使用表AUTO_INCREMENT `eb_search`
--
ALTER TABLE `eb_search`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_sms_code`
--
ALTER TABLE `eb_sms_code`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id';

--
-- 使用表AUTO_INCREMENT `eb_special`
--
ALTER TABLE `eb_special`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `eb_special_source`
--
ALTER TABLE `eb_special_source`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `eb_special_barrage`
--
ALTER TABLE `eb_special_barrage`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_special_barrage`
--
ALTER TABLE `eb_special_batch`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_special_buy`
--
ALTER TABLE `eb_special_buy`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_special_content`
--
ALTER TABLE `eb_special_content`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_special_course`
--
ALTER TABLE `eb_special_course`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_special_exchange`
--
ALTER TABLE `eb_special_exchange`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_special_record`
--
ALTER TABLE `eb_special_record`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_special_relation`
--
ALTER TABLE `eb_special_relation`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_special_reply`
--
ALTER TABLE `eb_special_reply`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_special_subject`
--
ALTER TABLE `eb_special_subject`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_special_task`
--
ALTER TABLE `eb_special_task`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_store_bargain`
--
ALTER TABLE `eb_store_bargain`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '砍价产品ID';

--
-- 使用表AUTO_INCREMENT `eb_store_bargain_user`
--
ALTER TABLE `eb_store_bargain_user`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户参与砍价表ID';

--
-- 使用表AUTO_INCREMENT `eb_store_bargain_user_help`
--
ALTER TABLE `eb_store_bargain_user_help`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '砍价用户帮助表ID';

--
-- 使用表AUTO_INCREMENT `eb_store_cart`
--
ALTER TABLE `eb_store_cart`
  MODIFY `id` bigint(8) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '购物车表ID';

--
-- 使用表AUTO_INCREMENT `eb_store_category`
--
ALTER TABLE `eb_store_category`
  MODIFY `id` mediumint(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '商品分类表ID';

--
-- 使用表AUTO_INCREMENT `eb_store_combination`
--
ALTER TABLE `eb_store_combination`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_store_coupon`
--
ALTER TABLE `eb_store_coupon`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '优惠券表ID';

--
-- 使用表AUTO_INCREMENT `eb_store_coupon_issue`
--
ALTER TABLE `eb_store_coupon_issue`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_store_coupon_user`
--
ALTER TABLE `eb_store_coupon_user`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '优惠券发放记录id';

--
-- 使用表AUTO_INCREMENT `eb_store_order`
--
ALTER TABLE `eb_store_order`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '订单ID';

--
-- 使用表AUTO_INCREMENT `eb_store_pink`
--
ALTER TABLE `eb_store_pink`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_store_pink_false`
--
ALTER TABLE `eb_store_pink_false`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_store_product`
--
ALTER TABLE `eb_store_product`
  MODIFY `id` mediumint(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '商品id';

--
-- 使用表AUTO_INCREMENT `eb_store_product_reply`
--
ALTER TABLE `eb_store_product_reply`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '评论ID';

--
-- 使用表AUTO_INCREMENT `eb_store_seckill`
--
ALTER TABLE `eb_store_seckill`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '商品秒杀产品表id';

--
-- 使用表AUTO_INCREMENT `eb_store_service`
--
ALTER TABLE `eb_store_service`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '客服id';

--
-- 使用表AUTO_INCREMENT `eb_store_service_log`
--
ALTER TABLE `eb_store_service_log`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '客服用户对话记录表ID';

--
-- 使用表AUTO_INCREMENT `eb_store_visit`
--
ALTER TABLE `eb_store_visit`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_system_admin`
--
ALTER TABLE `eb_system_admin`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '后台管理员表ID';

--
-- 使用表AUTO_INCREMENT `eb_system_attachment`
--
ALTER TABLE `eb_system_attachment`
  MODIFY `att_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_system_attachment_category`
--
ALTER TABLE `eb_system_attachment_category`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_system_config`
--
ALTER TABLE `eb_system_config`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '配置id';

--
-- 使用表AUTO_INCREMENT `eb_system_config_content`
--
ALTER TABLE `eb_system_config_content`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_system_config_tab`
--
ALTER TABLE `eb_system_config_tab`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '配置分类id';

--
-- 使用表AUTO_INCREMENT `eb_system_file`
--
ALTER TABLE `eb_system_file`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '文件对比ID';

--
-- 使用表AUTO_INCREMENT `eb_system_group`
--
ALTER TABLE `eb_system_group`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '组合数据ID';
--
-- 使用表AUTO_INCREMENT `eb_system_log`
--
ALTER TABLE `eb_system_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '管理员操作记录ID';

--
-- 使用表AUTO_INCREMENT `eb_system_menus`
--
ALTER TABLE `eb_system_menus`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '菜单ID';

--
-- 使用表AUTO_INCREMENT `eb_system_message`
--
ALTER TABLE `eb_system_message`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '消息管理';

--
-- 使用表AUTO_INCREMENT `eb_system_notice`
--
ALTER TABLE `eb_system_notice`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '通知模板id';

--
-- 使用表AUTO_INCREMENT `eb_system_notice_admin`
--
ALTER TABLE `eb_system_notice_admin`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '通知记录ID';

--
-- 使用表AUTO_INCREMENT `eb_system_role`
--
ALTER TABLE `eb_system_role`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '身份管理id';

--
-- 使用表AUTO_INCREMENT `eb_user`
--
ALTER TABLE `eb_user`
  MODIFY `uid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户id';

--
-- 使用表AUTO_INCREMENT `eb_user_address`
--
ALTER TABLE `eb_user_address`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户地址id';

--
-- 使用表AUTO_INCREMENT `eb_user_bill`
--
ALTER TABLE `eb_user_bill`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户账单id';

--
-- 使用表AUTO_INCREMENT `eb_user_enter`
--
ALTER TABLE `eb_user_enter`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '讲师申请ID';

--
-- 使用表AUTO_INCREMENT `eb_user_extract`
--
ALTER TABLE `eb_user_extract`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_user_group`
--
ALTER TABLE `eb_user_group`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_user_notice`
--
ALTER TABLE `eb_user_notice`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_user_notice_see`
--
ALTER TABLE `eb_user_notice_see`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_user_recharge`
--
ALTER TABLE `eb_user_recharge`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_wechat_media`
--
ALTER TABLE `eb_wechat_media`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '微信视频音频id';

--
-- 使用表AUTO_INCREMENT `eb_wechat_message`
--
ALTER TABLE `eb_wechat_message`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户行为记录id';

--
-- 使用表AUTO_INCREMENT `eb_wechat_news_category`
--
ALTER TABLE `eb_wechat_news_category`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '图文消息管理ID';

--
-- 使用表AUTO_INCREMENT `eb_wechat_qrcode`
--
ALTER TABLE `eb_wechat_qrcode`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '微信二维码ID';

--
-- 使用表AUTO_INCREMENT `eb_wechat_reply`
--
ALTER TABLE `eb_wechat_reply`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '微信关键字回复id';

--
-- 使用表AUTO_INCREMENT `eb_wechat_template`
--
ALTER TABLE `eb_wechat_template`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '模板id';

--
-- 使用表AUTO_INCREMENT `eb_member_card_batch`
--
ALTER TABLE `eb_member_card_batch`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_member_card`
--
ALTER TABLE `eb_member_card`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_member_ship`
--
ALTER TABLE `eb_member_ship`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_member_record`
--
ALTER TABLE `eb_member_record`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `eb_live_reward`
--
ALTER TABLE `eb_live_reward`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
  --
-- 使用表AUTO_INCREMENT `eb_live_goods`
--
ALTER TABLE `eb_live_goods`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `eb_event_registration`
--
ALTER TABLE `eb_event_registration`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `eb_event_sign_up`
--
ALTER TABLE `eb_event_sign_up`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `eb_user_sign`
--
ALTER TABLE `eb_user_sign`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `eb_sign_poster`
--
ALTER TABLE `eb_sign_poster`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `eb_wechat_user`
--
ALTER TABLE `eb_wechat_user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `eb_search_history`
--
ALTER TABLE `eb_search_history`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `eb_special_watch`
--
ALTER TABLE `eb_special_watch`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `eb_live_playback`
--
ALTER TABLE `eb_live_playback`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;
