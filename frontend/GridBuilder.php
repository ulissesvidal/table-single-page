<?php
/**
 * @version     2.5.1
 * @package     com_grid
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Tools JX <customers@toolsjx.com> - http://www.toolsjx.com
 */

//allow acces only trough joomla
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once(JPATH_ROOT.'/components/com_grid/GridConfigManager.php');
if(file_exists(JPATH_ROOT.'/components/com_grid/layouts/GridView.php'))
require_once(JPATH_ROOT.'/components/com_grid/layouts/GridView.php');
if(file_exists(JPATH_ROOT.'/components/com_grid/layouts/CardView.php'))
require_once(JPATH_ROOT.'/components/com_grid/layouts/CardView.php');
if(file_exists(JPATH_ROOT.'/components/com_grid/layouts/GraphView.php'))
require_once(JPATH_ROOT.'/components/com_grid/layouts/GraphView.php');
if(file_exists(JPATH_ROOT.'/components/com_grid/layouts/RecordView.php'))
require_once(JPATH_ROOT.'/components/com_grid/layouts/RecordView.php');

//load language

$language = JFactory::getLanguage();
$language->load('com_grid', JPATH_SITE);
$language->load('com_grid', JPATH_ADMINISTRATOR);

/**
 * 
 * @property GridConfigManager $_config  
 * @property JDatabase dbo 
 */
class GridBuilder{
	var $_ID= null;
	var $_config;

	var $dbo;
	var $_data;
	var $rows;  //number of all records
	var $queryRows; //number of records displayed on page - lower than $rpp only ol last page

	var $parentCont = false; //parent content data

	var $colList; //query columns list
	var $searchQuery;

	var $starttime;

	
	var $rpp; //rows per page
	var $queryStart; // limit offset
	var $queryLimit; // number of records in query


	var $orderBy = "";//order by field name
	var $direction = ""; //order direction
	var $page = ""; // records page
	var $searchStr = ""; //search string
	var $searchStrUnescaped = "";
	
	var $advSearchOption = "default"; //advanced search option
	var $searchField = "";
	var $isAjax = "";

	var $emptyForm = true;
	
	var $debug = false;
	

	function __construct ($id, &$config){
		$this->starttime = microtime(true);
		//$this->_ID je oblike '[id configuracije]_[karkoli]'
		$this->_ID=$id;
		//$this->setConfig($id);
		$this->_config = &$config;
		$this->setUserDefinedVars();
		$this->setDBO();
		$this->setUserDefinedVars();		
		$session = JFactory::getSession();
		if($session->has($id))
		$this->parentCont = $session->get($id);
		
		if($this->_config->typejx != 'grap'){ // TODO najdi lepšo rešitev
			if(!isset($this->_config->searchF['submitFirst']) || $this->_config->searchF['submitFirst']!=1 || "$this->isAjax"=="1"){
				// display data only after search submit
				$this->buildColumnsLists();
				if (isset($this->_config->searchF['submitFirst']) && $this->_config->searchF['submitFirst']==1 && $this->advSearchOption == 'exact' && $this->emptyForm){
					//ce naj se podatki prikazejo po vpisu iskalnega niza, ce je izbran exaktno iskanje in ce je forma prazna ne nalozi podatkov
				}
				else{
					$this->beforeQuery();
					$query = $this->buildQuery();
					$this->loadData($query);				
				}
				
			} 
		}
	}
	
	//php4 compatibility
	function GridBuilder($id, &$config){
		$args = func_get_args();
		if (method_exists($this, '__destruct')) {
			register_shutdown_function (array(&$this,'__destruct'));
		}
		call_user_func_array(array(&$this, '__construct'), $args);
	}


	/*function setConfig($id){
		//$this->_ID je oblike '[id configuracije]_[karkoli]'
		$idArr = array();
		$idArr = explode('_', $id);
		$this->_config = new GridConfigManager($idArr[0]);
		}*/

//DODAL SEM htmlentities();

