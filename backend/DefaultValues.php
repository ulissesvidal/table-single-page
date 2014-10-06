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
class DefaultValues{
	var $showTitle = true;
	var $nrRows = 20;
	var $paging = true;
	var $nrPages = 5;
	var $sortDirection = "ASC";
	
	#appearance
	
	var $nrRecSelect = false;
	var $nrRecords = false;
	var $lineNr = false;
	var $poweredBy = true;
	var $showtime = false;
	
	#search options
	
	var $searchF = array(
		"display" => "1",
		"options" => "0",
		"advanced" => "0",
		"submitFirst" => "0",
		"operator" => "1",
		"sortby" => "1"); 
	
	#colors
	var $hColor = "ffffff"; # header color
	var $sColor = "eeeeee"; # selected header color
	var $rColor1 = "ffffff"; # row color
	var $rColor2 = "eeeeee"; # row color
	var	$rColorMO = "F5F5BA"; //on mouse over color
	var $PBColor = "f7f7f7"; //page box color
	#card
	var $typejx = 'grid';
	var $cardsPerRow = 3;
	var $shAtrib = 1;
	var $showBorder = 1;
	
	#advanced
	var $caseSensitive = false;
	var $execPlugins = false;
	
		

}
