<?php defined('_JEXEC') or die('Restricted access');
?>

<div class="col100">
    <fieldset class="adminform">
        <legend><?php echo JText::_( 'Search Form' ); ?></legend>
        <table class="admintable">
	      <tr>
            <td align="right" class="key">
                <label for="searchF">
                    <?php echo JText::_( 'Show Search Form'); ?>:
                </label>
            </td>
                <td>
                	 <input type = "radio" name = "searchF_display"  value="1" 
                	<?php if ($this->grid->searchF['display'] == "1") echo 'checked'?> /> Yes
                	<input type = "radio" name = "searchF_display"  value = "0" 
                	<?php if ($this->grid->searchF['display'] == "0") echo 'checked'?> /> No
                	
            	</td>

            
        </tr>
        <tr>
            <td align="right" class="key">
                <label for="searchF" class="hasTip" title="Show Adwanced Search Options:: Allow frontend user to select one of the three search options: Default, Exact Search or Begins With..  ">
                    <?php echo JText::_( 'Show Advanced Search Options'); ?>:
                </label>
            </td>
           <td>
                <input type = "radio" name = "searchF_options"  value="1" 
                	<?php if ($this->grid->searchF['options'] == "1") echo 'checked'?> /> Yes
                <input type = "radio" name = "searchF_options"  value = "0" 
                	<?php if ($this->grid->searchF['options'] == "0") echo 'checked'?> /> No
                	
            </td>
            
        </tr>
        <tr>
            <td align="right" class="key">
                <label for="searchF">
                    <?php echo JText::_( 'Use Advanced Filters'); ?>:
                </label>
            </td>
           <td>
                <input onchange="showFltr()" id="advFltrRadio" type = "radio" name = "searchF_advanced"  value="1" 
                	<?php if ($this->grid->searchF['advanced'] == "1") echo 'checked'?> /> Yes
                <input onchange="showFltr()" type = "radio" name = "searchF_advanced"  value = "0" 
                	<?php if ($this->grid->searchF['advanced'] == "0") echo 'checked'?> /> No
                	
            </td>
            
        </tr>
        <tr>
            <td align="right" class="key">
                <label for="searchF" class="hasTip" title="Display Data After Submit::Display data only after the search form is submitted.">
                    <?php echo JText::_( 'Display Data After Submit'); ?>:
                </label>
            </td>
           <td>
                <input type = "radio" name = "searchF_submitFirst"  value="1" 
                	<?php if ($this->grid->searchF['submitFirst'] == "1") echo 'checked'?> /> Yes
                <input type = "radio" name = "searchF_submitFirst"  value = "0" 
                	<?php if ($this->grid->searchF['submitFirst'] == "0") echo 'checked'?> /> No
            </td>
        </tr>
        <tr>
			<td align="right" class="key">
                <label for="searchF" class="hasTip" title="Default Search Option::Default way of searching">
                    <?php echo JText::_( 'Default Search Option'); ?>:
                </label>
            </td>
           <td>
	           <select id = "searchF_aso" name = "searchF_aso" >
					<option value="default" <?php if(@$this->grid->searchF['aso'] == "default")echo 'selected'; ?> > <?php echo JText::_('JX_DEFAULT_SEARCH')?> </option>
					<option value="exact" <?php if(@$this->grid->searchF['aso'] == "exact") echo 'selected'; ?> > <?php echo JText::_('JX_EXACT_SEARCH')?> </option>
					<option value="begins" <?php if(@$this->grid->searchF['aso'] == "begins")echo 'selected'; ?> > <?php echo JText::_('JX_BEGINS_WITH') ?> </option>
				</select>
			</td>
            
        </tr>
        
        
	    </table>     
	</fieldset>
</div>