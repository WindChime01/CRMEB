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

namespace app\merchant\model\questions;

use traits\ModelTrait;
use basic\ModelBasic;
use app\merchant\model\questions\QuestionsCategpry as QuestionsCategpryModel;
use service\PhpSpreadsheetService;
use service\SystemConfigService;
use \PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * 试题 Model
 * Class Questions
 * @package app\merchant\model\questions
 */
class Questions extends ModelBasic
{
    use ModelTrait;

    /**条件处理
     * @param $where
     * @param array $arrays
     * @return Questions
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function setWhere($where, $arrays = [])
    {
        $model = self::order('sort desc,add_time desc')->field('id,sort,add_time,is_del,mer_id,question_type,stem,pid,difficulty,is_img')->where('is_del', 0);
        if (isset($where['type']) && $where['type']) $model = $model->where('question_type', $where['type']);
        if (isset($where['pid']) && $where['pid'] > 0) {
            $cate = QuestionsCategpryModel::where('id', $where['pid'])->find();
            if ($cate['pid'] > 0) {
                $model = $model->where('pid', $where['pid']);
            } else {
                $pids = QuestionsCategpryModel::categoryId($where['pid']);
                $model = $model->where('pid', 'in', $pids);
            }
        }
        if (isset($where['mer_id']) && $where['mer_id']) $model = $model->where('mer_id', $where['mer_id']);
        if ($arrays) $model = $model->where('id', 'not in', $arrays);
        if (isset($where['title']) && $where['title'] != '') $model = $model->where('stem', 'like', "%$where[title]%");
        return $model;
    }

    /**试题列表
     * @param $where
     * @param array $arrays
     */
    public static function questionsList($where, $arrays = [])
    {
        $model = self::setWhere($where, $arrays);
        if (isset($where['excel']) && $where['excel'] == 1) {
            $data = ($data = $model->select()) && count($data) ? $data->toArray() : [];
        } else {
            $data = ($data = $model->page((int)$where['page'], (int)$where['limit'])->select()) && count($data) ? $data->toArray() : [];
        }
        foreach ($data as $key => &$value) {
            switch ($value['question_type']) {
                case 1:
                    $value['types'] = '单选题';
                    break;
                case 2:
                    $value['types'] = '多选题';
                    break;
                case 3:
                    $value['types'] = '判断题';
                    break;
            }
            $value['cate'] = QuestionsCategpryModel::where(['id' => $value['pid'], 'mer_id' => $where['mer_id'], 'is_del' => 0])->value('title');
        }
        if (isset($where['excel']) && $where['excel'] == 1) {
            self::SaveExcel($data);
        }
        $count = self::setWhere($where, $arrays)->count();
        return compact('data', 'count');
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
            $export[] = [
                $item['id'],
                $item['cate'],
                $item['stem'],
                $item['image'],
                $item['types'],
                $item['option'],
                $item['answer'],
                $item['difficulty'],
                $item['analysis'],
                $item['number'],
                $item['add_time'] > 0 ? date('Y/m/d H:i', $item['add_time']) : '无'
            ];
        }
        $filename = '试题列表' . time() . '.xlsx';
        $head = ['编号', '分类', '题干', '配图', '提型', '选项', '答案', '难度', '答案解析', '答题人数', '添加时间'];
        PhpSpreadsheetService::outdata($filename, $export, $head);
    }


    /**
     * 下载试题导入文件
     */
    public static function getExcel()
    {
        $filename = 'template.xlsx'; //获取文件名称
        $site_url = SystemConfigService::get('site_url');//网站地址
        if (!$site_url) {
            $site_url = 'http';
            if ($_SERVER["HTTPS"] == "on") {
                $site_url .= 's';
            }
            $site_url = $site_url . '://' . $_SERVER['HTTP_HOST'] . '/'; //当前域名
        } else {
            $site_url .= '/';
        }
        //判断如果文件存在,则跳转到下载路径
        if (file_exists(ROOT_PATH . 'public' . DS . $filename)) {
            header('location:' . $site_url . $filename);
            exit;
        } else {
            header('HTTP/1.1 404 Not Found');
        }
    }

    /**文件导入
     * @param string $filename
     * @param int $startLine
     * @param array $width
     * @return array
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public static function GetExcelData($filename = '1.xlsx', $startLine = 4)
    {
        $width = [
            'question_type' => 'A',
            'pid' => 'B',
            'stem' => 'C',
            'image' => 'D',
            'is_img' => 'E',
            'a' => 'F',
            'b' => 'G',
            'c' => 'H',
            'd' => 'I',
            'e' => 'J',
            'f' => 'K',
            'answer' => 'L',
            'difficulty' => 'M',
            'analysis' => 'N',
            'sort' => 'O'
        ];
        $filename = ROOT_PATH . 'public' . $filename;
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        switch ($extension) {
            case 'xlsx':
                $reader = IOFactory::createReader('Xlsx');
                $spreadsheet = $reader->load($filename);
                break;
            case 'xls':
                $reader = IOFactory::createReader('Xls');
                $spreadsheet = $reader->load($filename);
                break;
            case 'csv':
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
                $reader->setInputEncoding('GBK');
                $reader->setDelimiter(',');
                $reader->setEnclosure('');
                $reader->setSheetIndex(0);
                $spreadsheet = $reader->load($filename);
                break;
        }
        $highestRow = $spreadsheet->getSheet(0)->getHighestRow(); // 取得总行数
        $getvalue = $spreadsheet->getActiveSheet();
        $data = [];
        for ($j = $startLine; $j <= (int)$highestRow; $j++) {
            $value = [];
            foreach ($width as $key => $val) {
                if ($v = $getvalue->getCell($val . $j)->getValue()) $value[$key] = $v;
                else $value[$key] = '';
            }
            if ($value) $data[] = $value;
        }
        return $data;
    }

    /**返回分类下试题的ID
     * @param $question_type
     * @param array $cateIds
     * @return array
     */
    public static function getCateIds($question_type, $cateIds = [])
    {
        $question = self::where(['question_type' => $question_type, 'is_del' => 0])->where('pid', 'in', $cateIds)->field('id,question_type')->select();
        if ($question) return $question->toArray();
        else return $question;
    }

    /**批量导入试题
     * @param array $data
     */
    public static function importQuestions($data = [], $mer_id)
    {
        foreach ($data as $key => $value) {
            $dat = [];
            switch ($value['question_type']) {
                case 1:
                    if ($value['a']) $dat['A'] = $value['a'];
                    if ($value['b']) $dat['B'] = $value['b'];
                    if ($value['c']) $dat['C'] = $value['c'];
                    if ($value['d']) $dat['D'] = $value['d'];
                case 2:
                    if ($value['a']) $dat['A'] = $value['a'];
                    if ($value['b']) $dat['B'] = $value['b'];
                    if ($value['c']) $dat['C'] = $value['c'];
                    if ($value['d']) $dat['D'] = $value['d'];
                    if ($value['e']) $dat['E'] = $value['e'];
                    if ($value['f']) $dat['F'] = $value['f'];
                    break;
                case 3:
                    if ($value['a']) $dat['A'] = $value['a'];
                    if ($value['b']) $dat['B'] = $value['b'];
                    break;
            }
            $array['question_type'] = $value['question_type'];
            $array['pid'] = $value['pid'];
            $array['stem'] = $value['stem'];
            $array['image'] = $value['image'];
            $array['is_img'] = $value['is_img'];
            $array['answer'] = trim($value['answer'], " ");
            $array['difficulty'] = $value['difficulty'];
            $array['analysis'] = $value['analysis'];
            $array['sort'] = (int)$value['sort'];
            $array['option'] = json_encode($dat);
            $array['add_time'] = time();
            $array['mer_id'] = $mer_id;
            if (self::be(['stem' => $value['stem'], 'mer_id' => $mer_id, 'is_del' => 0])) continue;
            self::set($array);
        }
        return true;
    }
}
