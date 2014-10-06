<?php
/**
 * @version     2.5.1
 * @package     com_grid
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Tools JX <customers@toolsjx.com> - http://www.toolsjx.com
 */
defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );
require_once(JPATH_ADMINISTRATOR.'/components/com_grid/DefaultValues.php');

//@error_reporting(E_ALL  & ~E_NOTICE & ~E_DEPRECATED);
/**
 * Grid Model
 */
class GridsModelGrid extends JModelLegacy
{
	var $_id;
	var $_data;
	var $_conn;
	var $_extDbObj;
	var $_tbList;
	var $_colList;
	var $_gridColumns;

	var $_msg;

	var $_formData;

	/**
	 * Constructor that retrieves the ID from the request
	 *
	 * @access    public
	 * @return    void
	 */
	function __construct()
	{
		parent::__construct();

		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
	}

	/**
	 * Method to set the hello identifier
	 *
	 * @access    public
	 * @param    int Grid identifier
	 * @return    void
	 */
	function setId($id)
	{
		// Set id and wipe data
		$this->_id        = $id;
		$this->_data    = null;
		$this->_conn    = null;
		$this->_gridClumns  = null;
	}

	/**
	 * Method to get a hello
	 * @return object with data
	 */
	function &getData($adjust=true)
	{
		// Load the data
		$session = JFactory::getSession();
		if($session->has('grid') && $session->get('grid')){
			$this->_data = $session->get('grid');

		}
		elseif (empty( $this->_data )) {
			//echo 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb'.$this->_id;
			$query = ' SELECT * FROM #__grids '.
	                '  WHERE idGrid = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
			$this->getFields();
		}
		if (!$this->_data) {
			//echo 'aaaaaaaaaaaaaaaaaaaaaaaaaaaa';
			$this->_data = new stdClass();

		}else{
			if($adjust)
				$this->adjustDataFromDB();
		}
		if($adjust)
			$this->setDefaultValues();
		return $this->_data;
	}

	function &getFields(){
		$query = ' SELECT * FROM #__grid_columns '.
                '  WHERE idGrid = '.$this->_id.' ORDER BY `order` ASC';
		$this->_db->setQuery( $query );
		$this->_gridColumns = $this->_db->loadObjectList();
		return $this->_gridColumns;
	}

	function &getConn()
	{
		$session = JFactory::getSession();
		if($session->has('conn') && $session->get('conn')){
			$this->_conn = $session->get('conn');
			//echo "1";
		}
		elseif (empty( $this->_conn )) {
			$query = ' SELECT * FROM #__grid_conn '.
	                '  WHERE idGrid = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_conn = $this->_db->loadObject();
			//echo "2";
		}
		if (!$this->_conn) {
			$this->_conn = new stdClass();
			$this->_conn->idGrid = 0;
			$this->_conn->dbType = 'mysql';
			$this->_conn->dbHost = 'localhost';
			//echo "3";
			$_DB = $this->_conn->dbType;
		}
		return $this->_conn;
	}

	function &getTbList()	{
		$db;
		if(isset($this->_data->connection) && $this->_data->connection == 'other'){
			$data = array();
			$data['dbType']  = $this->_conn->dbType;
			$data['dbHost'] = $this->_conn->dbHost;
			$data['dbUser'] = $this->_conn->dbUser;
			$data['dbPass'] = $this->_conn->dbPass;
			$data['dbName'] = $this->_conn->dbName;
			$_DB = $this->_conn->dbType;
			$this->setExtDbObj($data);
			$db=$this->_extDbObj;
		}
		else{
			$db=$this->_db;
		}
		if( JError::isError($db)){
			$this->_tbList = null;
		}
		else{
			if($this->_conn->dbType == "oracle"){
				$query = "SELECT TNAME FROM TAB";
				$db->setQuery( $query );
				$this->_tbList=$db->loadColumn();
			}elseif($_DB =='mssql' || $_DB =='mssql2000' || $_DB =='sqlsrv')
            {
                $query="SELECT T.TABLE_NAME
                     FROM INFORMATION_SCHEMA.TABLES T
                     WHERE TABLE_TYPE IN ('BASE TABLE','VIEW')";
                $db->setQuery( $query );
                $this->_tbList=$db->loadColumn();
            }
			
			else
				$this->_tbList=$db->getTableList();
			//print_r($this->_tbList);
		}
		return $this->_tbList;

	}

