<?php defined('_JEXEC') or die('Restricted access'); ?>
 <?php
 $document = JFactory::getDocument();
	$document->addScript( 'components/com_grid/js/editform.js' );
	$document->addScript( 'components/com_grid/js/jscolor/jscolor.js' );
	$document->addStyleSheet('components/com_grid/css/editform.css');
	
	JHTML::_('behavior.tooltip'); 


?>

<style type="text/css">
.hide {display:none}
.showRow {display:table-row;}
.showBlock {display:block;}
</style>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<table cellpadding="0" cellspacing="0" width = "100%">
<tr valign="top">
<td>
<div class="col100">
      <fieldset class="adminform">
        <legend><?php echo JText::_( 'Title' ); ?></legend>
        <table class="admintable">
        <tr>
            <td width="100" align="right" class="key">
                <label for="tableCaption">
                    <?php echo JText::_( 'Table Title' ); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" name="tableCaption" id="tableCaption" size="32" maxlength="250" value="<?php echo @$this->grid->tableCaption;?>" />
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="showTitle">
                    <?php echo JText::_( 'Show Title' ); ?>:
                </label>
            </td>
             <td>
                <input type = "radio" name = "showTitle"  value="1" 
                	<?php if ($this->grid->showTitle == "1") echo 'checked'?> /> Yes
                <input type = "radio" name = "showTitle"  value = "0" 
                	<?php if ($this->grid->showTitle == "0") echo 'checked'?> /> No
            </td>         
        </tr>        
      </table>
      </fieldset>
      </div>
    </td>
    <td>
    <div class="col100">
      <fieldset class="adminform">
        <legend><?php echo JText::_( 'Layout' ); ?></legend>
        <table class="admintable">
        <tr>
            <td width="100" align="right" class="key">
                <label for="view">
                    <?php echo JText::_( 'Choose Frontend Layout' ); ?>:
                </label>
            </td>
            <td>
               <?php include 'checkFile.php'; ?>
            </td>
            
        </tr>
        <tr>
            <td>&nbsp;
              </td>
              <td>&nbsp;
              </td>
        </tr>
        </table>
      </fieldset>
   </div>
    </td>
    </tr>
    



<tr>
<td>

<?php //preveri katera konekcija je nastavljena 
$isOther = false; if(@$this->grid->connection == 'other') $isOther=true;?>


<div class="col100">
    <fieldset class="adminform">
        <legend><?php echo JText::_( 'Connection' ); ?></legend>
        <table class="admintable">
        <tr>
            <td width="100" align="right" class="key">
                <label for="connection">
                    <?php echo JText::_( 'Select Connection' ); ?>:
                </label>
            </td>
            <td>
                <input type="radio" name="connection" id="joomla" value="joomla" 
                	<?php if (!$isOther) echo 'checked'?> onclick="enableConn()"/> Same as Joomla's
                <input type="radio" name="connection" value="other" 
                	<?php if ($isOther) echo 'checked'?> onclick="enableConn()"/> Other
            </td>
        </tr>
         <tr>
            <td width="100" align="right" class="key">
                <label for="dbType">
                    <?php echo JText::_( 'Database Type' ); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" name="dbType" id="dbType" size="32" maxlength="250" value="<?php echo @$this->conn->dbType;?>" <?php if(!$isOther)echo 'disabled="disabled"';?> />
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="dbHost">
                    <?php echo JText::_( 'Database Host' ); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" name="dbHost" id="dbHost" size="32" maxlength="250" value="<?php echo @$this->conn->dbHost;?>" <?php if(!$isOther)echo 'disabled="disabled"';?> />
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="dbUser">
                    <?php echo JText::_( 'Database User' ); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" name="dbUser" id="dbUser" size="32" maxlength="250" value="<?php echo @$this->conn->dbUser;?>" <?php if(!$isOther)echo 'disabled="disabled"';?> />
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="dbPass">
                    <?php echo JText::_( 'Password' ); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="password" name="dbPass" id="dbPass" size="32" maxlength="250" value="<?php echo @$this->conn->dbPass;?>" <?php if(!$isOther)echo 'disabled="disabled"';?> />
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="dbName">
                    <?php echo JText::_( 'Database Name' ); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" name="dbName" id="dbName" size="32" maxlength="250" value="<?php echo @$this->conn->dbName;?>" <?php if(!$isOther)echo 'disabled="disabled"';?> />
            </td>
        </tr>
        <tr>
        	<td></td>
            <td>
                <input class="button" type="button" name="dbcheck" id="dbcheck" size="32" maxlength="250" onclick="javascript: submitbutton('dbcheck')" value="<?php echo JText::_('Check and List Tables') ?>" />
            </td>
        </tr>         
    </table>
    </fieldset>
