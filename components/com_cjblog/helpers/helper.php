<?php
/**
 * @version		$Id: helper.php 01 2012-08-24 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.helpers
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class CjBlogHelper {
	
	public static function get_config($rebuild=false) {
		 
		$app = JFactory::getApplication();
		$config = $app->getUserState( BLOG_SESSION_CONFIG );
	
		if(empty($config) || $rebuild) {
			 
			$db = JFactory::getDBO();
	
			$query = 'SELECT config_name, config_value FROM '. T_CJBLOG_CONFIG;
			$db->setQuery($query);
			$configt = $db->loadObjectList();
	
			if($configt) {
				 
				foreach($configt as $ct) {
					 
					$config[$ct->config_name] = $ct->config_value;
				}
			}else {
				 
				return CJFunctions::throw_error(JText::_('MSG_UNAUTHORISED').' Error code: 10001.', 403);
			}
	
			$app->setUserState( BLOG_SESSION_CONFIG, $config );
		}
	
		return $config;
	}
	
	public static function get_first_image($html, $size=256){
	
		preg_match_all('/<img .*src=["|\']([^"|\']+)/i', $html, $matches);
	
		foreach ($matches[1] as $key=>$value) {
				
			return $value;
		}
	
		return CJBLOG_MEDIA_URI.'images/'.($size >= 160 ? 'thumbnail-big.png' : 'thumbnail-small.png');
	}
	
	public static function get_intro_text($string,$min=10,$clean=false) {
		$string = str_replace('<br />',' ',$string);
		$string = str_replace('</p>',' ',$string);
		$string = str_replace('<li>',' ',$string);
		$string = str_replace('</li>',' ',$string);
		$text = trim(strip_tags($string));
		if(strlen($text)>$min) {
			$blank = strpos($text,' ');
			if($blank) {
				# limit plus last word
				$extra = strpos(substr($text,$min),' ');
				$max = $min+$extra;
				$r = substr($text,0,$max);
				if(strlen($text)>=$max && !$clean) $r=trim($r,'.').'...';
			} else {
				# if there are no spaces
				$r = substr($text,0,$min).'...';
			}
		} else {
			# if original length is lower than limit
			$r = $text;
		}
		return trim($r);
	}
	
	public static function get_category_table($categories, $params, $options){
		
		if(empty($categories)) return '';
		
		//************************** PARAMS *********************************//
		$class = isset($options['class']) ? $options['class'] : 'category-table';
		$base_url = $options['base_url'];
		$itemid = $options['itemid'];
		$categories_excluded = $params->get('exclude_categories', array());
		//************************** PARAMS *********************************//
		$count_of_excluded = 0;
		
		foreach ($categories as $category){
			
			if(in_array($category->id, $categories_excluded)){
				
				$count_of_excluded++;
			}
		}
		
		$content = '<div class="'.$class.'" id="'.$class.'"><div class="row-fluid">';
		$column_span = 12 / $params->get('max_category_columns', 3);
		$categories_per_column = ceil((count($categories) - $count_of_excluded) / $params->get('max_category_columns', 3));
		$num_subcategories = 0;
		$i = 0;
		
		foreach ($categories as $category){
			
			if(!in_array($category->id, $categories_excluded)){
				
				if($i % $categories_per_column == 0){
					
					$content = $content .'<div class="span'.$column_span.'">';
				}
				
				$content = $content . '<ul class="category"><li class="parent">';
				$content = $content . '<a href="'.JRoute::_($base_url.'&id='.$category->id.':'.$category->alias.$itemid).'">'.CJFunctions::escape($category->title).'</a>';
				
				if($params->get('show_cat_num_articles')){
					
					$content = $content . ' <span class="muted">('.$category->numitems.')</span>';
				}
				
				if($params->get('show_base_description')){
					
					$content = $content . '<div>'.$category->description.'</div>';
				}
				
				
				if($params->get('show_base_image')){
					
					$category_params = json_decode($category->params);
					
					if(!empty($category_params) && !empty($category_params->image)){
						
						$content = $content . '<img class="img-polaroid padbottom-5" src="'.$category_params->image.'"/>';
					}
				}
				
				$content = $content . '</li>';
				
				$children = $category->getChildren();
				
				if(!empty($children)){
					
					$num_subcategories = 1;
					
					foreach ($children as $child){
						
						if(!in_array($child->id, $categories_excluded)){
							
							$content = $content.'<li>';
							$content = $content.'<a href="'.JRoute::_($base_url.'&id='.$child->id.':'.$child->alias.$itemid).'">'.CJFunctions::escape($child->title).'</a>';
										
							if($params->get('show_cat_num_articles')){
								
								$content = $content.' <span class="muted">('.$child->numitems.')</span></li>';
							}
							
							$content = $content.'</li>';
						}
						
						if($num_subcategories == $params->get('max_category_subitems')){
							
							break;
						}
					}
				}
				
				$content = $content . '</ul>';
				
				if(($i % $categories_per_column == $categories_per_column - 1) || ($i+1 == count($categories))){
					
					$content = $content .'</div>';
				}
				
				$i++;
			} // end if the category not excluded
		} // end for
		
		$content = $content . '</div></div>';
		
		return $content;
	}
	
	public static function get_category_table_reccursive($categories, $params){
		
		//************************** PARAMS *********************************//
		$base_url = $params['base_url'];
		$itemid = $params['itemid'];
		$parent_level = isset($params['parent_level']) ? $params['parent_level'] : 0;
		$max_columns = isset($params['max_columns']) ? $params['max_columns'] : 3;
		$max_subitems = isset($params['max_subitems']) ? $params['max_subitems'] : 5;
		$class = isset($params['class']) ? $params['class'] : 'category-table';
		$level = isset($params['level_column']) ? $params['level_column'] : 'nlevel';
		$exclude_categories = isset($params['exclude_categories']) ? explode(',', $params['exclude_categories']) : array();
		//************************** PARAMS *********************************//
		
		
		$content = '<div class="'.$class.'" id="'.$class.'">';
		
		$current_column = 0;
		$current_item = 0;
		$parent_categories = array();
		$category_level = $parent_level + 1;
		
		// get top level categories first
		foreach($categories as $category){
			
			if($category->$level == $category_level && !in_array($category->id, $exclude_categories)){
				
				$parent_categories[] = $category;
			}
		}
		
		if(count($parent_categories) > 0){
			
			// now we get number of parent categories, lets split to columns
			$categories_per_column = ceil(count($parent_categories) / $max_columns);
			
			$cursor = 0;
			$total_categories = count($categories);
			
			for($col = 0; $col < $max_columns; $col++){
				
				$content = $content .'<div class="span'.round(12/$max_columns).'">';
				$previous_column = 'none';
				$column_parent_count = 0;
				$sub_category_count = 0;
				
				for($i = $cursor; $i < $total_categories; $i++){
					
					$category = $categories[$i];
					
					if(in_array($category->id, $exclude_categories)){
						
						if($i+1 == $total_categories){
							
							break;
						}
						
						$temp = $categories[++$i];
						
						while($temp->$level > $category->$level || $i == $total_categories){
							
							$temp = $categories[++$i];
						}
						
						if($i == $total_categories){
							
							break;
						}
						
						$category = $temp;
					}
					
					$category_url = JRoute::_($base_url.'&id='.$category->id.':'.$category->alias.$itemid);
					
					if($category->$level == $category_level){
						
						if($previous_column != 'none'){
							
							$content = $content . '</ul>';
						}
						
						if($column_parent_count == $categories_per_column){
							
							$cursor = $i;
							break;
						}
						
						$content = $content . '<ul class="category"><li class="parent"><a href="'.$category_url.'">'.CJFunctions::escape($category->title).'</a>';
						
						if($params['show_cat_num_articles']){
							
							$content = $content . ' <span class="muted">('.$category->numitems.')</span></li>';
						}
						
						if($params['show_base_description']){
							
							$content = $content . '<div>'.$category->description.'</div>';
						}
						
						
						if($params['show_base_image']){
							
							$category_params = json_decode($category->params);
							
							if(!empty($category_params) && !empty($category_params->image)){
								
								$content = $content . '<img class="img-polaroid padbottom-5" src="'.$category_params->image.'"/>';
							}
						}
						
						$previous_column = 'parent';
						$column_parent_count++;
						$sub_category_count = 0;
					} else if ($category->$level == $category_level + 1 && ($sub_category_count < $max_subitems || $max_subitems == -1)){
						
						$content = $content . '<li><a href="'.$category_url.'">'.CJFunctions::escape($category->title).'</a>';
						
						if($params['show_cat_num_articles']){
							
							$content = $content . ' <span class="muted">('.$category->numitems.')</span></li>';
						}
						
						$previous_column = 'child';
						$sub_category_count++;
					}
					
					if($i == ($total_categories - 1)){
						
						$cursor = $i + 1;
						$content = $content . '</ul>';
					}
				}
				
				$content = $content . '</div>';
			}
		}
		
		$content = $content . '</div>';
		
		return $content;
	}
	
	public static function get_page_title($title){
		
		$app = JFactory::getApplication();
		
		if ($app->getCfg('sitename_pagetitles', 0) == 1) {
			
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		} elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		
		return $title;
	}
}