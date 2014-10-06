<?php
//defined('_JEXEC') or die('Restricted access');

define('DS', DIRECTORY_SEPARATOR);
$rootFolder = explode(DS, dirname(__FILE__));

//current level in diretoty structure
$currentfolderlevel = 4;

array_splice($rootFolder, -$currentfolderlevel);

$base_folder = implode(DS, $rootFolder);

if (is_dir($base_folder . DS . 'libraries' . DS . 'joomla')) {
	define('_JEXEC', 1);
	define('JPATH_BASE', implode(DS, $rootFolder));

	require_once ( JPATH_BASE . DS . 'includes' . DS . 'defines.php' );
	require_once ( JPATH_BASE . DS . 'includes' . DS . 'framework.php' );
	$userid = '';
	$usertype = '';
	$mainframe = & JFactory::getApplication('site');
	$mainframe->initialise();
	$user = & JFactory::getUser();
	$userid = $user->get('id');
	$usertype = $user->get('usertype');
} else exit;

$db = &JFactory::getDBO();

switch($_POST["option"]) {
	case "getMoreConfig": {
		$db->setQuery(sprintf("SELECT moreConfig FROM #__grid_columns WHERE id=%d AND idGrid=%d",
		mysql_real_escape_string($_POST["id"]), mysql_real_escape_string($_POST["idGrid"])));
		echo $db->loadResult();
	} break;
	case "saveMoreConfig": {
		$db->setQuery(sprintf("UPDATE #__grid_columns SET moreConfig='%s' WHERE id=%d AND idGrid=%d",
		mysql_real_escape_string($_POST["moreConfig"]), mysql_real_escape_string($_POST["id"]),
		mysql_real_escape_string($_POST["idGrid"])));
		$db->query();
		if($db->getErrorNum()!=0) {
			echo "!".$db->getErrorMsg();
		} else echo "OK";
	} break;
}

?>