	function setUserDefinedVars(){
		if(isset($_GET['rpp'])){
			$this->rpp=intval($_GET['rpp']);			
		}	
		else {
			$this->rpp=$this->_config->nrRows;
		}
		
		if(isset($_GET['aso']))	$this->advSearchOption=htmlentities($this->mysql_escape_mimic($_GET['aso']));
		else $this->advSearchOption=@$this->_config->searchF['aso'];
		//echo	$this->advSearchOption;	

		if (isset($_GET["o_b"])) $this->orderBy = htmlspecialchars($this->mysql_escape_mimic($_GET["o_b"]));
		else $this->orderBy = $this->_config->default_order[0];

		if (isset($_GET["o_d"])) $this->direction = htmlentities($this->mysql_escape_mimic($_GET["o_d"]));
		else $this->direction = $this->_config->default_order[1];

		if (isset($_GET["p"]))
			$this->page = intval($_GET["p"]);
		
		if (isset($_GET["data_search"])){
			$this->searchStr = htmlspecialchars($this->mysql_escape_mimic($_GET["data_search"]));
			$this->searchStrUnescaped = htmlspecialchars($_GET["data_search"]);
			if($this->_config->searchF['advanced']==1){
				//advanced filters
				//echo $this->searchStr;
				$arr=explode("|", $this->searchStr);
				$this->searchStr=array();
				for($i=0;$i<count($arr)/2;$i++){
					$name=$arr[$i*2];
					$string=@$arr[$i*2+1];
					$this->searchStr["$name"]=$string;
					//echo "<br>".$string;
				}
			}
		}	
		if (isset($_GET["s_f"])){
			
				$this->searchField = htmlspecialchars($this->mysql_escape_mimic($_GET["s_f"]));
		}
		if (isset($_GET["ajax"]))
			$this->isAjax = intval($_GET["ajax"]);

	}

	function setDBO(){
		if($this->_config->connection == 'joomla'){
			$this->dbo = JFactory::getDBO();
		}
		else{
			$option = array(); //prevent problems
			$option['driver']   = $this->_config->_dbtype;            // Database driver name
			$option['host']     = $this->_config->_dbhost;    // Database host name
			$option['user']     = $this->_config->_dbuser;     // User for database authentication
			$option['password'] = $this->_config->_dbpass;   // Password for database authentication
			$option['database'] = $this->_config->_dbname;      // Database name
			$option['prefix']   = '';             // Database prefix (may be empty)
			$_DB=$this->_config->_dbtype;
			$this->dbo = JDatabase::getInstance( $option );
			if ( JError::isError($this->dbo) ) {
				jexit('Database Error: ' . $this->dbo->toString() );
			}

		}
	}

	function loadData($query){
		//echo $query;
		$this->dbo->setQuery($query);
		$this->_data = $this->dbo->loadAssocList();
		$this->queryRows = count($this->_data);
		if ($this->debug){
      echo "<br>getAffectedRows: ".$this->dbo->getAffectedRows();
      echo "<br>count: ".count($this->_data);
    }
	}

	function getTablePrimareyKey() {
		$this->dbo->setQuery("SELECT COLUMN_NAME FROM  information_schema.`COLUMNS` ".
				"WHERE TABLE_NAME='".$this->_config->tableName."' AND COLUMN_KEY='PRI'");
		return $this->dbo->loadResult();
	}
	
