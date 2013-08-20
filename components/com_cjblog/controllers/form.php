<?php
/**
 * @version		$Id: form.php 01 2012-09-20 11:37:09Z maverick $
 * @package		CoreJoomla.CJBlog
 * @subpackage	Components.site
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controllerform');

class CjBlogControllerForm extends JControllerForm {

	function __construct() {

		parent::__construct();

		$this->registerDefaultTask('get_article_form');
	}

	function get_article_form(){

		$user = JFactory::getUser();
		$app = JFactory::getApplication();
		$view = $this->getView('form', 'html');
		$model = $this->getModel('articles');
		$id = $app->input->getInt('id', 0);
		
		if($id > 0){
			
			$asset = 'com_content.article.'.$id;
			
			if (!$user->authorise('core.edit', $asset) && !$user->authorise('core.edit.own', $asset)) {
					
				CJFunctions::throw_error(JText::_('JERROR_ALERTNOAUTHOR'), 401);
				return false;
			}

			jimport('joomla.database.table');
			JTable::addIncludePath(JPATH_PLATFORM.DS.'joomla'.DS.'database'.DS.'table'.DS);
			$row = JTable::getInstance('content');
			
			if (!$row || !$row->load($id)) {
				
				CJFunctions::throw_error($row->getError(), 403);
			}
			
			if (!$row->checkout($user->id)) {
				
				return CJFunctions::throw_error($row->getError(), 403);
			} else {
				
				$this->holdEditId('com_content.edit.article', $id);
			}
		} else {
			
			if (!$user->authorise('core.create', 'com_content')){
				
				CJFunctions::throw_error(JText::_('JERROR_ALERTNOAUTHOR'), 401);
				return false;
			}
		}
		
		$view->setModel($model, true);
		$view->assign('action', 'form');
		$view->display();
	}
}
?>