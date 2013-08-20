<?php
/**
 * @version		$Id: CjBlog.php 01 2011-01-11 11:37:09Z maverick $
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

class plgContentCjBlog extends JPlugin{

	function plgContentCjBlog( &$subject, $params ){

		parent::__construct( $subject, $params );
	}

	public function onContentPrepare($context, &$article, &$params, $page = 0){
		
		$app = JFactory::getApplication();
		$menu = $app->getMenu();
		
		if (($menu->getActive() == $menu->getDefault()) || $context != 'com_content.article' || $page > 0 || empty($article->id)) {
		
			return true;
		}
		
		$api = JPATH_ROOT.DS.'components'.DS.CJBLOG.DS.'api.php';
		
		if(file_exists($api)){
		
			require_once $api;
			require_once JPATH_ROOT.DS.'components'.DS.CJBLOG.DS.'router.php';
		}else{
		
			die('CjBlog component is not installed.');
		}
		
		$db = JFactory::getDbo();
		$document = JFactory::getDocument();
		$user = JFactory::getUser();
		
		$article_url = JRoute::_(ContentHelperRoute::getArticleRoute($article->id.':'.$article->alias, $article->catid.':'.$article->category_alias));
		
		/******************************** TRIGGER BADGE RULE ******************************************/
		CjBlogApi::trigger_badge_rule('com_content.num_hits', array('num_hits'=>$article->hits, 'ref_id'=>$article->id), $article->created_by);
		
		/******************************** TRIGGER POINTS RULE ******************************************/
		$created = JFactory::getDate()->toSql();
		$query = '
				select 
					count(*) 
				from 
					#__cjblog_points 
				where 
					user_id = '.$user->id.' and
					created > '.$db->quote($created).' - interval 1 minute and
					rule_id = (select id from #__cjblog_point_rules where name=\'com_content.hits\')';
		
		$db->setQuery($query);
		$count = (int)$db->loadResult();
		
		if($count == 0){
			
			CjBlogApi::award_points('com_content.hits', $user->id, 0, $article->id, JHtml::link($article_url, $article->title));
		}
				
		// Check if the plugin is disabled for the article category
		$include_categories = $this->params->get('include_categories');
		$exclude_categories = $this->params->get('exclude_categories');
		
		if(!empty($include_categories)){

			$include_categories = explode(',', $include_categories);
			if(!in_array($article->catid, $include_categories)){
			
				return true;
			}
		}else if(!empty($exclude_categories)){
			
			$exclude_categories = explode(',', $exclude_categories);
			if(in_array($article->catid, $exclude_categories)){
				
				return true;
			}
		}
		
		$print = $app->input->getInt('print', 0);
		if($print) return true;
		
		/********************************** CONTENT HEADER *********************************************/
		require_once JPATH_ROOT.DS.'components'.DS.CJBLOG.DS.'router.php';
		
		$CjLib = JPATH_ROOT.DS.'components'.DS.'com_cjlib'.DS.'framework.php';
		CJLib::import('corejoomla.framework.core');
		CJFunctions::load_jquery(array('libs'=>array('rating', 'social')));
		CJLib::import('corejoomla.ui.bootstrap');
		
		$document->addScript(JUri::root(true).'/media/'.CJBLOG.'/js/cjblog.min.js');
		$document->addStyleSheet(JUri::root(true).'/media/'.CJBLOG.'/css/cjblog.min.css');
		
		$articles_itemid = CJFunctions::get_active_menu_id(true, 'index.php?option='.CJBLOG.'&view=articles');
		$form_itemid = CJFunctions::get_active_menu_id(true, 'index.php?option='.CJBLOG.'&view=form');
		$profile = CjBlogApi::get_user_profile($article->created_by);
		$about = !empty($profile['about']) ? '<div>'.CJFunctions::escape($profile['about']).'</div>' : '';
		
		$appparams = JComponentHelper::getParams(CJBLOG);
		$params->merge($appparams);
		
		$show_category = $params->get('show_category', 1);
		$show_date = $params->get('show_date', 1);
		$show_hits = $params->get('show_hits', 1);
		$show_author = $params->get('show_author', 1);
		$show_favoured = $params->get('show_favoured', 1);
		$show_rating = $params->get('show_rating', 1);
		$user_name = $params->get('user_display_name', 'name');
		$hot_hits = $params->get('hot_article_num_hits', 250);
		$display_social_buttons = $params->get('display_social_buttons', 1);
		$sharing_position = $params->get('sharing_buttons_position', 'bottom');
		
		$meta_header = '';
		$meta_stats = '';
		$meta_rating = '';
		$meta_rating_readonly = '';
		$social_buttons = '';
		$tags_html = '';
		
		if($show_category && $show_date){
			
			$cat_url = JRoute::_('index.php?option='.CJBLOG.'&view=articles&task=latest&id='.$article->catid.':'.$article->category_alias.$articles_itemid);
			$cat_link = JHtml::link($cat_url, CJFunctions::escape($article->category_title));
			$formatted_date = CJFunctions::get_localized_date($article->created, 'd F Y');
			
			$meta_header = JText::sprintf('TXT_POSTED_IN_CATEGORY_ON', $cat_link, $formatted_date);
		}else if($show_category){
			
			$cat_url = JRoute::_('index.php?option='.CJBLOG.'&view=articles&task=latest&id='.$article->catid.':'.$article->category_alias.$articles_itemid);
			$cat_link = JHtml::link($cat_url, CJFunctions::escape($article->category_title));
			
			$meta_header = JText::sprintf('TXT_POSTED_IN_CATEGORY', $cat_link);
		} else if($show_date){

			$meta_header = JText::sprintf('TXT_POSTED_ON', CJFunctions::get_localized_date($article->created, 'd F Y'));
		}

		if($article->hits > $hot_hits){
			$meta_stats = $meta_stats. '<span class="label label-important hot-article">'.JText::_('LBL_HOT').'</span> ';
		}
		
		if($show_hits){
			
			$meta_stats = $meta_stats .'<span class="label label-info">'.JText::sprintf('TXT_NUM_HITS', $article->hits).'</span> ';
		}
		
		if($show_favoured){
			
			$query = 'select favorites from '.T_CJBLOG_CONTENT.' where id = '.$article->id;
			$db->setQuery($query);
			$favored = (int)$db->loadResult();

			if(!$user->guest){

				$query = 'select count(*) from '.T_CJBLOG_FAVORITES.' where content_id = '.$article->id.' and user_id = '.$user->id;
				$db->setQuery($query);
				$count = $db->loadResult();

				if($count == 0){
					
					$meta_stats = $meta_stats. '
						<span class="label label-info favorites">'.JText::sprintf('TXT_NUM_FAVOURED', $favored).'</span>
						<a id="btn-add-to-favorites" 
							class="btn btn-mini tooltip-hover" 
							href="'.JRoute::_('index.php?option='.CJBLOG.'&view=articles&task=favorite&id='.$article->id.$articles_itemid).'" 
							onclick="return false;" 
							title="'.JText::_('LBL_ADD_TO_FAVORITES').'">
							<i class="icon-star"></i>
						</a>';
				} else {
					
					$meta_stats = $meta_stats. '
						<span class="label label-info favorites tooltip-hover" title="'.JText::_('LBL_YOU_ADDED_TO_FAVORITES').'">
							<i class="icon-star icon-white"></i> '.JText::sprintf('TXT_NUM_FAVOURED', $favored).'
						</span> 
						<a id="btn-remove-favorite" 
							class="btn btn-mini tooltip-hover" 
							href="'.JRoute::_('index.php?option='.CJBLOG.'&view=articles&task=remove_favorite&id='.$article->id.$articles_itemid).'" 
							onclick="return false;" 
							title="'.JText::_('LBL_REMOVE_FAVORITE').'">
							<i class="icon-remove"></i>
						</a>';
				}
			} else {
				
				$meta_stats = $meta_stats. '<span class="label label-info favorites">'.JText::sprintf('TXT_NUM_FAVOURED', $favored).'</span>';
			}
		}
		
		$asset	= 'com_content.article.'.$article->id;
		$redirect_url = base64_encode($article_url);
		
		if(($user->authorise('core.edit.own', $asset) && $article->created_by == $user->id) || $user->authorise('core.manage')){
			
			$meta_stats = $meta_stats.'
						<a id="btn-edit-article" 
							class="btn btn-mini tooltip-hover" 
							href="'.JRoute::_('index.php?option='.CJBLOG.'&view=form&id='.$article->id.$form_itemid.'&return='.$redirect_url).'" 
							title="'.JText::_('LBL_EDIT_ARTICLE').'">
							<i class="icon-pencil"></i>
						</a>';
		}
		
		if($user->authorise('core.edit.state')){
			
			if($article->state == 1){
				
				$meta_stats = $meta_stats.'
						<a id="btn-publish-article"
							class="btn btn-mini tooltip-hover"
							href="'.JRoute::_('index.php?option='.CJBLOG.'&view=articles&task=unpublish&id='.$article->id.$articles_itemid.'&return='.$redirect_url).'"
							title="'.JText::_('LBL_PUBLISHED').'">
							<i class="icon-ok-circle"></i>
						</a>';
			} else {
				
				$meta_stats = $meta_stats.'
						<a id="btn-publish-article"
							class="btn btn-mini tooltip-hover"
							href="'.JRoute::_('index.php?option='.CJBLOG.'&view=articles&task=publish&id='.$article->id.$articles_itemid.'&return='.$redirect_url).'"
							title="'.JText::_('LBL_UNPUBLISHED').'">
							<i class="icon-ban-circle"></i>
						</a>';
			}
		}
		
		if($show_rating && $user->authorise('articles.rate', CJBLOG)){
			
			$rating = CJFunctions::get_rating(CJBLOG_ASSET_ID, $article->id);
			
			$meta_rating_readonly = '
				<div class="pull-right">
					<span class="article-star-rating-readonly" 
						data-rating-score="'.$rating['rating'].'" 
						data-rating-hints="'.JText::_('LBL_RATING_HINTS').'"
						data-rating-noratemsg="'.JText::_('LBL_RATING_NORATE_HINT').'"
						data-rating-cancelhint="'.JText::_('LBL_RATING_CANCEL_HINT').'"></span>
					('.JText::sprintf('TXT_RATINGS', $rating['total_ratings']).')
				</div>';
			
			$readonly = false;
			$hash = $app->getHash('com_content.article.rating.item_'.$article->id);
			$cookie = $app->input->cookie->get($hash, null);
				
			if($cookie){
			
				$readonly = true;
			} else if (!$user->guest){
			
				$query = 'select count(*) from '.T_CJ_RATING_DETAILS.' where item_id = '.$article->id.' and asset_id = 1 and created_by = '.$user->id;
				$db->setQuery($query);
				$count = (int)$db->loadResult();
				
				if($count > 0){
					
					$app->input->cookie->set($hash, 1, time()+60*60*24*365, $article_url);
					$readonly = true;
				}
			}
			
			if(!$readonly){
				
				$meta_rating = '
					<div>'.JText::_('LBL_RATE_THIS_ARTICLE').':</div>
					<span class="article-star-rating" 
						data-rating-score="'.$rating['rating'].'"
						data-rating-url="'.JRoute::_('index.php?option='.CJBLOG.'&view=articles&task=rate&id='.$article->id.$articles_itemid).'" 
						data-rating-hints="'.JText::_('LBL_RATING_HINTS').'"
						data-rating-noratemsg="'.JText::_('LBL_RATING_NORATE_HINT').'"
						data-rating-cancelhint="'.JText::_('LBL_RATING_CANCEL_HINT').'"></span>
					(<span id="article-rating-info">'.JText::sprintf('TXT_RATING_INFO', $rating['rating'], $rating['total_ratings']).'</span>)
					<hr>';
			}
		} 
		
		if($display_social_buttons == 1){
			
			$document->addScript('//s7.addthis.com/js/300/addthis_widget.js');
			$social_buttons = '
					<div class="social-sharing">
						<p>'.JText::_('LBL_SOCIAL_SHARING_DESC').'</p>
						<div class="addthis_toolbox addthis_default_style ">
							<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
							<a class="addthis_button_tweet"></a>
							<a class="addthis_button_google_plusone" g:plusone:size="medium"></a>
							<a class="addthis_counter addthis_pill_style"></a>
						</div>
					</div>';
		}
		
		JLoader::import('joomla.application.component.model');
		JLoader::import('articles', JPATH_ROOT.DS.'components'.DS.CJBLOG.DS.'models');
		$model = JModelLegacy::getInstance( 'articles', 'CjBlogModel' );
		
		$tags = $model->get_tags_by_itemids(array($article->id));
		
		if(!empty($tags)){
		
			$tags_html = '<div class="tags-list margin-top-10">';
			
			foreach($tags as $tag){
				
				$tags_html .= '
						<a title="'.JText::sprintf('TXT_TAGGED_ARTICLES', CJFunctions::escape($tag->tag_text)).' - '.CJFunctions::escape($tag->description).'" class="tooltip-hover label tag-item"
							href="'.JRoute::_('index.php?option='.CJBLOG.'&view=articles&task=tag&id='.$tag->tag_id.':'.$tag->alias.$articles_itemid).'">
							<i class="icon-tag icon-white"></i> '.CJFunctions::escape($tag->tag_text).'
						</a>';
			}
			
			$tags_html .= '</div>';
		}
				
		
		$avatar = CJFunctions::get_user_avatar(
				$params->get('user_avatar', 'cjblog'), $article->created_by, $user_name, 48, null, array(), array('class'=>'media-object'));
		$user_profile_link = CJFunctions::get_user_profile_link($params->get('user_avatar', 'cjblog'), $article->created_by, $profile[$user_name]);
		
		$html_top = '
			<div class="well well-small media">
				<div class="pull-left thumbnail">'.$avatar.'</div>
				<div class="media-body">
					<div class="muted">'.($show_author == 1 ? JText::sprintf('TXT_WRITTEN_BY', $user_profile_link).' ' : '').$meta_header.'</div>
					<div style="margin-top: 5px;" class="clearfix">
						<div class="pull-left">'.$meta_stats.'</div>
						'.$meta_rating_readonly.'
					</div>
				</div>
				'.($sharing_position == 'top' ? '<hr/>'.$social_buttons : '').'
			</div>';
		
		$html_bottom = 
			$tags_html . '
			<div class="well well-small well-transperant media">
				'.$meta_rating.'
				'.($sharing_position == 'bottom' ? $social_buttons.'<hr/>' : '').'
				<div class="pull-left thumbnail">'.$avatar.'</div>
				<div class="media-body">
					<h4 class="media-heading">'.JText::sprintf('TXT_AUTHOR', CJFunctions::escape($profile[$user_name])).'</h4>
					<div style="margin: 5px 0;">'.CjBlogApi::get_user_badges_markup($profile).'</div>
					'.(!empty($profile[$user_name]) ? CJFunctions::substrws($profile['about']).'...' : '').'
				</div>
			</div>
			<input id="cjblog_page_id" value="article" type="hidden">';
		
		$article->text = '<div id="cj-wrapper">'.$html_top . $article->text . $html_bottom.'</div>';
	}
	
	public function onContentAfterDisplay($context, $article, $params, $page = 0 ){
		
		$app = JFactory::getApplication();
		$menu = $app->getMenu();
		
		if (($menu->getActive() == $menu->getDefault()) || $context != 'com_content.article' || $page > 0 || empty($article->id) || $app->input->getCmd('view') != 'article' || $app->input->getCmd('print') == 1) {
		
			return '';
		}
		
		// Check if the plugin is disabled for the article category
		$exclude_categories = $this->params->get('exclude_categories');
		
		if(!empty($exclude_categories)){
				
			$exclude_categories = explode(',', $exclude_categories);
			if(in_array($article->catid, $exclude_categories)){
		
				return '';
			}
		}
		
		$meta_credits = '<div style="text-align: center;">Powered by <a rel="follow" href="http://www.corejoomla.com">CjBlog</a></div>';
		
		return $meta_credits;
	}
	
	public function onContentAfterSave($context, &$article, $isNew){
		
		if ($context != 'com_content.form' || empty($article->id)) {
		
			return true;
		}
		
		$api = JPATH_ROOT.DS.'components'.DS.CJBLOG.DS.'api.php';
		
		if(file_exists($api)){

			require_once $api;
			
			$db = JFactory::getDbo();

			// insert or update tags
			$app = JFactory::getApplication();
			$tags = $app->input->post->getString('tags', '');
			$this->insert_tags($article->id, $tags);

			$query = 'select count(*) from #__content where created_by = '.$article->created_by.' and state = 1';
			$db->setQuery($query);
			$count = (int)$db->loadResult();
			
			CjBlogApi::trigger_badge_rule('com_content.num_articles', array('num_articles'=>$count, 'ref_id'=>$article->created_by), $article->created_by);

			if($isNew){
				
				CjBlogApi::award_points('com_content.newarticle', $article->created_by, 0, $article->id, $article->title);
				
				$query = 'update #__cjblog_users set num_articles = '.$count.' where id = '.$article->created_by;
				$db->setQuery($query);
				$db->query();
			}
		}else{
		
			die('CjBlog component is not installed.');
		}
	}
	
	public function onContentBeforeDelete($context, $article){
		
		if ($context != 'com_content.article') {
		
			return true;
		}
		
		$api = JPATH_ROOT.DS.'components'.DS.CJBLOG.DS.'api.php';
			
		if(file_exists($api) && !empty($article->created_by) && !empty($article->id)){
		
			$db = JFactory::getDbo();
		
			$query = 'select count(*) from #__content where created_by = '.$article->created_by.' and state = 1';
			$db->setQuery($query);
			$count = $db->loadResult();
		
			require_once $api;
			CjBlogApi::trigger_badge_rule('com_content.delete_article', array('num_articles'=>$count), $article->created_by);
			CjBlogApi::award_points('com_content.deletearticle', $article->created_by, 0, $article->id, $article->title);
		}else{
				
			die('CjBlog component is not installed.');
		}
	}
	
	public function onContentChangeState($context, $pks, $value){
		
		if ($context != 'com_content.article') {
		
			return true;
		}
		
		$api = JPATH_ROOT.DS.'components'.DS.CJBLOG.DS.'api.php';
		
		if(file_exists($api)){

			$db = JFactory::getDbo();
			JArrayHelper::toInteger($pks);

			$query = '
					select 
						count(*) as articles, created_by 
					from 
						#__content 
					where 
						created_by in (select created_by from #__content where id in ('.implode(',', $pks).')) and state = 1
					group by 
						created_by';
			
			$db->setQuery($query);
			$contents = $db->loadObjectList();
						
			if(count($contents) > 0){
				
				require_once $api;
				
				foreach ($contents as $content){
					
					CjBlogApi::trigger_badge_rule('com_content.num_articles', array('num_articles'=>$content->articles), $content->created_by);
					
					$query = 'update #__cjblog_users set num_articles = '.$content->articles.' where id = '.$content->created_by;
					$db->setQuery($query);
					$db->query();
				}
			}
		}else{
				
			die('CjBlog component is not installed.');
		}
	}
	
	private function insert_tags($id, $strtags){

		$tags = explode(',', $strtags);
		$db = JFactory::getDbo();
		CjBlogApi::set_logging(true);
		
		// first filter out the tags
		foreach($tags as $i=>$tag){
				
			$tag = preg_replace('/[^-\pL.\x20]/u', '', $tag);
			if(empty($tag)) unset($tags[$i]);
		}
		
		// now if there are any new tags, insert them.
		if(!empty($tags)){
				
			$inserts = array();
			$sqltags = array();
			
			foreach($tags as $tag){
		
				$alias = JFilterOutput::stringURLUnicodeSlug($tag);
				$inserts[] = '('.$db->quote($tag).','.$db->quote($alias).')';
				$sqltags[] = $db->quote($tag);
			}
			
			$query = 'insert ignore into #__cjblog_tags (tag_text, alias) values '.implode(',', $inserts);
			$db->setQuery($query);
			
			if(!$db->query()){
				
				if(CjBlogApi::$_enable_logging){
						
					JLog::add('Insert Tags - 101 - Error: '.$db->getErrorMsg().'| Query: '.$query, JLog::ERROR, CJBLOG);
				}
				
				return false;
			}
					
			// we need to get all tag ids matching the input tags
			$query = 'select id from #__cjblog_tags where tag_text in ('.implode(',', $sqltags).')';
			$db->setQuery($query);
			$insertids = $db->loadColumn();

			if(!empty($insertids)){
		
				$mapinserts = array();
				$statinserts = array();
		
				foreach($insertids as $insertid){
						
					$mapinserts[] = '('.$insertid.','.$id.')';
					$statinserts[] = '('.$insertid.','.'1)';
				}
		
				$query = 'insert ignore into #__cjblog_tagmap(tag_id, item_id) values '.implode(',', $mapinserts);
				$db->setQuery($query);

				if(!$db->query()){

					if(CjBlogApi::$_enable_logging){
					
						JLog::add('Insert Tags - 102 - Error: '.$db->getErrorMsg().'| Query: '.$query, JLog::ERROR, CJBLOG);
					}
					
					return false;
				}
				
				$query = 'insert ignore into #__cjblog_tags_stats(tag_id, num_items) values '.implode(',', $statinserts);
				$db->setQuery($query);
				
				if(!$db->query()){

					if(CjBlogApi::$_enable_logging){
					
						JLog::add('Insert Tags - 103 - Error: '.$db->getErrorMsg().'| Query: '.$query, JLog::ERROR, CJBLOG);
					}
					
					return false;
				}
			}
			
			// now remove all non-matching tags ids from the map
			$where = '';
			
			if(!empty($insertids)){
				
				$where = ' and tag_id not in ('.implode(',', $insertids).')';
			}

			$query = 'select tag_id from #__cjblog_tagmap where item_id = '.$id.$where;
			$db->setQuery($query);
			$removals = $db->loadColumn();

			$where = '';
			
			if(!empty($removals)){
				
				$query = 'delete from #__cjblog_tagmap where tag_id in ('.implode(',', $removals).')';
				$db->setQuery($query);
				$db->query();
				
				$where = ' or s.tag_id in ('.implode(',', $removals).')';
			}

			// now update the stats
			$query = '
				update
					#__cjblog_tags_stats s
				set
					s.num_items = (select count(*) from #__cjblog_tagmap m where m.tag_id = s.tag_id)
				where
					s.tag_id in (select tag_id from #__cjblog_tagmap m1 where m1.item_id = '.$id.')'.$where;
			
			$db->setQuery($query);
			
			if(!$db->query()){

				if(CjBlogApi::$_enable_logging){
						
					JLog::add('Insert Tags - 104 - Error: '.$db->getErrorMsg().'| Query: '.$query, JLog::ERROR, CJBLOG);
				}
			}
		} else {
			
			$query = 'delete from #__cjblog_tagmap where item_id = '.$id;
			$db->setQuery($query);
			$db->query();
		}
	}
}