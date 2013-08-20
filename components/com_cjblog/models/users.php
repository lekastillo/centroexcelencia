<?php
/**
 * @version		$Id: users.php 01 2012-09-20 11:37:09Z maverick $
 * @package		CoreJoomla.CJBlog
 * @subpackage	Components.site
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modelitem');

class CjBlogModelUsers extends JModelLegacy {

	var $_pagination = null;
	
	function __construct() {

		parent::__construct ();
	}
	
	function get_users($action = 1, $options = array()){
		
		$app = JFactory::getApplication();
		$result = new stdClass();
		$wheres = array();
		$params = JComponentHelper::getParams(CJBLOG);
		
		$limit = $app->getCfg('list_limit', 20);
		$limitstart = $app->input->getInt('limitstart', 0);
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		$order = 'u.num_articles';
		$order_dir = 'desc';
		$search = !empty($options['query']) ? $options['query'] : '';
		
		$wheres[] = 'ju.block = 0';
		
		switch ($action){
			
			case 1: //latest
				
				$wheres[] = 'u.num_articles > 0';
				$order = 'ju.registerDate';
				break;
				
			case 2: //top
				
				$wheres[] = 'u.num_articles > 0';
				
				break;
				
			case 3: // badge owners
				
				$wheres[] = 'u.id in( select user_id from '.T_CJBLOG_USER_BADGE_MAP.' where badge_id = '.$options['badge_id'].')';
				break;
				
			case 4: //search
				
				$wheres[] = 'u.num_articles > 0';
				$wheres[] = 'ju.name like \'%'.$this->_db->escape($search).'%\' or ju.username like \'%'.$this->_db->escape($search).'%\'';
				break;
		}
		
		$excludes = $params->get('exclude_user_groups', array());
		
		if(!empty($excludes)){
			
			$wheres[] = 'u.id not in (
					select 
						distinct(user_id) 
					from 
						#__usergroups as ug1 
					inner join 
						#__usergroups AS ug2 ON ug2.lft >= ug1.lft AND ug1.rgt >= ug2.rgt 
					inner join 
						#__user_usergroup_map AS m ON ug2.id=m.group_id
					where 
						ug1.id in ('.implode(',', $excludes).'))';
		}
		
		$where = '('.implode(') and (', $wheres).')';
		
		$query = '
			select
				u.id, u.about, u.avatar, u.points, u.num_articles, u.num_badges,
				ju.name, ju.username, ju.registerDate, ju.lastvisitDate
			from
				'.T_CJBLOG_USERS.' u
			left join
				 #__users ju on ju.id = u.id
			where 
				'.$where.'
			order by
				'.$order.' '.$order_dir;

		$this->_db->setQuery($query, $limitstart, $limit);
		$result->users = $this->_db->loadAssocList('id');
		
		if (empty($this->_pagination)) {
			
			jimport('joomla.html.pagination');
			$query = 'select count(*) from '.T_CJBLOG_USERS.' u left join #__users ju on ju.id = u.id where '.$where;
			$this->_db->setQuery($query);
			$total = $this->_db->loadResult();
			
			$this->_pagination = new JPagination($total, $limitstart, $limit);
		}
		
		$result->pagination = $this->_pagination;
		$result->state = array('limit'=>$limit, 'limitstart'=>$limitstart, 'search'=>$search);
		
		return $result;
	}
	
	function save_about($id, $about){
		
		$query = '
			update
				'.T_CJBLOG_USERS.'
			set
				about = '.$this->_db->quote($about).'
			where
				id = '.$id;

		$this->_db->setQuery($query);
		
		if($this->_db->query()){
			
			return true;
		}
		
		return false;
	}
	
	function save_user_avatar_name($id, $avatar){
		
		$query = '
			update
				'.T_CJBLOG_USERS.'
			set
				avatar = '.$this->_db->quote($avatar).'
			where
				id = '.$id;
		
		$this->_db->setQuery($query);
		
		if($this->_db->query()){
			
			return true;
		}
		
		$this->setError($this->_db->getErrorMsg());
		return false;
	}
	
	function hit($id){
		
		$query = 'update '.T_CJBLOG_USERS.' set profile_views = profile_views + 1 where id = '.$id;
		$this->_db->setQuery($query);
		
		if($this->_db->query()){
			
			return true;
		}
		
		return false;
	}
	
	function get_user_point_details($user_id = 0, $params = array()){
		
		$app = JFactory::getApplication();
		
		$limit = $app->getCfg('list_limit', 20);
		$limitstart = $app->input->getInt('start', 0);
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		$order = isset($params['order']) ? $params['order'] : 'a.created';
		$order_dir = isset($params['order_dir']) ? $params['order_dir'] : 'desc';
		
		$wheres = array();
		
		if($user_id){
			
			$wheres[] = 'a.user_id = '.$user_id;
		}
		
		$where = !empty($wheres) ? 'where ('.implode(') and (', $wheres).')' : '';
		
		jimport('joomla.html.pagination');
		
		$query = 'select count(*) from '.T_CJBLOG_POINTS.' a '.$where;
		$this->_db->setQuery($query);
		$total = (int)$this->_db->loadResult();
		
		$result = new stdClass();
		$result->pagination = new JPagination($total, $limitstart, $limit);
		
		$query = '
			select
				a.id, a.points, a.description, a.created, 
				r.id as rule_id, r.description as rule_description
			from
				'.T_CJBLOG_POINTS.' a
			left join
				'.T_CJBLOG_POINT_RULES.' r on a.rule_id = r.id
			'.$where.'
			order by
				'.$order.' '.$order_dir;
		
		$this->_db->setQuery($query, $limitstart, $limit);
		$result->points = $this->_db->loadObjectList();
		$result->state = array('limit'=>$limit, 'limitstart'=>$limitstart);
		
		return $result;
	}
}
?>

