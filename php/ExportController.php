<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Db\BarcodesRecord;
use App\Models\Db\BarcodeParameterRecord;
use App\Models\Db\BarcodeGuideRecord;
use App\Models\Db\BarcodeOrderRecord;
use App\Models\Db\VistorRecord;
use App\Models\Db\BarcodePriceRecord;
use App\Models\Db\BarcodeSkuRecord;
use App\Models\Db\BarcodePayRecord;
use App\Models\Db\BarcodeTicketRecord;
use App\Models\Db\BarcodeTicket1;
use App\Models\Db\CompanyRecord;
use App\Models\Db\OpenIdTrackerRecord;
use App\Models\Db\OpenIdPayRecord;
use App\Models\Db\XlsxRecord;
use App\Models\Db\XlsxDetailRecord;
use App\Models\TyFunction;
use App\Models\ExcelFunction;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportController extends BaseController
{
  public $db;
  private $TyFun;
  private $ExcelFun;
  private $config;

  public function __construct()
  {
    helper('form');
    session();
    $this->TyFun = new TyFunction();
    //$this->ExcelFun = new ExcelFunction();
    $this->config = config(App::class);


    if (!$this->TyFun->checkDomain())
      die('授权错误.');

    $this->db = \Config\Database::connect();
  }
  
  private  function initPostData($TType)
  {
    if ($TType == 'get') {
      $post['Paras'] = array(
        'AppId' => $this->request->getGet('AppId'),
        'TUserId' => $this->request->getGet('TUserId'),
        'TPhone' => $this->request->getGet('TPhone'),
        'TPassword' => $this->request->getGet('TPassword'),
        'TGuid' => $this->request->getGet('TGuid'),
        'TLoginType' => $this->request->getGet('TLoginType'),
        'TType' => $this->request->getGet('TType')
      );
    } else {
      if (empty($_POST)) {
        $content = file_get_contents('php://input');
        $post    = (array)json_decode($content, true);
      } else {
        $post = $_POST;
      }
	  if(!isset($post['AppId']))
		$post['AppId']='';
	  if(!isset($post['TUserId']))
			$post['TUserId']='';
		if(!isset($post['TPhone']))
				$post['TPhone']='';
		if(!isset($post['TPassword']))
				$post['TPassword']='';
		if(!isset($post['TLoginType']))
				$post['TLoginType']=0;
		if(!isset($post['TType']))
				$post['TType']=0;
				
      $post['Paras'] = array(
        'AppId' => $post['AppId'],
        'TUserId' => $post['TUserId'],
        'TPhone' => $post['TPhone'],
        'TPassword' => $post['TPassword'],
        'TLoginType' => $post['TLoginType'],
        'TType' => $post['TType']
      );
    }
  
    return $post;
  }
  
  public function index(){
	  $res=['code'=>0,'msg'=>'ok'];
	  helper('system');
	  $post = $this->initPostData('post');
	  $auth = $this->TyFun->getUserInfo(1, $post['Paras']);
	  $TUserId1 = $auth['OpenIdTracker']['TUserId1'];
	  $res['TUserId']=$TUserId1;
	  $actType=$post['actType'];
	  
	  $spreadsheet = new Spreadsheet();
	  $sheet = $spreadsheet->getActiveSheet()->setTitle('Parameter');
	  //$sheet = $spreadsheet->getActiveSheet();
	  $sheet->SetCellValue('A1', 'TType');
	  $sheet->SetCellValue('B1', 'TName');
	  $sheet->SetCellValue('C1', 'TSort');
	  $sheet->SetCellValue('D1', 'TRemark');
	  // set Row
	  $query = $this->db->query("select * from barcodeparameterrecord where TIsDelete=0 and TUserId1='$TUserId1'");
	  $rowCount = 2;
	  foreach ($query->getResult() as $list) {
	    $sheet->SetCellValue('A' . $rowCount, $list->TType);
	    $sheet->SetCellValue('B' . $rowCount, $list->TName);
	    $sheet->SetCellValue('C' . $rowCount, ' ' . $list->TSort);
	    $sheet->SetCellValue('D' . $rowCount, ' ' . $list->TRemark);
	    //$sheet->SetCellValue('E' . $rowCount, $list->TQuantity);
	    //$sheet->SetCellValue('F' . $rowCount, $list->TDt);
	    $rowCount++;
	  }
	  
	  $sheet_2 = $spreadsheet->createSheet(2)->setTitle('Users');
	  $sheet_2->SetCellValue('A1', 'TNo');
	  $sheet_2->SetCellValue('B1', 'FirstName');
	  $sheet_2->SetCellValue('C1', 'Phone');
	  $sheet_2->SetCellValue('D1', 'TDept');
	  $sheet_2->SetCellValue('E1', 'TOpenId');
	  $sheet_2->SetCellValue('F1', 'TGuid');
	  $query = $this->db->query("select * from openidtrackerrecord where TIsDelete=0 and TUserId1='$TUserId1'");
	  $rowCount = 2;
	  foreach ($query->getResult() as $list) {
	    $sheet_2->SetCellValue('A' . $rowCount, $list->TNo);
	    $sheet_2->SetCellValue('B' . $rowCount, $list->FirstName);
	    $sheet_2->SetCellValue('C' . $rowCount, $list->Phone);
	    $sheet_2->SetCellValue('D' . $rowCount, $list->TDept);
	    $sheet_2->SetCellValue('E' . $rowCount, $list->TOpenId);
	    $sheet_2->SetCellValue('F' . $rowCount, $list->TGuid);
	    $rowCount++;
	  }
	  
	  $sheet_3 = $spreadsheet->createSheet(3)->setTitle('Ticket');
	  $sheet_3->SetCellValue('A1', '编号');
	  $sheet_3->SetCellValue('B1', '名称');
	  $sheet_3->SetCellValue('C1', 'TRemark');
	  $sheet_3->SetCellValue('D1', 'TGuid');
	  $sheet_3->SetCellValue('E1', 'TDt');
	  $query = $this->db->query("select * from barcodeticketrecord where TIsDelete=0 and TUserId1='$TUserId1'");
	  $rowCount = 2;
	  foreach ($query->getResult() as $list) {
	    $sheet_3->SetCellValue('A' . $rowCount, $list->TNo);
	    $sheet_3->SetCellValue('B' . $rowCount, $list->TName);
	    $sheet_3->SetCellValue('C' . $rowCount, ' ' . $list->TRemark);
	    $sheet_3->SetCellValue('D' . $rowCount, ' ' . $list->TGuid);
	    $sheet_3->SetCellValue('E' . $rowCount, ' ' . $list->TDt);
	    $rowCount++;
	  }
	  
	  
	  $sheet_4 = $spreadsheet->createSheet(4)->setTitle('Sku');
	  $sheet_4->SetCellValue('A1', '编号');
	  $sheet_4->SetCellValue('B1', '名称');
	  $sheet_4->SetCellValue('C1', 'TPrice');
	  $sheet_4->SetCellValue('D1', 'TDept');
	  $sheet_4->SetCellValue('E1', 'TRemark');
	  $sheet_4->SetCellValue('F1', 'TGuid');
	  $sheet_4->SetCellValue('G1', 'TTicketGuid');
	  $query = $this->db->query("select * from barcodeskurecord where TIsDelete=0 and TUserId1='$TUserId1'");
	  $rowCount = 2;
	  foreach ($query->getResult() as $list) {
	    $sheet_4->SetCellValue('A' . $rowCount, $list->TNo);
	    $sheet_4->SetCellValue('B' . $rowCount, $list->TName);
	    $sheet_4->SetCellValue('C' . $rowCount, ' ' . $list->TPrice);
	    $sheet_4->SetCellValue('D' . $rowCount, ' ' . $list->TDept);
	    $sheet_4->SetCellValue('E' . $rowCount, ' ' . $list->TRemark);
	    $sheet_4->SetCellValue('F' . $rowCount, ' ' . $list->TGuid);
	    $sheet_4->SetCellValue('G' . $rowCount, ' ' . $list->TTicketGuid);
	    $rowCount++;
	  }
	  
	  //if($actType==2){
		  $sheet_5 = $spreadsheet->createSheet(5)->setTitle('Barcode');
		  $sheet_5->SetCellValue('A1', 'TNo');
		  $sheet_5->SetCellValue('B1', 'TName');
		  $sheet_5->SetCellValue('C1', 'TQuantity');
		  $sheet_5->SetCellValue('D1', 'TQuantityB');
		  $sheet_5->SetCellValue('E1', 'TDt');
		  $sheet_5->SetCellValue('F1', 'TRemark');
		  $query = $this->db->query("select * from barcodesrecord where TIsDelete=0 and TUserId1='$TUserId1'");
		  $rowCount = 2;
		  foreach ($query->getResult() as $list) {
		    $sheet_5->SetCellValue('A' . $rowCount, $list->TNo);
		    $sheet_5->SetCellValue('B' . $rowCount, $list->TName);
		    $sheet_5->SetCellValue('C' . $rowCount, ' ' . $list->TQuantity);
		    $sheet_5->SetCellValue('D' . $rowCount, ' ' . $list->TQuantityB);
		    $sheet_5->SetCellValue('E' . $rowCount, ' ' . $list->TDt);
		    $sheet_5->SetCellValue('F' . $rowCount, ' ' . $list->TRemark);
		    $sheet_5->SetCellValue('G' . $rowCount, ' ' . $list->TGuid);
		    $sheet_5->SetCellValue('H' . $rowCount, ' ' . $list->TTicketGuid);
		    $sheet_5->SetCellValue('I' . $rowCount, ' ' . $list->TSn);
		    $sheet_5->SetCellValue('J' . $rowCount, ' ' . $list->TNum1);
		    $sheet_5->SetCellValue('K' . $rowCount, ' ' . $list->TNum2);
		    $sheet_5->SetCellValue('L' . $rowCount, ' ' . $list->TSize);
		    $sheet_5->SetCellValue('M' . $rowCount, ' ' . $list->TColor);
		    $sheet_5->SetCellValue('N' . $rowCount, ' ' . $list->TStyle);
		    $sheet_5->SetCellValue('O' . $rowCount, ' ' . $list->TIsFinish);
		    $rowCount++;
		  }
		  /*
		  TUserId:item[1],
		  TQuantity:parseInt(item[2]),
		  TPrice:Number(item[3]) ,
		  TBarcodeGuid:tools.strTrim(item[4]),
		  TSkuGuid:tools.strTrim(item[5]),
		  TDt:tools.strToDate(item[6]),
		  TStatus:parseInt(item[7]),
		  TDtFinish:dtFinish});
		  */
		  $sheet_6 = $spreadsheet->createSheet(6)->setTitle('Order');
		  $sheet_6->SetCellValue('A1', '编号');
		  $sheet_6->SetCellValue('B1', 'TUserId');
		  $sheet_6->SetCellValue('C1', 'TQuantity');
		  $sheet_6->SetCellValue('D1', 'TPrice');
		  $sheet_6->SetCellValue('E1', 'TBarcodeGuid');
		  $sheet_6->SetCellValue('F1', 'TSkuGuid');
		  $sheet_6->SetCellValue('G1', 'TDt');
		  $sheet_6->SetCellValue('H1', 'TStatus');
		  $sheet_6->SetCellValue('I1', 'TDtFinish');
		  $query = $this->db->query("select * from barcodeorderrecord where TIsDelete=0 and TUserId1='$TUserId1' order by Id desc limit 0,5000");
		  $rowCount = 2;
		  foreach ($query->getResult() as $list) {
		    $sheet_6->SetCellValue('A' . $rowCount, $list->TNo);
		    $sheet_6->SetCellValue('B' . $rowCount, $list->TUserId);
		    $sheet_6->SetCellValue('C' . $rowCount, '' . $list->TQuantity);
		    $sheet_6->SetCellValue('D' . $rowCount, '' . $list->TPrice);
		    $sheet_6->SetCellValue('E' . $rowCount, '' . $list->TBarcodeGuid);
		    $sheet_6->SetCellValue('F' . $rowCount, '' . $list->TSkuGuid);
		    $sheet_6->SetCellValue('G' . $rowCount, '' . $list->TDt);
		    $sheet_6->SetCellValue('H' . $rowCount, '' . $list->TStatus);
		    $sheet_6->SetCellValue('I' . $rowCount, '' );
		    $sheet_6->SetCellValue('J' . $rowCount, '' . $list->TGuid);
		    $rowCount++;
		  }
		  
	  //}
	  
	$filename =  "download/" . date("YmdHis") . ".xlsx";
		$writer = new Xlsx($spreadsheet);
		$writer->save(WRITEPATH.$filename);
	$res =  array(
	  'op' => 'ok',
	  //'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($xlsData)
	  'file' => 'writable/' . $filename
	);
		
	 return json_encode($res);
  }

	

  //=========================================================================================
}
