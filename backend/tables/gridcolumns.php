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
// No direct access
defined('_JEXEC') or die('Restricted access');
 
class TableGridColumns extends JTable
{
	var $id = null;
	var $idGrid = null;
	var $order = null;
	var $columnName = null;
	var $columnLabel = null;
	var $linkType = null;
	var $linkColumn = null;
	var $customLink = null;
	var $columnWidth = null;
	var $columnAlign = null;
	var $displayLabel = null;
	var $displayFilter = null;
	var $filterType = null;
	var $moreConfig = null;
	
	function TableGridColumns( &$db ) {
        parent::__construct('#__grid_columns', 'id', $db);
    }
}