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
$active_id = 4;

JPluginHelper::importPlugin( 'CjBlog' );
JPluginHelper::importPlugin('content');

$app = JFactory::getApplication();
$dispatcher = JDispatcher::getInstance();

$params = $app->getParams('com_content');

$old_start = $this->pagination->get('limitstart') + $this->pagination->get('limit');
$older_url = JRoute::_('index.php?option='.CJBLOG.'&view=blog&id='.$this->user['id'].':'.$this->user['username'].'&start='.$old_start.$itemid);

$new_start = $this->pagination->get('limitstart') - $this->pagination->get('limit');
$newer_url = JRoute::_('index.php?option='.CJBLOG.'&view=blog&id='.$this->user['id'].':'.$this->user['username'].'&start='.$new_start.$itemid);

$user_avatar = $this->params->get('user_avatar');
$user_name = $this->params->get('user_display_name');
?>
<div id="cj-wrapper">

	<?php include_once JPATH_COMPONENT.DS.'helpers'.DS.'header.php';?>
	
	<div class="container-fluid">	
		<div class="row-fluid">
			<div class="span12">
				<div class="well well-small clearfix">
					<div class="pull-left padright-20"><?php echo $this->user['avatar'];?></div>
					<div>
						<h2 class="nopad-top">
							<?php echo $this->escape($this->user['name'])?> 
							<small>
								( <a href="<?php echo CJFunctions::get_user_profile_link($user_avatar, $this->user['id'], $this->user[$user_name], array(), null, true);?>">
									<?php echo JText::_('LBL_PROFILE');?>
								</a> )
							</small>
						</h2>
						<p><?php echo CjBlogApi::get_user_badges_markup($this->user);?></p>
						<?php if(!empty($this->user['about'])):?>
						<div><?php echo $this->escape($this->user['about'])?></div>
						<?php endif;?>
					</div>
				</div>
				
				<?php if(!empty($this->articles)):?>
				<div>
					<?php foreach($this->articles as $article):?>
					<div class="page-header">
						<h1>
							<a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($article->id.':'.$article->alias, $article->catid.':'.$article->category_alias));?>">
							<?php echo $this->escape($article->title);?>
							</a>
							<?php if($article->hits > $this->params->get('hot_article_num_hits')):?>
							<sup><span class="label label-important"><i class="icon-fire icon-white"></i> <?php echo JText::_('LBL_HOT');?></span></sup>
							<?php endif;?>
						</h1>
						<div class="muted">
							<small class="align-middle">
								<i class="icon-eye-open"></i> <?php echo JText::sprintf('TXT_NUM_HITS', $article->hits)?>.
								<?php echo JText::sprintf(
										'TXT_POSTED_IN_CATEGORY_ON',
										JHtml::link(JRoute::_('index.php?option='.CJBLOG.'&view=category&id='.$article->catid.':'.$article->category_alias.$articles_itemid), $this->escape($article->category_title)),
										CJFunctions::get_localized_date($article->created, 'd F Y')
									);?>
							</small>
						</div>
					</div>
					<?php 
					$article->text = $article->introtext;
					$dispatcher->trigger('onContentPrepare', array ('com_cjblog.blog', &$article, &$params, 0));
					echo $article->text;
					?>
					
					<div class="well well-small cleafix">
						<span class="article-hits"><?php echo JText::sprintf('TXT_NUM_HITS', $article->hits);?></span>
						<div class="pull-right">
							<a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($article->id.':'.$article->alias, $article->catid.':'.$article->category_alias));?>">
								<?php echo JText::_('LBL_READ_MORE');?>
							</a>
						</div>
					</div>
					<?php endforeach;?>
				</div>
				<ul class="pager">
					<li class="previous<?php echo $this->pagination->get('pages.current') == $this->pagination->get('pages.total') ? ' disabled' : '';?>">
						<a href="<?php echo $this->pagination->get('pages.current') == $this->pagination->get('pages.total') ? '#' : $older_url;?>">
							&larr; <?php echo JText::_('LBL_OLDER');?>
						</a>
					</li>
					<li class="next<?php echo $this->pagination->get('pages.current') == 1 ? ' disabled' : '';?>">
						<a href="<?php echo $this->pagination->get('pages.current') == 1 ? '#' : $newer_url;?>"><?php echo JText::_('LBL_NEWER');?> &rarr;</a>
					</li>
				</ul>
				<?php else:?>
				<p><?php echo JText::_('LBL_NO_ARTICLES_FOUND');?></p>
				<?php endif;?>
			</div>
		</div>
	</div>
</div>