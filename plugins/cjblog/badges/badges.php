<?php
/**
 * @version		$Id: badges.php 01 2012-11-07 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.plugins
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.plugin.plugin' );
jimport( 'joomla.html.parameter' );

defined('DS') or define('DS', DIRECTORY_SEPARATOR);
defined('CJBLOG') or define('CJBLOG', 'com_cjblog');

require_once JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php';
require_once JPATH_ROOT.DS.'components'.DS.CJBLOG.DS.'api.php';

class plgCjBlogBadges extends JPlugin{

	function plgCjBlogBadges( &$subject, $params ){

		parent::__construct( $subject, $params );
	}
	
	public function onAfterCjBlogProfileDisplay($profile){
		
		$return = array();
		$return['header'] = JText::_('LBL_MY_BADGES');

		$badges = CjBlogApi::get_user_badges($profile['id']);
		
		if(!empty($badges)){
			
			$return['content'] = '<ul class="badges clearfix">';
			$itemid = CJFunctions::get_active_menu_id(true, 'index.php?option='.CJBLOG.'&view=users');
			
			foreach ($badges as $badge){
				
				$url = JRoute::_('index.php?option='.CJBLOG.'&view=users&task=badge&id='.$badge['badge_id'].':'.$badge['alias'].$itemid);
				$return['content'] = $return['content'] . 
					'<li><a href="'.$url.'" class="tooltip-hover" title="'.htmlspecialchars($badge['description'], ENT_COMPAT, 'UTF-8').'">';
				
				if(!empty($badge['icon'])){
					$return['content'] = $return['content'] . '
					<img src="'.CJBLOG_BADGES_BASE_URI.$badge['icon'].'"/>';
				} else {
					$return['content'] = $return['content'] . '
					<span class="badge '.$badge['css_class'].'">&bull; '.htmlspecialchars($badge['title'], ENT_COMPAT, 'UTF-8').'</span>';
				}
				
				$return['content'] = $return['content'] . '</a>';
				
				if($badge['num_times'] > 1){
				
					$return['content'] = $return['content'] . '<small> x '.$badge['num_times'].'</small>';
				}
				
				$return['content'] = $return['content'] . '</li>';
			}
						
			$return['content'] = $return['content'] . '</ul>';
		} else {
			
			$return['content'] = '<p>'.JText::_('LBL_NO_RESULTS_FOUND').'</p>';
		}
		
		return $return;
	}
}