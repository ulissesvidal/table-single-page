<?php defined('_JEXEC') or die('Restricted access'); 

 //$document = &JFactory::getDocument();
	$document->addScript( 'components/com_grid/js/filter.js' );

	/***************************** F I L T E R  *************************************/ ?>

<div class="col100 <?php if ($this->grid->searchF['advanced'] != "1")echo "hide" ?>" id="advFltrBlok">
    <fieldset class="adminform">
        <legend><?php echo JText::_( 'Advanced Filter' ); ?></legend>
        <?php if($this->colList==null){
        	echo 'Select table!';
        }
        else{
	        ?>
	        <table class="admintable">
	        <!-- <tr><td class="key">
	          <label for="filter">
                    <?php echo JText::_( 'Enable Advanced Filter'); ?>:
                </label>
                </td>
	        	<td colspan="2"> 
		        	<input type = "radio" name = "filter"  value="1" 
	                	<?php// if ($this->grid->filter == "1") echo 'checked'?> /> Yes
	                <input type = "radio" name = "filter"  value = "0" 
	                	<?php// if ($this->grid->filter == "0") echo 'checked'?> /> No
	            </td>
                <td></td>
            </tr> -->
            
            <tr><td class="key">
            	<label for="filter"><?php echo JText::_( 'Default Operator'); ?>:</label>
            	</td>
	        	<td colspan="2"> 
		        	<input type = "radio" name = "searchF_operator"  value="0" 
	                	<?php if (isset($this->grid->searchF['operator']) && $this->grid->searchF['operator'] == "0") echo 'checked'?> /> OR
	                <input type = "radio" name = "searchF_operator"  value = "1" 
	                	<?php if (isset($this->grid->searchF['operator']) && $this->grid->searchF['operator'] == "1") echo 'checked'?> /> AND
	            </td>
                <td></td>
            </tr>
            <!-- 
	        <tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
	        <tr><td class="key" style="text-align: left;">Unused Fields:</td>
	        <td></td>
	        
	        <td class="key" style="text-align: left; width:auto;" >Used Fields:</td>
	        <td></td>
	        </tr>
	        <tr><td valign="top">
	        <input type="button" id="selectAllButton" value="mark all" onclick="selectAll(true, 'selectFltrList')">
	        
	        <br><br>
	        <select id="selectFltrList" size="15" multiple="multiple" style="width:100%;">
			        	<?php /*
			        $i=0;
			        foreach($this->colList as $col){
			        	echo "AAAAAAAAAAAAAAAAA";
			        	if (!array_key_exists($col, $this->grid->colMap))continue;
			        	?>
				        	<option value="<?php echo $col; ?>" id="<?php echo 'opt'.$i;?>"> 
				            <?php echo $col; ?>
				         </option>
		        		<?php $i++;} */?>	
	        </select>

	        </td>
	        <td ><input type="button" onclick="addFltr()" value=">>"><br><input type="button" value="<?php echo '<<';?>" onclick="removeFltr()" > </td>
	        -->
	        <tr>
	        <td class="key" style="text-align: left; width:auto;" colspan="3">Filter Fields:</td>
	        </tr>
	        <tr>
	        <td valign="top" colspan="3">

 <table class="adminlist" id="fltrTable" width = "100%">
	        <tr>
	        	<th width="20">
              		<input type="checkbox" id="checkAllFltr" name="toggle" onclick="checkAll(this, 'selectFltrCbx[]')" />
            	</th>
	            <th width="100"  style="text-align: left;">
	                
	                    <?php echo JText::_( 'Field Name' ); ?>
	               
	            </th>
	            <!-- 
	            <th width="100"   style="text-align: left;">
	                
	                    <?php echo JText::_( 'Field Captions' ); ?>
	              
	            </th>
	             
	            <th width="100"   style="text-align: left;">
	                
	                    <?php echo JText::_( 'Field Type' ); ?>
	              
	            </th>
	           
	            <th  style="text-align: left;">
	                
	                    <?php echo JText::_( 'Field Order' ); ?>
	                <a href="javascript:setOrder('fltrTable')" title="Order">
	                	<img src="images/filesave.png" alt="Order">
	                </a>
	            </th>
	             
	            <th>
	            </th>
	            -->
	        </tr>
	        
	        <?php 
	       $i=0;
	      
	        if(is_array($this->grid->colMap)){
	        	$i=1;
	        foreach($this->fields as $field){ //echo $field->type;?>
		        <tr >
		            <td>
		               <input type="checkbox" id="<?php echo 'cbx'.$i;?>" name="selectFltrCbx[]" value="<?php echo $field->columnName; ?>" <?php if ($field->displayFilter == 1) echo 'checked'; ?>/>
		            </td>
		            <td><?php echo $field->columnName; ?>
		            	  <input type="hidden" name="fltrIndex[]" value="<?php echo $field->columnName;?>" />
		            	   
		            </td>
					
		            <td>
		            	<select name="fltrType[<?php echo $field->columnName; ?>]">
		            		<option value = "textbox" <?php if ($field->filterType == 'textbox') echo 'selected'; ?> >Textbox</option>
		            		<!-- option value = "checkbox" <?php if ($field->filterType == 'checkbox') echo 'selected'; ?>>Checkbox</option -->
		            		<!-- option value = "radio" <?php if ($field->filterType == 'radio') echo 'selected'; ?>>Radio</option -->
		            		<option value = "list" <?php if ($field->filterType == 'list') echo 'selected'; ?>>List</option>
		            	</select>
		            </td>
		            <!-- 	       
		            <td class="order" >
		            <?php /* if( $i == 1 ){ ?>
						<span>&nbsp;</span>
					<?php }else{ ?>	
						<span><a href="javascript:move('fltrTable', 'up', <?php echo $i; ?>)" title="Move Up">   <img src="images/uparrow.png" alt="Move Up" border="0" height="16" width="16"></a></span>
					<?php }if( $i == count($this->form->fields)){ ?>	
						<span>&nbsp;</span>
					<?php }else{ ?>	
						<span><a href="javascript:move('fltrTable', 'down', <?php echo $i; ?>)" title="Move Down">  <img src="images/downarrow.png" alt="Move Down" border="0" height="16" width="16"></a></span>
					<?php } */?>	
						<input name="fltrTableOrder[]" size="5" value="<?php echo $i; ?>" class="text_area" style="text-align: center;" type="text">
					</td>
					
					<td>
		                 <a href="#">configure</a>
		            </td> -->

		        </tr>
        <?php $i++;}} ?>
    </table>
    
    </td>
   <td>
   Check the fields that you want to use in filter form.
   </td>
    </tr> 
 
	</table>
		       
    <?php } ?>
    
    
    </fieldset>
</div>

