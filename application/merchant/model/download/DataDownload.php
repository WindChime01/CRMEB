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

namespace app\merchant\model\download;

use think\Db;
use traits\ModelTrait;
use basic\ModelBasic;
use app\merchant\model\download\DataDownloadCategpry;

/**资料 model
 * Class DataDownload
 * @package app\merchant\model\download
 */
class DataDownload extends ModelBasic
{
    use ModelTrait;

    /**字段过滤
     * @param string $alias
     * @param null $model
     * @return DataDownload
     */
    public static function PreWhere($alias = '', $model = null)
    {
        if (is_null($model)) $model = new self();
        if ($alias) {
            $model = $model->alias($alias);
            $alias .= '.';
        }
        return $model->where([$alias . 'is_show' => 1, $alias . 'is_del' => 0, $alias . 'status' => 1]);
    }

    /**条件处理
     * @param $where
     * @return DataDownload
     */
    public static function setWhere($where)
    {
        $model = new self();
        $model = $model->alias('d');
        $time['data'] = '';
        if (isset($where['start_time']) && isset($where['end_time']) && $where['start_time'] != '' && $where['end_time'] != '') {
            $time['data'] = $where['start_time'] . ' - ' . $where['end_time'];
            $model = $model->getModelTime($time, $model, 'd.add_time');
        }
        if (isset($where['title']) && $where['title']) {
            $model = $model->where('d.title|d.id|d.abstract', 'like', "%$where[title]%");
        }
        if (isset($where['cate_id']) && $where['cate_id']) {
            $model = $model->where('d.cate_id', $where['cate_id']);
        }
        if (isset($where['is_show']) && $where['is_show'] != '') {
            $model = $model->where('d.is_show', $where['is_show']);
        }
        if (isset($where['mer_id']) && $where['mer_id'] != '') {
            $model = $model->where('d.mer_id', $where['mer_id']);
        }
        if (isset($where['status']) && $where['status'] != '') {
            $model = $model->where('d.status', $where['status']);
        } else {
            $model = $model->where('d.status', 'in', [1, -1, 0]);
        }
        $model = $model->join('DataDownloadCategpry c', 'd.cate_id=c.id','left');
        return $model->where(['d.is_del' => 0]);
    }

    /**获取列表
     * @param $where
     * @return array
     * @throws \think\Exception
     */
    public static function get_download_list($where)
    {
        $data = self::setWhere($where)->order('d.sort DESC,d.id DESC')->field('d.*,c.title as cate_name')
            ->page((int)$where['page'], (int)$where['limit'])
            ->select();
        foreach ($data as $key => &$item) {
            $item['add_time'] = ($item['add_time'] != 0 || $item['add_time']) ? date('Y-m-d H:i:s', $item['add_time']) : '';
        }
        $data = count((array)$data) ? $data->toArray() : [];
        $count = self::setWhere($where)->count();
        return compact('data', 'count');
    }

    /**获取列表
     * @param $where
     * @return array
     * @throws \think\Exception
     */
    public static function get_download_examine_list($where)
    {
        $data = self::setWhere($where)->order('sort DESC,id DESC')
            ->page((int)$where['page'], (int)$where['limit'])
            ->select()
            ->each(function ($item) {
                $item['fail_time'] = date('Y-m-d H:i:s', $item['fail_time']);
                $item['add_time'] = ($item['add_time'] != 0 || $item['add_time']) ? date('Y-m-d H:i:s', $item['add_time']) : '';
                $item['cate_name'] = DataDownloadCategpry::where('id', $item['cate_id'])->value('title');
            });
        $data = count((array)$data) ? $data->toArray() : [];
        $count = self::setWhere($where)->count();
        return compact('data', 'count');
    }

    /**获取资料
     * @param $where
     */
    public static function dataDownloadLists($where, $source)
    {
        $data = self::setWhere($where)->where('d.id', 'not in', $source)->field('d.*,c.title as cate_name')->page($where['page'], $where['limit'])->select();
        $count = self::setWhere($where)->where('d.id', 'not in', $source)->count();
        return compact('data', 'count');
    }

}
