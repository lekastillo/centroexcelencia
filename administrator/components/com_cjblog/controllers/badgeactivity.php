<?php
/**
 * @version		$Id: points.php 01 2012-08-24 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.controllers
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class CjBlogControllerBadgeactivity extends JControllerLegacy {

	function __construct() {

		parent::__construct();

		$this->registerDefaultTask('get_recent_activity');
	}
	
	function get_recent_activity(){
		
		$view = $this->getView('badgeactivity', 'html');
		$model = $this->getModel('badges');
		$user_model = $this->getModel('users');
		
		$view->setModel($model, true);
		$view->setModel($user_model, false);
		$view->assign('action', 'default');
		$view->display();
	}
	
	function remove(){
	
		$model = $this->getModel('badges');
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');
	
		if(!empty($ids) && $model->delete_activity($ids)){
	
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=badgeactivity', false), JText::_('COM_CJBLOG_REMOVE_SUCCESS'));
		} else {
	
			$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=badgeactivity', false), JText::_('COM_CJBLOG_REMOVE_FAILED'));
		}
	}
	
	function cancel(){
	
		$this->setRedirect(JRoute::_('index.php?option='.CJBLOG.'&view=badgeactivity', false), JText::_('COM_CJBLOG_OPERATION_CANCELLED'));
	}
}