	function buildColumnsLists(){

		$query_columns = $this->_config->columnNames;
		//Dodal sem da najde primary key ($this->_config->tablePK) tabele, ker rabim pri RecordJX za urejanje (UPDATE ... WHERE ...)
		// ne dela za oracle in mssql... bo treba re�it --toma�
//		$this->_config->tablePK=$this->getTablePrimareyKey();
//		if(!in_array($this->_config->tablePK, $query_columns)) 
//			array_push($query_columns, $this->_config->tablePK);
		
		$conf_columns_count = count($this->_config->columns);

		if ($conf_columns_count > 0) {
				
			//links
			foreach ($this->_config->columns as $column) {
				//if type isn't 0 and column is not already in array

				if($column->linkType!=0 && !in_array($column->linkColumn, $query_columns))
				$query_columns[] = $column->linkColumn;
			}

			$columns_list = "";
			$search_query = "";
				
			for ($i = 0; $i < count($query_columns); $i++) {
				
				//set search string for this column
				$search_string;
				if(isset($this->_config->searchF['advanced']) && $this->_config->searchF['advanced']==1 && is_array($this->searchStr)){
					//advanced filters
					$search_string = $this->searchStr[$query_columns[$i]];
				}
				else $search_string = $this->searchStr;
				
				if($search_string!="")$this->emptyForm = false;
				
				if ("$i"!="0") {
					//except for the first time
					$columns_list .= ", ";
				}
				
				if ("$this->searchField"=="0" || (isset($this->_config->searchF['advanced']) && $this->_config->searchF['advanced']==1)) {
					
					if ("$search_string"!="" and trim($search_query)!="") {
						
							if($this->_config->searchF['advanced']==1 && $this->_config->searchF['operator']==1){
								
								$search_query .= "	AND ";
							}else{
								$search_query .= "	OR ";
							}
							
						}
					}
				
				
				
					
				$columns_list .= $this->dbo->quoteName($query_columns[$i]);

				if ("$this->searchField"=="0" || "$this->searchField"=="$query_columns[$i]"
					|| (isset($this->_config->searchF['advanced']) && ($this->_config->searchF['advanced']==1)
					&& "$search_string"!="")) {
					
					if ("$search_string"!="") {
						if($this->_config->caseSensitive){
							switch ($this->advSearchOption){
								case 'exact':
									$search_query .= $this->dbo->quoteName($query_columns[$i])." = '$search_string'";
									break;	
								case 'begins':
									$search_query .= $this->dbo->quoteName($query_columns[$i])." LIKE '$search_string%'";
									break;								
								default:
									$search_query .= $this->dbo->quoteName($query_columns[$i])." LIKE '%$search_string%'";
							}
							
						}
						else{
							//echo	$this->advSearchOption;				
							switch ($this->advSearchOption){
								case 'exact':
									$search_query .= "UPPER(".$this->dbo->quoteName($query_columns[$i]).") = '".strtoupper($search_string)."'";
									break;	
								case 'begins':
									$search_query .= "UPPER(".$this->dbo->quoteName($query_columns[$i]).") LIKE ('".strtoupper($search_string)."%')";
									break;								
								default:
									$search_query .= "UPPER(".$this->dbo->quoteName($query_columns[$i]).") LIKE ('%".strtoupper($search_string)."%')";
							}
							
						}
					}
				}

				$this->colList = $columns_list;
				$this->searchQuery = $search_query;
			}
		} else {
			return "No mySQL columns specified for data listings.";
		}
	}

