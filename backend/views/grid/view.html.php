<?php
	/**
 * @version     2.5.1
 * @package     com_grid
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Tools JX <customers@toolsjx.com> - http://www.toolsjx.com
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
 
jimport( 'joomla.application.component.view' );

class GridsViewGrid extends JViewLegacy{
	/**
	 * display method of Hello view
	 * @return void
	 **/
	function display($tpl = null)
	{
	    //get the grid
	    $grid       = $this->get('Data');
	    $conn       = $this->get('Conn');
	    $tbList		= $this->get('TbList');
	    $colList	= $this->get('ColList');
	    $fields		= $this->get('Fields');
	    $rows		= $this->get('Count');
	    
	    $isNew        = (!isset($grid->idGrid) || $grid->idGrid < 1);
	    
	    // gumbi
	    if($isNew){
        	JToolBarHelper::apply();
			JToolBarHelper::save();
			JToolBarHelper::cancel();			
			JToolBarHelper::publish('article', 'to Article Manager');	    
	    }else{
	    	$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
			$cid = intval($cid[0]);
			JToolBarHelper::preview( '../index.php?option=com_grid&gid='.$cid.'#'.$cid, true );
			JToolBarHelper::apply();
			JToolBarHelper::save();
			JToolBarHelper::cancel();
			JToolBarHelper::publish('article', 'to Article Manager');	    	
	    }
		//set layout
	    $typejx=$grid->typejx;
	    
	    $grid_file = JPATH_COMPONENT_ADMINISTRATOR.'/views/grid/tmpl/editform.php';
	    $card_file = JPATH_COMPONENT_ADMINISTRATOR.'/views/grid/tmpl/card.php';
        $grap_file = JPATH_COMPONENT_ADMINISTRATOR.'/views/grid/tmpl/grap.php';
        $reco_file = JPATH_COMPONENT_ADMINISTRATOR.'/views/grid/tmpl/record.php';
	    
	    if ($this->getLayout()!='default'){
	    	// don't do anything. Layout has already been set
	    	//TODO poenoti typejx in layout
	    }  
        else if($isNew){
	    	if(file_exists($grid_file)){$this->setLayout('editform');$grid->typejx='grid';}
		    else if(file_exists($card_file)){$this->setLayout('card');$grid->typejx='card';}
        	else if(file_exists($grap_file)){$this->setLayout('grap');$grid->typejx='grap';}
        	else if(file_exists($reco_file)){$this->setLayout('record');$grid->typejx='reco';}              
	    }
	    else {
	    	// forma
	    	switch ($typejx){
		    	case 'card':{ 
		          //echo 'case card';
		          if(file_exists($card_file))$this->setLayout('card');
		          break;
		        }
		    	case 'grid': {
			          //echo 'case grid';
			          if(file_exists($grid_file))$this->setLayout('editform');
			          break;
		    	}
	            case 'grap': {
		          //echo 'case grap';
		          if(file_exists($grap_file))$this->setLayout('grap');
		          break;
		    	}
	            case 'reco': {
		          //echo 'case grap';
		          if(file_exists($reco_file))$this->setLayout('record');
		          break;
		    	}
		    }
	    }

	    $title="";
	    switch($this->getLayout()){
	    	case 'card': $title='Card';break;
	    	case 'editform': $title='Grid';break;
	    	case 'grap': $title='Graph';break;
	    	case 'record': $title='Record';break;
	    }	    
	 
	    $text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
	    JToolBarHelper::title(   $title.': <small><small>[ ' . $text.' ]</small></small>' );
	    /*JToolBarHelper::save();
	    if ($isNew)  {
	        JToolBarHelper::cancel();
	    } else {
	        // for existing items the button is renamed `close`
	        JToolBarHelper::cancel( 'cancel', 'Close' );
	    }*/
	    
	    
	 
	    $this->assignRef('grid', $grid);
	    $this->assignRef('conn', $conn);
	    $this->assignRef('tbList', $tbList);
	    $this->assignRef('colList', $colList);
	    $this->assignRef('fields', $fields);
	    $this->assignRef('rows', $rows);
	    parent::display($tpl);
	}

}
?>
