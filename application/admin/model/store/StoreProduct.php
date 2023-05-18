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

namespace app\admin\model\store;

use app\admin\model\system\RecommendRelation;
use app\admin\model\wechat\WechatUser;
use think\Db;
use traits\ModelTrait;
use basic\ModelBasic;
use app\admin\model\store\StoreCategory as CategoryModel;
use service\SystemConfigService;
use app\admin\model\questions\Relation;
use app\admin\model\special\SpecialBuy;
use app\admin\model\special\Special;
use app\admin\model\special\SpecialSource;
use app\admin\model\order\StoreOrder;
use app\admin\model\system\SystemConfig;
use service\PhpSpreadsheetService;
use app\admin\model\questions\TestPaperObtain;
use app\admin\model\download\DataDownloadBuy;
use app\admin\model\merchant\Merchant;
use service\WechatTemplateService;
use app\wap\model\routine\RoutineTemplate;

/**
 * 产品管理 model
 * Class StoreProduct
 * @package app\admin\model\store
 */
class StoreProduct extends ModelBasic
{
    use ModelTrait;

    /**
     * 获取连表查询条件
     * @param $type
     * @return array
     */
    public static function setData($type)
    {
        $store_stock = SystemConfig::getValue('store_stock');
        switch ((int)$type) {
            case 1:
                $data = ['p.is_show' => 1, 'p.is_del' => 0];
                break;
            case 2:
                $data = ['p.is_show' => 0, 'p.is_del' => 0];
                break;
            case 3:
                $data = ['p.is_del' => 0];
                break;
            case 4:
                $data = ['p.is_show' => 1, 'p.is_del' => 0, 'p.stock' => 0];
                break;
            case 5:
                $data = ['p.is_show' => 1, 'p.is_del' => 0, 'p.stock' => ['elt', $store_stock]];
                break;
            case 6:
                $data = ['p.is_del' => 1];
                break;
        };
        return isset($data) ? $data : [];
    }

    public static function PreWhere($alert = '')
    {
        $alert = $alert ? $alert . '.' : '';
        return self::where([$alert . 'is_show' => 1, $alert . 'status' => 1, $alert . 'is_del' => 0]);
    }

    /**
     * 获取连表MOdel
     * @param $model
     * @return object
     */
    public static function getModelObject($where = [])
    {
        $model = new self();
        $model = $model->alias('p');
        if (!empty($where)) {
            $model = $model->group('p.id');
            if (isset($where['type']) && $where['type'] != '' && ($data = self::setData($where['type']))) {
                $model = $model->where($data);
            }
            if (isset($where['store_name']) && $where['store_name'] != '') {
                $model = $model->where('p.store_name|p.keyword|p.id', 'LIKE', "%$where[store_name]%");
            }
            if (isset($where['cate_id']) && trim($where['cate_id']) != 0) {
                $model = $model->where('p.cate_id', $where['cate_id']);
            }
            if (isset($where['mer_id']) && trim($where['mer_id']) != '') {
                $model = $model->where('p.mer_id', $where['mer_id']);
            }
            if (isset($where['order']) && $where['order'] != '') {
                $model = $model->order(self::setOrder($where['order']));
            } else {
                $model = $model->order('p.sort DESC,p.add_time DESC');
            }
        }
        $model = $model->join('StoreCategory c', 'p.cate_id=c.id', 'left');
        return $model->where('p.status', 1);
    }

    /**
     * 获取商品
     */
    public static function storeProductList($where, $special_source)
    {
        $where['store_name'] = $where['title'];
        $model = self::getModelObject($where)->where('p.id', 'not in', $special_source)->field(['p.*']);
        $model = $model->page((int)$where['page'], (int)$where['limit']);
        $data = ($data = $model->select()) && count($data) ? $data->toArray() : [];
        foreach ($data as $key => &$value) {
            if ($value['mer_id']) {
                $value['mer_name'] = Merchant::where('id', $value['mer_id'])->value('mer_name');
            } else {
                $value['mer_name'] = '总平台';
            }
        }
        $count = self::getModelObject($where)->where('p.id', 'not in', $special_source)->count();
        return compact('count', 'data');
    }

