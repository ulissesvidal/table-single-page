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
 

class TableConn extends JTable
{
    /**
     * Primary Key
     *
     * @var int
     */
    var $idConn = null;
 
    var $idGrid = null;
    var $dbType = null;
 	var $dbHost = null;
 	var $dbUser = null;
 	var $dbPass = null;
 	var $dbName = null;
    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function TableConn( &$db ) {
        parent::__construct('#__grid_conn', 'idConn', $db);
    }
}
