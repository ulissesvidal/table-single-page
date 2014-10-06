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
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.modellist' );


class GridsModelGrids extends JModelList
{
	/**
	 * Hellos data array
	 *
	 * @var array
	 */
	var $_data;

	function __construct($config = array())
	{
		$session =  JFactory::getSession();
		if($session->has('grid')){
			$session->clear('grid');
		}
		if($session->has('conn')){
			$session->clear('conn');
		}
		
		if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                 'idGrid', 'a.idGrid',
                'tableCaption', 'a.tableCaption',
                'tableName', 'a.tableName',
                'typejx', 'a.typejx',

            );
		}
		
		parent::__construct($config);
	}
	
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');       
        
        
		// List state information.
		parent::populateState('a.idGrid', 'asc');
	}
	
	/**
	 * Returns the query
	 * @return string The query to be used to retrieve the rows from the database
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$query->select(
			$this->getState(
				'list.select',
				'a.*'
			)
		);
		$query->from('#__grids as a');
		
		// Add the list ordering clause.
        $orderCol	= $this->state->get('list.ordering');
        $orderDirn	= $this->state->get('list.direction');
        if ($orderCol && $orderDirn) {
            $query->order($db->escape($orderCol.' '.$orderDirn));
        }
        
		return $query;
	}

	/**
	 * Retrieves the hello data
	 * @return array Array of objects containing the data from the database
	 */
//	function getData()
//	{
//		// Lets load the data if it doesn't already exist
//		if (empty( $this->_data ))
//		{
//			$query = $this->_buildQuery();
//			$this->_data = $this->_getList( $query );
//		}
//
//		return $this->_data;
//	}

	function delete()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
			
		foreach($cids as $cid) {
			$query = "delete from #__grid_columns where idGrid='$cid'";
			$this->_db->setQuery($query);
			$this->_db->query();
			if($this->_db->getErrorNum()>0) 	
				return false;			
		}
			
		return true;
	}

}