    /*
     * 获取产品列表
     * @param $where array
     * @return array
     *
     */
    public static function ProductList($where)
    {
        $model = self::getModelObject($where)->field('p.*,c.cate_name');
        if ($where['excel'] == 0) $model = $model->page((int)$where['page'], (int)$where['limit']);
        $data = ($data = $model->select()) && count($data) ? $data->toArray() : [];
        foreach ($data as &$item) {
            $item['recommend'] = RecommendRelation::where('a.link_id', $item['id'])->where('a.type', 4)->alias('a')
                ->join('__RECOMMEND__ r', 'a.recommend_id=r.id')->column('a.id,r.title');
            if ($item['mer_id']) {
                $item['mer_name'] = Merchant::where('id', $item['mer_id'])->value('mer_name');
            } else {
                $item['mer_name'] = '总平台';
            }
        }
        if ($where['excel'] == 1) {
            self::SaveExcel($data);
        }
        $count = self::getModelObject($where)->count();
        return compact('count', 'data');
    }

    public static function SaveExcel($data)
    {
        $export = [];
        foreach ($data as $index => $item) {
            $export[] = [
                $item['store_name'],
                $item['store_info'],
                $item['cate_name'],
                '￥' . $item['price'],
                $item['stock'],
                $item['sales'],
            ];
        }
        $filename = '产品导出' . time() . '.xlsx';
        $head = ['产品名称', '产品简介', '产品分类', '价格', '库存', '销量'];
        PhpSpreadsheetService::outdata($filename, $export, $head);
    }

    /**
     * 获取连表MOdel
     * @param $model
     * @return object
     */
    public static function getModelExamine($where = [])
    {
        $model = new self();
        $model = $model->alias('p');
        $model = $model->group('p.id');
        if (isset($where['store_name']) && $where['store_name'] != '') {
            $model = $model->where('p.store_name|p.keyword|p.id', 'LIKE', "%$where[store_name]%");
        }
        if (isset($where['cate_id']) && trim($where['cate_id']) != 0) {
            $model = $model->where('p.cate_id', $where['cate_id']);
        }
        if (isset($where['mer_id']) && trim($where['mer_id']) != '') {
            $model = $model->where('p.mer_id', $where['mer_id']);
        }
        if (isset($where['order']) && $where['order'] != '') {
            $model = $model->order(self::setOrder($where['order']));
        } else {
            $model = $model->order('p.sort DESC,p.add_time DESC');
        }
        $model = $model->join('StoreCategory c', 'p.cate_id=c.id', 'left');
        if (isset($where['status']) && $where['status'] != '') {
            $model = $model->where('status', $where['status']);
        } else {
            $model = $model->where('status', 'in', [-1, 0]);
        }
        return $model;
    }

    /*
     * 获取产品列表
     * @param $where array
     * @return array
     *
     */
    public static function productExamineList($where)
    {
        $model = self::getModelExamine($where)->field('p.*,c.cate_name');
        $model = $model->page((int)$where['page'], (int)$where['limit']);
        $data = ($data = $model->select()) && count($data) ? $data->toArray() : [];
        foreach ($data as &$item) {
            $item['fail_time'] = date('Y-m-d H:i:s', $item['fail_time']);
            if ($item['mer_id']) {
                $item['mer_name'] = Merchant::where('id', $item['mer_id'])->value('mer_name');
            } else {
                $item['mer_name'] = '总平台';
            }
        }
        $count = self::getModelExamine($where)->count();
        return compact('count', 'data');
    }

