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
 
class TableGrid extends JTable
{
    /**
     * Primary Key
     *
     * @var int
     */
    var $idGrid = null;
 	var $typejx = null;
    var $tableName = null;
    var $showTitle = null;
    var $tableCaption = null;
 	var $connection = null;
 	var $columnNames = null;
 	var $columnCaptions = null;
 	var $link = null;
 	var $execPlugins = null;
 	//card
 	var $cardsPerRow = null;
 	var $shAtrib = null;
 	var $cardBorder = null;
 	//endCard
 	
 	var $paging = null;
 	var $nrPages = null;
 	var $default_order = null;
 	var $nrRows = null;
 	
 	var $searchF =null;
 	var $nrRecSelect = null;
 	var $nrRecords = null;
 	var $lineNr = null;
 	var $showtime = null;
 	var $poweredBy = null;
 	var $hColor = null;
 	var $sColor = null;
 	var $rColor1 = null;
 	var $rColor2 = null;
 	var $rColorMO = null;
 	var $PBColor = null;
 	var $lineHeight = null;
 	
 	var $whereCond=null;
 	var $caseSensitive=null;
 	var $secOrder = null;
 	
 	//filter
  //var $filter = null;
 	
    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function TableGrid( &$db ) {
        parent::__construct('#__grids', 'idGrid', $db);
    }
}