	function buildQuery()
	{
		if ($this->_config->tableName=="") return "No mySQL table specified for data listings.";

		//orderby
		$order='';
		if ("$this->orderBy"!="") {
			$order .= " ORDER BY ".$this->dbo->quoteName($this->orderBy)." ".$this->direction;
				
			//secondary order by
			$secOrder=explode('|', $this->_config->secOrder);
			if($secOrder[0]!= '0'){					//if secondary orderby is defined
				if($this->orderBy != $secOrder[0]){ //if is not same as current order
					if($secOrder[2] == 1){						// if only on default sort is set
						if($this->_config->default_order[0] == $this->orderBy)
						$order.=','.$this->dbo->quoteName($secOrder[0]).' '.$secOrder[1]; // if current sort == default sort
					}
					else $order.=','.$this->dbo->quoteName($secOrder[0]).' '.$secOrder[1];
				}}
		}

		//where
		$conf_query = $this->translateWhereCond($this->_config->whereCond);
		if (("$conf_query"!="") && ($this->searchQuery!="")) {
			$where = " WHERE (".$conf_query.") AND ( $this->searchQuery )";
		} elseif ("$conf_query"!="") {
			$where = " WHERE $conf_query";
		} elseif ("$this->searchQuery"!="") {
			$where = " WHERE $this->searchQuery";
		}else $where = "";

		if ($this->page=="")	$this->page = "0";
		$count = $this->count($where);

		$table_name = $this->dbo->quoteName($this->_config->tableName);
		//query
		//echo ' before if $this->dbo->name: '.$this->dbo->name; 
		if($this->dbo->name=='mysql' || $this->dbo->name=='mysqli')
		{
			$query = "SELECT ".$this->colList;
				
			$query .= " FROM ".$table_name."";

			//limit
			$limit = " LIMIT $this->queryStart, ".$this->queryLimit;

			$query = $query.$where.$order.$limit;

			//echo "MySQL frontend:\n".$query;
		}
		else if($this->_config->_dbtype == 'mssql' || $this->_config->_dbtype == 'sqlsrv')
		{

			if("$where"=="")
			{
				$query = "
			SELECT ".$this->colList." 
			FROM (
  				SELECT
    			ROW_NUMBER() OVER (".$order.") AS rownum,
    			".$this->colList."
  				FROM ".$table_name."
				) AS foo
			WHERE rownum > ".$this->queryStart." AND rownum <= (".$this->queryLimit."+".$this->queryStart.")";
			}
			else
			{
				$query = "
			SELECT ".$this->colList." 
			FROM (
  				SELECT
    			ROW_NUMBER() OVER (".$order.") AS rownum,
    			".$this->colList."
  				FROM ".$table_name.$where."
				) AS foo
			WHERE rownum > ".$this->queryStart." AND rownum <= (".$this->queryLimit."+".$this->queryStart.")";
			}

			//echo "SQL frontend:\n".$query;
		}
		else if($this->_config->_dbtype == 'mssql2000')
		{
			//TODO
			//POPRAVI SPREMENLJIVKE!!!!
			if ($this->rows > $this->queryLimit + $this->queryStart) $rppc = $this->queryLimit;
			else $rppc = $this->rows - $this->queryStart;
				
			$opositedirection = "ASC";
			if($this->direction=="") $this->direction="ASC";
			if($this->direction=="ASC") $opositedirection = "DESC";
			if("$where"=="")
			{
				$query = "
				SELECT ".$this->colList."
	            FROM (
	                        SELECT TOP ".$rppc." *
	                        FROM (
	                                   SELECT TOP ".($this->queryLimit + $this->queryStart)." *
	                                   FROM [".$table_name."]
	                                   ORDER BY ".$this->orderBy." ".$this->direction."
	                        ) AS foo ORDER BY ".$this->orderBy." ".$opositedirection."                   
	            ) AS fooo ORDER BY ".$this->orderBy." ".$this->direction;
			}
			else
			{
				$query = "
				SELECT ".$this->colList."
	            FROM (
	                        SELECT TOP ".$rppc." *
	                        FROM (
	                                   SELECT TOP ".($this->queryLimit + $this->queryStart)." *
	                                   FROM [".$table_name."]".$where."
	                                   ORDER BY ".$this->orderBy." ".$this->direction."
	                        ) AS foo ORDER BY ".$this->orderBy." ".$opositedirection."                   
	            ) AS fooo ORDER BY ".$this->orderBy." ".$this->direction;
			}

			//echo "SQL frontend:\n".$query;
		}
		else if($this->_config->_dbtype == 'oracle' || $this->_config->_dbtype == 'oracle_jx')
		{
			if("$where"=="")
			{
				$query = "
			SELECT ".$this->colList."
			FROM (
  				SELECT
    			ROW_NUMBER() OVER (".$order.") AS rownumber,
    			".$this->colList."
  				FROM ".$table_name."
				)
			WHERE rownumber > ".$this->queryStart." AND rownumber <= (".$this->queryLimit."+".$this->queryStart.")";
			}
			else
			{
				$query = "
			SELECT ".$this->colList."
			FROM (
  				SELECT
    			ROW_NUMBER() OVER (".$order.") AS rownumber,
    			".$this->colList."
  				FROM ".$table_name.$where."
				)
			WHERE rownumber > ".$this->queryStart." AND rownumber <= (".$this->queryLimit."+".$this->queryStart.")";
			}

			//echo "SQL frontend:\n".$query;
		}
		if($this->debug	)
			echo '<div style="width:500px;"><strong>Query: </strong><br>'. $query.'</div>';
		return $query;
	}


	function count($where){
		$query = "SELECT COUNT(*) FROM ".$this->dbo->quoteName($this->_config->tableName).$where.";";
		if($this->_config->_dbtype == 'oracle' || $this->_config->_dbtype == 'oracle_jx')
		{
			$query = "SELECT COUNT(*) FROM ".$this->dbo->quoteName($this->_config->tableName).$where;
		}
		$this->dbo->setQuery($query);
		$count = $this->dbo->loadResult();
		$this->rows = $count;
		return $count;
	}