    /**审核失败
     * @param $id
     * @param $fail_msg
     * @return bool
     * @throws \think\exception\DbException
     */
    public static function changeFail($id, $mer_id, $fail_message)
    {
        $fail_time = time();
        $status = -1;
        $uid = Merchant::where('id', $mer_id)->value('uid');
        try {
            $wechat_notification_message = SystemConfigService::get('wechat_notification_message');
            if ($wechat_notification_message == 1) {
                WechatTemplateService::sendTemplate(WechatUser::uidToOpenid($uid), WechatTemplateService::EXAMINE_RESULT, [
                    'first' => '尊敬的讲师，您添加的商品审核结果已出。',
                    'keyword1' => '审核失败',
                    'keyword2' => $fail_message,
                    'keyword3' => date('Y-m-d H:i:s', time()),
                    'remark' => '感恩您的努力付出，谢谢！'
                ], '');
            } else {
                $dat['phrase5']['value'] = '商品审核失败';
                $dat['time24']['value'] = date('Y-m-d H:i:s', time());
                $dat['thing4']['value'] = '商品失败原因:' . $fail_message;
                RoutineTemplate::sendExamineResult($dat, $uid, '');
            }
        } catch (\Exception $e) {
        }
        return self::edit(compact('fail_time', 'fail_message', 'status'), $id);
    }

    /**审核成功
     * @param $id
     * @return bool
     */
    public static function changeSuccess($id, $mer_id)
    {
        $success_time = time();
        $status = 1;
        $uid = Merchant::where('id', $mer_id)->value('uid');
        try {
            $wechat_notification_message = SystemConfigService::get('wechat_notification_message');
            if ($wechat_notification_message == 1) {
                WechatTemplateService::sendTemplate(WechatUser::uidToOpenid($uid), WechatTemplateService::EXAMINE_RESULT, [
                    'first' => '尊敬的讲师，您添加的商品审核结果已出。',
                    'keyword1' => '审核成功',
                    'keyword2' => '商品信息符合标准',
                    'keyword3' => date('Y-m-d H:i:s', time()),
                    'remark' => '感恩您的努力付出，谢谢！'
                ], '');
            } else {
                $dat['phrase5']['value'] = '商品审核成功';
                $dat['time24']['value'] = date('Y-m-d H:i:s', time());
                $dat['thing4']['value'] = '您添加的商品审核结果已出！';
                RoutineTemplate::sendExamineResult($dat, $uid, '');
            }
        } catch (\Exception $e) {
        }
        return self::edit(compact('status', 'success_time'), $id);
    }

    public static function getChatrdata($type, $data)
    {
        $legdata = ['销量', '数量', '点赞', '收藏'];
        $model = self::setWhereType(self::order('id desc'), $type);
        $list = self::getModelTime(compact('data'), $model)
            ->field('FROM_UNIXTIME(add_time,"%Y-%c-%d") as un_time,count(id) as count,sum(sales) as sales')
            ->group('un_time')
            ->distinct(true)
            ->select()
            ->each(function ($item) use ($data) {
                $item['collect'] = self::getModelTime(compact('data'), new StoreProductRelation)->where(['type' => 'collect'])->count();
                $item['like'] = self::getModelTime(compact('data'), new StoreProductRelation)->where(['type' => 'like'])->count();
            })->toArray();
        $chatrList = [];
        $datetime = [];
        $data_item = [];
        $itemList = [0 => [], 1 => [], 2 => [], 3 => []];
        foreach ($list as $item) {
            $itemList[0][] = $item['sales'];
            $itemList[1][] = $item['count'];
            $itemList[2][] = $item['like'];
            $itemList[3][] = $item['collect'];
            array_push($datetime, $item['un_time']);
        }
        foreach ($legdata as $key => $leg) {
            $data_item['name'] = $leg;
            $data_item['type'] = 'line';
            $data_item['data'] = $itemList[$key];
            $chatrList[] = $data_item;
            unset($data_item);
        }
        unset($leg);
        $badge = self::getbadge(compact('data'), $type);
        $count = self::setWhereType(self::getModelTime(compact('data'), new self()), $type)->count();
        return compact('datetime', 'chatrList', 'legdata', 'badge', 'count');

    }

