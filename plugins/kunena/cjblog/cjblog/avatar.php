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

class KunenaAvatarCjBlog extends KunenaAvatar {
	
	protected $params = null;

	public function __construct($params) {
		
		$this->params = $params;
	}

	public function getEditURL(){
		
		return JRoute::_('index.php?option=com_cjblog&view=profile');
	}

	public function _getURL($user, $sizex, $sizey){

		$user = KunenaFactory::getUser($user);
		$size = $this->getSize($sizex, $sizey);
		
		$avatar = CjBlogApi::get_user_avatar_image($user->userid, $sizex);
		
		return $avatar;
	}
	
	public function load($userlist){
		
		CjBlogApi::load_users($userlist);
	}
}
