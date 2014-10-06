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

//Require the controller
require_once(JPATH_COMPONENT.'/controller.php');

//Create the controller
$controller = new GridsController();

//Perform the request task
$controller->execute(JRequest::getVar('task'));

//Redirect if set by controller
$controller->redirect();