    //获取 badge 内容
    public static function getbadge($where, $type)
    {
        $StoreOrderModel = new StoreOrder;
        $replenishment_num = SystemConfig::getValue('replenishment_num');
        $replenishment_num = $replenishment_num > 0 ? $replenishment_num : 20;
        $stock1 = self::getModelTime($where, new self())->where('stock', '<', $replenishment_num)->column('stock');
        $sum_stock = self::where('stock', '<', $replenishment_num)->column('stock');
        $stk = [];
        foreach ($stock1 as $item) {
            $stk[] = $replenishment_num - $item;
        }
        $lack = array_sum($stk);
        $sum = [];
        foreach ($sum_stock as $val) {
            $sum[] = $replenishment_num - $val;
        }
        return [
            [
                'name' => '商品数量',
                'field' => '件',
                'count' => self::setWhereType(new self(), $type)->where('add_time', '<', mktime(0, 0, 0, date('m'), date('d'), date('Y')))->sum('stock'),
                'content' => '商品数量总数',
                'background_color' => 'layui-bg-blue',
                'sum' => self::sum('stock'),
                'class' => 'fa fa fa-ioxhost',
            ],
            [
                'name' => '新增商品',
                'field' => '件',
                'count' => self::setWhereType(self::getModelTime($where, new self), $type)->where('is_new', 1)->sum('stock'),
                'content' => '新增商品总数',
                'background_color' => 'layui-bg-cyan',
                'sum' => self::where('is_new', 1)->sum('stock'),
                'class' => 'fa fa-line-chart',
            ],
            [
                'name' => '活动商品',
                'field' => '件',
                'count' => self::getModelTime($where, $StoreOrderModel)->sum('total_num'),
                'content' => '活动商品总数',
                'background_color' => 'layui-bg-green',
                'sum' => $StoreOrderModel->sum('total_num'),
                'class' => 'fa fa-bar-chart',
            ],
            [
                'name' => '缺货商品',
                'field' => '件',
                'count' => $lack,
                'content' => '总商品数量',
                'background_color' => 'layui-bg-orange',
                'sum' => array_sum($sum),
                'class' => 'fa fa-cube',
            ],
        ];
    }

    public static function setWhereType($model, $type)
    {
        switch ($type) {
            case 1:
                $data = ['is_show' => 1, 'is_del' => 0];
                break;
            case 2:
                $data = ['is_show' => 0, 'is_del' => 0];
                break;
            case 3:
                $data = ['is_del' => 0];
                break;
            case 4:
                $data = ['is_show' => 1, 'is_del' => 0, 'stock' => 0];
                break;
            case 5:
                $data = ['is_show' => 1, 'is_del' => 0, 'stock' => ['elt', 1]];
                break;
            case 6:
                $data = ['is_del' => 1];
                break;
        }
        if (isset($data)) $model = $model->where($data);
        return $model;
    }

    /*
     * layui-bg-red 红 layui-bg-orange 黄 layui-bg-green 绿 layui-bg-blue 蓝 layui-bg-cyan 黑
     * 销量排行 top 10
     */
    public static function getMaxList($where)
    {
        $classs = ['layui-bg-red', 'layui-bg-orange', 'layui-bg-green', 'layui-bg-blue', 'layui-bg-cyan'];
        $model = StoreOrder::alias('a')->join('StoreOrderCartInfo c', 'a.id=c.oid')->join('store_product b', 'b.id=c.product_id');
        $list = self::getModelTime($where, $model, 'a.add_time')->group('c.product_id')->order('p_count desc')->limit(10)
            ->field(['count(c.product_id) as p_count', 'b.store_name', 'sum(b.price) as sum_price'])->select();
        if (count($list)) $list = $list->toArray();
        $maxList = [];
        $sum_count = 0;
        $sum_price = 0;
        foreach ($list as $item) {
            $sum_count += $item['p_count'];
            $sum_price = bcadd($sum_price, $item['sum_price'], 2);
        }
        unset($item);
        foreach ($list as $key => &$item) {
            $item['w'] = bcdiv($item['p_count'], $sum_count, 2) * 100;
            $item['class'] = isset($classs[$key]) ? $classs[$key] : (isset($classs[$key - count($classs)]) ? $classs[$key - count($classs)] : '');
            $item['store_name'] = self::getSubstrUTf8($item['store_name']);
        }
        $maxList['sum_count'] = $sum_count;
        $maxList['sum_price'] = $sum_price;
        $maxList['list'] = $list;
        return $maxList;
    }

