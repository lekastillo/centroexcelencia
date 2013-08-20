<?php
/**
 * @version		$Id: default.php 01 2012-08-22 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.site
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
$itemid = CJFunctions::get_active_menu_id();
$page_heading = $this->params->get('page_heading');
$active_id = 2;
?>

<div id="cj-wrapper">
	
	<?php include_once JPATH_COMPONENT.DS.'helpers'.DS.'header.php';?>
	
	<div class="container-fluid">
	
		<?php if(!empty($page_heading)):?>
	    <h1 class="nopad-top padbottom-5 page-header"><?php echo $this->escape($page_heading);?></h1>
	    <?php endif;?>
		<div class="row-fluid">
			<div class="span12">
			    <form action="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=users&task=search')?>">
			    	<div class="input-append padbottom-20">
			    		<input name="search" type="text" class="span3" value="<?php echo $this->state['search'];?>">
			    		<button type="submit" class="btn"><?php echo JText::_('LBL_SEARCH');?></button>
			    	</div>
			    </form>
			</div>
		</div>
		
	    <?php if(!empty($this->users)):?>
			<?php foreach ($this->users as $i=>$user): ?>
			<div class="row-fluid">
				<div class="span12">
					<div class="row-fluid users-row">
						<div class="media">
							<?php if($this->params->get('user_avatar', 'cjblog') != 'none'):?>
							<div class="pull-left thumbnail">
								<?php echo $user['avatar'];?>
							</div>
							<?php endif;?>
							<div class="pull-left thumbnail num-items-block">
								<p class="numheader"><?php echo $user['num_articles'];?></p>
								<span class="muted"><?php echo $user['num_articles'] != 1 ? JText::_('LBL_ARTICLES') : JText::_('LBL_ARTICLE');?></span>
							</div>
							<div class="media-body">
								<h4 class="media-heading"><?php echo $user['link'];?></h4>
								<div class="muted">
									<span class="margin-right-10"><?php echo JText::_('LBL_POINTS').': '.$user['points'];?></span>
									<span class="margin-right-10"><?php echo JText::_('LBL_BADGES').': '.$user['num_badges'];?></span>
								</div>
								<div class="muted padbottom-5"><?php echo CJFunctions::substrws($this->escape($user['about']));?></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php endforeach;?>
			
			<div class="row-fluid">
				<div class="span12">
					<?php 
					echo CJFunctions::get_pagination(
							$this->page_url, 
							$this->pagination->get('pages.start'), 
							$this->pagination->get('pages.current'), 
							$this->pagination->get('pages.total'),
							JFactory::getApplication()->getCfg('list_limit', 20),
							true
						);
					?>
				</div>
			</div>
		<?php else:?>
		<p><?php echo JText::_('LBL_NO_RESULTS_FOUND');?></p>
		<?php endif;?>
	</div>
</div>