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

namespace app\wap\model\special;

use traits\ModelTrait;
use basic\ModelBasic;
use app\wap\model\store\StorePink;
use app\wap\model\topic\TestPaperObtain;
use app\wap\model\material\DataDownloadBuy;

/**专题兑换
 * Class SpecialExchange
 * @package app\wap\model\special
 */
class SpecialExchange extends ModelBasic
{
    use ModelTrait;

    /**提交兑换
     * @param $uid
     * @param $code
     */
    public static function userExchangeSubmit($uid, $special_id, $code)
    {
        if (!$uid) return self::setErrorInfo('参数错误!');
        $exchange = SpecialExchange::where(['exchange_code' => $code, 'special_id' => $special_id])->find();
        if (!$exchange) return self::setErrorInfo('兑换码不存在或活动和兑换码不匹配!');
        if (!$exchange['status']) return self::setErrorInfo('兑换码已结束!');
        if ($exchange['use_uid'] > 0 || $exchange['use_time'] > 0) return self::setErrorInfo('兑换码已兑换!');
        $batch = SpecialBatch::where('id', $exchange['card_batch_id'])->find();
        if (!$batch['status']) return self::setErrorInfo('活动已结束!');
        self::beginTrans();
        $pinkId = StorePink::where(['cid' => $special_id, 'status' => '1', 'uid' => $uid])->order('add_time desc')->value('id');
        if ($pinkId) {
            return self::setErrorInfo('您正在拼团，无法兑换!', true);
        }
        $res = self::edit(['use_uid' => $uid, 'use_time' => time()], $exchange['id'], 'id');
        if ($res && $batch) {
            $res1 = SpecialBatch::edit(['use_num' => bcadd($batch['use_num'], 1, 0)], $exchange['card_batch_id'], 'id');
            if (!$res1) {
                return self::setErrorInfo('数据修改有误!', true);
            }
            $special = Special::PreWhere()->where('id', $special_id)->field('id,is_light,type,money,pay_type,validity')->find();
            if (!$special) {
                return self::setErrorInfo('兑换码关联的专题不存在!', true);
            }
            if (in_array($special['money'], [0, 0.00]) || in_array($special['pay_type'], [PAY_NO_MONEY, PAY_PASSWORD])) {
                $isPay = 1;
            } else {
                $isPay = (!$uid || $uid == 0) ? false : SpecialBuy::PaySpecial($special_id, $uid);
            }
            if ($isPay) {
                return self::setErrorInfo('该专题是免费的或者已购买，无需兑换!', true);
            }
            if ($special['type'] == SPECIAL_COLUMN) {
                $special_source = SpecialSource::getSpecialSource($special['id']);
                if ($special_source) {
                    foreach ($special_source as $k => $v) {
                        $task_special = Special::PreWhere()->where(['id'=>$v['source_id']])->find();
                        if ($task_special['is_show'] == 1) {
                            SpecialBuy::setBuySpecial('', $uid, $v['source_id'], 4, $task_special['validity'], $special_id);
                        }
                    }
                }
            }
            SpecialBuy::setBuySpecial('', $uid, $special_id, 4, $special['validity']);
            TestPaperObtain::setTestPaper('', $uid, $special_id, 2);
            DataDownloadBuy::setDataDownload('', $uid, $special_id, 1);
            self::commitTrans();
            $data['id'] = $special['id'];
            $data['is_light'] = $special['is_light'];
            return $data;
        } else {
            return self::setErrorInfo('数据修改有误!', true);
        }
    }

}