    //获取利润
    public static function ProfityTop10($where)
    {
        $classs = ['layui-bg-red', 'layui-bg-orange', 'layui-bg-green', 'layui-bg-blue', 'layui-bg-cyan'];
        $model = StoreOrder::alias('a')->join('StoreOrderCartInfo c', 'a.id=c.oid')->join('store_product b', 'b.id=c.product_id');
        $list = self::getModelTime($where, $model, 'a.add_time')->group('c.product_id')->order('profity desc')->limit(10)
            ->field(['count(c.product_id) as p_count', 'b.store_name', 'sum(b.price) as sum_price', '(b.price-b.cost) as profity'])
            ->select();
        if (count($list)) $list = $list->toArray();
        $maxList = [];
        $sum_count = 0;
        $sum_price = 0;
        foreach ($list as $item) {
            $sum_count += $item['p_count'];
            $sum_price = bcadd($sum_price, $item['sum_price'], 2);
        }
        foreach ($list as $key => &$item) {
            $item['w'] = bcdiv($item['sum_price'], $sum_price, 2) * 100;
            $item['class'] = isset($classs[$key]) ? $classs[$key] : (isset($classs[$key - count($classs)]) ? $classs[$key - count($classs)] : '');
            $item['store_name'] = self::getSubstrUTf8($item['store_name'], 30);
        }
        $maxList['sum_count'] = $sum_count;
        $maxList['sum_price'] = $sum_price;
        $maxList['list'] = $list;
        return $maxList;
    }

    //获取缺货
    public static function getLackList($where)
    {
        $replenishment_num = SystemConfig::getValue('replenishment_num');
        $replenishment_num = $replenishment_num > 0 ? $replenishment_num : 20;
        $list = self::where('stock', '<', $replenishment_num)->field(['id', 'store_name', 'stock', 'price'])->page((int)$where['page'], (int)$where['limit'])->order('stock asc')->select();
        if (count($list)) $list = $list->toArray();
        $count = self::where('stock', '<', $replenishment_num)->count();
        return ['count' => $count, 'data' => $list];
    }

    //获取差评
    public static function getnegativelist($where)
    {
        $list = self::alias('s')->join('StoreProductReply r', 's.id=r.product_id')
            ->field('s.id,s.store_name,s.price,count(r.product_id) as count')
            ->page((int)$where['page'], (int)$where['limit'])
            ->where('r.product_score', 1)
            ->order('count desc')
            ->group('r.product_id')
            ->select();
        if (count($list)) $list = $list->toArray();
        $count = self::alias('s')->join('StoreProductReply r', 's.id=r.product_id')->group('r.product_id')->where('r.product_score', 1)->count();
        return ['count' => $count, 'data' => $list];
    }

    public static function TuiProductList()
    {
        $perd = StoreOrder::alias('s')->join('StoreOrderCartInfo c', 's.id=c.oid')
            ->field('count(c.product_id) as count,c.product_id as id')
            ->group('c.product_id')
            ->where('s.status', -1)
            ->order('count desc')
            ->limit(10)
            ->select();
        if (count($perd)) $perd = $perd->toArray();
        foreach ($perd as &$item) {
            $item['store_name'] = self::where(['id' => $item['id']])->value('store_name');
            $item['price'] = self::where(['id' => $item['id']])->value('price');
        }
        return $perd;
    }

    //编辑库存
    public static function changeStock($stock, $productId)
    {
        return self::edit(compact('stock'), $productId);
    }

    public static function getTierList($model = null)
    {
        if ($model === null) $model = new self();
        return $model->field('id,store_name')->where('is_del', 0)->select()->toArray();
    }

    /**
     * 设置查询条件
     * @param array $where
     * @return array
     */
    public static function setWhere($where)
    {
        $time['data'] = '';
        if (isset($where['start_time']) && $where['start_time'] != '' && isset($where['end_time']) && $where['end_time'] != '') {
            $time['data'] = $where['start_time'] . ' - ' . $where['end_time'];
        } else {
            $time['data'] = isset($where['data']) ? $where['data'] : '';
        }
        $model = self::getModelTime($time, db('store_cart')->alias('a')->join('store_product b', 'a.product_id=b.id'), 'a.add_time');
        if (isset($where['title']) && $where['title'] != '') {
            $model = $model->where('b.store_name|b.id', 'like', "%$where[title]%");
        }
        return $model;
    }