	function translateWhereCond($whereCond){
		$user = JFactory::getUser();
		$userId = $user->get( 'id' );
		$groupId =$user->get( 'gid' );
		if(isset($this->parentCont)){
			if(isset($this->parentCont->id) && $this->parentCont->id)
				$contentId=$this->parentCont->id;
			else $contentId=0;
			
			if(isset($this->parentCont->catid) && $this->parentCont->catid)
				$catId=$this->parentCont->catid;
			else $catId=0;
		}
		
		$whereCond = str_replace('@article_id', $contentId, $whereCond);
		$whereCond = str_replace('@category_id', $catId, $whereCond);
		$whereCond = str_replace('@user_id', $userId, $whereCond);
		$whereCond = str_replace('@group_id', $groupId, $whereCond);

		//echo $contentId.' '.$catId.' '.$sectionId.' '.$userId.' '.$groupId;

		return $whereCond;

	}

	function beforeQuery(){
		//implemented in child class
	}
	
	/**
	 * Ta funkcija je namesto mysql_real_escape_string()
	 * 
	 * @param unknown_type $inp
	 */
	function mysql_escape_mimic($inp) {
    if(is_array($inp))
        return array_map(__METHOD__, $inp);

    if(!empty($inp) && is_string($inp)) {
        return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
    }

    return $inp;
	} 
	/**
	 * reverse_escape($str)
	 * reverse escape
	 * @param $str
	 */
	function reverse_escape($str)
	{
	  $search=array("\\\\","\\0","\\n","\\r","\Z","\'",'\"');
	  $replace=array("\\","\0","\n","\r","\x1a","'",'"');
	  return str_replace($search,$replace,$str);
	}
	
	
	
	function createLink($type, $link, $data, $customLink=""){
		//echo( "type:".$type." link:". $link." label:". $data." custom link:". $customLink."<br>");
		$output="";
		switch($type){
			case '0':{
				$output = $data;
			}break;
			case '1':{
				$output = '<a href="'.$link.'">'.$data.'</a>';
			}break;
			case '2':{
				$output = '<a href="?option=com_content&view=article&id='.$link.'">'.$data.'</a>';
			}break;
			case '3':{
				$link = str_replace('@ID', $link, $customLink);
				$output = '<a href="'.$link.'">'.$data.'</a>';
			}break;
			default:{
				$output = $data;
			}
		}
		return $output;
	}
	