</div>

</td>
<td>

<div class="col100">
    <fieldset class="adminform">
        <legend><?php echo JText::_( 'Table Settings' ); ?></legend>
        <table class="admintable">
        <tr>
            <td width="100" align="right" class="key">
                <label for="tableName">
                    <?php echo JText::_( 'Table Name in Database' ); ?>:
                </label>
            </td>
            <?php if($this->tbList == null){ ?>
            <td>
                <input class="text_area" type="text" name="tableName" id="tableName" size="32" maxlength="250" value="<?php echo $this->grid->tableName;?>" />
            </td>
            <?php } else{ ?>
            <td>
            	<select name="tableName" id="selectTable" onchange ="Joomla.submitbutton('dbcheck')">
            	<option value="null" id = "opt">--Select Table--</option>
                <?php foreach($this->tbList as $table){
                echo '<option value="'.$table.'" ';
                if (isset($this->grid->tableName) && $this->grid->tableName == $table) echo 'selected="selected"';
                echo ' >'.$table.'</option>'; 
                
                
         		   }?>
                
                
                </select>
            </td>
            <?php } ?>
            <td>
                <!-- <input class="button" type="button" name="dbcheck" id="dbcheck2" size="32" maxlength="250" onclick="javascript: submitbutton('dbcheck')" 
                	value="<?php echo JText::_('List Columns')?>" >  -->
            </td>
            
        </tr>
         <tr>
            <td width="100" align="right" class="key">
                <label for="nrRows">
                    <?php echo JText::_( 'Number of Rows' ); ?>:
                </label>
            </td>
           <td>
                <input class="text_area" type="text" name="nrRows" id="nrRows" size="6" maxlength="6" value="<?php echo $this->grid->nrRows?>" />
            </td>
            <td>
               
            </td>
            
        </tr>
         <tr>
            <td width="100" align="right" class="key">
                <label for="paging">
                    <?php echo JText::_( 'Enable Pagination' ); ?>:
                </label>
            </td>
           <td>
                <input type = "radio" name = "paging" id="paging" value="1" 
                	<?php if ($this->grid->paging == "1") echo 'checked'?> onclick="enableNrPages()"/> Yes
                <input type = "radio" name = "paging" id="paging2" value = "0" 
                	<?php if ($this->grid->paging == "0") echo 'checked'?> onclick="enableNrPages()"/> No
            </td>
            <td>
               
            </td>
            
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="nrPages">
                    <?php echo JText::_( 'Number of Pages' ); ?>:
                </label>
            </td>
           <td>
                <input class="text_area" type="text" name="nrPages" id="nrPages" size="2" maxlength="2" value="<?php echo $this->grid->nrPages;?>" />
            </td>
            <td>
               
            </td>
            
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="sortField">
                    <?php echo JText::_( 'Default Sort Column' ); ?>:
                </label>
            </td>
            
            <td>
            	<select name="sortField" id="sortField" >
            	 <?php if($this->colList==null){
            	 		echo '<option>#############</option>';
            	 		}
            	 	 else{
            	 	 	
			                 foreach($this->colList as $col){
				                echo '<option value="'.$col.'" ';
				                if ($this->grid->sortField == $col) echo 'selected="selected"';
				                echo ' >'.$col.'</option>'; 
                
                
         		   			}
            	 	 }?>
                
                
                </select>
            </td>
          
            <td>
               <?php // if($this->colList==null)echo 'Select table and press button \'List Columns\'.'; ?>
            </td>
            
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="sortDirection">
                    <?php echo JText::_( 'Default Sort Direction' ); ?>:
                </label>
            </td>
           <td>
                <input type="radio" name="sortDirection" id="sd1" value="ASC" 
                	<?php if ($this->grid->sortDirection == 'ASC') echo 'checked'?> /> Ascending
                <input type="radio" name="sortDirection" id="sd2" value="DESC" 
                	<?php if ($this->grid->sortDirection == 'DESC') echo 'checked'?> /> Descending
            </td>
            <td>
               
            </td>
            
        </tr>
        <tr>
            <td width="100" align="right" >
                &nbsp;
            </td>
           <td>
               
            </td>
            <td>
               
            </td>
            
        </tr>                   
    </table>
    </fieldset>
