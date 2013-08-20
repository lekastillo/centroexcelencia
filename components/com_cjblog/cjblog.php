<?php
/**
 * @version		$Id: cjblog.php 01 2012-09-20 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.site
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');

require_once 'api.php';

CJLib::import('corejoomla.nestedtree.core');
CJLib::import('corejoomla.template.core');

require_once JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php';
require_once JPATH_COMPONENT.DS.'helpers'.DS.'helper.php';

$input = JFactory::getApplication()->input;
$view = $input->getCmd('view', 'categories');
$task = $input->getCmd('task');

$path = JPATH_COMPONENT.DS.'controllers'.DS.$view.'.php';
if(!file_exists($path)) CJFunctions::throw_error('View '.JString::ucfirst($view).' not found!', 500);
require_once $path;

if(APP_VERSION <= 2.5){
	
	$params = JComponentHelper::getParams(CJBLOG);
	
	if($params->get('disable_jquery', 0) == 1){
		
		$app = JFactory::getApplication();
		$app->set('jquery', true);
	} else {
		
		CJFunctions::load_jquery(array('libs'=>array()));
	}
	
	if($params->get('disable_bootstrap', 0) == 0){
	
		CJLib::import('corejoomla.ui.bootstrap', true);
	}
} else {
	
	JHtml::_('jquery.framework');
}

/**************************** MEDIA **************************************/
$document = JFactory::getDocument();
$document->addStyleSheet(CJBLOG_MEDIA_URI.'css/cjblog.min.css');
$document->addScript(CJBLOG_MEDIA_URI.'js/cjblog.min.js');
/**************************** MEDIA **************************************/

$class = 'CjBlogController'.JString::ucfirst($view);
$controller = new $class();

$controller->execute($task);
echo '<input id="cjblog_page_id" value="'.$view.'" type="hidden">';
echo '<div class="center">Powered by <a href="http://www.corejoomla.com" rel="follow">CjBlog</a></div>';

$controller->redirect();
?>