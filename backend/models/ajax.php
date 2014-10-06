<?php
/**
 * @version     2.5.1
 * @package     com_grid
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Tools JX <customers@toolsjx.com> - http://www.toolsjx.com
 */
defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );
require_once(JPATH_ADMINISTRATOR.'/components/com_grid/DefaultValues.php');

//@error_reporting(E_ALL  & ~E_NOTICE & ~E_DEPRECATED);
/**
 * Grid Model
 */
class AjaxModelGrid extends JModelLegacy
{
	function getColumnConfig(){
		$db=$this->_db;
		$db->setQuery(sprintf("SELECT moreConfig FROM #__grid_columns WHERE id=%d AND idGrid=%d",
				mysql_real_escape_string($_POST["id"]), mysql_real_escape_string($_POST["idGrid"])));
		return $db->loadResult();
	}
	
	function saveColumnConfig(){
		$db=$this->_db;
		$db->setQuery(sprintf("UPDATE #__grid_columns SET moreConfig='%s' WHERE id=%d AND idGrid=%d",
				mysql_real_escape_string($_POST["moreConfig"]), mysql_real_escape_string($_POST["id"]),
				mysql_real_escape_string($_POST["idGrid"])));
		$db->query();
		if($db->getErrorNum()!=0) {
			return "!".$db->getErrorMsg();
		} else return "OK";
	}

}