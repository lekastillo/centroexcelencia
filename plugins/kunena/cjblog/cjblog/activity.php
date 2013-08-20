<?php
/**
 * @version		$Id: helper.php 01 2012-04-21 11:37:09Z maverick $
 * @package		CoreJoomla.Kunena
 * @subpackage	Modules.plugins
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined ( '_JEXEC' ) or die ();

jimport('joomla.utilities.string');

class KunenaActivityCjBlog extends KunenaActivity {

	protected $params = null;

	public function __construct($params) {
		
		$this->params = $params;
	}

	public function onAfterPost($message) {
		
		// Check for permisions of the current category - activity only if public or registered
		if ( $this->_checkPermissions($message) ) {
			
			$description = '<a rel="nofollow" href="' . $message->getTopic()->getPermaUrl() . '">' . $message->subject . '</a>';
			
			if (JString::strlen($message->message) > $this->params->get('activity_points_limit', 0)) {
				
				CjBlogApi::award_points('com_kunena.newtopic', $message->userid, 0, $message->id , $description);
			}
			
			$db = JFactory::getDbo();
			$query = 'select posts from #__kunena_users where userid = '.$message->userid;
			$db->setQuery($query);
			$posts = (int)$db->loadResult();
			
			if($posts > 0){
			
				CjBlogApi::trigger_badge_rule('com_kunena.num_posts', array('num_posts'=>$posts), $message->userid);
			}
		}
		
		return true;
	}

	public function onAfterReply($message) {
		
		// Check for permisions of the current category - activity only if public or registered
		if ( $this->_checkPermissions($message) ) {
			
			$description = '<a rel="nofollow" href="' . $message->getTopic()->getPermaUrl() . '">' . $message->subject . '</a>';
			
			if (JString::strlen($message->message) > $this->params->get('activity_points_limit', 0)) {
				
				CjBlogApi::award_points('com_kunena.newreply', $message->userid, 0, $message->id , $description);
			}
			
			$db = JFactory::getDbo();
			$query = 'select posts from #__kunena_users where userid = '.$message->userid;
			$db->setQuery($query);
			$posts = (int)$db->loadResult();
			
			if($posts > 0){
			
				CjBlogApi::trigger_badge_rule('com_kunena.num_posts', array('num_posts'=>$posts), $message->userid);
			}
		}
		
		return true;
	}

	public function onAfterDelete($message) {
		
		// Check for permisions of the current category - activity only if public or registered
		if ( $this->_checkPermissions($message) ) {
			
			$description = '<a rel="nofollow" href="' . $message->getTopic()->getPermaUrl() . '">' . $message->subject . '</a>';
			
			if (JString::strlen($message->message) > $this->params->get('activity_points_limit', 0)) {
				
				CjBlogApi::award_points('com_kunena.deletepost', $message->userid, 0, $message->id , $description);
			}
		}
		
		return true;
	}

	public function onAfterThankyou($target, $actor, $message) {
		
		// Check for permisions of the current category - activity only if public or registered
		if ( $this->_checkPermissions($message) ) {
			
			if (JString::strlen($message->message) > $this->params->get('activity_points_limit', 0)) {
				
				$username = KunenaFactory::getUser($actor)->name;
				$description = JText::sprintf('PLG_KUNENA_CJBLOG_ACTIVITY_THANKYOU_RECEIVED', $username)
					.'<br>'.JHtml::link($message->getTopic()->getPermaUrl(), $message->subject);
				CjBlogApi::award_points('com_kunena.gotthankyou', $actor, 0, $message->id, $description);
				
				$username = KunenaFactory::getUser($target)->name;
				$description = JText::sprintf('PLG_KUNENA_CJBLOG_ACTIVITY_THANKYOU_SAID', $username)
					.'<br>'.JHtml::link($message->getTopic()->getPermaUrl(), $message->subject);
				CjBlogApi::award_points('com_kunena.saidthankyou', $target, 0, $message->id, $description);
			}
			
			$db = JFactory::getDbo();
			$query = 'select thankyou from #__kunena_users where userid = '.$actor;
			$db->setQuery($query);
			$count = (int)$db->loadResult();
			
			if($posts > 0){
			
				CjBlogApi::trigger_badge_rule('com_kunena.num_thankyou', array('num_thankyou'=>$count), $actor);
			}
		}
	}

	function escape($var) {
		return htmlspecialchars ( $var, ENT_COMPAT, 'UTF-8' );
	}

	private function _checkPermissions($message) {
		
		$category = $message->getCategory();
		$accesstype = $category->accesstype;
		
		if ($accesstype != 'joomla.group' && $accesstype != 'joomla.level') {
			
			return false;
		}
		
		if (version_compare(JVERSION, '1.6','>')) {
			
			// FIXME: Joomla 1.6 can mix up groups and access levels
			if ($accesstype == 'joomla.level' && $category->access <= 2) {
				return true;
			} elseif ($category->pub_access == 1 || $category->pub_access == 2) {
				return true;
			} elseif ($category->admin_access == 1 || $category->admin_access == 2) {
				return true;
			}
			
			return false;
		} else {
			
			// Joomla access levels: 0 = public,  1 = registered
			// Joomla user groups:  29 = public, 18 = registered
			if ($accesstype == 'joomla.level' && $category->access <= 1) {
				return true;
			} elseif ($category->pub_access == 0 || $category->pub_access == - 1 || $category->pub_access == 18 || $category->pub_access == 29) {
				return true;
			} elseif ($category->admin_access == 18 || $category->admin_access == 29) {
				return true;
			}
			
			return false;
		}
	}
}
