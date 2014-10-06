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
defined('_JEXEC') or die;
class com_gridInstallerScript{
	public function install($adapter ){
		$this->update($adapter);
	}

	public function update($adapter){
		$installer = JInstaller::getInstance();
		$path = $installer->getPath('source');
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();

		#DATABASE DRIVERS
		//	$msg="";
		$dbpath=JPATH_LIBRARIES.'/joomla'.'/database';
		if(file_exists($dbpath.'mssql2000.php'))
		unlink($dbpath.'mssql2000.php');
		//		if(file_exists($dbpath.'mssql2000_old.php'))
		//			unlink($dbpath.'mssql2000_old.php');
		if(file_exists($dbpath.'mssql.php'))
		unlink($dbpath.'mssql.php');
		//		if(file_exists($dbpath.'oracle.php'))
		//			unlink($dbpath.'oracle.php');
		//		if(file_exists($dbpath.'oracle_old.php'))
		//			unlink($dbpath.'oracle_old.php');
		//
		copy($path.'/database/driver/mssql.php',$dbpath.'/driver/mssql.php');
		copy($path.'/database/query/mssql.php',$dbpath.'/query/mssql.php');
		copy($path.'/database/driver/mssql2000.php',$dbpath.'/driver/mssql2000.php');
		
		#PLUGINS
		//uninstall first
		$this->rrmdir(JPATH_PLUGINS.'/content/displaygrid');
		$this->rrmdir(JPATH_PLUGINS.'/editors-xtd/insertgrid');

		$query = "DELETE FROM #__extensions WHERE `type` = 'plugin' AND (element = 'displaygrid' OR element = 'insertgrid') ";
		$db->setQuery($query);
		$db->query();

		//install and enable
		InstallationOfPlugins::install($path.'/plgx_insertgrid', 'Insert Grid: ' );
		InstallationOfPlugins::install($path.'/plgx_displaygrid','Display Grid: ' );

		$query = "UPDATE #__extensions set enabled = '1' WHERE `type` = 'plugin' AND (element = 'displaygrid' OR element = 'insertgrid')";
		$db->setQuery($query);
		if ($db->query()) $app->enqueueMessage("Plugins Enabled");
		else echo JError::raiseNotice('SOME_ERROR_CODE', "Plugins not enabled. Should be enabled manually");
	}

	function rrmdir($dir) {
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
				}
			}
			reset($objects);
			rmdir($dir);
		}
	}
}

class InstallationOfPlugins{
	public static function install($p_dir, $txt)
	{
		$app = JFactory::getApplication();
		$p_dir = JPath::clean( $p_dir );

		// Did you give us a valid directory?
		if (!is_dir($p_dir)) {
			JError::raiseWarning('SOME_ERROR_CODE', JText::_('Package directory is not correct: '.$p_dir));
			echo JText::_('Please enter a package directory');
			return false;
		}

		// Detect the package type
		$type = JInstallerHelper::detectType($p_dir);

		// Did you give us a valid package?
		if (!$type) {
			JError::raiseWarning('SOME_ERROR_CODE', JText::_('Path does not have a valid package: '.$p_dir));
			echo JText::_('Path does not have a valid package: '.$p_dir);
				
			return false;
		}

		$package['packagefile'] = null;
		$package['extractdir'] = null;
		$package['dir'] = $p_dir;
		$package['type'] = $type;

			
		// Was the package unpacked?

		if (!$package) {
			//$this->setState('message', 'Unable to find install package');
			echo 'Unable to find install package';
			return false;
		}

		// Get a database connector
		//$db = & JFactory::getDBO();

		// Get an installer instance
		$installer = new JInstaller();

		// Install the package
		if (!$installer->install($package['dir'])) {
			// There was an error installing the package
			$msg = "There was an error installing the plugin";
			//echo $msg;
			$result = false;
		} else {
			// Package installed sucessfully
			$msg = "Installation was successful";
			//echo $msg;
			$result = true;
		}
		$app->enqueueMessage($txt.$msg);


		// Cleanup the install files
		/*if (!is_file($package['packagefile'])) {
		 $config =& JFactory::getConfig();
		 $package['packagefile'] = $config->getValue('config.tmp_path').DS.$package['packagefile'];
		 }*/

		//JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);

		return $result;
	}
}