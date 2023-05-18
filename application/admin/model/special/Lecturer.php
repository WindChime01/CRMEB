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

namespace app\admin\model\special;

use traits\ModelTrait;
use basic\ModelBasic;
use app\admin\model\order\StoreOrder as StoreOrderModel;
use app\admin\model\special\Special;
use app\admin\model\special\SpecialTask;
use app\admin\model\questions\TestPaper;
use app\admin\model\questions\Questions;
use app\admin\model\questions\QuestionsCategpry;
use app\admin\model\questions\Certificate;
use app\admin\model\user\User;
use app\admin\model\merchant\Merchant;
use app\admin\model\merchant\MerchantAdmin;
use app\admin\model\system\RecommendRelation;
use app\admin\model\system\WebRecommendRelation;
use app\admin\model\download\DataDownload as DownloadModel;
use app\admin\model\store\StoreProduct as ProductModel;
use app\admin\model\ump\EventRegistration as EventRegistrationModel;

/**
 * Class Lecturer 讲师
 * @package app\admin\model\special
 */
class Lecturer extends ModelBasic
{
    use ModelTrait;

    //设置where条件
    public static function setWhere($where, $alirs = '', $model = null)
    {
        $model = $model === null ? new self() : $model;
        $model = $alirs !== '' ? $model->alias($alirs) : $model;
        $alirs = $alirs === '' ? $alirs : $alirs . '.';
        $model = $model->where("{$alirs}is_del", 0);
        if (isset($where['is_show']) && $where['is_show'] !== '') $model = $model->where("{$alirs}is_show", $where['is_show']);
        if ($where['title'] && $where['title']) $model = $model->where("{$alirs}lecturer_name", 'LIKE', "%$where[title]%");
        $model = $model->order("{$alirs}sort desc");
        return $model;
    }


    public static function getRecommendLecturerList($where, $lecturer)
    {
        $data = self::setWhere($where)->where('id', 'not in', $lecturer)->page((int)$where['page'], (int)$where['limit'])->select();
        $data = count($data) ? $data->toArray() : [];
        $count = self::setWhere($where)->where('id', 'not in', $lecturer)->count();
        return compact('data', 'count');
    }

    public static function setMerWhere($where, $alirs = '', $model = null)
    {
        $model = $model === null ? new self() : $model;
        $model = $alirs !== '' ? $model->alias($alirs) : $model;
        $alirs = $alirs === '' ? $alirs : $alirs . '.';
        $model = $model->where("{$alirs}is_del", 0);
        if (isset($where['is_show']) && $where['is_show'] !== '') $model = $model->where("{$alirs}is_show", $where['is_show']);
        if ($where['title'] && $where['title']) $model = $model->where("{$alirs}lecturer_name", 'LIKE', "%$where[title]%");
        $model = $model->order("{$alirs}sort desc,{$alirs}id desc");
        $model = $model->join('Merchant m', 'l.mer_id=m.id', 'left')->field('l.*,m.status,m.is_source');
        return $model;
    }

    /**讲师列表
     * @param $where
     * @return array
     * @throws \think\Exception
     */
    public static function getLecturerList($where)
    {
        $data = self::setMerWhere($where, 'l')->page((int)$where['page'], (int)$where['limit'])->select();
        $data = count($data) ? $data->toArray() : [];
        foreach ($data as $key => &$item) {
            $item['recommend'] = RecommendRelation::where('a.link_id', $item['id'])->where('a.type', 6)->alias('a')
                ->join('__RECOMMEND__ r', 'a.recommend_id=r.id')->column('a.id,r.title');
            $item['web_recommend'] = WebRecommendRelation::where('a.link_id', $item['id'])->where('a.type', 2)->alias('a')
                ->join('__WEB_RECOMMEND__ r', 'a.recommend_id=r.id')->column('a.id,r.title');
        }
        $count = self::setMerWhere($where, 'l')->count();
        return compact('data', 'count');
    }

