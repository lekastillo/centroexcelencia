<?php
/**
 * Kunena Plugin
 * @package Kunena.Plugins
 * @subpackage CjBlog
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaProfileCjBlog extends KunenaProfile {
	
	protected $params = null;

	public function __construct($params) {
		
		$this->params = $params;
	}

	public function getUserListURL($action = '', $xhtml = true) {
		
		$config = KunenaFactory::getConfig ();
		$my = JFactory::getUser();
		
		if ( $config->userlist_allowed == 1 && $my->id == 0  ) return false;
		
		$itemid = CJFunctions::get_active_menu_id(true, 'index.php?option=com_cjblog&view=users');
		
		return JRoute::_('index.php?option=com_cjblog&view=users'.$itemid);
	}

	public function getProfileURL($user, $task = '', $xhtml = true) {
		
		if ($user == 0) return false;
		
		$user = KunenaFactory::getUser ( $user );
		
		if ($user === false) return false;
		
		return CjBlogApi::get_user_profile_url($user->userid, 'name', true, null, $xhtml);
	}

	public function _getTopHits($limit=0) {
		
		$db = JFactory::getDBO ();
		$query = "SELECT id, profile_views AS count FROM #__cjblog_users WHERE profile_views > 0 ORDER BY profile_views DESC";
		$db->setQuery ( $query, 0, $limit );
		$top = $db->loadObjectList ();
		KunenaError::checkDatabaseError ();
		return $top;
	}

	public function showProfile($view, &$params) {
		
	}
}
