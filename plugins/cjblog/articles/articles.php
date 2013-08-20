<?php
/**
 * @version		$Id: articles.php 01 2012-11-07 11:37:09Z maverick $
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

require_once JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php';

class plgCjBlogArticles extends JPlugin{

	function plgCjBlogArticles( &$subject, $params ){

		parent::__construct( $subject, $params );
	}
	
	public function onAfterCjBlogProfileDisplay($profile){
		
		$cache = JFactory::getCache();
		$return = array();
		
		$return['header'] = JText::_('LBL_MY_ARTICLES');

		JLoader::import('joomla.application.component.model');
		JLoader::import('articles', JPATH_ROOT.DS.'components'.DS.CJBLOG.DS.'models');
		$model = JModelLegacy::getInstance( 'articles', 'CjBlogModel' );
		
		$articles = $cache->call(
				array($model, 'get_articles'), 
				array('published'=>1, 'pagination'=>false, 'limitstart'=>0, 'limit'=>5, 'user_id'=>$profile['id']));
		
		if(!empty($articles->articles)){
						
			$return['content'] = '<div>';
			
			foreach ($articles->articles as $article){
							
				$return['content'] = $return['content'] . '
					<div style="margin-bottom: 10px;">
						<a href="'.JRoute::_(ContentHelperRoute::getArticleRoute($article->id.':'.$article->alias, $article->catid.':'.$article->category_alias)).'">'.
							htmlspecialchars($article->title, ENT_COMPAT, 'UTF-8').'
						</a>
					</div>';
			}
						
			$return['content'] = $return['content'] . '</div>';
		} else {
			
			$return['content'] = '<p>'.JText::_('LBL_NO_RESULTS_FOUND').'</p>';
		}
		
		return $return;
	}
}