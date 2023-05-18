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

namespace app\merchant\model\store;

use think\Db;
use traits\ModelTrait;
use basic\ModelBasic;
use app\merchant\model\system\Relation;
use app\merchant\model\special\SpecialBuy;
use app\merchant\model\special\Special;
use app\merchant\model\special\SpecialSource;
use app\merchant\model\order\StoreOrder;
use app\admin\model\system\SystemConfig;
use service\PhpSpreadsheetService;
use app\merchant\model\download\DataDownloadBuy;

/**
 * 产品管理 model
 * Class StoreProduct
 * @package app\merchant\model\store
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
                $data = ['p.is_show' => 1, 'p.is_del' => 0, 'p.status' => 1];
                break;
            case 2:
                $data = ['p.is_show' => 0, 'p.is_del' => 0, 'p.status' => 1];
                break;
            case 3:
                $data = ['p.is_del' => 0, 'p.status' => 1];
                break;
            case 4:
                $data = ['p.is_show' => 1, 'p.is_del' => 0, 'p.stock' => 0, 'p.status' => 1];
                break;
            case 5:
                $data = ['p.is_show' => 1, 'p.is_del' => 0, 'p.status' => 1, 'p.stock' => ['elt', $store_stock]];
                break;
            case 6:
                $data = ['p.is_del' => 1, 'p.status' => 1];
                break;
            case 7:
                $data = ['p.is_del' => 0];
                break;
        };
        return isset($data) ? $data : [];
    }

    public static function PreWhere($alert = '')
    {
        $alert = $alert ? $alert . '.' : '';
        return self::where([$alert . 'is_show' => 1, $alert . 'is_del' => 0]);
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
        $model = $model->group('p.id');
        if (isset($where['type']) && $where['type'] != '' && ($data = self::setData($where['type']))) {
            $model = $model->where($data);
        }
        if (isset($where['type']) && $where['type'] == 7) {
            if (isset($where['status']) && $where['status'] != '') {
                $model = $model->where('status', $where['status']);
            } else {
                $model = $model->where('status', 'in', [1, -1, 0]);
            }
        }
        if (isset($where['store_name']) && $where['store_name'] != '') {
            $model = $model->where('p.store_name|p.keyword|p.id', 'LIKE', "%$where[store_name]%");
        }
        if (isset($where['cate_id']) && trim($where['cate_id']) != 0) {
            $model = $model->where('p.cate_id', $where['cate_id']);
        }
        if (isset($where['mer_id']) && $where['mer_id']) {
            $model = $model->where('p.mer_id', $where['mer_id']);
        }
        if (isset($where['order']) && $where['order'] != '') {
            $model = $model->order(self::setOrder($where['order']));
        } else {
            $model = $model->order('p.sort DESC,p.add_time DESC');
        }
        $model = $model->join('StoreCategory c', 'p.cate_id=c.id', 'left');
        return $model;
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
     * 获取商品
     */
    public static function storeProductList($where, $special_source)
    {
        $where['store_name'] = $where['title'];
        $model = self::getModelObject($where)->where('p.id', 'not in', $special_source)->field(['p.*']);
        $model = $model->page((int)$where['page'], (int)$where['limit']);
        $data = ($data = $model->select()) && count($data) ? $data->toArray() : [];
        $count = self::getModelObject($where)->where('p.id', 'not in', $special_source)->count();
        return compact('count', 'data');
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
        if (isset($where['mer_id']) && $where['mer_id']) {
            $model = $model->where('p.mer_id', $where['mer_id']);
        }
        if (isset($where['order']) && $where['order'] != '') {
            $model = $model->order(self::setOrder($where['order']));
        } else {
            $model = $model->order('p.sort DESC,p.add_time DESC');
        }
        $model = $model->join('StoreCategory c', 'p.cate_id=c.id', 'left');
        $model = $model->where('p.status', 'in', [1, -1, 0])->where('is_del',0);
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
        foreach ($data as $key => &$value) {
            $value['fail_time'] = date('Y-m-d H:i:s', $value['fail_time']);
        }
        $count = self::getModelExamine($where)->count();
        return compact('count', 'data');
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
                $data = ['is_show' => 1, 'is_del' => 0, 'status' => 1];
                break;
            case 2:
                $data = ['is_show' => 0, 'is_del' => 0, 'status' => 1];
                break;
            case 3:
                $data = ['is_del' => 0, 'status' => 1];
                break;
            case 4:
                $data = ['is_show' => 1, 'is_del' => 0, 'stock' => 0, 'status' => 1];
                break;
            case 5:
                $data = ['is_show' => 1, 'is_del' => 0, 'status' => 1, 'stock' => ['elt', 1]];
                break;
            case 6:
                $data = ['is_del' => 1, 'status' => 1];
                break;
        }
        if (isset($data)) $model = $model->where($data);
        return $model;
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
}
