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

//Ce je v seznamu le ena tabela ali ce je dolocen 'gid', pokaze tabelo, sicer pokaze seznam. 

$showList=false;
$gid;
$list;
$jinput = JFactory::getApplication()->input;
$gid = $gid=$jinput->get('gid', 0, 'string');



if(!$gid){
	$query="SELECT * FROM #__grids";
	$db = JFactory::getDBO();
	$db->setQuery($query);
	$list = $db->loadObjectList();
	$showList=TRUE;
}

if(!$showList){	
	//prikaz komponente
	$document = JFactory::getDocument();
	$document->addScript(JUri::root().'components/com_grid/js/ajax_queue.js' );
	$document->addScript( JUri::root().'components/com_grid/js/grid.js' );
	$document->addStyleSheet(JUri::root().'components/com_grid/css/grid.css');
    $document->addScript(JUri::root()."components/com_grid/js/jquery.min.js");

	require_once(JPATH_ROOT.'/components'.'/com_grid'.'/GridBuilder.php');
	
	$config = new GridConfigManager($gid);
	if(!$config->error){
		switch($config->typejx){
			case 'grid':$builder = new GridView($gid, $config);break;
			case 'card':$builder = new CardView($gid, $config);break;
	        case 'grap':$builder = new GraphView($gid, $config);break;
	        case 'reco':$builder = new RecordView($gid, $config);
		}	
		
		echo $builder->build();		
	}
}
else{
	//load language
	
	$language = JFactory::getLanguage();
	$language->load('com_grid', JPATH_SITE);
	$language->load('com_grid', JPATH_ADMINISTRATOR);
	
	//prikaz seznama
	$output = JText::_('JX_LIST_OF_VIEWS');
	
	$output.= '<ul>';
	
	foreach($list as $row){
		$output.='<li><a href="index.php?option=com_grid&gid='.$row->idGrid.'">'.$row->tableCaption.'</a>';	
	}
	$output.='</ul>';
	echo $output;
}
?>
