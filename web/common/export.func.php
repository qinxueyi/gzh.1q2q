<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/10/18
 * Time: 10:20
 */
/**
 * 创建(导出)Excel数据表格
 * @param  array $list 要导出的数组格式的数据
 * @param  string $filename 导出的Excel表格数据表的文件名
 * @param  array $indexKey $list数组中与Excel表格表头$header中每个项目对应的字段的名字(key值)
 * @param  array $startRow 第一条数据在Excel表格中起始行
 * @param  [bool]  $excel2007   是否生成Excel2007(.xlsx)以上兼容的数据表
 * 比如: $indexKey与$list数组对应关系如下:
 *     $indexKey = array('id','username','sex','age');
 *     $list = array(array('id'=>1,'username'=>'YQJ','sex'=>'男','age'=>24));
 */
function exportExcel($list, $filename, $indexKey, $startRow = 2, $excel2007 = false, $type = "fan")
{
    //文件引入
    require_once "Classes/PHPExcel.php";
    require_once 'Classes/PHPExcel/Writer/Excel2007.php';
    if (empty($filename)) $filename = time();
    if (!is_array($indexKey)) return false;
    //初始化PHPExcel()
    $objPHPExcel = new PHPExcel();
    //设置保存版本格式
    if ($excel2007) {
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $filename = $filename . '.xlsx';
    } else {
        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        $filename = $filename . '.xls';
    }
    if ($type == "fan") {
        $headerOne = array('公众号名称', '统计日期', '新增粉丝', '取关粉丝', '净增粉丝', '取关率', '粉丝数', '活跃粉丝', '活跃度');
        $header_arr = array('A', 'B', 'C', 'D', 'E', 'F', 'G','H', 'I');
        //接下来就是写数据到表格里面去
        //第一条
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setCellValue("A" . 1, $headerOne[0]);
        $objActSheet->setCellValue("B" . 1, $headerOne[1]);
        $objActSheet->setCellValue("C" . 1, $headerOne[2]);
        $objActSheet->setCellValue("D" . 1, $headerOne[3]);
        $objActSheet->setCellValue("E" . 1, $headerOne[4]);
        $objActSheet->setCellValue("F" . 1, $headerOne[5]);
        $objActSheet->setCellValue("G" . 1, $headerOne[6]);
        $objActSheet->setCellValue("H" . 1, $headerOne[7]);
        $objActSheet->setCellValue("I" . 1, $headerOne[8]);
    } else {
        $headerOne = array('公众号名称', '文章标题', '文章位置', '发文时间', '送达人数', '阅读数', '阅读率', '原文阅读数', '原文阅读率');
        $header_arr = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I');
        //接下来就是写数据到表格里面去
        //第一条
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setCellValue("A" . 1, $headerOne[0]);
        $objActSheet->setCellValue("B" . 1, $headerOne[1]);
        $objActSheet->setCellValue("C" . 1, $headerOne[2]);
        $objActSheet->setCellValue("D" . 1, $headerOne[3]);
        $objActSheet->setCellValue("E" . 1, $headerOne[4]);
        $objActSheet->setCellValue("F" . 1, $headerOne[5]);
        $objActSheet->setCellValue("G" . 1, $headerOne[6]);
        $objActSheet->setCellValue("H" . 1, $headerOne[7]);
        $objActSheet->setCellValue("I" . 1, $headerOne[8]);
    }
    if (!empty($list)) {
        foreach ($list as $row) {
            foreach ($indexKey as $key => $value) {
                //这里是设置单元格的内容
                $objActSheet->setCellValue($header_arr[$key] . $startRow, $row[$value]);
            }
            $startRow++;
        }
    }

    // 下载这个表格，在浏览器输出
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
    header("Content-Type:application/force-download");
    header("Content-Type:application/vnd.ms-execl");
    header("Content-Type:application/octet-stream");
    header("Content-Type:application/download");;
    header('Content-Disposition:attachment;filename=' . $filename . '');
    header("Content-Transfer-Encoding:binary");
    $objWriter->save('php://output');
}