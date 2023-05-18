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

use service\UtilService;
use think\Db;
use traits\ModelTrait;
use basic\ModelBasic;
use app\admin\model\user\User;
use app\admin\model\user\UserBill;
use app\admin\model\user\MemberCardBatch;
use service\PhpSpreadsheetService;

/**
 * 会员卡批次 model
 * Class MemberCard
 * @package app\admin\model\user
 */
class MemberCard extends ModelBasic
{
    use ModelTrait;


    public static function getCardOne(array $where)
    {
        if (empty($where)) {
            return false;
        }
        return self::where($where)->find();
    }


    /**根据批次id和数量生成卡
     * @param int $batch_id
     * @param int $total_num
     * @return bool
     */
    public static function addCard(int $batch_id, int $total_num)
    {
        if (!$batch_id || $batch_id == 0 || !$total_num || $total_num == 0) {
            return false;
        }
        try {
            $inster_card = array();
            for ($i = 0; $i < $total_num; $i++) {
                $inster_card['card_number'] = UtilService::makeRandomNumber("CR", 5, $batch_id);
                $inster_card['card_password'] = UtilService::makeRandomNumber('', 5);
                $inster_card['card_batch_id'] = $batch_id;
                $inster_card['create_time'] = time();
                $res[] = $inster_card;
            }
            //数据切片批量插入，提高性能
            $chunk_inster_card = array_chunk($res, 100, true);
            foreach ($chunk_inster_card as $v) {
                self::insertAll($v);
            }
            return true;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }


    }

    public function getCreateTimeAttr($time)
    {
        return $time;//返回create_time原始数据，不进行时间戳转换。
    }

    public static function setCardWhere($where)
    {
        $model = new self();
        if (isset($where['card_batch_id']) && $where['card_batch_id']) {
            $model = $model->where('card_batch_id', $where['card_batch_id']);
        }
        if (isset($where['card_number']) && $where['card_number']) {
            $model = $model->where('card_number', 'like', "%$where[card_number]%");
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
        return $model->order('use_uid desc,id desc');
    }

    public static function getCardList(array $where)
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

    /*
    * 保存并下载excel
    * $list array
    * return
    */
    public static function SaveExcel($list)
    {
        $export = [];
        foreach ($list as $index => $item) {
            $batch = MemberCardBatch::where('id', $item['card_batch_id'])->value('title');
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
                $item['card_number'],
                $item['card_password'],
                $item['status'] == 1 ? '激活' : '冻结',
                $item['use_uid'] > 0 ? '使用' : '未使用',
                $username,
                $user_phone,
                $use_time
            ];
        }
        $filename = '会员卡导出' . time() . '.xlsx';
        $head = ['批次编号', '批次名称', '卡号', '密码', '是否激活', '是否使用', '领取人', '领取人电话', '领取时间'];
        PhpSpreadsheetService::outdata($filename, $export, $head);
    }

    /**
     * @param $code
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function cateDays($code)
    {
        $cate = self::where('card_number', $code)->find();
        $use_day = MemberCardBatch::where('id', $cate['card_batch_id'])->value('use_day');
        return $use_day;
    }
}