</div>

</td>
</tr>
</table>

<?php /*****************************COLUMNS*****************************************/ ?>

<div class="col100">
    <fieldset class="adminform">
        <legend><?php echo JText::_( 'Columns' ); ?></legend>
        <?php if($this->colList==null){
        	echo 'Select table!';
        }
        else{
	        ?>
	        <table class="admintable columnsTable" id=haupttabelle>
	        <tr><td class="key" style="text-align: left;">Unused columns:</td>
	        <td></td>
	        
	        <td class="key" style="text-align: left; width:auto;" >Shown columns:</td>
	        <td></td>
	        </tr>
	        <tr><td valign="top">
	        <input type="button" id="selectAllButton" value="mark all" onclick="selectAll(true, 'selectColList')">
	        <!-- <input type="button" id="unselectAllButton" value="unmark all" onclick="selectAll(false)"> -->
	        <br><br>
	        <select id="selectColList" size="15" multiple="multiple" style="width:100%;">
			        	<?php
			        $i=0;  
			        foreach($this->colList as $col){ 
			        	
			        
			        ?>
			        	<?php if(!isset($this->grid->colMap[$col]) || $this->grid->colMap[$col]== '' ){?>
				        	<option value="<?php echo $col; ?>" id="<?php echo 'opt'.$i;?>"> 
				            <?php echo $col; ?>
				         </option>
		        		<?php $i++;} 
			        } ?>	
	        </select>
	        </td>
	        <td ><input type="button" onclick="addCol('table')" value=">>"><br><input type="button" value="<?php echo '<<';?>" onclick="removeCol()" > </td>
	        
	        <td valign="top">
	        <table class="adminlist" id="colTable">
	        <tr>
	        	<th width="20">
              		<input type="checkbox" id="checkAllCbx" name="toggle" onclick="checkAllCB(this,'selectColCbx[]')" />
            	</th>
	            <th width="100"  style="text-align: left;">
	                
	                    <?php echo JText::_( 'Column Names' ); ?>
	               
	            </th>
	            <th width="100"   style="text-align: left;">
	                
	                    <?php echo JText::_( 'Column Captions' ); ?>
	              
	            </th>
	            <th width="100"   style="text-align: left;width:20px">
	                
	                    <?php echo JText::_( 'Link?' ); ?>
	              
	            </th>
	            
	            <th width="100" style="text-align: left;width:100px;">
		            <?php echo JText::_( 'Column Order' ); ?>
		           <a href="javascript:setOrder('colTable')" title="Order" class="saveorder">Save order</a>
	            </th>
	            
	            <th width="100"   style="text-align: left;width:20px" class="hasTip" title="Width::Type a number in px or leave empty if you do not want to set specific width.">
	                
	                    <?php echo JText::_( 'Width' ); ?>
	              
	            </th>
	        	            
	        </tr>
	        
	        <?php 
	       $i=0;
	        if(is_array($this->grid->colMap)){
	        foreach($this->grid->colMap as $col => $caption){  
	        	if(trim($col) != '' && in_array($col, $this->colList)){ ?>
		        <tr id="<?php echo 'row_'.$col; ?>">
		            <td>
		               <input type="checkbox" id="<?php echo 'cbx'.$i;?>" name="selectColCbx[]" value="<?php echo $col; ?>" />
		               <input type="hidden" name="selectCol[]" value="<?php echo $col; ?>" />
		            </td>
		            <td><?php echo $col; ?></td>
		            <td>
		                 <input class="text_area" type="text" name="colMap[<?php echo $col; ?>]" size="32" maxlength="250" value="<?php echo $caption;?>" />
		            </td>
		            <td>
		            	<input type="hidden" name="linkTypes[<?php echo $col; ?>]" value="0">
		            	<input type="hidden" name="linkMap[<?php echo $col; ?>]" value="0">
		                 <input type="checkbox" name="linkCbx" onclick="linkCbxChange(this, '<?php echo $col; ?>')" <?php if($this->grid->linkMap[$col]!='0')echo 'checked';?>/>
		            </td>
		            <td class="order" >
		            <?php if( $i == 0 ){ ?>
						<span>&nbsp;</span>
					<?php }else{ ?>	
						<span><a class="jgrid" href="javascript:move('colTable', 'up', <?php echo $i+1; ?>)" title="Move Up"><span class="state uparrow"><span class="text">Move Up</span></span></a></span>					
					<?php }if( $i == count($this->grid->colMap)-1){ ?>	
						<span>&nbsp;</span>
					<?php }else{ ?>	
						
						<span><a class="jgrid" href="javascript:move('colTable', 'down', <?php echo $i+1; ?>)" title="Move Down"><span class="state downarrow"><span class="text">Move Down</span></span></a></span>
					<?php } ?>	
						<input name="colTableOrder[]" size="5" value="<?php echo $i+1; ?>" class="text_area" style="text-align: center;" type="text">
					</td>
					<td>
		                 <input class="text_area"  type="text" name="columnWidth[<?php echo $col; ?>]" size="3" maxlength="3" value="<?php echo $this->grid->columnWidth[$col];?>" />
		            </td>

		        </tr>
        <?php $i++;}}} ?>
    </table>
    </td>
    <td rowspan="3" valign="top"><!-- For 'Width' type a number in px or leave empty if you do not want to set specific width.
   		-->  
    </td>
    </tr>

<?php /*****************************LINKS*****************************************/ ?>
	
	<?php //preveri, �e ima linke
	$hasLinks=false; if (is_array($this->grid->linkMap))foreach($this->grid->linkMap as $value){if($value!='0'){$hasLinks=true;break;}}?>
    
    <tr id = "linksRow1" <?php if(!$hasLinks) echo 'class="hide"';?>><td class="key" 
    	style="text-align: left;width:auto;" colspan="3" 
    	 >
    Links:</td></tr>
    <tr id = "linksRow2" <?php if(!$hasLinks) echo 'class="hide"';?> ><td colspan="3">
    
    <table class="adminlist" id="linksTable">
	        <tr>
	            <th width="100"  style="text-align: left;">
	                
	                    <?php echo JText::_( 'Link for Column' ); ?>
	               
	            </th>
	            <th width="100"   style="text-align: left;">
	                
	                    <?php echo JText::_( 'Link Column' ); ?>
	              
	            </th>
	            
	            <th width="490" style="text-align: left;">
	                
	                    <?php echo JText::_( 'Link Column Type' ); ?>

	            </th>
	            
	        </tr>
	        
	        <?php 
	       $i=0;
	        if(is_array($this->grid->linkMap)){
	        foreach($this->grid->linkMap as $col => $link){  
	        	if($link != '0' && in_array($col, $this->colList)){ ?>
		        <tr>
		            <td><?php echo $col; ?><input type="hidden" name="linkIndex[]" value="<?php echo $col; ?>"></input></td>
		            <td>
				       <select name="linkMap[<?php echo $col; ?>]">
			            	 <?php 			             	
			                foreach($this->colList as $col1){
				                echo '<option value="'.$col1.'" ';
				                if ($link == $col1) echo 'selected="selected"';
				                echo ' >'.$col1.'</option>'; 
			            	}?>
		                </select>
		            </td>
		            <td >
		            	<input name="linkTypes[<?php echo $col; ?>]" value="1" type="radio" <?php if($this->grid->linkTypes[$col]=='1')echo'checked="checked"'?>> URL
		            	<input name="linkTypes[<?php echo $col; ?>]" value="2" type="radio" <?php if($this->grid->linkTypes[$col]=='2')echo'checked="checked"'?>> Article ID
		            	<input name="linkTypes[<?php echo $col; ?>]" value="3" type="radio" <?php if($this->grid->linkTypes[$col]=='3')echo'checked="checked"'?>> Custom ID:
		            	<input name="customLink[<?php echo $col; ?>]" class="text_area" type="text" size="60" value="<?php echo $this->grid->customLink[$col]; ?>" >
						</td>

		        </tr>
        <?php $i++;}}} ?>
    </table>

    </td>
   
    </tr>
    
        <tr id = "linksRow3" <?php if(!$hasLinks) echo 'class="hide"';?>>
    <td colspan="4">
    <div style="width:600px;">
    For using 'Links' you should have a column in your database table, that consists of link data.
    Link data could be one of the three types:
    <ul> 
    <li>If you have in your 'Link Column' <strong>whole URL addresses</strong>, use 'URL' option.
    <li>If you have in your 'Link Column' <strong>article ID's</strong>, use 'Article ID' option. Links will point to article view.
    <li>If your 'Link Column' consists of <strong>any other kind od ID's</strong>, you can use 'Custom ID' option. In the textbox insert URL address with symbol <strong>@ID</strong> where the real ID should be inserted.
    <br><strong>Example:</strong> <code>index.php?option=com_content&view=category&layout=blog&id=@ID</code> This will make links to a blog category views. On the place of @ID id from your database will be inserted.
 	</ul>
 	</div>
 	</td>
   
    </tr>
 
	</table>
    <?php } ?>
    
    
    </fieldset>
