<?php
/**
 * @version		$Id: view.html.php 01 2012-08-22 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.site
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
jimport ( 'joomla.application.component.view' );

class CjBlogViewForm extends JViewLegacy {
	
	function display($tpl = null) {
		
		$app = JFactory::getApplication();
		$id = $app->input->getInt('id', 0);
		$user = JFactory::getUser();
		$article = null;
		$articles_model = $this->getModel();
		
		if($id){
			
			JLoader::import('joomla.application.component.model');
			JLoader::import('form', JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'models');
			$model = JModelLegacy::getInstance( 'form', 'ContentModel' );
			
			$article = $model->getItem($id);
			$tagobjects = $articles_model->get_tags_by_itemids(array($id));
			$tags = array();
			
			if(!empty($tagobjects)){
				
				foreach($tagobjects as $tag){
				
					$tags[] = $tag->tag_text;
				}
			}
			
			$article->tags = $tags;
		} else {
			
			$article = new stdClass();
			
			$article->catid = $app->input->getInt('catid', 0);
			$article->id = $article->state = 0;
			$article->title = $article->alias = $article->articletext = $article->publish_up = $article->publish_down = '';
			$article->metakey = $article->metadesc = '';
			$article->language = '*';
			$article->tags = array();
		}
		
		if (empty($article->id)) {
			$authorised = $user->authorise('core.create', 'com_content') || (count($user->getAuthorisedCategories('com_content', 'core.create')));
		}
		else {
			$authorised = $article->params->get('access-edit');
		}
		
		if ($authorised !== true) {
			
			CJFunctions::throw_error(JText::_('JERROR_ALERTNOAUTHOR'), 403);
			return false;
		}
		
		$params = JComponentHelper::getParams(CJBLOG);
		$excludes = $params->get('exclude_categories', array());
		$excluded_categories = array();
		
		if(!empty($excludes)){
			
			$excluded_categories = $articles_model->get_excluded_categories($excludes);
		}
		
		$this->assignRef('article', $article);
		$this->assignRef('params', $params);
		$this->assignRef('excluded_categories', $excluded_categories);
		
		parent::display ( $tpl );
	}
}