	function getColList() {
		$db;
		$tbName = @$this->_data->tableName;
		if(isset($this->_data->connection) && $this->_data->connection == 'other'){
			$db=$this->_extDbObj;
		}
		else $db=$this->_db;

		if( JError::isError($db)){
			$this->_colList = null;
		}
		else if(isset($this->_tbList) && in_array($tbName, $this->_tbList)){
//			$_DB = $this->_conn->dbType;
//
//			$querry = "SHOW COLUMNS FROM `$tbName`";
//
//			if($_DB =='oracle')
//			{
//				$querry = "SELECT column_name
//                              FROM ALL_TAB_COLUMNS
//                              WHERE table_name = '".$tbName."'";
//			}
//			if($_DB =='mssql' || $_DB =='mssql2000' || $_DB =='sqlsrv')
//			{
//				$querry="SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.Columns WHERE TABLE_NAME = '$tbName'";
//			}
//			$db->setQuery( $querry );
			try {
				
				$this->_colList=array_keys($db->getTableColumns($tbName));
				//echo "<pre>";print_r($this->_colList);echo "</pre>";
			} catch (Exception $e) {
				print_r($e);
			}
			
			return $this->_colList;
		}
		else return array();
	}
	
	function getCount(){
		$db;
		if (isset($this->_extDbObj))$db=$this->_extDbObj;
		else $db = JFactory::getDbo();
		if(isset($this->_tbList) && in_array(@$this->_data->tableName, $this->_tbList)){
			$db->setQuery("SELECT COUNT(*) FROM " . $db->quoteName($this->_data->tableName));
			//$db->query();
			if($rows=$db->loadResult())
				return $rows;
		}
			return 0;
		
	}

	/**
	 * Method to store a record
	 *
	 * @access    public
	 * @return    boolean    True on success
	 */
	function store($saveSession = false)
	{

		$row = $this->getTable();
			
		$data = JRequest::get( 'post' );
			
			
			
		//adjust data
		$data = $this->adjustData($data);
		$data = $this->adjustDataBeforeSaving($data);
		//echo $data['columnNames'].'<br>'.$data['columnCaptions'].'aaa';

		//Nastavim default vrednosti za RecordJX
		if($data["typejx"]=="reco") {
			$data["paging"]=1;
			$data["nrRows"]=1;
			$data["nrPages"]=5;
			$data["cardsPerRow"]=1;
			$data["shAtrib"]=1;
			$data["cardBorder"]=1;
			//$data["graphConfig"]=json_encode($this->_data->width);
		}
			
			
		// Bind the form fields to the hello table
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			$this->_msg = '<B>bind error</B>';
			$this->_msg .= $this->_db->getErrorMsg();
			return false;
		}