</div>

<?php ////////////////////////////////////////////// A P P E A R A N C E /////////////////////////////////////////////////////////?>
<table cellpadding="0" cellspacing="0" width = "100%"> <tr><td width = "50%">
<div class="col100">
    <fieldset class="adminform">
        <legend><?php echo JText::_( 'Appearance' ); ?></legend>
        <table cellpadding="0" cellspacing="0"><tr><td valign="top">
        <table class="admintable">
        	
	        <tr>
	            <td width="100" align="right" class="key">
	                <label for="nrRecords">
	                    <?php echo JText::_( 'Show Nr. of Records'); ?>:
	                </label>
	            </td>
	           <td>
                	 <input type = "radio" name = "nrRecords"  value="1" 
                	<?php if ($this->grid->nrRecords == "1") echo 'checked'?> /> Yes
                	<input type = "radio" name = "nrRecords"  value = "0" 
                	<?php if ($this->grid->nrRecords == "0") echo 'checked'?> /> No
            	</td>

	        </tr>
	        <tr>
	            <td width="100" align="right" class="key">
	                <label for="lineNr">
	                    <?php echo JText::_( 'Show Line Numbers'); ?>:
	                </label>
	            </td>
	           <td>
                	 <input type = "radio" name = "lineNr"  value="1" 
                	<?php if ($this->grid->lineNr == "1") echo 'checked'?> /> Yes
                	<input type = "radio" name = "lineNr"  value = "0" 
                	<?php if ($this->grid->lineNr == "0") echo 'checked'?> /> No
            	</td>
	        </tr>
	       <!--  <tr>
	            <td width="100" align="right" class="key">
	                <label for="searchF">
	                    <?php echo JText::_( 'Show Search Form'); ?>:
	                </label>
	            </td>
	           <td>
                	 <input type = "radio" name = "searchF"  value="1" 
                	<?php if ($this->grid->searchF == "1") echo 'checked'?> /> Yes
                	<input type = "radio" name = "searchF"  value = "0" 
                	<?php if ($this->grid->searchF == "0") echo 'checked'?> /> No
            	</td>
	        </tr> -->
	         <tr>
            <td width="100" align="right" class="key">
                <label for="nrRecSelect">
                    <?php echo JText::_( 'Show \'Records per Page\' Selection List'); ?>:
                </label>
            </td>
           <td>
                 <input type = "radio" name = "nrRecSelect"  value="1" 
                	<?php if ($this->grid->nrRecSelect == "1") echo 'checked'?> /> Yes
                <input type = "radio" name = "nrRecSelect"  value = "0" 
                	<?php if ($this->grid->nrRecSelect == "0") echo 'checked'?> /> No
            </td>
            <td>
                
            </td>
            
        	</tr>
	         <tr>
            <td width="100" align="right" class="key">
                <label for="showtime">
                    <?php echo JText::_( 'Show \'Query took\''); ?>:
                </label>
            </td>
           <td>
                 <input type = "radio" name = "showtime"  value="1" 
                	<?php if ($this->grid->showtime == "1") echo 'checked'?> /> Yes
                <input type = "radio" name = "showtime"  value = "0" 
                	<?php if ($this->grid->showtime == "0") echo 'checked'?> /> No
            </td>
            <td>
                
            </td>
            
        	</tr>
	        <tr>
            <td width="100" align="right" class="key">
                <label for="poweredBy">
                    <?php echo JText::_( 'Show \'Powered by\''); ?>:
                </label>
            </td>
           <td>
                 <input type = "radio" name = "poweredBy"  value="1" 
                	<?php if ($this->grid->poweredBy == "1") echo 'checked'?> /> Yes
                <input type = "radio" name = "poweredBy"  value = "0" 
                	<?php if ($this->grid->poweredBy == "0") echo 'checked'?> /> No
            </td>
            <td>
                
            </td>
            
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="lineHeight" class="hasTip" title= "Line Height::Type '0' or leave empty if you do not want to set specific height." >
                    <?php echo JText::_( 'Line Height' ); ?>:
                </label>
            </td>
           <td>
                <input class="text_area" type="text" name="lineHeight" size="3" maxlength="3" value="<?php echo @$this->grid->lineHeight;?>" />&nbsp;px
            </td><td></td>
	        
