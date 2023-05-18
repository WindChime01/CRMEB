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

namespace service;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use service\JsonService as Json;
use think\Request;

class PhpSpreadsheetService
{
    //PHPExcel实例化对象
    private static $PHPExcel = null;
    //表头计数
    protected static $count;
    //表头占行数
    protected static $topNumber = 3;
    //表能占据表行的字母对应self::$cellkey
    protected static $cells;
    //表头数据
    protected static $data = [];
    //文件名
    protected static $title = '订单导出';
    //行宽
    protected static $where = 30;
    //行高
    protected static $height = 50;
    //表行名
    private static $cellKey = array(
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
        'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
        'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM',
        'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ'
    );
    //设置style
    private static $styleArray = array(
        'borders' => array(
            'allborders' => array(
                'style' => \PHPExcel_Style_Border::BORDER_THIN,//细边框
            ),
        ),
        'font' => [
            'bold' => true
        ],
        'alignment' => [
            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER
        ]
    );

    /**
     *设置字体格式
     * @param $title string 必选
     * return string
     */

    public static function setUtf8($title)
    {
        return iconv('utf-8', 'gb2312', $title);
    }

    /**
     * 通用导出方法。传入参数即可
     * @param unknown $filename 导出的excel文件名称，不包括后缀
     * @param unknown $rows 要导出的数据，数组
     * @param unknown $head 要导出数据的表头，数组
     * @param unknown $keys 要导出数据的键值对对应
     */
    public static function outdata($filename, $rows = [], $head = [])
    {
        $count = count($head);  //计算表头数量

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        //设置样式,设置剧中，加边框，设置行高
        $styleArray = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '6184542'],
                ],
            ],
        ];
        $rows_count = count($rows);
        $sheet->getDefaultRowDimension()->setRowHeight(18);//设置默认行高。
        $sheet->getStyle('A1:' . strtoupper(chr($count + 65 - 1)) . strval($rows_count + 1))->applyFromArray($styleArray);
        $sheet->getStyle('A4:' . strtoupper(chr($count + 65 - 1)) . '1')->getFont()->setBold(true)->setName('Arial')->setSize(10)->applyFromArray($styleArray);
        //设置样式结束

        //写入表头信息
        for ($i = 65; $i < $count + 65; $i++) {
            //数字转字母从65开始，循环设置表头：
            $sheet->setCellValue(strtoupper(chr($i)) . '1', $head[$i - 65]);
        }

        //写入数据信息
        foreach ($rows as $key => $item) {
            //循环设置单元格：
            //$key+2,因为第一行是表头，所以写到表格时   从第二行开始写
            for ($i = 65; $i < $count + 65; $i++) {
                //数字转字母从65开始：
                $sheet->setCellValue(strtoupper(chr($i)) . ($key + 2), $item[$i - 65]);
                $spreadsheet->getActiveSheet()->getColumnDimension(strtoupper(chr($i)))->setWidth(30); //固定列宽
                // 支持换行
//                $sheet->getStyle(strtoupper(chr($i)))->getAlignment()->setWrapText(true);
            }

        }
        //header('Content-Type: application/vnd.ms-excel');xls
        header('Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//xlsx
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

        //删除清空：
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit;
    }
}