		// Make sure the hello record is valid
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			$this->_msg = '<B>check error</B>';
			$this->_msg .= $this->_db->getErrorMsg();
			return false;
		}
			
		// Store the web link table to the database
		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg());
			$this->_msg = '<B>store error</B>';
			$this->_msg .= $this->_db->getErrorMsg();
			return false;
		}
		if ($data['idGrid']==0){
			$data['idGrid']=$this->_db->insertid();
		}

		if (!$this->storeColumns($data)){
			$this->setError($this->_db->getErrorMsg());
			$this->_msg ='<B>error storing columns</B>';
			$this->_msg .= $this->_db->getErrorMsg();
			return false;
		}
			
		//store connection settings if is set to 'other';
		if ($row->connection == 'other' ){


			if(!$this->storeConn($data)){
				return false;
			}
		}
		if(!$saveSession){
			$session = JFactory::getSession();
			if($session->has('grid')){
				$session->set('grid', false);
			}
			if($session->has('conn')){
				$session->set('conn', false);
			}
		}
		$this->setId($row->idGrid);
		//$this->saveForm($data);
		return true;
	}

	function storeConn($data){
		$row = $this->getTable('Conn');


		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			if ($row->idGrid=='') $row->idGrid = $this->_db->insertid();
			echo '<B>bind error conn</B>';
			echo $this->_db->getErrorMsg();
			return false;
		}
		// Make sure the hello record is valid
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			echo '<B>check error conn</B>';
			echo $this->_db->getErrorMsg();
			return false;
		}
			
		//delete the existing connection for this grid
		//	    $this->_db->setQuery("DELETE FROM #__grid_conn where idGrid = $row->idGrid");
		//		$this->_db->query();
			
		// Store the web link table to the database
		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg());
			echo '<B>stor error conn</B>';
			echo $this->_db->getErrorMsg();
			return false;
		}
			
		return true;
	}

	/**
	 * Method to delete record(s)
	 *
	 * @access    public
	 * @return    boolean    True on success
	 */
	function delete()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$row =& $this->getTable();
			

		foreach($cids as $cid) {
			$query = "delete from #__grid_conn where idGrid='$cid'";
			$this->_db->Execute($query);
			if (!$row->delete( $cid )) {
				echo 'br br';
				//$this->setError( $row->getErrorMsg() );
				return false;
			}
		}
			
		return true;
	}

	function dbcheck(){
		$data = JRequest::get( 'post' );
		$data = $this->adjustData($data);
		$this->_id = $data['idGrid'];
		$grid = new stdClass();

		foreach ($data as $name => $value) {
			$grid->$name = $value;
		}

		if($data['connection'] == 'other'){
			$this->setExtDbObj($data);
			$conn = new stdClass();
			$conn->dbType = $data['dbType']; // Database driver name
			$conn->dbHost     = $data['dbHost'];    // Database host name
			$conn->dbUser     = $data['dbUser'];      // User for database authentication
			$conn->dbPass 		= $data['dbPass'];   // Password for database authentication
			$conn->dbName 		= $data['dbName'];
			$_DB = $data['dbType'];
			$conn->idConn 		= $data['idConn'];
		}

		$session = JFactory::getSession();
		$session->set('grid', $grid);
		$session->set('conn', $conn);

	}

	function adjustData(&$data){

		//table name
		if ($data['tableCaption'] == ''){
			$data['tableCaption'] = $data['tableName'];
		}

		//where condition
		$data['whereCond']= str_replace('{','<',$data['whereCond']);
		$data['whereCond']= str_replace('}','>',$data['whereCond']);
			
		return $data;
	}

	function adjustDataFromDB(){
		//column captions, names, links, width
		//        $colCaptions = explode('|', $this->_data->columnCaptions);
		//        $colNames = explode('|', $this->_data->columnNames);
		//        $links = explode('|', $this->_data->link);
		//        $widths = explode('|', $this->_data->columnWidth);
		$colMap = array();
		$linkMap = array();
		$linkTypes = array();
		$customLink = array();
		$widthMap = array();
		$showLabel = array();
		$id=array();
		//zato da poljem v javascript, da nariÅ¡e graf (nastavitve)
		$moreConfig = array();
		//pri recordJX, Ä�e dovoliÅ¡ urejanje ali ne
		$edit = array();

		if(count($this->_gridColumns)){	
			foreach($this->_gridColumns as $column){
				$colMap[$column->columnName]=$column->columnLabel;
				$linkMap[$column->columnName]=$column->linkColumn;
				$linkTypes[$column->columnName]=$column->linkType;
				$customLink[$column->columnName] = $column->customLink;
				$widthMap[$column->columnName]=$column->columnWidth;
				$showLabel[$column->columnName]=$column->displayLabel;
				$id[$column->columnName]=$column->id;
				$moreConfig[$column->columnName]=$column->moreConfig;
			}
		}
		//$this->_data = new stdClass();
		$this->_data->colMap = $colMap;
		$this->_data->linkMap = $linkMap;
		$this->_data->linkTypes = $linkTypes;
		$this->_data->customLink = $customLink;
		$this->_data->columnWidth = $widthMap;
		$this->_data->showLabel= $showLabel;
		$this->_data->id=$id;
		$this->_data->moreConfig=$moreConfig;


		//default order
		$this->_data->default_order = @explode('|',$this->_data->default_order);
		if (count($this->_data->default_order)>1) {
			$this->_data->sortField = $this->_data->default_order[0];
			$this->_data->sortDirection = $this->_data->default_order[1];
		}else{
			$this->_data->sortField = "";
			$this->_data->sortDirection = "";
		}	
		//second Order By
		@$arr=explode('|',$this->_data->secOrder);
		@$this->_data->secOrder=$arr[0];
		if(count($arr)>1){
			$this->_data->secOrderRange=$arr[2];
			$this->_data->secOrderDirection=$arr[1];
		}else{
			$this->_data->secOrderRange="";
			$this->_data->secOrderDirection="";
		}
		//search form
		$arr=@explode("\n",$this->_data->searchF);
		$this->_data->searchF = array();
		if(is_array($arr)){
			foreach($arr as $param){
				$arr2=@explode('=', $param);
				$this->_data->searchF[trim($arr2[0])]=@trim($arr2[1]);
				//echo trim($arr2[0]).'=>'.trim($arr2[1]).'<br>';
			}
		}
		//echo "submit first".$this->_data->searchF['submitFirst'];

		//advanced filter
		if(isset($this->_data->filter)){
			$arr=explode('|', $this->_data->filter);
			$this->_data->filter=$arr[0];
			$this->_data->filterOperator=$arr[1];
		}
	}

	function adjustDataBeforeSaving(&$data){
		//column captions, names, links, width
		//		$columns='';
		//		$captions='';
		//		$links='';
		//		$widths='';
		//		foreach($data['colMap'] as $col => $caption){
		//			$columns.=$col.'|';
		//			$captions.=$caption.'|';
		//			if($data['linkTypes'][$col] == '3')$data['linkTypes'][$col]=$data['customLink'][$col];
		//			$links.=$data['linkTypes'][$col].'|'.$data['linkMap'][$col].'|';
		//			$widths.=$data['columnWidth'][$col].'|';
		//		}
		//		$data['columnNames']=substr($columns, 0, strlen($columns)-1);
		//		$data['columnCaptions']=substr($captions, 0, strlen($captions)-1);
		//		$data['link']=substr($links, 0, strlen($links)-1);
		//		$data['columnWidth']=substr($widths, 0, strlen($widths)-1);

		//default order
		if (!isset($data['sortDirection']))$data['sortDirection']="";
		if (!isset($data['sortField']))$data['sortField']="";
		$data['default_order'] = $data['sortField'].'|'.$data['sortDirection'];

		//Default values for graph
		if(!isset($data["size_x"]) || $data["size_x"]=="")	$data["size_x"]=500;
		if(!isset($data["size_y"]) || $data["size_y"]=="")	$data["size_y"]=350;
		
		if($data["method"]==0){ // če ni fixed
		if(!isset($data["sizeData"]) || $data["sizeData"]=="" || $data["sizeData"]==0) $data["sizeData"]=20;
		elseif($data["sizeData"] > $data["sizeDataMax"]) {
			$data["sizeData"]=$data["sizeDataMax"];
				JFactory::getApplication()->enqueueMessage( JText::_( 'You have set "Size of shown data" over the maximum, setting is set automaticaly to maximum.' ), 'Notice' );
		}
		}
		
		if(!isset($data["refresh"]) || $data["refresh"]=="" || $data["refresh"]==0) $data["refresh"]=1;

		switch($data["typejx"]) {
			case "grap" :
				//Shranjevanje vrednosti nastavitev za graf
				$data['graphConfig']=json_encode(array("size"=>array("x"=>$data["size_x"], "y"=>$data["size_y"]), "method"=>$data["method"],
		"sizeData"=>$data["sizeData"], "refresh"=>$data["refresh"], "useForX"=>$data["useForX"],
		"gridBgC"=>$data["gridBgC"], "showGrid"=>($data["showGrid"]=="1"?"1":"0"), "gridTextC"=>$data["gridTextC"], 
		"showLeg"=>($data["showLeg"]=="1"?"1":"0"), "legBgC"=>$data["legBgC"], "legBgO"=>$data["legBgO"]/100,
		"legPos"=>$data["legPos"], "legNoCol"=>$data["legNoCol"], "gridTicMar"=>$data["gridTicMar"], "gridTicHei"=>$data["gridTicHei"], 
		"gridTicAng"=>$data["gridTicAng"]));
				break;
			case "reco" :
				$data["graphConfig"]=json_encode(array("width"=>$data["width"]));
				break;
			default: $data["graphConfig"]="";
		}

		//search form
		// dobimo searchF_option = 1 v bazo pa gre "option = 1\n" kot string
		$str="";
		foreach ($data as $key => $param){
			if(strlen($key)> 8)
			if (substr_compare($key, "searchF_", 0, 8)==0)
			$str.=substr($key, 8)."=".$param."\n";
		}
		$data['searchF']=$str;

		//second order by
		if(!isset($data['secOrderDirection']))$data['secOrderDirection']="";
		if(!isset($data['secOrderRange']))$data['secOrderRange']="";
		$data['secOrder']= $data['secOrder'].'|'.$data['secOrderDirection'].'|'.$data['secOrderRange'];

		//advanced filter
		if(!isset($data['filter']))$data['filter']="";
		if(!isset($data['filterOperator']))$data['filterOperator']="";
		$data['filter']=$data['filter'].'|'.$data['filterOperator'];
			
		return $data;
	}

	//set external database object
	function setExtDbObj($data){
		$option = array(); //prevent problems
		$option['driver']   = $data['dbType'];            // Database driver name
		$option['host']     = $data['dbHost'];    // Database host name
		$option['user']     = $data['dbUser'];      // User for database authentication
		$option['password'] = $data['dbPass'];   // Password for database authentication
		$option['database'] = $data['dbName'];      // Database name
		$option['prefix']   = '';             // Database prefix (may be empty)
		
		$db = JDatabase::getInstance( $option );
		$this->_msg = $db->getErrorMsg(true);

		//else $this->_msg = 'Database Connection is OK.';
		/*if ($db->getErrorNum() > 0) {
			JError::raiseError(500, 'JDatabase::getInstance: Could not connect to database <br />');
			$msg = 'Could not connect to database';
			}*/
		$this->_extDbObj=$db;

	}

	/**
	 *
	 * Nastavi privzete nastavitve, Ä�e le te Å¾e niso nastavljene.
	 */

	function setDefaultValues(){
		$DV = new DefaultValues();
		$class_vars = get_class_vars(get_class($DV));
			
		foreach ($class_vars as $name => $value) {
			if (!isset($this->_data->$name))
			$this->_data->$name = $value;

			if ($name == "searchF"){ //nastavitve za search form
				if (count($this->_data->searchF)<=1){
					$this->_data->searchF=$DV->searchF;
				}
			}

		}
		//print_r($this->_data->searchF);
	}

	function storeColumns(&$data){

		$query = "DELETE FROM #__grid_columns where idGrid = $data[idGrid]";
		$this->_db->setQuery($query);
		if(!$this->_db->query())
		return false;

		$column = $this->getTable('gridcolumns');
			
		$k=1;
		foreach($data['colMap'] as $col => $caption){
			$column->id = null;
			$column->idGrid = $data['idGrid'];
			$column->order = $k;
			$k++;
			$column->columnName = $col;
			$column->columnLabel = $caption;
			$column->linkType = $data['linkTypes'][$col];
			$column->linkColumn = $data['linkMap'][$col];
			if(isset($data['customLink']))	$column->customLink = $data['customLink'][$col];
			$column->columnWidth = $data['columnWidth'][$col];
			$column->columnAlign = null;
			if(isset($data['showLabelCbx'][$col]))$column->displayLabel = $data['showLabelCbx'][$col];
			else $column->displayLabel = false;
			//print_r($data['selectFltrCbx']);
			if(is_array($data['selectFltrCbx']))
			$column->displayFilter = in_array($col, $data['selectFltrCbx'])?1:0;
			if(isset($data['fltrType'][$col]))$column->filterType = $data['fltrType'][$col];
			if(!isset($data['moreConfig'][$col]))$data['moreConfig'][$col] = "";
			if(isset($data['edit']) && $data['edit']!=null)
				$column->moreConfig = json_encode(array("edit" => $data['edit'][$col]=="on"));
			else
				$column->moreConfig = ($data['moreConfig'][$col]==""?"Line|0|".strtoupper(dechex(rand(0,10000000)))."|0":$data['moreConfig'][$col]);

			if (!$column->store()) {
				$this->setError($this->_db->getErrorMsg());
				echo '<B>store error</B>';
				echo $this->_db->getErrorMsg();
				return false;
			}

		}
		return true;
	}

	function duplicate(){
		$table=& $this->getTable();
		$table->bind($this->getData(false));
		$table->idGrid=null;

		$table->tableCaption.="_copy";
		//echo "<pre>".print_r($table, true)."</pre>";

		// Store the table to the database
		if (!$table->store()) {
			$this->setError($this->_db->getErrorMsg());
			$this->_msg = '<B>store error</B>';
			$this->_msg .= $this->_db->getErrorMsg();
			return false;
		}else{
			$id=$this->_db->insertid();
			$table=& $this->getTable('conn');
			$table->bind($this->getConn());
			$table->idConn=null;
			$table->idGrid=$id;
			if (!$table->store()) {
				$this->setError($this->_db->getErrorMsg());
				$this->_msg = '<B>store error</B>';
				$this->_msg .= $this->_db->getErrorMsg();
				return false;
			}else{

				foreach($this->getFields() as $field){
					//echo $field->columnName;
					$table = & $this->getTable('gridcolumns');
					$table->bind($field);
					$table->id=null;
					$table->idGrid=$id;
					if (!$table->store()) {
						$this->setError($this->_db->getErrorMsg());
						$this->_msg = '<B>store error</B>';
						$this->_msg .= $this->_db->getErrorMsg();
						return false;
					}
				}
			}

		}

			
	}

	function getDataForGraph() {
		$doc = JFactory::getDocument();
		$doc->addScript(JURI::root()."components/com_grid/js/flot/jquery.js");
		$doc->addScript(JURI::root()."components/com_grid/js/flot/jquery.flot.js");
		$doc->addScript(JURI::root()."components/com_grid/js/graph.js");
		
		
		if(isset($this->_data->graphConfig)){
			echo "<div id=\"placeForGraph\" style=\"width:500px; height:270px;\"></div>";
			$js="\n$(function () {";
			//$js.="alert('jabadabadu');";
			$js.="\n$.post('".JURI::root()."index.php?option=com_grid&gid=$this->_id&ajax=1', {table:'".$this->_data->tableName.
			"',\ncaptions: '".json_encode($this->_data->colMap)."', moreConfig: '".json_encode($this->_data->moreConfig).
			"',\ngraphConfig: '".$this->_data->graphConfig."', step:0, whereCond: '".htmlspecialchars($this->_data->whereCond, ENT_QUOTES).
			"',\norderBy: '".json_encode($this->_data->default_order)."'}";
			$js.=",\nfunction(data){";
			$js.="\nif(data[0]==\"!\") {showAlert(data, 'warning');";
			//$js.="alert(data);";
			$js.="\n} else {";
			//$js.="alert(data);";
			$js.="\nvar tmp=data.split(\"|\");";
			$js.="\ndata=JSON.parse(tmp[0]);";
			$js.="\nvar options=JSON.parse(tmp[1]);";
			//$js.="alert(data[0].label);";
			$js.="\n$.plot($('#placeForGraph'), data, options);";
			$js.="\n}";
			$js.="\n});";
			$js.="\n});";
			/*if($this->_data->graphConfig->refresh!="0") {
				$js="drawGraphDynamic('".JURI::root()."components/com_grid/layouts/DataGraph.php',
				'".$this->_data->tableName."', '".json_encode($this->_data->colMap)."', '".json_encode($this->_data->moreConfig)."',
				'".$this->_data->graphConfig."', '#placeForGraph', '".$this->_data->graphConfig->refresh."', 'true');";
				} else {
				$js="drawGraphFix('".JURI::root()."components/com_grid/layouts/DataGraph.php',
				'".$this->_data->tableName."', '".json_encode($this->_data->colMap)."', '".json_encode($this->_data->moreConfig)."',
				'".$this->_data->graphConfig."', '#placeForGraph', 'true');";
				}*/
	
			$doc->addScriptDeclaration($js);
		} else echo "<div id=\"placeForGraph\" style=\"width:500px; height:270px;\">Edit settings and click the Save button on the top.</div>";
	}

}