</table></td><td valign="top">
<table class="admintable">
	     
	     <tr>

	            <td width="100" align="right" class="key">
	                <label for="hColor">
	                    <?php echo JText::_( 'Show Headers'); ?>:
	                </label>
	            </td>
	           <td>
	            <input type = "radio" name = "shAtrib"  value="1" 
                	<?php if ($this->grid->shAtrib == "1") echo 'checked'?> /> Yes
                <input type = "radio" name = "shAtrib"  value = "0" 
                	<?php if ($this->grid->shAtrib == "0") echo 'checked'?> /> No
            </td>
			<td></td>

	        </tr>   
        <tr>

	            <td width="100" align="right" class="key">
	                <label for="hColor">
	                    <?php echo JText::_( 'Header Color'); ?>:
	                </label>
	            </td>
	           <td>
	                <input class="color" type="text" name="hColor" id="hColor" size="6" maxlength="6" value="<?php echo @$this->grid->hColor?>" />
	            </td><td></td>

	        </tr>
	        <tr>
	            <td width="100" align="right" class="key">
	                <label for="sColor">
	                    <?php echo JText::_( 'Selected Header Color'); ?>:
	                </label>
	            </td>
	           <td>
	                <input class="color" type="text" name="sColor" id="sColor" size="6" maxlength="6" value="<?php echo $this->grid->sColor?>" />
	            </td><td></td>

	        </tr>
	       
	        <tr>
	            <td width="100" align="right" class="key">
	                <label for="rColor1">
	                    <?php echo JText::_( 'Row Color 1'); ?>:
	                </label>
	            </td>
	           <td>
	                <input class="color" type="text" name="rColor1" id="rColor1" size="6" maxlength="6" value="<?php echo $this->grid->rColor1?>" />
	            </td><td></td>

	        </tr>
	        <tr>
	            <td width="100" align="right" class="key">
	                <label for="rColor1">
	                    <?php echo JText::_( 'Row Color 2'); ?>:
	                </label>
	            </td>
	           <td>
	                <input class="color" type="text" name="rColor2" id="rColor2" size="6" maxlength="6" value="<?php echo $this->grid->rColor2?>" />
	            </td><td></td>

	        </tr>
	        <tr>
	            <td width="100" align="right" class="key">
	                <label for="rColorMO">
	                    <?php echo JText::_( 'Color on Mouse Over'); ?>:
	                </label>
	            </td>
	           <td>
	                <input class="color" type="text" name="rColorMO" id="rColorMO" size="6" maxlength="6" value="<?php echo $this->grid->rColorMO?>" />
	            </td><td></td>

	        </tr>
	        
	        <tr>
	            <td width="100" align="right" class="key">
	                <label for="PBColor">
	                    <?php echo JText::_( 'Page Box Color'); ?>:
	                </label>
	            </td>
	           <td>
	                <input class="color" type="text" name="PBColor" id="PBColor" size="6" maxlength="6" value="<?php echo $this->grid->PBColor?>" />
	            </td><td></td>
	        </tr>


            
        
	    </table>
	   </td> </tr></table>
	</fieldset>
