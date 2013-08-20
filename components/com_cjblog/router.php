<?php
/**
 * @version		$Id: router.php 01 2012-09-20 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.site
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

function CjBlogBuildRoute(&$query) {
	
    static $items;
    $segments	= array();
    
    if(isset($query['task'])) {
    	
        $segments[] = $query['task'];
        unset($query['task']);
    }
    
    if(isset($query['id'])) {
    	
        $segments[] = $query['id'];
        unset($query['id']);
    }
    
    unset($query['view']);
    
    return $segments;
}
/*
 * Function to convert a SEF URL back to a system URL
 */
function CjBlogParseRoute($segments) {
	
    $app = JFactory::getApplication();
    $menu = $app->getMenu();
    $item = $menu->getActive();
    
    $vars = array();
    
    if($item){
    
    	$vars['view'] = $item->query['view'];
    }
    
    if(count($segments) == 2 ){
    	 
    	$vars['task'] = $segments[0];
    	$vars['id'] = $segments[1];
    } elseif(count($segments) == 1 ){

    	if(!empty($vars['view'])){
    		
	    	switch ($vars['view']){
	    		
	    		case 'articles':
	    		case 'users':
	    		case 'tags':
	    			
	    			$vars['task']	= $segments[0];
	    			break;
	    			
	    		default:
	    			
	    			$vars['id'] = $segments[0];
	    			break;
	    	}
    	}
    } elseif(count($segments) == 3) { // this should not come in ideal situation
    	
    	$vars['view'] = $segments[0];
    	$vars['task'] = $segments[1];
    	$vars['id'] = $segments[2];
    }
    
    return $vars;
}
?>