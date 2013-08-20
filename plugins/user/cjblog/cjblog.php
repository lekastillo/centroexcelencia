<?php
/**
 * @version		$Id: cjblog.php 01 2012-11-07 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.plugins
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

class plgUserCjBlog extends JPlugin{
	
	public function onUserLogin($user, $options){
		
		$app = JFactory::getApplication();
		if ($app->isAdmin()) return true;
		
		$api = JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_cjblog'.DIRECTORY_SEPARATOR.'api.php';
		
		if(file_exists($api)){
		
			require_once $api;
		}else{

			return true;
		}
		
		$userid = intval(JUserHelper::getUserId($user['username']));
		CjBlogApi::award_points('com_users.login', $userid, 0, date('Ymd'), date('F j, Y, g:i a'));
		
		return true;
	}
	
	public function onUserAfterSave($user, $isnew, $success, $msg){

		if($isnew && $success){
			
			$api = JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_cjblog'.DIRECTORY_SEPARATOR.'api.php';
			
			if(file_exists($api)){
			
				require_once $api;
			}else{
			
				return true;
			}
			
			$userid = intval(JUserHelper::getUserId($user['username']));
			
			if($userid > 0){
				
				$db = JFactory::getDbo();
				
				$query = 'insert into #__cjblog_users(id, num_articles) values ('.$userid.', 0)';
				$db->setQuery($query);
				$db->query();

				@CjBlogApi::award_points('com_users.signup', $userid, 0, $userid, date('F j, Y, g:i a'));
			}
		}
				
		return true;
	}
}