</div>

<?php ////////////////////////////////////////////// S E A R C H F O R M /////////////////////////////////////////////////////////?>
</td><td valign="top">
	 <?php
                 include 'search.php';
     ?>
</td></tr></table>

<?php 
 	/***************************** F I L T E R  *************************************/
 	
 	$file=JPATH_COMPONENT_ADMINISTRATOR.'/views'.'/grid'.'/tmpl'.'/filter.php';
 	if(file_exists($file))include $file;
 	
 ?>

<?php ////////////////////////////////////////////// A D V A N C E D /////////////////////////////////////////////////////////?>
<div class="col100">
    <fieldset class="adminform">
        <legend><?php echo JText::_( 'Advanced' ); ?></legend>
        <table class="admintable">
	      <tr>
            <td width="100" align="right" class="key">
                <label for="whereCond">
                    <?php echo JText::_( 'Additional Condition'); ?>:
                </label>
            </td>
           <td>
                <div style="width:350px;">WHERE&nbsp;<input onchange="moveWhereCond()" type="text" style="width:300px;" name="whereCond2" id="whereCond2" maxlength="255" value="<?php echo @$this->grid->whereCond;?>" /></div>
            </td>
            <td>
               Instead of constants you can use as values also following expressions: <b>'@article_id', '@category_id', '@section_id', '@user_id'</b> and<b> '@group_id'</b>.
      			Before the grid is displayed, these expressions will be replaced with current values of Article ID, Category ID, Section ID, User ID and User Group ID (according to field `id` of table `core_acl_aro_groups`) from Joomla! database.
      			<br>Example: <code>`myfield_category` = '@category_id' AND `myfield_user_level` &lt;= '@group_id'</code>
            </td>
            
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="whereCond" class="hasTip" title="Case Sensitive Search::For correct behaviour collation on your database colums must be set to case sensitive">
                    <?php echo JText::_( 'Case Sensitive Search'); ?>:
                </label>
            </td>
           <td>
                 <input type = "radio" name = "caseSensitive"  value="1" 
                	<?php if ($this->grid->caseSensitive == "1") echo 'checked'?> /> Yes
                <input type = "radio" name = "caseSensitive"  value = "0" 
                	<?php if ($this->grid->caseSensitive == "0") echo 'checked'?> /> No
            </td>
            <td>
               
            </td>
            
        </tr>
         <tr>
            <td width="100" align="right" class="key">
                <label for="secOrder">
                    <?php echo JText::_( 'Secondary \'Order by\''); ?>:
                </label>
            </td>
           <td>  <select name="secOrder" id="secOrder" onchange="enableSecOrder()">
	            	 <?php 
	             	echo '<option value="0"> -- not defined -- </option>';
	             	if($this->colList != null)
	             	{
	                foreach($this->colList as $col){
		                echo '<option value="'.$col.'" ';
		                if ($this->grid->secOrder == $col) echo 'selected="selected"';
		                echo ' >'.$col.'</option>';
	                }
	            	}?>
                </select>
                <input type="radio" name="secOrderDirection" id="sod1" value="ASC" 
                	<?php if (@$this->grid->secOrderDirection == 'ASC') echo 'checked'?> /> ASC
                <input type="radio" name="secOrderDirection" id="sod2" value="DESC" 
                	<?php if (@$this->grid->secOrderDirection == 'DESC') echo 'checked'?> /> DESC
                	<br>
                <input type="radio" name="secOrderRange" id="sor1" value="0" 
                	<?php if (@$this->grid->secOrderRange == '0') echo 'checked'?> /> Apply always
                <input type="radio" name="secOrderRange" id="sor2" value="1" 
                	<?php if (@$this->grid->secOrderRange == '1') echo 'checked'?> /> Only on Default Sort
            </td>
            <td>
                
            </td>
            
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="execPlugins" class="hasTip" title="Execute Content Plugins:: If 'Yes' than the event onContentPrepare will be fired on the active Content Plugins" >
                    <?php echo JText::_( 'Execute Content Plugins'); ?>:
                </label>
            </td>
           <td>
                 <input type = "radio" name = "execPlugins"  value="1" 
                	<?php if ($this->grid->execPlugins == "1") echo 'checked'?> /> Yes
                <input type = "radio" name = "execPlugins"  value = "0" 
                	<?php if ($this->grid->execPlugins == "0") echo 'checked'?> /> No
            </td>
            <td>
               
            </td>
            
        </tr>
        
        
	    </table>     
	</fieldset>
</div>
 
<div class="clr"></div>
 
<input type="hidden" name="option" value="com_grid" />
<input type="hidden" name="idGrid" value="<?php echo @$this->grid->idGrid; ?>" />
<!--<input type="hidden" name="typejx" value="grid" />-->
<input type="hidden" name="idConn" value="<?php echo @$this->conn->idConn; ?>" />
<input type="hidden" name="task" value="" />

<input type="hidden" id="whereCond" name="whereCond" value="<?php echo @str_replace(array('<', '>'), array('{','}'), @$this->grid->whereCond);?>" />
<!--  <input type="hidden" name="controller" value="hello" /> -->
</form>