    /**
     * 删除讲师
     * @param $id
     * @return bool|int
     * @throws \think\exception\DbException
     */
    public static function delLecturer($id)
    {
        $lecturer = self::get($id);
        if (!$lecturer) return self::setErrorInfo('删除的数据不存在');
        Special::where('is_del', 0)->where('lecturer_id', $id)->update(['is_show' => 0, 'is_del' => 1]);
        if ($lecturer['mer_id'] > 0) {
            $merchant = Merchant::get($lecturer['mer_id']);
            if ($merchant) {
                Merchant::where('id', $lecturer['mer_id'])->update(['is_del' => 1]);
                User::where('uid', $merchant['uid'])->update(['business' => 0]);
                MerchantAdmin::where('mer_id', $lecturer['mer_id'])->update(['is_del' => 1]);
                DownloadModel::where(['is_del' => 0, 'mer_id' => $lecturer['mer_id']])->update(['is_show' => 0, 'is_del' => 1]);
                ProductModel::where(['is_del' => 0, 'mer_id' => $lecturer['mer_id']])->update(['is_show' => 0, 'is_del' => 1]);
                EventRegistrationModel::where(['is_del' => 0, 'mer_id' => $lecturer['mer_id']])->update(['is_show' => 0, 'is_del' => 1]);
                SpecialTask::where(['is_del' => 0, 'mer_id' => $lecturer['mer_id']])->update(['is_show' => 0, 'is_del' => 1]);
                SpecialTaskCategory::where(['is_del' => 0, 'mer_id' => $lecturer['mer_id']])->update(['is_del' => 1]);
                TestPaper::where(['is_del' => 0, 'mer_id' => $lecturer['mer_id']])->update(['is_show' => 0, 'is_del' => 1]);
                Questions::where(['is_del' => 0, 'mer_id' => $lecturer['mer_id']])->update(['is_del' => 1]);
                QuestionsCategpry::where(['is_del' => 0, 'mer_id' => $lecturer['mer_id']])->update(['is_del' => 1]);
                Certificate::where(['is_del' => 0, 'mer_id' => $lecturer['mer_id']])->update(['is_del' => 1]);
            }
        }
        return self::where('id', $id)->update(['is_del' => 1]);
    }

    /**获取商户id
     * @param $id
     */
    public static function getMerId($id)
    {
        return self::where('id', $id)->value('mei_id');
    }

    /**讲师课程订单
     * @param $lecturer_id
     */
    public static function lecturerOrderList($where)
    {
        $model = self::getOrderWhere($where)->field('a.order_id,a.pay_price,a.pay_type,a.paid,a.status,a.total_price,a.refund_status,a.type,s.title,r.nickname,a.uid');
        $data = ($data = $model->page((int)$where['page'], (int)$where['limit'])->select()) && count($data) ? $data->toArray() : [];

        foreach ($data as $key => &$value) {
            if ($value['paid'] == 0) {
                $value['status_name'] = '未支付';
            } else if ($value['paid'] == 1 && $value['status'] == 0 && $value['refund_status'] == 0 && $value['type'] != 2) {
                $value['status_name'] = '已支付';
            } else if ($value['paid'] == 1 && $value['status'] == 0 && $value['refund_status'] == 1 && $value['type'] != 2) {
                $value['status_name'] = '退款中';
            } else if ($value['paid'] == 1 && $value['status'] == 0 && $value['refund_status'] == 2 && $value['type'] != 2) {
                $value['status_name'] = '已退款';
            }
            if ($value['nickname']) {
                $value['nickname'] = $value['nickname'] . '/' . $value['uid'];
            } else {
                $value['nickname'] = '暂无昵称/' . $value['uid'];
            }
            if ($value['paid'] == 1) {
                switch ($value['pay_type']) {
                    case 'weixin':
                        $value['pay_type_name'] = '微信支付';
                        break;
                    case 'yue':
                        $value['pay_type_name'] = '余额支付';
                        break;
                    case 'zhifubao':
                        $value['pay_type_name'] = '支付宝支付';
                        break;
                    default:
                        $value['pay_type_name'] = '其他支付';
                        break;
                }
            } else {
                switch ($value['pay_type']) {
                    default:
                        $value['pay_type_name'] = '未支付';
                        break;
                }
            }
        }
        $count = self::getOrderWhere($where)->count();
        return compact('count', 'data');
    }

