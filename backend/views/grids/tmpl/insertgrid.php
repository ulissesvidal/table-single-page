<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php
 $document = JFactory::getDocument();
	$document->addScript( 'components/com_grid/js/insertgrid.js' );
?>
<form action="index.php" method="post" name="adminForm">
<div id="editcell">
    <table class="adminlist">
    <thead>
        <tr>
            <th width="5">
                <?php echo JText::_( 'ID' ); ?>
            </th>

            <th>
                <?php echo JText::_( 'Title' ); ?>
            </th>
            <th>
                <?php echo JText::_( 'Table Name' ); ?>
            </th>
        </tr>            
    </thead>
    <?php
    $k = 0;
    for ($i=0, $n=count( $this->items ); $i < $n; $i++)
    {
        $row =& $this->items[$i];
        $checked    = JHTML::_( 'grid.id', $i, $row->idGrid );
        $link = ""; //JRoute::_( 'index.php?option=com_grid&task=edit&cid[]='. $row->idGrid );
 		$onclick = "insertGrid('".$row->idGrid."')";
        ?>
        <tr class="<?php echo "row$k"; ?>">
            <td>
                <?php echo $row->idGrid; ?>
            </td>

            <td>
                <a href="" onclick="<?php echo $onclick; ?>"><?php echo $row->tableCaption; ?></a>
            </td>
            <td>
                <?php echo $row->tableName; ?>
            </td>
        </tr>
        <?php
        $k = 1 - $k;
    }
    ?>
    </table>
</div>
 
<input type="hidden" name="option" value="com_grid" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<!-- if we wanted to use a new controller<input type="hidden" name="controller" value="grid" />-->
 
</form>

<div style="font-weight: bolder"></div>
