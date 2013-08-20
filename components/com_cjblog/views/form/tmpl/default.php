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
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');

$user = JFactory::getUser();
$itemid = CJFunctions::get_active_menu_id(true, 'index.php?option='.CJBLOG.'&view=form');
$redirect_url = JFactory::getApplication()->input->getString('return', '');
$article_itemid = CJFunctions::get_active_menu_id(true, 'index.php?option=com_content&view=form&layout=edit');

$languages = JLanguageHelper::createLanguageList($this->article->language, JPATH_SITE, true, true);
array_unshift($languages, array('text'=>JText::_('JALL'), 'value'=>'*'));

$categories = JHtml::_('category.categories', 'com_content');

if(!empty($this->excluded_categories) && !empty($categories)){
	
	foreach ($categories as $id=>$category){
		
		if(in_array($category->value, $this->excluded_categories)) {
			
			unset($categories[$id]);
		}
	}
}

$status = array(
			array('value'=>1, 'text'=>JText::_('JPUBLISHED')),
			array('value'=>0, 'text'=>JText::_('JUNPUBLISHED')),
			array('value'=>2, 'text'=>JText::_('JARCHIVED')),
			array('value'=>-2, 'text'=>JText::_('JTRASHED')),
		);
$page_heading = $this->params->get('page_heading');
$active_id = 8;
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'article.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		} else {
			alert('Invalid form');
		}
	};

	jQuery(document).ready(function($){
		$('.inputbox').find('input[type="text"],textarea').addClass('span8');
	});
</script>

<div id="cj-wrapper">

	<?php include_once JPATH_COMPONENT.DS.'helpers'.DS.'header.php';?>
	
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span12">
	    
		    	<?php if(!empty($page_heading)):?>
		    	<h1 class="nopad-top padbottom-5 page-header"><?php echo $this->escape($page_heading);?></h1>
		    	<?php endif;?>
			
				<form id="adminForm" name="adminForm" class="form form-validate" method="post" action="<?php echo JRoute::_('index.php?option=com_content'.$article_itemid); ?>">
				
					<h2><?php echo JText::_('LBL_FILL_BASIC_DETAILS');?>:</h2>
					
					<table class="table table-hover table-striped">
						<tr>
							<th class="span3"><?php echo JText::_('LBL_TITLE');?><sup>*</sup></th>
							<td class="inputbox">
								<input type="text" 
									name="jform[title]" 
									id="jform_title" 
									value="<?php echo $this->escape($this->article->title);?>" 
									class="inputbox required span8" 
									size="30" 
									aria-required="true" 
									required="required">
							</td>
						</tr>
						<tr>
							<td class="span3"><?php echo JText::_('LBL_ALIAS');?></td>
							<td class="inputbox">
								<input type="text" 
									name="jform[alias]" 
									id="jform_alias" 
									value="<?php echo $this->escape($this->article->alias);?>" 
									class="inputbox span8" 
									size="30">
							</td>
						</tr>
						<tr>
							<th><?php echo JText::_('LBL_CATEGORY');?><sup>*</sup></th>
							<td><?php echo JHtml::_('select.genericlist', $categories, 'jform[catid]', array('list.select'=>$this->article->catid));?></td>
						</tr>
						<?php if($user->authorise('core.edit.state')):?>
						<tr>
							<th><?php echo JText::_('LBL_STATUS');?><sup>*</sup></th>
							<td><?php echo JHtml::_('select.genericlist', $status, 'jform[state]', array('list.select'=>$this->article->state));?></td>
						</tr>
						<tr>
							<td><?php echo JText::_('LBL_PUBLISH_UP');?></td>
							<td>
								<?php echo JHtml::_('calendar', $this->article->publish_up, 'publish_up', 'publish_up', '%Y-%m-%d %H:%M:%S', array('placeholder' => 'yyyy-mm-dd')); ?>
							</td>
						</tr>
						<tr>
							<td><?php echo JText::_('LBL_PUBLISH_DOWN');?></td>
							<td>
								<?php echo JHtml::_('calendar', $this->article->publish_down, 'publish_down', 'publish_down', '%Y-%m-%d %H:%M:%S', array('placeholder' => 'yyyy-mm-dd')); ?>
							</td>
						</tr>
						<?php endif;?>
						<tr>
							<td><?php echo JText::_('LBL_LANGUAGE');?></td>
							<td><?php echo JHtml::_('select.genericlist', $languages, 'jform[language]', array('list.select'=>$this->article->language));?></td>
						</tr>
					</table>
					
					<h2><?php echo JText::_('LBL_WRITE_ARTICLE_HERE');?>:</h2>
					<?php echo CJFunctions::load_editor('wysiwyg', 'jform_articletext', 'jform[articletext]', $this->article->articletext, '5', '40', '100%', '200px', 'required');?>
					
					<h2><?php echo JText::_('LBL_WRITE_META_INFORMATION');?>:</h2>
					<table class="table table-hover">
						<tr>
							<td class="span3"><?php echo JText::_('LBL_META_KEY'); ?></td>
							<td class="inputbox">
								<textarea 
									name="jform[metakey]" 
									id="jform_metakey" 
									cols="50" rows="5" 
									class="inputbox span8" 
									aria-invalid="false"><?php echo $this->escape($this->article->metakey);?></textarea>
							</td>
						</tr>
						<tr>
							<td><?php echo JText::_('LBL_META_DESCRIPTION'); ?></td>
							<td class="inputbox">
								<textarea 
									name="jform[metadesc]" 
									id="jform_metadesc" 
									cols="50" 
									rows="5" 
									class="inputbox span8" 
									aria-invalid="false"><?php echo $this->escape($this->article->metadesc);?></textarea>
							</td>
						</tr>
						<tr>
							<td><?php echo JText::_('LBL_TAGS');?>:</td>
							<td>
								<input type="text" size="40" id="input-tags" name="input-tags" data-provide="typeahead"
									class="input-xlarge" autocomplete="off" placeholder="<?php echo JText::_('TXT_FLDHLP_TAGS');?>">
								<div class="cjblog-tags">
									<ul class="clearfix unstyled">
										<?php foreach($this->article->tags as $tag):?>
										<?php if(!empty($tag)):?>
										<li class="label">
											<a href="#" class="btn-remove-tag" onclick="return false;"><i class="icon-remove icon-white"></i></a>&nbsp;
											<span class="tag-item"><?php echo $this->escape($tag)?></span>
										</li>
										<?php endif;?>
										<?php endforeach;?>
									</ul>
									<input type="hidden" name="tags" value="">
								</div>
							</td>
						</tr>
					</table>
					
					<div class="form-actions formelm-buttons">
						<button class="btn" type="button" onclick="Joomla.submitbutton('article.cancel')"><?php echo JText::_('JCANCEL') ?></button>
						<div class="pull-right">
							<button class="btn btn-primary btn-submit-form" type="button"><?php echo JText::_('JSUBMIT') ?></button>
						</div>
					</div>
					
					<input type="hidden" name="a_id" value="<?php echo $this->article->id;?>">
					<input type="hidden" name="id" value="<?php echo $this->article->id;?>">
					<input type="hidden" name="task" value="article.apply" />
					<input type="hidden" name="return" value="<?php echo $redirect_url?>" />
					
					<?php echo JHtml::_( 'form.token' ); ?>
				</form>
			</div>
		</div>
	</div>
</div>
<div style="display: none;">
	<div id="url-get-tags"><?php echo JRoute::_('index.php?option='.CJBLOG.'&view=articles&task=get_tags'.$articles_itemid)?></div>
</div>