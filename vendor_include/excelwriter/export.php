<?php
require_once 'simpleexcel.php';
require_once 'excelwriter/excelwriter.inc.php';

/**
* 
*/
error_reporting(E_ALL);
ini_set('display_errors', 0);
class Export
{
	
	public function export(){
		$xlsx = new SimpleXLSX('report_eva.xlsx');
		$this->data($xlsx->rows());
		// print_r( $xlsx->rows() );die;
	}

	protected function data($data){
		unset($data[0]);
        // print_r($data);die;        
        $filename = "Eva" . time() . ".xls";
        $file = dirname(__FILE__) .'/'.$filename;
        $excel = new \ExcelWriter($file);
        if ($excel == false) {
            echo $excel->error;
        }
        $row_title = array(' ', 'List Bài dự thi', ' ', ' ', ' ', ' ', ' ', ' ');
        $row_space = array(' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ');

        $excel->writeLine($row_title);
        $excel->writeLine($row_space);
        $excel->writeLine(array(
            'STT', 'ID bài', 'Họ tên', 'Email',
            'Lượt chia sẻ', 'Lượt thích', 'Ngày đăng', 
            'Phone', 'CMND', 'FB ID','Hình Ảnh'
            ));
        $i = 1;
        foreach ($data as $value) {
            $image = 'http://socialapps.vn/evaairlive/api/media/files/'.$value[3];
            // $image = BASE_URL . '/' . str_replace('media/contest/', 'media/contest/crop_', $value['preview']);
            $excel->writeLine(array(
                $i, $value[0], $value[1], $value[2],
                $value[4], $value[5],
                date('d/m/Y H:i:s' ,strtotime($value[6])), 
            	$value[7],$value[8], $value[9],
                '<img src="' . $image . '"/>'
                ), "style='height:450px;'"
            );
            $i++;
        }
        $excel->close();
        $filesize = filesize($file);
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment;filename=$filename");
        readfile($file);
        exit();

    }

}

$a = new Export();
$a->export();