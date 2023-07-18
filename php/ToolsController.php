<?php

namespace App\Controllers;

use App\Models\Db\BarcodeParameterRecord;
use App\Models\AstDb\AstSupportRecord;
use App\Models\AstDb\AstInoutRecord;
use App\Models\AstDb\AstVistorRecord;
use App\Models\AstDb\AstVcRecord;
use App\Models\AstDb\AstViRecord;
use App\Models\AstDb\AstVmRecord;
use App\Models\AstDb\AstVpRecord;
use App\Models\TyFunction;

class ToolsController extends BaseController
{
  public $db;
  private $TyFun;
  public function __construct()
  {
    helper('form');
    session();
    $this->TyFun = new TyFunction();
    if (!$this->TyFun->checkDomain())
      die('授权错误.');

    $this->db = \Config\Database::connect();
  }

	
//https://i3.xinbda.com/ct?TUserId1=13580704353&drop=0
public function ct()
{	
	echo '<br/> =====================================  Begin ===================================================';
	$TUserId1= $this->request->getGet('TUserId1');	
	$IsDrop= $this->request->getGet('drop');	
	echo $TUserId1;
	echo '<br/>';
	echo $IsDrop;
	echo '<br/>';
	if($TUserId1=='')
		return;	

	if($IsDrop==1){
		echo $this->db->simpleQuery("DROP TABLE IF EXISTS `h_$TUserId1`");
		echo $this->db->simpleQuery("DROP TABLE IF EXISTS `b_$TUserId1`");
		echo $this->db->simpleQuery("DROP TABLE IF EXISTS `p_$TUserId1`");
		echo $this->db->simpleQuery("DROP TABLE IF EXISTS `x_$TUserId1`");
		echo $this->db->simpleQuery("DROP TABLE IF EXISTS `bp_$TUserId1`");
		echo $this->db->simpleQuery("DROP TABLE IF EXISTS `s_$TUserId1`");
		echo $this->db->simpleQuery("DROP TABLE IF EXISTS `o_$TUserId1`");
		echo $this->db->simpleQuery("DROP TABLE IF EXISTS `t_$TUserId1`");
		echo '<br/>';
	}

$sql=<<<_SQL
CREATE TABLE IF NOT EXISTS `h_$TUserId1` (
  `Id` bigint(20) NOT NULL AUTO_INCREMENT,
  `TGuid` varchar(40) NOT NULL,
  `TNo` varchar(8) DEFAULT NULL,
  `TName` varchar(100) DEFAULT NULL,
  `TDt` datetime DEFAULT NULL,
  `TRemark` varchar(150) DEFAULT NULL,
  `TUserId` varchar(20) DEFAULT NULL,
  `TUserId1` varchar(20) DEFAULT NULL,
  `TIsDelete` tinyint(1) DEFAULT NULL,
  `TDtUpdate` datetime DEFAULT NULL,
  `TIsLock` tinyint(1) DEFAULT '0',
  `TTicketGuid` varchar(40) DEFAULT NULL,
  `TStyleName` varchar(100) DEFAULT NULL,
  `TStyle` varchar(100) DEFAULT NULL,
  `TQuantity` int(11) DEFAULT NULL,
  `TQuantityB` int(11) DEFAULT NULL,
  `TColor` varchar(1000) DEFAULT NULL,
  `TSize` varchar(1000) DEFAULT NULL,
  `TContent` varchar(2000) DEFAULT NULL,
  `TCreateType` int(11) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `H__TGuid` (`TGuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
_SQL;
	echo "<br/> $sql <br/>";
	echo $this->db->simpleQuery($sql);
$sql=<<<_SQL
CREATE TABLE IF NOT EXISTS `o_$TUserId1` (
	`Id` bigint(20) NOT NULL AUTO_INCREMENT, 
	`TGuid` varchar(40) NOT NULL,
	`TBarcodeGuid` varchar(40) NOT NULL,
	`TSkuGuid` varchar(40) NOT NULL,
	`TNo` varchar(10) DEFAULT NULL,
	`TName` varchar(40) DEFAULT NULL,
	`TStyle` varchar(30) DEFAULT NULL,
	`TType` varchar(20) DEFAULT NULL,
	`TSn` varchar(10) DEFAULT NULL,
	`TProductGuid` varchar(10) DEFAULT NULL,
	`TSaleNo` varchar(10) DEFAULT NULL,
	`TColor` varchar(20) DEFAULT NULL,
	`TSize` varchar(20) DEFAULT NULL,		  
	`TEmpName` varchar(20) DEFAULT NULL,
	`TSkuName` varchar(40) DEFAULT NULL,
	`TPayGuid` varchar(40) DEFAULT NULL,
	`TQuantity` decimal(10,0) DEFAULT NULL,
	`TStatus` bigint(2) DEFAULT NULL,
	`TPrice` decimal(10,4) DEFAULT NULL,
	`TAmt` decimal(10,3) DEFAULT NULL,
	`TNum1` decimal(10,0) DEFAULT NULL,
	`TNum2` decimal(10,0) DEFAULT NULL,
	`TDt` datetime DEFAULT NULL,
	`TDtFinish` datetime DEFAULT NULL,
	`TAccTime` decimal(10,0) DEFAULT NULL,
	`TRemark` varchar(150) DEFAULT NULL,
	`TUserId` varchar(40) DEFAULT NULL,
	`TUserId1` varchar(20) DEFAULT NULL,
	`TIsDelete` tinyint(1) DEFAULT NULL,
	`TDtUpdate` datetime DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `O__TGuid` (`TGuid`),
  KEY `TBarcodeGuid` (`TBarcodeGuid`),
  KEY `TSkuGuid` (`TSkuGuid`),
  KEY `TUserId` (`TUserId`),
  KEY `TDt` (`TDt`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
_SQL;
	echo "<br/> $sql <br/>";
	echo $this->db->simpleQuery($sql);
$sql=<<<_SQL
CREATE TABLE IF NOT EXISTS `p_$TUserId1` (
  `Id` bigint(20) NOT NULL AUTO_INCREMENT,
  `TGuid` varchar(40) NOT NULL,
  `TKeyGuid` varchar(50) DEFAULT NULL,
  `TType` bigint(20) DEFAULT NULL,
  `TSort` bigint(20) DEFAULT NULL,
  `TName` varchar(120) DEFAULT NULL,
  `TUserId1` varchar(20) DEFAULT NULL,
  `TIsDelete` tinyint(1) DEFAULT NULL,
	`TChecked` tinyint(1) DEFAULT NULL,	
	`TCheckedP` tinyint(1) DEFAULT NULL,	
  `TDtUpdate` datetime DEFAULT NULL,
  `TNo` varchar(8) DEFAULT NULL,
  `TUserId` varchar(20) DEFAULT NULL,
  `TDt` datetime DEFAULT NULL,
  `TId` bigint(20) DEFAULT NULL,
  `TSort2` bigint(20) DEFAULT NULL,
  `TPhoto` varchar(120) DEFAULT NULL,
  `TValue` decimal(10,0) DEFAULT NULL,
  `TRemark` longtext,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `P__TGuid` (`TGuid`),
  KEY `TType` (`TType`),
  KEY `TName` (`TName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
_SQL;
	echo "<br/> $sql <br/>";
	echo $this->db->simpleQuery($sql);
$sql=<<<_SQL
CREATE TABLE IF NOT EXISTS `x_$TUserId1` (
  `Id` bigint(20) NOT NULL AUTO_INCREMENT,
  `TNo` varchar(8) DEFAULT NULL,
  `TGuid` varchar(40) NOT NULL,
  `TName` varchar(30) DEFAULT NULL,
  `TStatus` bigint(20) DEFAULT NULL,
  `TDt` datetime DEFAULT NULL,
  `TDtBegin` varchar(30) DEFAULT NULL,
  `TDtEnd` varchar(30) DEFAULT NULL,
  `TDtPay` varchar(30) DEFAULT NULL,
  `TRemark` varchar(150) DEFAULT NULL,
  `TUserId` varchar(40) DEFAULT NULL,
  `TUserId1` varchar(20) DEFAULT NULL,
  `TNum1` decimal(10,0) DEFAULT NULL,
  `TNum2` decimal(10,0) DEFAULT NULL,
  `TNum3` decimal(10,0) DEFAULT NULL,
  `TNum4` decimal(10,0) DEFAULT NULL,
  `TNum5` decimal(10,0) DEFAULT NULL,
  `TNum6` decimal(10,0) DEFAULT NULL,
  `TNum7` decimal(10,0) DEFAULT NULL,
  `TNum8` decimal(10,0) DEFAULT NULL,
  `TNum9` decimal(10,0) DEFAULT NULL,
  `TNum10` decimal(10,0) DEFAULT NULL,
  `TNum11` decimal(10,0) DEFAULT NULL,
  `TNum12` decimal(10,0) DEFAULT NULL,
  `TNum13` decimal(10,0) DEFAULT NULL,
  `TNum14` decimal(10,0) DEFAULT NULL,
  `TNum15` decimal(10,0) DEFAULT NULL,
  `TAmt` decimal(10,0) DEFAULT NULL,
  `TIsDelete` tinyint(1) DEFAULT NULL,
  `TDtUpdate` datetime DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `X__TGuid` (`TGuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
_SQL;
	echo "<br/> $sql <br/>";
	echo $this->db->simpleQuery($sql);
$sql=<<<_SQL
CREATE TABLE IF NOT EXISTS `bp_$TUserId1` (
  `Id` bigint(20) NOT NULL AUTO_INCREMENT,
  `TGuid` varchar(40) DEFAULT NULL,
  `TSkuGuid` varchar(40) DEFAULT NULL,
  `TPrice` decimal(10,4) DEFAULT NULL,
  `TName` varchar(40) DEFAULT NULL,
  `TSize` varchar(40) DEFAULT NULL,
  `TUserId1` varchar(20) DEFAULT NULL,
  `TIsDelete` tinyint(1) DEFAULT NULL,
  `TDtUpdate` datetime DEFAULT NULL,
  `TNo` varchar(8) DEFAULT NULL,
  `TUserId` varchar(40) DEFAULT NULL,
  `TDt` datetime DEFAULT NULL,
  `TQuantity` decimal(10,0) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `TGuid` (`TGuid`),
	KEY `TSkuGuid` (`TSkuGuid`),
	KEY `TName` (`TName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
_SQL;
	echo "<br/> $sql <br/>";
	echo $this->db->simpleQuery($sql);
$sql=<<<_SQL
CREATE TABLE IF NOT EXISTS `s_$TUserId1` (
  `Id` bigint(20) NOT NULL AUTO_INCREMENT,
  `TGuid` varchar(40) NOT NULL,
  `TNo` varchar(10) DEFAULT NULL,
  `TSn` varchar(20) DEFAULT NULL,
  `TName` varchar(100) DEFAULT NULL,
  `TPrice` decimal(10,4) DEFAULT NULL,
  `TRemark` varchar(150) DEFAULT NULL,
  `TDept` varchar(30) DEFAULT NULL,
  `TUserId1` varchar(20) DEFAULT NULL,
  `TIsDelete` tinyint(1) DEFAULT NULL,
  `TDtUpdate` datetime DEFAULT NULL,
  `TTicketGuid` varchar(40) NOT NULL,
  `TUserId` varchar(40) DEFAULT NULL,
  `TDt` datetime DEFAULT NULL,
  `TIsPublic` tinyint(1) DEFAULT NULL,
  `TNum1` decimal(10,2) DEFAULT NULL,
  `TNum2` decimal(10,2) DEFAULT NULL,
  `TNum3` decimal(10,2) DEFAULT NULL,
  `TPriceT` decimal(10,4) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `S__TGuid` (`TGuid`),
  KEY `TName` (`TName`),
  KEY `TTicketGuid` (`TTicketGuid`),  
  KEY `TSn` (`TSn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
_SQL;
	echo "<br/> $sql <br/>";
	echo $this->db->simpleQuery($sql);
$sql=<<<_SQL
CREATE TABLE IF NOT EXISTS `b_$TUserId1` (
  `Id` bigint(20) NOT NULL AUTO_INCREMENT,
  `TGuid` varchar(40) NOT NULL,
  `TTicketGuid` varchar(40) NOT NULL,
  `TNo` varchar(8) DEFAULT NULL,
  `TSn` varchar(20) DEFAULT NULL,
  `TName` varchar(100) DEFAULT NULL,
  `TStyle` varchar(100) DEFAULT NULL,
  `TType` varchar(100) DEFAULT NULL,
  `TSize` varchar(20) DEFAULT NULL,
  `TColor` varchar(20) DEFAULT NULL,
  `TQuantity` decimal(10,0) DEFAULT NULL,
  `TQuantityB` decimal(10,0) DEFAULT NULL,
  `TNum1` bigint(20) DEFAULT NULL,
  `TNum2` bigint(20) DEFAULT NULL,
  `TDt` datetime DEFAULT NULL,
  `TRemark` varchar(150) DEFAULT NULL,
  `TUserId` varchar(20) DEFAULT NULL,
  `TUserId1` varchar(20) DEFAULT NULL,
  `TIsDelete` tinyint(1) DEFAULT NULL,
  `TDtUpdate` datetime DEFAULT NULL,
  `TIsLock` tinyint(1) DEFAULT '0',
  `TIsFinish` tinyint(1) DEFAULT '0',
  `TDtPay` datetime DEFAULT NULL,
  `TCustomer` varchar(30) DEFAULT NULL,
  `TAttr1` varchar(20) DEFAULT NULL,
  `TAttr2` varchar(20) DEFAULT NULL,
  `TAttr3` varchar(20) DEFAULT NULL,
  `TSaleNo` varchar(20) DEFAULT NULL,
  `TProductGuid` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `B__TGuid` (`TGuid`),
  KEY `TTicketGuid` (`TTicketGuid`),
  KEY `TNo` (`TNo`),
  KEY `TDt` (`TDt`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
_SQL;
	echo "<br/> $sql <br/>";
	echo $this->db->simpleQuery($sql);
$sql=<<<_SQL
CREATE TABLE IF NOT EXISTS `t_$TUserId1` (
  `Id` bigint(20) NOT NULL AUTO_INCREMENT,
  `TGuid` varchar(40) NOT NULL,
  `TNo` varchar(8) DEFAULT NULL,
  `TName` varchar(100) DEFAULT NULL,
  `TDt` datetime DEFAULT NULL,
  `TRemark` varchar(150) DEFAULT NULL,
  `TUserId` varchar(20) DEFAULT NULL,
  `TUserId1` varchar(20) DEFAULT NULL,
  `TIsDelete` tinyint(1) DEFAULT NULL,
  `TDtUpdate` datetime DEFAULT NULL,
  `TProductGuid` varchar(40) DEFAULT NULL,
  `TIsLock` tinyint(1) DEFAULT '0',
  `TBomGuid` varchar(40) DEFAULT NULL,
  `TBomName` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `T__TGuid` (`TGuid`),
  KEY `TName` (`TName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	 
_SQL;
	echo "<br/> $sql <br/>";
	echo $this->db->simpleQuery($sql);

//***************************************************************************************************
$sql=<<<_SQL
CREATE TABLE IF NOT EXISTS `pi_$TUserId1` (
	`Id` bigint(20) NOT NULL AUTO_INCREMENT,
	`TGuid` varchar(40) NOT NULL,
	`TName` varchar(100) DEFAULT NULL,
	`TStyle` varchar(30) DEFAULT NULL,
	`TUnit` varchar(20) DEFAULT NULL,
	`TQuantity`  decimal(10,2) DEFAULT NULL,
	`TDt` datetime DEFAULT NULL,
	`TRemark` varchar(150) DEFAULT NULL,
	`TIsDelete` tinyint(1) DEFAULT NULL,
	`TDtUpdate` datetime DEFAULT NULL,
	PRIMARY KEY (`Id`),
	UNIQUE KEY `T__TGuid` (`TGuid`),
	KEY `TName` (`TName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	 
_SQL;
	echo "<br/> $sql <br/>";
	echo $this->db->simpleQuery($sql);
$sql=<<<_SQL
CREATE TABLE IF NOT EXISTS `pi_s_$TUserId1` (
	`Id` bigint(20) NOT NULL AUTO_INCREMENT,
	`TGuid` varchar(40) NOT NULL,
	`TName` varchar(100) DEFAULT NULL,
	`TColor` varchar(30) DEFAULT NULL,
	`TSize` varchar(30) DEFAULT NULL,
	`TPrice`  decimal(10,2) DEFAULT NULL,
	`TQuantity`  decimal(10,2) DEFAULT NULL,
	`TDt` datetime DEFAULT NULL,
	`TRemark` varchar(150) DEFAULT NULL,
	`TIsDelete` tinyint(1) DEFAULT NULL,
	`TDtUpdate` datetime DEFAULT NULL,
	PRIMARY KEY (`Id`),
	UNIQUE KEY `T__TGuid` (`TGuid`),
	KEY `TName` (`TName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	 
_SQL;
	echo "<br/> $sql <br/>";
	echo $this->db->simpleQuery($sql);
//入库表
$sql=<<<_SQL
CREATE TABLE IF NOT EXISTS `pi_io_0_$TUserId1` (
	`Id` bigint(20) NOT NULL AUTO_INCREMENT,
	`TGuid` varchar(40) NOT NULL,
	`TName` varchar(100) DEFAULT NULL,
	`TCorpGuid` varchar(30) DEFAULT NULL,
	`TCorpName` varchar(30) DEFAULT NULL,
	`TAddress` varchar(30) DEFAULT NULL,
	`TPhone` varchar(20) DEFAULT NULL,
	`TAmt`  decimal(10,2) DEFAULT NULL,
	`TQuantity` decimal(10,2) DEFAULT NULL,
	`TDt` datetime DEFAULT NULL,
	`TRemark` varchar(150) DEFAULT NULL,
	`TUserId` varchar(20) DEFAULT NULL,
	`TIsDelete` tinyint(1) DEFAULT NULL,
	`TDtUpdate` datetime DEFAULT NULL,
	PRIMARY KEY (`Id`),
	UNIQUE KEY `T__TGuid` (`TGuid`),
	KEY `TName` (`TName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	 
_SQL;
	echo "<br/> $sql <br/>";
	echo $this->db->simpleQuery($sql);
$sql=<<<_SQL
CREATE TABLE IF NOT EXISTS `pi_io_1_$TUserId1` (
	`Id` bigint(20) NOT NULL AUTO_INCREMENT,
	`TGuid` varchar(40) NOT NULL,
	`TName` varchar(100) DEFAULT NULL,
	`TCorpGuid` varchar(30) DEFAULT NULL,
	`TCorpName` varchar(30) DEFAULT NULL,
	`TAddress` varchar(30) DEFAULT NULL,
	`TPhone` varchar(20) DEFAULT NULL,
	`TAmt`  decimal(10,2) DEFAULT NULL,
	`TQuantity` decimal(10,2) DEFAULT NULL,
	`TDt` datetime DEFAULT NULL,
	`TRemark` varchar(150) DEFAULT NULL,
	`TUserId` varchar(20) DEFAULT NULL,
	`TIsDelete` tinyint(1) DEFAULT NULL,
	`TDtUpdate` datetime DEFAULT NULL,
	PRIMARY KEY (`Id`),
	UNIQUE KEY `T__TGuid` (`TGuid`),
	KEY `TName` (`TName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	 
_SQL;
	echo "<br/> $sql <br/>";
	echo $this->db->simpleQuery($sql);
$sql=<<<_SQL
CREATE TABLE IF NOT EXISTS `pi_io_d_0_$TUserId1` (
	`Id` bigint(20) NOT NULL AUTO_INCREMENT,
	`TGuid` varchar(40) NOT NULL,
	`TKeyGuid` varchar(40) NOT NULL,
	`TProductGuid` varchar(20) DEFAULT NULL,
	`TName` varchar(100) DEFAULT NULL,
	`TColor` varchar(20) DEFAULT NULL,
	`TSize` varchar(20) DEFAULT NULL,
	`TAttr` varchar(20) DEFAULT NULL,
	`TUnit1` varchar(20) DEFAULT NULL,
	`TAmt`  decimal(10,2) DEFAULT NULL,
	`TQuantity` decimal(10,2) DEFAULT NULL,
	`TPrice` decimal(10,3) DEFAULT NULL,
	`TDt` datetime DEFAULT NULL,
	`TRemark` varchar(150) DEFAULT NULL,
	`TIsDelete` tinyint(1) DEFAULT NULL,
	`TDtUpdate` datetime DEFAULT NULL,
	PRIMARY KEY (`Id`),
	UNIQUE KEY `T__TGuid` (`TGuid`),
	KEY `TName` (`TName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	 
_SQL;
	echo "<br/> $sql <br/>";
	echo $this->db->simpleQuery($sql);
$sql=<<<_SQL
CREATE TABLE IF NOT EXISTS `pi_io_d_1_$TUserId1` (
	`Id` bigint(20) NOT NULL AUTO_INCREMENT,
	`TGuid` varchar(40) NOT NULL,
	`TKeyGuid` varchar(40) NOT NULL,
	`TProductGuid` varchar(20) DEFAULT NULL,
	`TName` varchar(100) DEFAULT NULL,
	`TColor` varchar(20) DEFAULT NULL,
	`TSize` varchar(20) DEFAULT NULL,
	`TAttr` varchar(20) DEFAULT NULL,
	`TUnit1` varchar(20) DEFAULT NULL,
	`TAmt`  decimal(10,2) DEFAULT NULL,
	`TQuantity` decimal(10,2) DEFAULT NULL,
	`TPrice` decimal(10,3) DEFAULT NULL,
	`TDt` datetime DEFAULT NULL,
	`TRemark` varchar(150) DEFAULT NULL,
	`TIsDelete` tinyint(1) DEFAULT NULL,
	`TDtUpdate` datetime DEFAULT NULL,
	PRIMARY KEY (`Id`),
	UNIQUE KEY `T__TGuid` (`TGuid`),
	KEY `TName` (`TName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	 
_SQL;
	echo "<br/> $sql <br/>";
	echo $this->db->simpleQuery($sql);

	$sql=<<<_SQL
CREATE TABLE IF NOT EXISTS `m_$TUserId1` (
  	`Id` bigint(20) NOT NULL AUTO_INCREMENT,
	`TGuid` varchar(40) NOT NULL,
	`TName` varchar(100) DEFAULT NULL,
	`TStyle` varchar(30) DEFAULT NULL,
	`TUnit` varchar(20) DEFAULT NULL,
	`TQuantity`  decimal(10,2) DEFAULT NULL,
	`TDt` datetime DEFAULT NULL,
	`TRemark` varchar(150) DEFAULT NULL,
	`TIsDelete` tinyint(1) DEFAULT NULL,
	`TDtUpdate` datetime DEFAULT NULL,
	PRIMARY KEY (`Id`),
	UNIQUE KEY `T__TGuid` (`TGuid`),
	KEY `TName` (`TName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	 
_SQL;
	echo "<br/> $sql <br/>";
	echo $this->db->simpleQuery($sql);

$sql=<<<_SQL
CREATE TABLE IF NOT EXISTS `m_io_0_$TUserId1` (
	`Id` bigint(20) NOT NULL AUTO_INCREMENT,
	`TGuid` varchar(40) NOT NULL,
	`TName` varchar(100) DEFAULT NULL,
	`TCorpGuid` varchar(30) DEFAULT NULL,
	`TCorpName` varchar(30) DEFAULT NULL,
	`TAddress` varchar(30) DEFAULT NULL,
	`TPhone` varchar(20) DEFAULT NULL,
	`TAmt`  decimal(10,2) DEFAULT NULL,
	`TQuantity` decimal(10,2) DEFAULT NULL,
	`TDt` datetime DEFAULT NULL,
	`TRemark` varchar(150) DEFAULT NULL,
	`TUserId` varchar(20) DEFAULT NULL,
	`TIsDelete` tinyint(1) DEFAULT NULL,
	`TDtUpdate` datetime DEFAULT NULL,
	PRIMARY KEY (`Id`),
	UNIQUE KEY `T__TGuid` (`TGuid`),
	KEY `TName` (`TName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	 
_SQL;
	echo "<br/> $sql <br/>";
	echo $this->db->simpleQuery($sql);
$sql=<<<_SQL
CREATE TABLE IF NOT EXISTS `m_io_1_$TUserId1` (
	`Id` bigint(20) NOT NULL AUTO_INCREMENT,
	`TGuid` varchar(40) NOT NULL,
	`TName` varchar(100) DEFAULT NULL,
	`TCorpGuid` varchar(30) DEFAULT NULL,
	`TCorpName` varchar(30) DEFAULT NULL,
	`TAddress` varchar(30) DEFAULT NULL,
	`TPhone` varchar(20) DEFAULT NULL,
	`TAmt`  decimal(10,2) DEFAULT NULL,
	`TQuantity` decimal(10,2) DEFAULT NULL,
	`TDt` datetime DEFAULT NULL,
	`TRemark` varchar(150) DEFAULT NULL,
	`TUserId` varchar(20) DEFAULT NULL,
	`TIsDelete` tinyint(1) DEFAULT NULL,
	`TDtUpdate` datetime DEFAULT NULL,
	PRIMARY KEY (`Id`),
	UNIQUE KEY `T__TGuid` (`TGuid`),
	KEY `TName` (`TName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	 
_SQL;
	echo "<br/> $sql <br/>";
	echo $this->db->simpleQuery($sql);
$sql=<<<_SQL
CREATE TABLE IF NOT EXISTS `m_io_d_0_$TUserId1` (
	`Id` bigint(20) NOT NULL AUTO_INCREMENT,
	`TGuid` varchar(40) NOT NULL,
	`TKeyGuid` varchar(40) NOT NULL,
	`TProductGuid` varchar(20) DEFAULT NULL,
	`TName` varchar(100) DEFAULT NULL,
	`TColor` varchar(20) DEFAULT NULL,
	`TSize` varchar(20) DEFAULT NULL,
	`TAttr` varchar(20) DEFAULT NULL,
	`TUnit1` varchar(20) DEFAULT NULL,
	`TAmt`  decimal(10,2) DEFAULT NULL,
	`TQuantity` decimal(10,2) DEFAULT NULL,
	`TPrice` decimal(10,3) DEFAULT NULL,
	`TDt` datetime DEFAULT NULL,
	`TRemark` varchar(150) DEFAULT NULL,
	`TIsDelete` tinyint(1) DEFAULT NULL,
	`TDtUpdate` datetime DEFAULT NULL,
	PRIMARY KEY (`Id`),
	UNIQUE KEY `T__TGuid` (`TGuid`),
	KEY `TName` (`TName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	 
_SQL;
	echo "<br/> $sql <br/>";
	echo $this->db->simpleQuery($sql);
$sql=<<<_SQL
CREATE TABLE IF NOT EXISTS `m_io_d_1_$TUserId1` (
	`Id` bigint(20) NOT NULL AUTO_INCREMENT,
	`TGuid` varchar(40) NOT NULL,
	`TKeyGuid` varchar(40) NOT NULL,
	`TProductGuid` varchar(20) DEFAULT NULL,
	`TName` varchar(100) DEFAULT NULL,
	`TColor` varchar(20) DEFAULT NULL,
	`TSize` varchar(20) DEFAULT NULL,
	`TAttr` varchar(20) DEFAULT NULL,
	`TUnit1` varchar(20) DEFAULT NULL,
	`TAmt`  decimal(10,2) DEFAULT NULL,
	`TQuantity` decimal(10,2) DEFAULT NULL,
	`TPrice` decimal(10,3) DEFAULT NULL,
	`TDt` datetime DEFAULT NULL,
	`TRemark` varchar(150) DEFAULT NULL,
	`TIsDelete` tinyint(1) DEFAULT NULL,
	`TDtUpdate` datetime DEFAULT NULL,
	PRIMARY KEY (`Id`),
	UNIQUE KEY `T__TGuid` (`TGuid`),
	KEY `TName` (`TName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	 
_SQL;
	echo "<br/> $sql <br/>";
	echo $this->db->simpleQuery($sql);
//******************************************************************************************************


	$this->initTableData("barcodesrecord","b_$TUserId1",$TUserId1);
	$this->initTableData("barcodeticketrecord","t_$TUserId1",$TUserId1);
	$this->initTableData("barcodeskurecord","s_$TUserId1",$TUserId1);
	$this->initTableData("barcodeorderrecord","o_$TUserId1",$TUserId1);
	$this->initTableData("barcodeparameterrecord","p_$TUserId1",$TUserId1);
	$this->initTableData("barcodepayrecord","x_$TUserId1",$TUserId1);
	$this->initTableData("barcodepricerecord","bp_$TUserId1",$TUserId1);
	$sql="update o_$TUserId1 set TAmt=TQuantity*TPrice";
	echo $this->db->simpleQuery($sql);
	echo '<br/> ===================================== OK ===================================================';

}

private  function initTableData($oldtable,$table,$TUserId1){
	$db_name = $this->db->database; //"cicms"; //数据库名
    //$db_user = $this->db->username;
    //$db_pwd = $this->db->password;
	$fieldsSql="SELECT GROUP_CONCAT(COLUMN_NAME) as f FROM information_schema.COLUMNS WHERE table_name = '$oldtable' AND TABLE_SCHEMA = '$db_name' and COLUMN_NAME != 'Id' and COLUMN_NAME != 'TUserId1'";
	$query = $this->db->query($fieldsSql);
    $result = $query->getRowArray();
	$fields=$result['f'];
	//$sqlField="SELECT CONCAT(' select ',GROUP_CONCAT(COLUMN_NAME),' from ', TABLE_NAME,' ;') FROM information_schema.COLUMNS WHERE table_name = '$table' AND TABLE_SCHEMA = '$db_name' and COLUMN_NAME != 'Id'";
	if($oldtable=='barcodeorderrecord'){
		$sql="insert into $table (TGuid,TBarcodeGuid,TSkuGuid,TNo,TName,TStyle,TType,TSn,TColor,TSize,TEmpName,TSkuName,TPayGuid,TQuantity,TStatus,TPrice,TNum1,TNum2,TDt,TRemark,TUserId,TIsDelete,TDtUpdate) select o.TGuid,o.TBarcodeGuid,o.TSkuGuid,b.TNo as TNo,b.TName as TName,b.TStyle as TStyle,b.TType as TType,b.TSn as TSn,b.TColor as TColor,b.TSize as TSize,u.FirstName as TEmpName,s.TName as TSkuName,o.TPayGuid,o.TQuantity,o.TStatus,o.TPrice,o.TNum1,o.TNum2,o.TDt,o.TRemark,o.TUserId,o.TIsDelete,o.TDtUpdate FROM barcodeorderrecord o  LEFT JOIN (barcodesrecord b,barcodeskurecord s,openidtrackerrecord u) ON (o.TBarcodeGuid=b.TGuid and o.TSkuGuid=s.TGuid and o.TUserId=u.TOpenId) where o.TUserId1='$TUserId1' and o.TIsDelete=0;";
	}
	else{
		$sql="insert into $table ($fields) select $fields from $oldtable where TUserId1='$TUserId1' and TIsDelete=0;";
	}
	
	echo '<br/>';
	echo $sql;
	echo '<br/>';
	echo $this->db->simpleQuery($sql);
}


//公司资料To新版
public function cton()
  {
    $TableName = strtolower('CompanyRecord');
    $QuerySql = "select * from $TableName where TIsDelete=0";
	$page= $this->request->getGet('page');
	if($page>0)
		$QuerySql = "select * from $TableName where TIsDelete=0 limit " . $page*200 . "," . ($page+1)*200;
	$i=1;
    $namelist='';
	$query = $this->db->query($QuerySql);		
	$data = $query->getResult();
	$domain='';
	  try{
		$domain=$_SERVER["HTTP_HOST"];
	  }catch(Exception $e){
		
	  }

	$nd= $this->request->getGet('domain');
	if($nd=='')
		$nd='n.yunitapp.com';
	$url = 'https://'.$nd.'/api/upUC';
	echo ' <br/> New domain: '.$url.'<br/>';
	foreach ($data as $row) {		  	 
	  //$row->TIsPay = (bool)$row->TIsPay;		  
	  $postData = array(
		'type' => 1,//公司
		'domain' => $domain,
		'company' => $row
	  );
		
	  $namelist=$namelist."$i ". $row->TCorp. ";  ";
	  $i++;
	  
	  $ResT = $this->TyFun->curlPost($url, $postData);
	}
	
    $Res = array(
      'Code' => 0,
		'res'=>$ResT,
		'namelist'=>$namelist,
		'url'=>$url,
      'Msg' => 'Ok'
    );
    return json_encode($Res);
  }
  

//员工资料到新版
public function uton()
{
	$TableName = strtolower('openidtrackerrecord');
	$page= $this->request->getGet('page');
	$TUserId1= $this->request->getGet('TUserId1');	
	$QuerySql = "select * from $TableName where TIsDelete=0";
	
	echo "TUserId1: ".$TUserId1."<br/>";
	echo "page: ".$page."<br/>";
	if($TUserId1!='' && $TUserId1!=null)
		$QuerySql = "select * from $TableName where TIsDelete=0 and TUserId1='$TUserId1'";
	if($page>0)
		$QuerySql = "select * from $TableName where TIsDelete=0 limit " . $page*200 . "," . ($page+1)*200;
	echo $QuerySql;
	$namelist='';
	$query = $this->db->query($QuerySql);		
	$data = $query->getResult();
	$nd= $this->request->getGet('domain');
	if($nd=='')
		$nd='n.yunitapp.com';
	$url = 'https://'.$nd.'/api/upUC';
	echo ' <br/> New domain: '.$url.'<br/>';
	//$url = 'https://n.yunitapp.com/api/upUC';
	$i=1;
	foreach ($data as $row) {		  	 
	  $postData = array(
		'type' => 0,//员工
		'user' => $row
	  );
	  
	  $ResT = $this->TyFun->curlPost($url, $postData);
	  $namelist=$namelist."$i ". $row->FirstName. ";  ";
	  $i++;
	}
	$Res = array(
	  'Code' => 0,
	  'namelist'=>$namelist,
	  'res'=>$ResT,
	  'Msg' => 'Ok'
	);
	return json_encode($Res);
}

  

  //=========================================================================================
}
