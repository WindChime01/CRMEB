<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016～2023 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

namespace app\admin\model\user;

use service\SystemConfigService;
use think\Db;
use traits\ModelTrait;
use basic\ModelBasic;
use app\admin\model\user\MemberCard;

/**
 * 会员卡批次 model
 * Class MemberCardBatch
 * @package app\admin\model\user
 */
class MemberCardBatch extends ModelBasic
{
    use ModelTrait;

    const fileLocation = 'public/qrcode/';

    /**批量获取批次卡
     * @param array $where
     * @return array|bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getBatchList(array $where)
    {
        if (!is_array($where)) {
            return false;
        }
        $batch_where = array();
        if (isset($where['title']) && $where['title']) {
            $batch_where['title'] = ['like', '%' . $where['title']];
        }
        $data = self::where($batch_where)->order('id DESC')
            ->page((int)$where['page'], (int)$where['limit'])
            ->select()
            ->each(function ($item) {
                $item['create_time'] = ($item['create_time'] != 0 || $item['create_time']) ? date('Y-m-d H:i:s', $item['create_time']) : "";
            });
        $data = count((array)$data) ? $data->toArray() : [];
        $count = self::where($batch_where)->count();
        return compact('data', 'count');
    }

    /**
     * 生成会员卡批次二维码
     */
    public static function qrcodes_url($id = 0, $size = 5)
    {
        vendor('phpqrcode.phpqrcode');
        $urls = SystemConfigService::get('site_url') . '/';
        $url = $urls . 'wap/special/member_manage/type/2/bid/' . $id;
        $value = $url;            //二维码内容
        $errorCorrectionLevel = 'H';    //容错级别
        $matrixPointSize = $size;            //生成图片大小
        //生成二维码图片
        $filename = self::fileLocation . rand(10000000, 99999999) . '.png';
        \QRcode::png($value, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
        return $urls . $filename;
    }

    /**获取单条批次信息
     * @param $id
     * @return array|bool|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getBatchOne($id)
    {
        if (!$id) {
            return false;
        }
        return self::where(['id' => $id])->find();
    }

    public static function getBatchAll(array $where)
    {

        if (!$where || !is_array($where)) {
            $where = array();
        }
        return self::where($where)->select();
    }

    /**增加批次表
     * @param array $insert_data
     * @return bool|int|string
     */
    public static function addBatch(array $insert_data)
    {
        if (!$insert_data) {
            return false;
        }
        return self::insertGetId($insert_data);
    }


    public function getCreateTimeAttr($time)
    {
        return $time;//返回create_time原始数据，不进行时间戳转换。
    }

    public static function delMemberCard($id)
    {
        $res = self::where('id', $id)->delete();
        $res1 = false;
        if ($res) {
            $res1 = MemberCard::where('card_batch_id', $id)->delete();
        }
        $res2 = $res && $res1;
        return $res2;
    }
}
