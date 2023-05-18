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


namespace app\admin\model\system;

use traits\ModelTrait;
use basic\ModelBasic;

/**
 * 文件检验model
 * Class SystemAttachment
 * @package app\admin\model\system
 */
class SystemAttachment extends ModelBasic
{
    use ModelTrait;

    /**添加附件记录
     */
    public static function attachmentAdd($name, $title, $att_size, $att_type, $att_dir, $satt_dir = '', $pid = 0)
    {
        $data['name'] = $name;
        $data['title'] = $title == '' ? $name : $title;
        $data['att_dir'] = $att_dir;
        $data['satt_dir'] = $satt_dir;
        $data['att_size'] = $att_size;
        $data['att_type'] = $att_type;
        $data['time'] = time();
        $data['pid'] = $pid;
        return self::create($data);
    }

    /**编辑修改图片名称
     * @param $data
     * @return SystemAttachment|bool
     */
    public static function attachmentTitle($data)
    {
        if (self::be(['att_id' => $data['att_id']])) {
            return self::where(['att_id' => $data['att_id']])->update(['title' => $data['title']]);
        } else {
            return false;
        }
    }

    public static function setWhere($where)
    {
        $model = self::order('att_id desc,time desc');
        if (isset($where['pid']) && $where['pid']) $model = $model->where('pid', $where['pid']);
        if ($where['title'] != '') $model = $model->where('title|name', 'like', "%$where[title]%");
        return $model;
    }

    /** 获取图片列表
     * @param $where
     * @return array
     */
    public static function getImageList($where)
    {
        $list = self::setWhere($where)->page((int)$where['page'], (int)$where['limit'])->select();
        $list = count($list) ? $list->toArray() : [];
        $site_url = SystemConfig::getValue('site_url');
        foreach ($list as &$item) {
            if ($site_url) {
                $item['satt_dir'] = (strpos($item['satt_dir'], $site_url) !== false || strstr($item['satt_dir'], 'http') !== false) ? $item['satt_dir'] : $site_url . $item['satt_dir'];
                $item['att_dir'] = (strpos($item['att_dir'], $site_url) !== false || strstr($item['att_dir'], 'http') !== false) ? $item['satt_dir'] : $site_url . $item['att_dir'];
            }
        }
        $count = self::setWhere($where)->count();
        return compact('list', 'count');
    }

    /**
     * 获取分类图
     * */
    public static function getAll($id)
    {
        $model = new self;
        $where['pid'] = $id;
        $model->where($where)->order('att_id desc');
        return $model->page($model, $where, '', 30);
    }

    /**
     * 获取单条信息
     * */
    public static function getinfo($att_id)
    {
        $model = new self;
        $where['att_id'] = $att_id;
        return $model->where($where)->select()->toArray()[0];
    }

}
