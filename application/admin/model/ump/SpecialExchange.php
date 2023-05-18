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

namespace app\admin\model\ump;

use service\SystemConfigService;
use think\Db;
use traits\ModelTrait;
use basic\ModelBasic;
use service\UtilService;
use app\admin\model\user\User;
use app\admin\model\special\Special;
use service\PhpSpreadsheetService;

/**
 * 活动批次 model
 * Class SpecialExchange
 * @package app\admin\model\ump
 */
class SpecialExchange extends ModelBasic
{
    use ModelTrait;

    const fileLocation = 'public/qrcode/';


    /**
     * 生成会员卡批次二维码
     */
    public static function qrcodes_url($special_id, $size = 5)
    {
        vendor('phpqrcode.phpqrcode');
        $urls = SystemConfigService::get('site_url') . '/';
        $url = $urls . 'wap/special/exchange/special_id/' . $special_id;
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


    public function getCreateTimeAttr($time)
    {
        return $time;//返回create_time原始数据，不进行时间戳转换。
    }

    /**根据批次id和数量生成卡
     * @param int $batch_id
     * @param int $total_num
     * @return bool
     */
    public static function addCard($batch_id, $total_num, $special_id)
    {
        if (!$batch_id || $batch_id == 0 || !$special_id || $special_id == 0 || !$total_num || $total_num == 0) {
            return false;
        }
        try {
            $inster_card = array();
            for ($i = 0; $i < $total_num; $i++) {
                $inster_card['special_id'] = $special_id;
                $inster_card['exchange_code'] = UtilService::makeRandomNumber('', 6);
                $inster_card['card_batch_id'] = $batch_id;
                $inster_card['add_time'] = time();
                $res[] = $inster_card;
            }
            //数据切片批量插入，提高性能
            $chunk_inster_card = array_chunk($res, 100, true);
            foreach ($chunk_inster_card as $v) {
                SpecialExchange::insertAll($v);
            }
            return true;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function setCardWhere($where)
    {
        $model = new self();
        if (isset($where['card_batch_id']) && $where['card_batch_id']) {
            $model = $model->where('card_batch_id', $where['card_batch_id']);
        }
        if (isset($where['exchange_code']) && $where['exchange_code']) {
            $model = $model->where('exchange_code', 'like', "%$where[exchange_code]%");
        }
        if (isset($where['is_status']) && $where['is_status'] != "") {
            $model = $model->where('status', $where['is_status']);
        }
        if (isset($where['is_use']) && $where['is_use'] != "") {
            if ($where['is_use'] == 1) {
                $model = $model->where('use_uid', '>=', 1);
            } else {
                $model = $model->where('use_uid', 0);
            }
        }
        if (isset($where['phone']) && $where['phone']) {
            $uid = User::where(['phone' => $where['phone']])->value('uid');
            $model = $model->where('use_uid', $uid);
        }
        $time['data'] = '';
        if ($where['start_time'] != '' && $where['end_time'] != '') {
            $time['data'] = $where['start_time'] . ' - ' . $where['end_time'];
        }
        $model = self::getModelTime($time, $model, 'use_time');
        return $model->order('use_time desc,use_uid desc,id desc');
    }

    public static function getCardList($where = [])
    {
        if (!is_array($where)) {
            return false;
        }
        $model = self::setCardWhere($where);
        if (isset($where['excel']) && $where['excel'] == 1) {
            $data = ($data = $model->select()) && count($data) ? $data->toArray() : [];
            self::SaveExcel($data);
        } else {
            $data = ($data = $model->page((int)$where['page'], (int)$where['limit'])->select()) && count($data) ? $data->toArray() : [];
            if (!empty($data)) {
                foreach ($data as $k => $v) {
                    $data[$k]['use_time'] = ($v['use_time'] != 0 || $v['use_time']) ? date('Y-m-d H:i:s', $v['use_time']) : "";
                    if ($v['use_uid'] && $v['use_uid'] > 0) {
                        $user_info = User::where(['uid' => $v['use_uid']])->field("account, nickname, phone")->find();
                        if ($user_info) {
                            $data[$k]['username'] = (isset($user_info['nickname']) && $user_info['nickname']) ? $user_info['nickname'] : $user_info['account'];
                            $data[$k]['user_phone'] = (isset($user_info['phone']) && $user_info['phone']) ? $user_info['phone'] : "";
                        } else {
                            $data[$k]['username'] = "用户已被删除";
                            $data[$k]['user_phone'] = "无";
                        }
                    } else {
                        $data[$k]['username'] = "";
                        $data[$k]['user_phone'] = "";
                    }
                }
            }
            $count = self::setCardWhere($where)->count();
            return compact('data', 'count');
        }
    }

    /**
     * 保存并下载excel
     * $list array
     * return
     */
    public static function SaveExcel($list)
    {
        $export = [];
        foreach ($list as $index => $item) {
            $batch = SpecialBatch::where('id', $item['card_batch_id'])->value('title');
            $special_title = Special::where('id', $item['special_id'])->value('title');
            $use_time = ($item['use_time'] != 0 || $item['use_time']) ? date('Y-m-d H:i:s', $item['use_time']) : "";
            if ($item['use_uid'] && $item['use_uid'] > 0) {
                $user_info = User::where(['uid' => $item['use_uid']])->field("account, nickname, phone")->find();
                if ($user_info) {
                    $username = (isset($user_info['nickname']) && $user_info['nickname']) ? $user_info['nickname'] : $user_info['account'];
                    $user_phone = (isset($user_info['phone']) && $user_info['phone']) ? $user_info['phone'] : "";
                } else {
                    $username = "用户已被删除";
                    $user_phone = "无";
                }
            } else {
                $username = "";
                $user_phone = "";
            }
            $export[] = [
                $item['card_batch_id'],
                $batch,
                $special_title,
                $item['exchange_code'],
                $item['status'] == 1 ? '激活' : '冻结',
                $item['use_uid'] > 0 ? '使用' : '未使用',
                $username,
                $user_phone,
                $use_time
            ];
        }
        $filename = '兑换码导出' . time() . '.xlsx';
        $head = ['活动编号', '活动名称', '专题名称', '兑换码', '是否激活', '是否使用', '兑换人', '兑换人电话', '兑换时间'];
        PhpSpreadsheetService::outdata($filename, $export, $head);
    }
}