    /**
     * 获取真实销量排行
     * @param array $where
     * @return array
     */
    public static function getSaleslists($where)
    {
        $data = self::setWhere($where)->where('a.is_pay', 1)
            ->group('a.product_id')
            ->field(['sum(a.cart_num) as num_product', 'b.store_name', 'b.image', 'b.price', 'b.id'])
            ->order('num_product desc')
            ->page((int)$where['page'], (int)$where['limit'])
            ->select();
        $count = self::setWhere($where)->where('a.is_pay', 1)->group('a.product_id')->count();
        foreach ($data as &$item) {
            $item['sum_price'] = bcdiv($item['num_product'], $item['price'], 2);
        }
        return compact('data', 'count');
    }

    /*
     *  单个商品详情的头部查询
     *  $id 商品id
     *  $where 条件
     */
    public static function getProductBadgeList($id, $where)
    {
        $data['data'] = $where;
        $list = self::setWhere($data)
            ->field(['sum(a.cart_num) as num_product', 'b.id', 'b.price'])
            ->where('a.is_pay', 1)
            ->group('a.product_id')
            ->order('num_product desc')
            ->select();
        //排名
        $ranking = 0;
        //销量
        $xiaoliang = 0;
        //销售额 数组
        $list_price = [];
        foreach ($list as $key => $item) {
            if ($item['id'] == $id) {
                $ranking = $key + 1;
                $xiaoliang = $item['num_product'];
            }
            $value['sum_price'] = $item['price'] * $item['num_product'];
            $value['id'] = $item['id'];
            $list_price[] = $value;
        }
        //排序
        $list_price = self::my_sort($list_price, 'sum_price', SORT_DESC);
        //销售额排名
        $rank_price = 0;
        //当前销售额
        $num_price = 0;
        if ($list_price !== false && is_array($list_price)) {
            foreach ($list_price as $key => $item) {
                if ($item['id'] == $id) {
                    $num_price = $item['sum_price'];
                    $rank_price = $key + 1;
                    continue;
                }
            }
        }
        return [
            [
                'name' => '销售额排名',
                'field' => '名',
                'count' => $rank_price,
                'background_color' => 'layui-bg-blue',
            ],
            [
                'name' => '销量排名',
                'field' => '名',
                'count' => $ranking,
                'background_color' => 'layui-bg-blue',
            ],
            [
                'name' => '商品销量',
                'field' => '名',
                'count' => $xiaoliang,
                'background_color' => 'layui-bg-blue',
            ],
            [
                'name' => '点赞次数',
                'field' => '个',
                'count' => db('store_product_relation')->where('product_id', $id)->where('type', 'like')->count(),
                'background_color' => 'layui-bg-blue',
            ],
            [
                'name' => '销售总额',
                'field' => '元',
                'count' => $num_price,
                'background_color' => 'layui-bg-blue',
                'col' => 12,
            ],
        ];
    }

    /*
     * 处理二维数组排序
     * $arrays 需要处理的数组
     * $sort_key 需要处理的key名
     * $sort_order 排序方式
     * $sort_type 类型 可不填写
     */
    public static function my_sort($arrays, $sort_key, $sort_order = SORT_ASC, $sort_type = SORT_NUMERIC)
    {
        if (is_array($arrays)) {
            foreach ($arrays as $array) {
                if (is_array($array)) {
                    $key_arrays[] = $array[$sort_key];
                } else {
                    return false;
                }
            }
        }
        if (isset($key_arrays)) {
            array_multisort($key_arrays, $sort_order, $sort_type, $arrays);
            return $arrays;
        }
        return false;
    }