    /**条件处理
     * @param $where
     * @return StoreOrderModel
     */
    public static function getOrderWhere($where)
    {
        $model = StoreOrderModel::alias('a')->join('user r', 'r.uid=a.uid', 'LEFT')
            ->join('Special s', 'a.cart_id=s.id')
            ->where('a.type', 0)->where('a.paid', 1);
        if ($where['lecturer_id']) {
            $model = $model->where('s.lecturer_id', $where['lecturer_id']);
        }
        if ($where['data'] !== '') {
            $model = self::getModelTime($where, $model, 'a.add_time');
        }
        $model = $model->order('a.id desc');
        return $model;
    }

    /**
     * 处理订单金额
     * @param $where
     * @return array
     */
    public static function getOrderPrice($where)
    {
        $price = array();
        $price['pay_price'] = 0;//支付金额
        $price['pay_price_wx'] = 0;//微信支付金额
        $price['pay_price_yue'] = 0;//余额支付金额
        $price['pay_price_zhifubao'] = 0;//支付宝支付金额

        $list = self::getOrderWhere($where)->field([
            'sum(a.total_num) as total_num',
            'sum(a.pay_price) as pay_price',
        ])->find()->toArray();
        $price['total_num'] = $list['total_num'];//商品总数
        $price['pay_price'] = $list['pay_price'];//支付金额
        $list = self::getOrderWhere($where)->field('sum(a.pay_price) as pay_price,a.pay_type')->group('a.pay_type')->select()->toArray();
        foreach ($list as $v) {
            if ($v['pay_type'] == 'weixin') {
                $price['pay_price_wx'] = $v['pay_price'];
            } elseif ($v['pay_type'] == 'yue') {
                $price['pay_price_yue'] = $v['pay_price'];
            } elseif ($v['pay_type'] == 'zhifubao') {
                $price['pay_price_zhifubao'] = $v['pay_price'];
            } else {
                $price['pay_price_other'] = $v['pay_price'];
            }
        }
        $price['order_sum'] = self::getOrderWhere($where)->count();
        return $price;
    }

    public static function getBadge($where)
    {
        $price = self::getOrderPrice($where);
        return [
            [
                'name' => '订单数量',
                'field' => '件',
                'count' => $price['order_sum'],
                'background_color' => 'layui-bg-blue',
                'col' => 4
            ],
            [
                'name' => '售出课程',
                'field' => '件',
                'count' => $price['total_num'],
                'background_color' => 'layui-bg-blue',
                'col' => 4
            ],
            [
                'name' => '订单金额',
                'field' => '元',
                'count' => $price['pay_price'],
                'background_color' => 'layui-bg-blue',
                'col' => 4
            ],
            [
                'name' => '微信支付金额',
                'field' => '元',
                'count' => $price['pay_price_wx'],
                'background_color' => 'layui-bg-blue',
                'col' => 4
            ],
            [
                'name' => '余额支付金额',
                'field' => '元',
                'count' => $price['pay_price_yue'],
                'background_color' => 'layui-bg-blue',
                'col' => 4
            ],
            [
                'name' => '支付宝支付金额',
                'field' => '元',
                'count' => $price['pay_price_zhifubao'],
                'background_color' => 'layui-bg-blue',
                'col' => 4
            ]
        ];
    }

    /**生成讲师
     * @param $data
     * @param $mer_id
     * @return object
     */
    public static function addLecturer($data, $mer_id)
    {
        $array = [
            'mer_id' => $mer_id,
            'lecturer_name' => $data['merchant_name'],
            'lecturer_head' => $data['merchant_head'],
            'phone' => $data['link_tel'],
            'label' => $data['label'],
            'introduction' => $data['introduction'],
            'explain' => $data['explain'],
            'add_time' => time()
        ];
        $res = self::set($array);
        return $res;
    }
}
