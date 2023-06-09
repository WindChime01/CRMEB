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


class ExportService
{
    public static function exportCsv($list, $filename, $header = [], $br = '_')
    {
        $tableStr = count($header) > 0 ? '"' . implode('","', $header) . '"' . PHP_EOL : '';
        $tableStr .= self::tidyCsvStr($list, str_repeat($br, 99));
        ob_end_clean();
        ob_start();
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:filename=" . $filename . ".csv");
        header('Content-Type:application/download');
        exit(iconv('UTF-8', "GB2312//IGNORE", $tableStr));
    }

    private static function tidyCsvStr($list, $br = '')
    {
        $tableStr = '';
        foreach ($list as $row) {
            if (is_array($row)) {
                $max = 1;
                foreach ($row as $k => $item) {
                    if (is_array($item)) {
                        if ($max < ($l = count($item))) $max = $l;
                    } else
                        $row[$k] = [$item];
                }
                for ($i = 0; $i <= $max; $i++) {
                    $exportRow = [];
                    if ($max == $i) {
                        if ($br == '')
                            continue;
                        else
                            $exportRow = array_fill(0, count($row), $br);
                    } else {
                        foreach ($row as $item) {
                            $exportRow[] = isset($item[$i]) && !empty($item[$i]) ? $item[$i] : ' ';
                        }
                    }
                    $tableStr .= '"' . implode('","', $exportRow) . '"," "' . PHP_EOL;
                }
                $tableStr = rtrim($tableStr, PHP_EOL);
            } else {
                $tableStr .= implode('', ['"', $row, '"', ',']);
            }
            $tableStr .= PHP_EOL;
        }
        return $tableStr;
    }
}