    /*
     * 查询单个商品的销量曲线图
     *
     */
    public static function getProductCurve($where)
    {
        $list = self::setWhere($where)
            ->where('a.product_id', $where['id'])
            ->where('a.is_pay', 1)
            ->field(['FROM_UNIXTIME(a.add_time,"%Y-%m-%d") as _add_time', 'sum(a.cart_num) as num'])
            ->group('_add_time')
            ->order('_add_time asc')
            ->select();
        $seriesdata = [];
        $date = [];
        $zoom = '';
        foreach ($list as $item) {
            $date[] = $item['_add_time'];
            $seriesdata[] = $item['num'];
        }
        if (count($date) > $where['limit']) $zoom = $date[$where['limit'] - 5];
        return compact('seriesdata', 'date', 'zoom');
    }

    /*
     * 查询单个商品的销售列表
     *
     */
    public static function getSalelList($where)
    {
        return self::setWhere($where)
            ->where(['a.product_id' => $where['id'], 'a.is_pay' => 1])
            ->join('user c', 'c.uid=a.uid')
            ->field(['FROM_UNIXTIME(a.add_time,"%Y-%m-%d") as _add_time', 'c.nickname', 'b.price', 'a.id', 'a.cart_num as num'])
            ->page((int)$where['page'], (int)$where['limit'])
            ->select();
    }

    /**获取关联专题
     * @param int $id
     */
    public static function setAssociatedTopics($order)
    {
        if ($order['type'] != 2) return true;
        $product_id = Db::name('store_order_cart_info')->where('oid', $order['id'])->value('product_id');
        if (!$product_id) return false;
        $special_source = Relation::setWhere(5, $product_id)->column('relation_id');
        foreach ($special_source as $key => $special_id) {
            if (SpecialBuy::be(['uid' => $order['uid'], 'special_id' => $special_id, 'is_del' => 0, 'type' => 5])) continue;
            $special = Special::PreWhere()->where(['id'=>$special_id])->find();
            if (!$special) continue;
            if ($special['type'] == 5) {
                $special_source = SpecialSource::getSpecialSource($special['id']);
                if ($special_source) {
                    foreach ($special_source as $k => $v) {
                        $task_special = Special::PreWhere()->where(['id'=>$v['source_id']])->find();
                        if (!$task_special) continue;
                        if ($task_special['is_show'] == 1) {
                            SpecialBuy::setBuySpecial('', $order['uid'], $v['source_id'], 5, $task_special['validity'], $special_id);
                        }
                    }
                }
            }
            SpecialBuy::setBuySpecial('', $order['uid'], $special_id, 5, $special['validity']);
            TestPaperObtain::setTestPaper('', $order['uid'], $special_id, 3);
            DataDownloadBuy::setDataDownload('', $order['uid'], $special_id, 2);
        }
        return true;
    }

    /**商品退款清除赠送专题
     * @param $oid
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function returnAssociatedTopics($oid)
    {
        $order = StoreOrder::where('id', $oid)->where('type', 2)->find();
        if (!$order) return true;
        $product_id = Db::name('store_order_cart_info')->where('oid', $oid)->value('product_id');
        if (!$product_id) return false;
        $special_source = Relation::setWhere(5, $product_id)->column('relation_id');
        foreach ($special_source as $key => $special_id) {
            $special = Special::PreWhere()->where(['id'=>$special_id])->find();
            if (!$special) continue;
            if ($special['type'] == SPECIAL_COLUMN) {
                $special_source = SpecialSource::getSpecialSource($special['id']);
                if ($special_source) {
                    foreach ($special_source as $k => $v) {
                        SpecialBuy::where(['uid' => $order['uid'], 'special_id' => $v['source_id'], 'type' => 5, 'is_del' => 0, 'column_id' => $special_id])->update(['is_del' => 1]);
                    }
                }
            }
            SpecialBuy::where(['uid' => $order['uid'], 'special_id' => $special_id, 'type' => 5, 'is_del' => 0])->update(['is_del' => 1]);
            TestPaperObtain::delTestPaper('', $order['uid'], $special_id, 3);
            DataDownloadBuy::delDataDownload('', $order['uid'], $special_id, 2);
        }
    }
}
