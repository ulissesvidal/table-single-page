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
//no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * Grid Component Administrator Controller
 */

class GridsController extends JControllerLegacy
{
	/**
 * constructor (registers additional tasks to methods)
 * @return void
 */
	function __construct(){
    	parent::__construct(); 
	    // Register Extra tasks
	    //The first parameter of JController::registerTask is the task to map, 
	    //and the second is the method to map it to.
	    $this->registerTask( 'add'  ,     'edit' );
	}
	function display($cachable = false, $urlparams = false){				
		parent::display($cachable = false, $urlparams = false);
	}
	
	function edit()	{
	    JRequest::setVar( 'view', 'grid' );
	    //JRequest::setVar( 'layout', 'editform'  );
	    JRequest::setVar('hidemainmenu', 1);
 
   		$this->display();
	}

	function insert()	{
	    JRequest::setVar( 'view', 'grids' );
	    JRequest::setVar( 'layout', 'insertgrid'  );
	    JRequest::setVar('hidemainmenu', 1);
 
   		$this->display();
	}
	
	
	function save()
	{
	    $model = $this->getModel('grid');
	 	
	    if ($model->store()) {
	        $msg = JText::_( 'Grid Saved!' );
	    } else {
	        $msg = JText::_( 'Error Saving Grid' ).'<br>'.$model->_msg;
	    }
	 
	    // Check the table in so it can be edited.... we are done with it anyway
	    $link = 'index.php?option=com_grid';
	    $this->setRedirect($link, $msg);
	}
	
	function remove()
	{
	    $model = $this->getModel('grid');
	    if(!$model->delete()) {
	        $msg = JText::_( 'Error: One or More Grid(s) Could not be Deleted' );
	    } else {
	        $msg = JText::_( 'Grid(s) Deleted' );
	    }
	 
	   $this->setRedirect( 'index.php?option=com_grid', $msg );
	}
	
	function cancel()
	{
	    //$msg = JText::_( 'Operation Cancelled' );
	    $this->setRedirect( 'index.php?option=com_grid');
	}
	
	function apply()
	{
		 $model = $this->getModel('grid');
		 if ($model->store()) {
	        $msg = JText::_( 'Grid Applied!' );
	    } else {
	        $msg = JText::_( 'Error Saving Grid' ).'<br>'.$model->_msg;
	    }
	 	$id = $model->_id;
	    // Check the table in so it can be edited.... we are done with it anyway
	    $link = 'index.php?option=com_grid&task=edit&cid[]='. $id . '';
	    //echo 'aa'.$id;
	   $this->setRedirect($link, $msg);			
	}
	
	function dbcheck(){
		$model = $this->getModel('grid');
		$model->dbcheck();
		$id = $model->_id;
		$msg = $model->_msg;
		if ($id == 0) {
			switch(JRequest::getVar('typejx')){
				case "grid": $link = 'index.php?option=com_grid&task=add&layout=editform';break;
				case "card": $link = 'index.php?option=com_grid&task=add&layout=card';break;
				case "grap": $link = 'index.php?option=com_grid&task=add&layout=grap';break;
				case "reco": $link = 'index.php?option=com_grid&task=add&layout=record';break;
				default: $link = 'index.php?option=com_grid&task=add';			
			}
			
		}
		else $link = 'index.php?option=com_grid&task=edit&cid[]='. $id;// .'&layout='.JRequest::getVar('layout');
		//echo $link;
		$this->setRedirect($link, $model->_msg);
	}
	
	function article(){
		 $model = $this->getModel('grid');
	 
	    if ($model->store()) {
	        $msg = JText::_( 'Grid Saved!' );
	    } else {
	        $msg = JText::_( 'Error Saving Grid' );
	    }
	 
	    $link = 'index.php?option=com_content';
	    $this->setRedirect($link, $msg);
	}
	
	function duplicate(){
		//echo "duplicate button pressed";
		$model = $this->getModel('grid');
		$model->duplicate();
		//echo "<pre>".print_r($model, true)."</pre>";
		
		$this->setRedirect( 'index.php?option=com_grid', $model->_msg);
	}
}
?>
