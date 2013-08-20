<?php
/**
 * @version		$Id: polls.php 01 2012-11-07 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.plugins
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_SITE.DS.'components'.DS.'com_communitypolls'.DS.'router.php');

// CJLib includes
$cjlib = JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_cjlib'.DIRECTORY_SEPARATOR.'framework.php';

if(file_exists($cjlib)){

	require_once $cjlib;
}else{

	die('CJLib (CoreJoomla API Library) component not found. Please download and install it to continue.');
}

jimport( 'joomla.plugin.plugin' );
jimport( 'joomla.html.parameter' );
CJLib::import('corejoomla.framework.core');

defined('CJBLOG') or define('CJBLOG', 'com_cjblog');

class plgCjBlogPolls extends JPlugin{

	function plgCjBlogPolls( &$subject, $params ){

		parent::__construct( $subject, $params );
	}
	
	public function onAfterCjBlogProfileDisplay($profile){
		
		$db = JFactory::getDbo();
		$lang = JFactory::getLanguage();
		$return = array();
		
		$lang->load('plg_cjblog_polls', JPATH_ADMINISTRATOR, $lang->getDefault(), true);
		$lang->load('plg_cjblog_polls', JPATH_ADMINISTRATOR, $lang->getTag(), true);
		
		$return['header'] = JText::_('LBL_MY_POLLS');
		$max_length = $this->params->get('max_length', 256);
		
		$query = '
				select 
					a.id, a.title, a.votes, a.alias, a.description 
				from 
					#__jcp_polls a 
				where 
					a.published = 1 and a.created_by='.$profile['id'].'
				order by 
					a.created desc';
							
		$db->setQuery($query , 0, $this->params->get('num_polls', 5));
		$rows = $db->loadObjectList();
				
		if(!empty($rows)){

			$return['content'] = '<div>';
			$menu = JFactory::getApplication()->getMenu();
			$mnuitem = $menu->getItems('link', 'index.php?option=com_communitypolls&view=polls', true);
			$itemid = isset($mnuitem) ? '&Itemid='.$mnuitem->id : '';

			foreach ($rows as $row){
				
				$desc = '';
				
				if($max_length > 0){
				
					$desc = CJFunctions::process_html($row->description, $this->params->get('default_editor', 'bbcode') == 'bbcode');
					$desc = CJFunctions::substrws($desc, $max_length);
					$desc = '<div style="margin-top: 5px;">'.$desc.'</div>';
				}
							
				$return['content'] = $return['content'] . '
					<div style="margin-bottom: 10px;">
						<a href="'.JRoute::_('index.php?option=com_communitypolls&view=polls&task=viewpoll&id='.$row->id.':'.$row->alias.$itemid).'">'.
							htmlspecialchars($row->title, ENT_COMPAT, 'UTF-8').'
						</a>
						'.$desc.'
					</div>';
			}
						
			$return['content'] = $return['content'] . '</div>';
		} else {
			
			$return['content'] = '<p>'.JText::_('LBL_NO_RESULTS_FOUND').'</p>';
		}
		
		return $return;
	}
}