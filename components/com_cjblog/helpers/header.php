<?php 
/**
 * @version		$Id: header.php 01 2012-08-22 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.site
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

$categories_itemid = CJFunctions::get_active_menu_id(true, 'index.php?option='.CJBLOG.'&view=categories');
$users_itemid = CJFunctions::get_active_menu_id(true, 'index.php?option='.CJBLOG.'&view=users');
$user_itemid = CJFunctions::get_active_menu_id(true, 'index.php?option='.CJBLOG.'&view=user');
$blog_itemid = CJFunctions::get_active_menu_id(true, 'index.php?option='.CJBLOG.'&view=blog');
$profile_itemid = CJFunctions::get_active_menu_id(true, 'index.php?option='.CJBLOG.'&view=profile');
$articles_itemid = CJFunctions::get_active_menu_id(true, 'index.php?option='.CJBLOG.'&view=articles');
$search_itemid = CJFunctions::get_active_menu_id(true, 'index.php?option='.CJBLOG.'&view=search');
$badges_itemid = CJFunctions::get_active_menu_id(true, 'index.php?option='.CJBLOG.'&view=badges');
$form_itemid = CJFunctions::get_active_menu_id(true, 'index.php?option='.CJBLOG.'&view=form');
$tags_itemid = CJFunctions::get_active_menu_id(true, 'index.php?option='.CJBLOG.'&view=tags');

$user = JFactory::getUser();
$app = JFactory::getApplication();
?>
<?php if($this->params->get('show_header_bar') == 1):?>
<div class="navbar">
	<div class="navbar-inner">
		<div class="header-container">

			<a class="btn btn-navbar" data-toggle="collapse" data-target=".cjblog-nav-collapse"> 
				<span class="icon-bar"></span> 
				<span class="icon-bar"></span> 
				<span class="icon-bar"></span>
			</a>
			 
			<?php if($active_id == 1):?>
			<a class="brand" href="#" onclick="return false"><?php echo JText::_('LBL_CATEGORIES');?></a>
			<?php elseif($active_id == 2):?>
			<a class="brand" href="#" onclick="return false"><?php echo JText::_('LBL_BLOGGERS');?></a>
			<?php elseif($active_id == 3):?>
			<a class="brand" href="#" onclick="return false"><?php echo JText::_('LBL_ARTICLES');?></a>
			<?php elseif($active_id == 4):?>
			<a class="brand" href="#" onclick="return false"><?php echo JText::sprintf('TXT_USERS_BLOG', $this->user['name']);?></a>
			<?php elseif($active_id == 5):?>
			<a class="brand" href="#" onclick="return false"><?php echo JText::_('LBL_PROFILE');?></a>
			<?php elseif($active_id == 6):?>
			<a class="brand" href="#" onclick="return false"><?php echo JText::_('LBL_ACCOUNT');?></a>
			<?php elseif($active_id == 7):?>
			<a class="brand" href="#" onclick="return false"><?php echo JText::_('LBL_BADGES');?></a>
			<?php elseif($active_id == 8):?>
			<a class="brand" href="#" onclick="return false"><?php echo JText::_('LBL_SUBMIT_ARTICLE');?></a>
			<?php elseif($active_id == 9):?>
			<a class="brand" href="#" onclick="return false"><?php echo JText::_('LBL_TAGS');?></a>
			<?php endif;?>
			
			<div class="cjblog-nav-collapse nav-collapse collapse">
				<ul class="nav">
					
					<li <?php echo $active_id == 1 ? 'class="active"' : '';?>><a href="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=categories'.$categories_itemid)?>"><?php echo JText::_('LBL_CATEGORIES');?></a></li>
					<li <?php echo $active_id == 2 ? 'class="active"' : '';?>><a href="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=users'.$users_itemid)?>"><?php echo JText::_('LBL_BLOGGERS');?></a></li>
					
					<?php if(!empty($tags_itemid)):?>
					<li <?php echo $active_id == 9 ? 'class="active"' : '';?>><a href="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=tags'.$tags_itemid)?>"><?php echo JText::_('LBL_TAGS');?></a></li>
					<?php endif;?>
					
					<li class="dropdown<?php echo ($active_id == 3 || $active_id == 7) ? ' active' : '';?>">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo JText::_('LBL_DISCOVER');?> <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<?php if(!empty($articles_itemid)):?>
							<li class="nav-header"><?php echo JText::_('LBL_ARTICLES');?></li>
							<li>
								<a href="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=articles&task=latest'.$articles_itemid);?>">
									<i class="icon-tasks"></i> <?php echo JText::_('LBL_MOST_RECENT');?>
								</a>
							</li>
							<li>
								<a href="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=articles&task=trending'.$articles_itemid);?>">
									<i class="icon-star"></i> <?php echo JText::_('LBL_TRENDING_ARTICLES');?>
								</a>
							</li>
							<li>
								<a href="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=articles&task=popular'.$articles_itemid);?>">
									<i class="icon-fire"></i> <?php echo JText::_('LBL_ALL_TIME_POPULAR');?>
								</a>
							</li>
							<?php endif;?>
							
							<?php if(!empty($users_itemid)):?>
							<li class="divider"></li>
							<li class="nav-header"><?php echo JText::_('LBL_BLOGGERS');?></li>
							<li>
								<a href="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=users&task=top'.$users_itemid)?>">
									<i class="icon-star"></i> <?php echo JText::_('LBL_TOP_BLOGGERS');?>
								</a>
							</li>
							<li>
								<a href="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=users'.$users_itemid)?>">
									<i class="icon-user"></i> <?php echo JText::_('LBL_NEW_BLOGGERS');?>
								</a>
							</li>
							<?php endif;?>
							
							<?php if(!empty($badges_itemid)):?>
							<li class="divider"></li>
							<li class="nav-header"><?php echo JText::_('LBL_BADGES');?></li>
							<li>
								<a href="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=badges'.$badges_itemid)?>">
									<i class="icon-tags"></i> <?php echo JText::_('LBL_VIEW_ALL_BADGES');?>
								</a>
							</li>
							<?php endif;?>
							
							<?php if (!empty($search_itemid)):?>
							<li class="divider"></li>
							<li class="nav-header"><?php echo JText::_('LBL_SEARCH');?></li>
							<li><a href="#"><i class="icon-search"></i> <?php echo JText::_('LBL_ADVANCED_SEARCH')?></a></li>
							<?php endif;?>
						</ul>
					</li>

					<?php if(!$user->guest):?>
					<li class="dropdown<?php echo ($active_id == 5 || $active_id == 6) ? ' active' : '';?>">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo JText::_('LBL_ACCOUNT');?> <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<?php if(!empty($profile_itemid)):?>
							<li>
								<a href="<?php echo CJFunctions::get_user_profile_link($this->params->get('user_profile', 'cjblog'), $user->id, '', array(), null, true);?>">
									<i class="icon-user"></i> <?php echo JText::_('LBL_MY_PROFILE');?>
								</a>
							</li>
							<?php endif;?>
							<?php if(!empty($blog_itemid)):?>
							<li>
								<a href="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=blog&id='.$user->id.':'.$user->username.$blog_itemid);?>">
									<i class="icon-book"></i> <?php echo JText::_('LBL_MY_BLOG');?>
								</a>
							</li>
							<?php endif;?>
							<li class="divider"></li>
							<?php if(!empty($articles_itemid)):?>
							<li>
								<a href="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=articles&task=favorites'.$articles_itemid);?>">
									<i class="icon-bookmark"></i> <?php echo JText::_('LBL_MY_FAVORITES');?>
								</a>
							</li>
							<?php endif;?>
							<?php if(!empty($user_itemid)):?>
							<li>
								<a href="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=user&task=articles&id='.$user->id.':'.$user->username.$user_itemid);?>">
									<i class="icon-pencil"></i> <?php echo JText::_('LBL_MY_ARTICLES');?>
								</a>
							</li>
							<li>
								<a href="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=user'.$user_itemid);?>">
									<i class="icon-gift"></i> <?php echo JText::_('LBL_MY_POINTS');?>
								</a>
							</li>
							<?php endif;?>
						</ul>
					</li>
					<?php endif;?>

				</ul>
			</div>
		</div>
	</div>
</div>
<?php endif;?>