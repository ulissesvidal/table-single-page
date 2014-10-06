<?php
#-------------------------------------------------------------------------------
# Copyright (c) 2009 Gomilsek-informatika.
# All rights reserved. This program and the accompanying materials
# are made available under the terms of the GNU Public License v2.0
# which accompanies this distribution, and is available at
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
# 
# Contributors:
# 	Gomilsek-informatika  (initial API and implementation)
# Contact:
# 	customers@toolsjx.com
#-------------------------------------------------------------------------------
//allow acces only trough joomla
defined( '_JEXEC' ) or die( 'Restricted access' );

class GridConfigManager
{
	var $_ID = 0;
	var $typejx;
	var $tableCaption;
	var $connection;
	var $tableName;
	var $columns = array();
	var $columnNames; //array of column names
	var $columnCaptions;
	var $link; // array: link type, link column name, link type, link column name,...
	var $columnWidth; //array of column widths
	var $cardsPerRow;
	var $shAtrib;
	var $cardBorder;
		
	var $_idConn;
	var $_dbtype;
	var $_dbhost;
	var $_dbuser;
	var $_dbpass;
	var $_dbname;
	
	var $_dbo;
	
	# headers: 0 = no column names displayed, 1 = column names are displayed
	# rows to display: number of rows to return, and whether to enable pages.  e.g. 20, would list 20 results.  20|1 would list 20 + other pages.  blank or 0/default is all results, and no pages (max 999999 results).
	# default order = order of results, field by desc/asc.  e.g. FIELDNAME|DESC
	# edit = position|type, type 0 = no, 1 = radio, 2 = checkbox. position 0 = default, on left, 1 = on right
	# query = 
	# header = places input (html or text) after header names
	# footer = places input (html or text) after rows, before page links.
	# button = places input (html or text) in bottom left, instead of 'Page 1 of 2' text.
	#settings
	
	var $showTitle; //show table title
	
	var $headers = "1";
	var $nrRows = 10; // number of rows per page
	var $paging = false;
	var $nrPages = 5;
	var $default_order;
	
	var $edit = "0|0";
	var $whereCond; //custom mySQL query.  after the WHERE.  e.g. FIELDNAME = 'keyword', the WHERE is inserted automatically, incase an AND is required
	var $header;
	var $footer;
	
	#show search form
	var $searchF; //array 
	var $caseSensitive = true; //eneble case sensitive search
	
	#appearance
	var $nrRecSelect; //show Records per page selection list
	var $nrRecords = false; // show number of records
	var $lineNr = false;
	var $showtime = false;
	var $poweredBy = true;
	var $lineHeight;
	
	#colors
	var $hColor = "ffffff"; # header color
	var $sColor = "eeeeee"; # selected header color
	var $rColor1 = "ffffff"; # row color 1
	var $rColor2 = "eeeeee"; # row color 2
	var $row_color_selected = "FBFBC4"; # row color
	var	$rColorMO = "F5F5BA"; # row color on mouse over
	var $PBColor = "f7f7f7";  # page box color
	
	#advanced
	var $secOrder; //second oder by: field|direction|range; range: 0 - apply everywhere, 1 - apply on default sort 
	var $graphConfig; //refresh|size|pointsOnGraph|legend|title
	var $execPlugins;
		
	var $error = false;
	#images	 
	//var	$url_images = "components/com_grid/images";
	
	function __construct($_ID)
	{
		//$_ID je oblike '[id configuracije]_[karkoli]'
		$idArr = array();
		$idArr = explode('_', $_ID);
		$this->_ID=$idArr[0];
		$this->getDBO();
		$this->setGridVarsFromDB();
		$this->setConnVarsFromDB();
		$this->setColumnVarsFromDB();
		$this->adjustData();		
	}
	// php4 compatibility
	function GridConfigManager($id){
	    $args = func_get_args();
	    if (method_exists($this, '__destruct')) {
	    	register_shutdown_function(array(&$this,'__destruct'));
	    }
	    call_user_func_array(array(&$this, '__construct'), $args);
    }
	
		
	function getDBO(){
		 $this->_dbo = JFactory::getDBO();		
	}
	
	function setGridVarsFromDB(){
		$query = "SELECT * FROM `#__grids` WHERE idGrid = '$this->_ID'";
		$this->_dbo->setQuery($query);
		if($row = $this->_dbo->loadObject()){
			
			//assign data from db to this
			$object_vars=get_object_vars($row);
			foreach ($object_vars as $name => $value) {
				if(property_exists($this,$name)){
					$this->$name = $value;
			//		echo $name.": DB => ".$value." CM => ".$this->$name."<br>";
				}
			}
			//rows
			if($this->nrRows <= 0) $this->nrRows = 999999;
			if($this->cardsPerRow <= 0) $this->cardsPerRow = 3;
		}
		else{
			$this->error = "Tools JX Error: Definition with ID ".$this->_ID." does not exist.";
			echo $this->error;
		}
		
		
		
		
	}
	
	function setConnVarsFromDB(){
		$query = "SELECT * FROM `#__grid_conn` WHERE idGrid = '$this->_ID'";
		$this->_dbo->setQuery($query);
		$row = $this->_dbo->loadObject();
		
		if(isset($row)){
			$this->_dbtype = $row->dbType;
			$this->_dbhost = $row->dbHost;
			$this->_dbuser = $row->dbUser;
			$this->_dbpass = $row->dbPass;
			$this->_dbname = $row->dbName;
		}
	}
	
	function setColumnVarsFromDB(){
		 $query = ' SELECT * FROM #__grid_columns '.
	                '  WHERE idGrid = '.$this->_ID .' ORDER BY `order` ASC';
		$this->_dbo->setQuery($query);
		$this->columns = $this->_dbo->loadObjectList();

		
	}
	
	function adjustData(){
		$this->columnNames = array();
		$this->columnWidth = array();
		foreach($this->columns as $column){
			$this->columnNames[] = $column->columnName;
			$this->columnWidth[] = $column->columnWidth;
			
		}
		

		//$this->columnCaptions = explode("|",$this->columnCaptions);
		//$this->link = explode("|",$this->link);
		$this->default_order = explode("|",$this->default_order);
		//$this->columnWidth = explode("|",$this->columnWidth);
		
		//search form
		$arr=@explode("\n",$this->searchF);
		$this->searchF = array();
		foreach($arr as $param){
			$arr2=@explode('=', $param);
			$this->searchF[trim($arr2[0])]=@trim($arr2[1]);
			//echo trim($arr2[0]).trim($arr2[1]);
		}
		#TODO vse podatke iz baze se prilagodi tukaj
	}
	
}