	function searchForm($search_menu, $grid_url){
		$config = $this->_config;
		$search_form = '<form action="javascript:;" name="asearch_form" onsubmit="searchjx(\''.$this->ajaxURL("", "", 0).'\', \''.$this->_ID.'\')" style="margin:0px;">
		';
		
		//echo "<pre>".print_r($config->searchF, true)."</pre>";
		if($config->searchF['display']==1){
			if($config->searchF['advanced']==1){
				//advanced filters
				$search_form .= '<table cellpadding="0" cellspacing="0" border="0">';
				foreach ($config->columns as $column){
					
					if($column->displayFilter){
						$search_form .= "\n<tr><td><strong>".$column->columnLabel.':</strong>&nbsp;</td><td>';
						switch ($column->filterType){
							case 'list': {
								$q = "SELECT DISTINCT ".$this->dbo->quoteName($column->columnName)." FROM ".$this->dbo->quoteName($this->_config->tableName)." ORDER BY ".$this->dbo->quoteName($column->columnName)." ASC LIMIT 0, 100";
								$this->dbo->setQuery($q);
								$list=$this->dbo->loadAssocList();
								$options='<option value="">&nbsp;</option>';
								foreach($list as $item){
									$value=$item[$column->columnName];
									$options.='<option value="'.$value.'" ';
									
									if(isset($this->searchStr[$column->columnName]) && $value==stripcslashes($this->searchStr[$column->columnName]))$options.= ' selected="selected" ';
									$options.='>'.$value.'</option>';
								}
								$search_form .= '<select id="'.$column->columnName.'.'.$this->_ID.'" name="adf.'.$this->_ID.'" class="input searchData">'.$options.'</select>';
							}
								
							break;
							
							default:
								$search_form .= '<input id="'.$column->columnName.'.'.$this->_ID.'" type="text" name="adf.'.$this->_ID.'" size=20 value="';
								if (isset($this->searchStr[$column->columnName]))
									 $search_form .= stripcslashes($this->searchStr[$column->columnName]);
								$search_form .='" class="input searchData">';
								break;
						}
						$search_form .=	'</td></tr>';
					}
				}
				$search_form .= '</table>';
				$search_form .= '<input type="submit" name="submit" value="'.JText::_('JX_SEARCH_BTN').'" class="input searchSubmit">';
			}
			else{
				//default search form
				$search_form .='<div style="float:left">'.JText::_('JX_SEARCH').': <select id="sf'.$this->_ID.'" name="s_f" class="input searchField">'.$search_menu.'</select> <input id="ds'.$this->_ID.'" type="text" name="data_search" size=20 value="'.$this->searchStrUnescaped.'" class="input searchData">
				<input type="submit" name="submit" value="'.JText::_('JX_SEARCH_BTN').'" class="input searchSubmit"></div>';
			}
		}
		if($config->searchF['options']==1){
			//echo "<br>ASO:".$this->advSearchOption;
			$search_form.= '<div style="float:left;position:relative;">'.JText::_('JX_SEARCH_OPTIONS'). 
			': <select id="aso'.$this->_ID.'" name="aso" class="input sfCombo">
				\n<option value="default" '; if($this->advSearchOption == "default")$search_form .='selected'; $search_form .='>'.JText::_('JX_DEFAULT_SEARCH').'</option>
				\n<option value="exact" '; if($this->advSearchOption == "exact")$search_form .='selected'; $search_form .='>'.JText::_('JX_EXACT_SEARCH').'</option>
				\n<option value="begins" '; if($this->advSearchOption == "begins")$search_form .='selected'; $search_form .='>'.JText::_('JX_BEGINS_WITH').'</option>
			</select></div>';
		}
		
		if($config->nrRecSelect){
			$search_form.='<div style="float:left;position:relative;">'.JText::_('JX_RECORDS_PER_PAGE').': '.
			'<select id="rpp'.$this->_ID.'" name="rpp" onchange="searchjx(\''.$grid_url.'\', \''.$this->_ID.'\')" class="input sfCombo">
			<option value="5" '; if($this->rpp == 5)$search_form .='selected'; $search_form .='>5</option>
				<option value="10" '; if($this->rpp == 10)$search_form .='selected'; $search_form .='>10</option>
				<option value="20" '; if($this->rpp == 20)$search_form .='selected'; $search_form .='>20</option>
				<option value="30" '; if($this->rpp == 30)$search_form .='selected'; $search_form .='>30</option>
				<option value="50" '; if($this->rpp == 50)$search_form .='selected'; $search_form .='>50</option>
				<option value="70" '; if($this->rpp == 70)$search_form .='selected'; $search_form .='>70</option>
				<option value="100" '; if($this->rpp == 100)$search_form .='selected'; $search_form .='>100</option>	
			</select></div>';
		}
		
		
		
		
		$search_form.='</form>';
		
		if($config->nrRecords){
			$search_form.='</td></tr><tr><td>'.JText::sprintf("JX_RECORDS_PLURAL", $this->rows);
		}
		
		return $search_form;
	}
	
	function ajaxURL($order_by_field, $order_by_direction, $page=-1){
		
//		jimport('joomla.language.helper');
//		$languages = JLanguageHelper::getLanguages('lang_code');
//		$lang_code = JFactory::getLanguage()->getTag();
//		$language_tag = $languages[$lang_code]->sef;
//		
//				
//		echo $language_tag;
		
		//JRoute::_(JURI::base().'index.php?option=com_foo&task=foo&view=foo&format=raw&lang='.$sef ); 
		//$grid_url = JURI::base()."index.php?option=com_grid&lang=$language_tag&gid=".$this->_ID;
		$grid_url = "?option=com_grid&gid=".$this->_ID;
		
		//order by field
		if($order_by_field!=""){
			$grid_url.="&o_b=$order_by_field";
		}else{
			$grid_url.="&o_b=$this->orderBy";
		}
		//order by direction
		if($order_by_direction!=""){
			$grid_url.="&o_d=$order_by_direction";
		}else{
			$grid_url.="&o_d=$this->direction";
		}
		//page
		if($page!=-1){
			$grid_url.="&p=$page";
			//echo '&p=$page';
		}
		else{
			$grid_url.="&p=$this->page";
			//echo '&p=$this->page';
		}
		//rows per page
		$grid_url.= "&rpp=$this->rpp";
		
//		echo $grid_url;
//		echo "<br>";
		
		return JRoute::_($grid_url);
	}
	
	function postprocessing(& $outputText){
		//execute content plugins
		if(($this->_config->execPlugins  && "$this->isAjax"=="1") || !$this->parentCont){
			$outputText = JHTML::_('content.prepare', $outputText );
		}
	}

}

