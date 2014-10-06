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

class GridView extends GridBuilder{
	
	function beforeQuery() {
		//for frontent rows per page selection
		$this->queryStart = $this->page*$this->rpp; //for frontent rows per page selection
		$this->queryLimit = $this->rpp; 
	}
	
	function build() {

		$conf_edit = "0";
		$conf_headers = "1";
		
		$field;
		$input;

		$grid_url = $_SERVER['PHP_SELF']."?option=com_grid&gid=".$this->_ID;
		$grid_url2 = $_SERVER['PHP_SELF']."?option=com_grid";
		
		$images_url = JURI::base().'components/com_grid/images/';
		
		$input['get_action'] = "";
		$input['post_action'] = "";
		if (isset($_GET["a"])) {
			$input['get_action'] = $_GET["a"];
		}
		if (isset($_POST["a"])) {
			$input['post_action'] = $_POST["a"];
		}
		if ("$input[get_action]"=="") {
			$input['get_action'] = $input['post_action'];
		}
		$field['get_action'] = $this->mysql_escape_mimic($input['get_action']);
		$field['post_action'] = $this->mysql_escape_mimic($input['post_action']);

		$this->_config->hColor = "#".$this->_config->hColor;
		$this->_config->sColor = "#".$this->_config->sColor;
		$row_colora1 = $this->_config->rColor1;
		$this->_config->rColor1 = "#".$this->_config->rColor1;
		$row_colora2 = $this->_config->rColor2;
		$this->_config->rColor2 = "#".$this->_config->rColor2;
		$row_colora_selected = $this->_config->row_color_selected;
		$this->_config->PBColor = "#".$this->_config->PBColor;

		$conf_edit = explode("|",$conf_edit);

		$field['search_menu'] = "";

		$field['search_menu'] .= "<option value=\"0\"";

		if ("$this->searchField"=="0") {

			$field['search_menu'] .= " selected";

		}

		$field['search_menu'] .= ">".JText::_('JX_ALL_FIELDS');


		$field['output_script'] = "<form action=\"$grid_url2\" method=\"GET\" name=\"form\" style=\"margin:0px\">
<input type=\"hidden\" id=\"img_url\" value=\"".$images_url."\">
<input type=\"hidden\" name=\"gid\" value=\"".$this->_ID."\">
<table";
		if (in_array(0, $this->_config->columnWidth))
			$field['output_script'].= " width=\"100%\"";
		$field['output_script'].= " class=\"tableJX". $this->_config->_ID ." tableJX\" border=0 cellpadding=4 cellspacing=0 style=\"margin:auto;\">";
		
		//$column_captions = $this->_config->columnCaptions;
		//$conf_columns = $this->_config->columnNames;
		$conf_columns_count = count($this->_config->columns);
		$conf_order = $this->_config->default_order;
		//$links = @explode('|',$this->_config->link);
		//$linkTypes=Array();
		//$linkCols=Array();
				
		
			if ($this->_config->shAtrib==1) {


				$field['output_script'] .= "<tr class=\"tableJX".$this->_config->_ID."headerRow". "\">";

				//line numbers
				if($this->_config->lineNr){
					$field['output_script'] .="<td style=\"width:10px;background-color:".$this->_config->hColor.";\">#</td>";
				}

				if ("$conf_edit[0]"=="1") {

					if ("$conf_edit[1]"=="2") {

						$field['output_script'] .= "<td style=\"background-color:".$this->_config->hColor."\"><input type=\"checkbox\" name=\"CheckAll\" value=\"\" onClick=\"checkAll(document.form.data_input,this)\"></td>
				";

					} else {

						$field['output_script'] .= "<td style=\"background-color:".$this->_config->hColor."\"><span class=\"field\">&nbsp;</span></td>
				";

					}
				}
			}
			
			foreach ($this->_config->columns as $column) {
				
				//$linkTypes[$i]=substr($links[$i],0,1);
				//$linkCols[$i]= substr($links[$i],2);
				
				$field['search_menu'] .= "<option value=\"$column->columnName\"";

				if ("$this->searchField"=="$column->columnName") {

					$field['search_menu'] .= " selected";

				}

				$field['search_menu'] .= ">".$column->columnLabel; //ucwords(str_replace("_"," ",$column->columnName));

				$field['order_direction'] = "DESC";

				if ("$this->orderBy"=="$column->columnName") {

					if ("$this->direction"=="ASC") {

						$field['order_direction'] = "DESC";

					} else {

						$field['order_direction'] = "ASC";

					}

				}

				if ($this->_config->shAtrib==1) {

					if ("$this->orderBy"=="$column->columnName") {

						$field['order_bgcolor'] = $this->_config->sColor;

					} else {

						$field['order_bgcolor'] = $this->_config->hColor;

					}


					$field['output_script'] .= "<td class=\"tableJX".$this->_config->_ID."header".$column->columnName. "\" nowrap style=\"background-color:$field[order_bgcolor];";
					//echo " width:".$this->_config->columnWidth[$i]."px";
					if($column->columnWidth!=0) $field['output_script'] .= " width:".$column->columnWidth."px;";
					$field['output_script'] .= "\" ><span class=\"field\">&nbsp;<b><a href=\"javascript:;\" onclick=\"searchjx('".$this->ajaxURL($column->columnName, $field['order_direction'])."', '$this->_ID');\" class=\"field\">";
					if($column->columnLabel=="")$field['output_script'] .= ucwords(str_replace("_"," ",$column->columnName));
					else $field['output_script'] .= $column->columnLabel;

					$field['output_script'] .= "</a></b>";


					if ("$this->orderBy"=="$column->columnName") {
						if ("$this->direction"=="ASC") {
							$field['output_script'] .= "&nbsp;<img src=\"".$images_url."asc.gif\" style=\"border:none;margin:0px;\">";

						} else {
							$field['output_script'] .= "&nbsp;<img src=\"".$images_url."dsc.gif\" style=\"border:none;margin:0px;\">";

						}
					}

					else{
						$field['output_script'] .= "&nbsp;<img src=\"".$images_url."x.gif\" style=\"border:none;margin:0px;\">";
					}

					$field['output_script'] .= "</span></td>
			";

				}

			}

			if ($this->_config->shAtrib==1) {

				if ("$conf_edit[0]"=="2") {

					if ("$conf_edit[1]"=="2") {
						$field['output_script'] .= "<td style=\"background-color:".$this->_config->hColor."\" align=right><input type=\"checkbox\" name=\"CheckAll\" value=\"\" onClick=\"checkAll(document.form.data_input,this)\"></td>
				";

					} else {

						$field['output_script'] .= "<td style=\"background-color:".$this->_config->hColor."\"><span class=\"field\">&nbsp;</span></td>
				";

					}
				}

				$field['output_script'] .= "</tr>
		";

			}


		///////////////////////////////////////////////

		

///////////////////////////////////////////////////////////

		$field['total_pages'] = ceil($this->rows/$this->rpp);
		$field['total_pages'] = round($field['total_pages']);


		##### HEADER AND SEARCH MENU LAYOUT #####

		$title = "<table cellpadding=0 cellspacing=0 border=0 class='noborder'><tr><td valign=\"bottom\" style=\"border:none;\"><a name=\"$this->_ID\"></a>";
		if($this->_config->showTitle)$title.="<span class=\"grid_title \">".$this->_config->tableCaption."</span>";
		$title.="</td><td style=\"border:none;\"><div style=\"width:25px; height:25px;\"  id=\"data_listings$this->_ID-showimg\"></div></td></tr>";
		$title.="<tr><td style=\"border:none;\" colspan=\"2\">";
		
		$title.=$this->searchForm($field['search_menu'], $grid_url);
		
		$title.= "</td></tr></table><br/>";



		$field['output_script']=$title.$field['output_script'];

		if (!isset($field['row_color'])) {
			$field['row_color'] = "";
		}
		
		
///////////////////////////////////DiISPLAYING ROW BY ROW////////////////////////////////////////////////////////////////////

		
		
		$nr;
		for ($i = 0; $i < $this->queryRows; $i++) {
		
			if ("$field[row_color]"==$this->_config->rColor2) {

				$field['row_color'] = $this->_config->rColor1;
				$field['row_colora'] = $row_colora1;
				$nr=1;
				
	
			} else {

				$field['row_color'] = $this->_config->rColor2;
				$field['row_colora'] = $row_colora2;
				$nr=2;
			}

			$field['output_script'] .= "<tr class=\"jxrow$nr\" id=\"row".$this->_ID."_$i\" style=\"background-color:$field[row_color];\" onmouseover=\"checktoggle_over('row".$this->_ID."_$i','".$this->_config->rColorMO."');\" onmouseout=\"checktoggle('row".$this->_ID."_$i','$field[row_colora]',$nr);\">
	";

			if($this->_config->lineNr){
				$lnr=$this->page*$this->rpp+$i+1;
				$field['output_script'] .="<td style=\"width:10px;\" >".$lnr."</td>";
			}
			
			
			foreach ($this->_config->columns as $column) {
				
				$podatek=nl2br($this->_data[$i][$column->columnName]);
												
				$field['output_script'] .= "<td class=\"tableJX".$this->_config->_ID.$column->columnName."\"";
				if($column->columnWidth!=0) $field['output_script'] .= " style=\"width:".$column->columnWidth."px;\" ";
				if($this->_config->lineHeight!=0) $field['output_script'] .= " height=\"".$this->_config->lineHeight."\" ";
				$field['output_script'] .= "><span class=\"field\">";
				if($column->linkType==0){
					$field['output_script'] .= $podatek;
				}
				else{
					$link=$this->_data[$i][$column->linkColumn];
					$field['output_script'] .=$this->createLink($column->linkType,$link, $podatek, $column->customLink);
					
				}
				$field['output_script'] .= "</span></td>";

			
			}

			$field['output_script'] .= "</tr>
	";
		}

		if ("$this->rows"=="0") {

			$conf_columns_count2 = $conf_columns_count;

			if ("$conf_edit[0]"!="0") {

				$conf_columns_count2 = $conf_columns_count + 1;

			}

			$field['output_script'] .= "<tr><td colspan=$conf_columns_count2 align=center><span class=\"field\">&nbsp;<br><b>".JText::_('JX_NO_RECORDS')."</b><br>&nbsp;</span></td></tr>
	";

		}

   $colspan=$conf_columns_count+$this->_config->lineNr;
		
	if(($this->_config->searchF['submitFirst']==1 && "$this->isAjax"!="1")
		|| ($this->_config->searchF['submitFirst']==1 && $this->advSearchOption == 'exact' && $this->emptyForm )){
			// display data only after search submit
			$field['output_script'] .="<tr><td colspan=$colspan align=\"center\"><span class=\"field\">&nbsp;<br><b>".JText::_('JX_PLEASE_ENTER')."</b><br>&nbsp;</span></td></tr>";
		}
	if ($this->dbo->getErrorNum()) 
  {
      $field['output_script'] = "<tr><td colspan=$colspan align=\"center\"><p>Database Error</p>";
      $field['output_script'] = "<p>Error:".$this->dbo->getErrorNum()."-".$this->dbo->getErrorMsg()."</p></td></tr>";
  }

/////////////////////////////// pagination ////////////////////////////////////////////////////////////////////////////////

		
		if ($this->_config->paging) {
			
			$field['output_script'] .="<tr><td colspan=$colspan>";

			if ("$field[total_pages]"=="0") {

				$field['total_pages'] = 1;

			}

			$field['output_script'] .= "
		<table width=\"100%\" border=0 cellpadding=3 cellspacing=0 style=\"width:100%;margin-top: 0.5em ;\"><tr style=\"background-color:".$this->_config->PBColor."\"><td style=\"border:none;margin:0px;width: 9em ;\">";

			$field['output_script'] .= JText::sprintf("JX_PAGES", $this->page+1, $field['total_pages']);
			$field['output_script'] .= "</td><td style=\"border:none;margin:0px;padding:0px;\" >";

			if($this->rows > $this->rpp){

				$field['output_script'] .="<table border=0 cellpadding=0 cellspacing=0 style=\"border:none;margin:0px;padding:0px;\">
			<tr style=\"border:none;margin:0px;padding:0px;\">";
					
				if ($this->page>0) {

					$field['output_script'] .= "<td style=\"border:none;margin:0px;padding:0px;\">
					<a href=\"javascript:;\" onclick=\"searchjx('".$this->ajaxURL("", "", 0 )."', '$this->_ID');\"	>
					&lt;&lt;".JText::_('JX_FIRST_PAGE')."</a></td>
					";
					$field['output_script'] .= "<td style=\"border:none;margin:0px;padding:0px 0px 0px 3px;\">
				<a href=\"javascript:;\" onclick=\"searchjx('".$this->ajaxURL("", "", $this->page-1)."','$this->_ID');\" >
				&lt;".JText::_('JX_PREVIOUS_PAGE')."</a></td>
				";

				}
				else{
					$field['output_script'] .= "<td style=\"border:none;margin:0px;padding:0px;\"><span class=\"pageButtonsDisabled\">
					&lt;&lt;".JText::_('JX_FIRST_PAGE')."</span></td>
					";
					$field['output_script'] .= "<td style=\"border:none;margin:0px;padding:0px 0px 0px 3px;\"><span class=\"pageButtonsDisabled\">
				&lt;".JText::_('JX_PREVIOUS_PAGE')."</span></td>
				";
				}
					
				if ($this->_config->nrPages > 0){

					$start;
					$half = ceil($this->_config->nrPages/2);

					if($this->page + 1 < $half || $this->_config->nrPages >= $field['total_pages']){
						$start=0;
					}
					elseif($this->page+1 >= $half && $this->page+1 <= ($field['total_pages']-$half)){
						$start = $this->page-$half+1;
					}else{
						$start = $field['total_pages']-$this->_config->nrPages;
					}



					$k = 0;

					for ($i = $start; $i<$field['total_pages']; $i++) {
						$k++;

						if (($i)==$this->page) {

							$field['output_script'] .= "<td style=\"border:none;margin:0px;padding:0px;width: 1.5em ;text-align:center;padding:0px 1px 0px 1px;\">";
							$field['output_script'] .= "<a href=\"javascript:;\" onclick=\"searchjx('".$this->ajaxURL("", "", $i)."', '$this->_ID');\" class=\"currentPageNum\" >";
							$field['output_script'] .= "<b>".($i+1)."</b>";
							$field['output_script'] .= "</a></td>";


						} else {

							$field['output_script'] .= "<td style=\"border:none;margin:0px;padding:0px;width: 1.5em ;text-align:center;padding:0px 1px 0px 1px;\">
								<a href=\"javascript:;\" onclick=\"searchjx('".$this->ajaxURL("", "", $i)."', '$this->_ID');\" class=\"pageNum\" >";
							$field['output_script'] .= ($i+1);
							$field['output_script'] .= "</a></td>";
						}
							
							
						if($k >= $this->_config->nrPages) break;
					}

				}
				else $field['output_script'] .= '<td style="border:none;width: 1em ;"> </td>';

				if (($this->page+1)<$field['total_pages']) {

					$field['output_script'] .= "<td style=\"border:none;margin:0px;padding:0px;\">
				<a href=\"javascript:;\" onclick=\"searchjx('".$this->ajaxURL("", "", $this->page+1)."', '$this->_ID');\" >".JText::_('JX_NEXT_PAGE')."&gt;</a>
				</td>";
					$field['output_script'] .= "<td style=\"border:none;margin:0px;padding:0px 0px 0px 3px;\">
					<a href=\"javascript:;\" onclick=\"searchjx('".$this->ajaxURL("", "", $field['total_pages']-1)."', '$this->_ID');\" >
					".JText::_('JX_LAST_PAGE')."&gt;&gt;</a></td>
					";

				}
				else{
					$field['output_script'] .= "<td style=\"border:none;margin:0px;padding:0px;\">
				<span class=\"pageButtonsDisabled\">".JText::_('JX_NEXT_PAGE')."&gt;</span>
				</td>";
					$field['output_script'] .= "<td style=\"border:none;margin:0px;padding:0px 0px 0px 3px;\">
					<span class=\"pageButtonsDisabled\">
					".JText::_('JX_LAST_PAGE')."&gt;&gt;</span></td>
					";
				}
				$field['output_script'] .="</tr></table>";
			}

			
			$field['output_script'] .= "</td></tr></table></td></tr>
		";

		}


		$field['output_script'] .= "</table></form>
";
		//postprocessing
		$this->postprocessing($field['output_script']);
		
		if($this->_config->showtime){
			$time= (microtime(true)-$this->starttime)*1000;
			$pointpos=strpos($time,'.');
			if ($pointpos >= 3)
				$str_time .= substr($time,0,$pointpos+1);
			else
				$str_time .= substr($time,0,4);
			$field['output_script'] .= "<div>".JText::sprintf('JX_QUERY_TOOK', $str_time)."</div>";
		}
		if($this->_config->poweredBy){
			$field['output_script'] .= "<div style=\"font-size:6px;float:right;\">Powered by <a href=\"http://www.toolsjx.com/\">Tools JX</a></div>";
		}
		
		$output = '<div  id="data_listings'.$this->_ID.'"';
		if(!in_array(0, $this->_config->columnWidth)){
			$width=array_sum($this->_config->columnWidth)+4*count($this->_config->columnWidth);
			
		$output .=' style="width:'.$width.'px;margin:auto;" ';
		}
		$output.='>'.$field['output_script'].'</div>
		<noscript>For proper performance enable JavaScript. Pages:';
		$output .="&nbsp;<a href=\"$grid_url&p=0\">1</a>";
		for($i=1; $i<$field['total_pages']; $i++){
			$output .=",&nbsp;<a href=\"$grid_url&p=$i\">".(1+$i)."</a>";
		}		
		$output .='<br>Powered by <a href=\"http://www.toolsjx.com/\">Tools JX</a>.</noscript>';
		
		

		if ("$this->isAjax"=="1") {
			header("Content-Type: text/html; charset=utf-8");
			
			//execute content plugins
			//$field['output_script']=JHTML::_('content.prepare', $field['output_script'] );
//			JPluginHelper::importPlugin( 'system' );
//			$dispatcher =& JDispatcher::getInstance();
//			$results = $dispatcher->trigger( 'onAfterDispatch', array() );
//			$results = $dispatcher->trigger( 'onAfterRender', array() );
			
			echo $field['output_script'];
			exit;

		}
		else
		  return $output;

	}
	/*
	function createLink($type, $link, $datum){
		$output="";
		switch($type){
			case '0':{
				$output = $datum;
			}break;
			case '1':{
				$output = '<a href="'.$link.'">'.$datum.'</a>';
			}break;
			case '2':{
				$output = '<a href="?option=com_content&view=article&id='.$link.'">'.$datum.'</a>';
			}break;
			default:{
				$link = str_replace('@ID', $link, $type);
				$output = '<a href="'.$link.'">'.$datum.'</a>';
			}
		}
		return $output;
	}
	
	function searchForm($grid_url, $search_menu, $data_search){
		$search_form = '<form action="javascript:;" name="asearch_form" onsubmit="searchjx(\''.$grid_url.'\', \''.$this->_ID.'\')" style="margin:0px;">
		<span class="searchForm">';
		if($this->_config->searchF){
			$search_form .='Search: <select id="sf'.$this->_ID.'" name="s_f" class="input searchField">'.$search_menu.'</select> <input id="ds'.$this->_ID.'" type="text" name="data_search" size=20 value="'.$data_search.'" class="input searchData">
			<input type="submit" name="submit" value="Search" class="input searchSubmit">';
		}
		if($this->_config->nrRecSelect){
			$search_form.='&nbsp;&nbsp;Records per Page:
			<select id="rpp'.$this->_ID.'" name="rpp" onchange="searchjx(\''.$grid_url.'\', \''.$this->_ID.'\')" style="margin:0px;">
				<option value="'.$this->_config->nrRows.'">&nbsp;</option>
				<option value="5" '; if($this->rpp == 5)$search_form .='selected'; $search_form .='>5</option>
				<option value="10" '; if($this->rpp == 10)$search_form .='selected'; $search_form .='>10</option>
				<option value="20" '; if($this->rpp == 20)$search_form .='selected'; $search_form .='>20</option>
				<option value="30" '; if($this->rpp == 30)$search_form .='selected'; $search_form .='>30</option>
				<option value="50" '; if($this->rpp == 50)$search_form .='selected'; $search_form .='>50</option>
				<option value="70" '; if($this->rpp == 70)$search_form .='selected'; $search_form .='>70</option>
				<option value="100" '; if($this->rpp == 100)$search_form .='selected'; $search_form .='>100</option>
			</select>';
		}
		$search_form.= '</span></form>';
		return $search_form;
	}*/
	
}
