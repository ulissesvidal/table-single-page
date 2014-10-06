<?php defined('_JEXEC') or die('Restricted access'); 

JHtml::_('behavior.tooltip');
JHtml::_('bootstrap.tooltip');
JHTML::_('script','system/multiselect.js',false,true);
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<div id="editcell" class="center">
    <table class="adminlist" width="500">
    <thead>
        <tr>
        	<th  class="hidden-phone">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
			</th>
            <th  class="nowrap">
                    <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'a.idGrid', $listDirn, $listOrder); ?>
             </th>
            <th>
            	<?php echo JHtml::_('grid.sort',  'Title', 'a.tableCaption', $listDirn, $listOrder); ?>
            </th>
            <th>
                <?php echo JHtml::_('grid.sort',  'Table Name', 'a.tableName', $listDirn, $listOrder); ?>
            </th>
             <th>
                 <?php echo JHtml::_('grid.sort',  'Layout', 'a.typejx', $listDirn, $listOrder); ?>
            </th>
        </tr>            
    </thead>
    <tfoot>
			<tr>
				<td colspan="5">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
	</tfoot>
    <?php
    	if (is_array($this->items))
    	foreach ($this->items as $i => $item) :
        $link = JRoute::_( 'index.php?option=com_grid&task=edit&cid[]='. $item->idGrid );
 
        ?>
       <tr class="row<?php echo $i % 2; ?>">
       		<td >
					<?php echo JHtml::_('grid.id', $i, $item->idGrid); ?>
			</td>
			<td >
					<?php echo (int) $item->idGrid; ?>
			</td>
            <td >
                <a href="<?php echo $link; ?>"><?php echo $item->tableCaption; ?></a>
            </td>
            <td >
                <?php echo $item->tableName; ?>
            </td>
            <td class="left">
                <?php 
                	switch ($item->typejx):
						case 'grid':
						 	echo "Table JX"; break;
					 	case 'card':
					 		echo "Card JX"; break;
				 		case 'grap':
					 		echo "Graph JX"; break;
			 		endswitch;
				 ?>
            </td>
        </tr>
        <?php
       endforeach;
    ?>
    </table>
</div>
 
<input type="hidden" name="option" value="com_grid" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
<?php echo JHtml::_('form.token'); ?>
<!-- if we wanted to use a new controller<input type="hidden" name="controller" value="grid" />-->
 
</form>
