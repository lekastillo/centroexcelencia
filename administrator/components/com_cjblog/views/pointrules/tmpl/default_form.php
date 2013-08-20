<?php
/**
 * @version		$Id: default_form.php 01 2012-08-22 11:37:09Z maverick $
 * @package		CoreJoomla.CjBlog
 * @subpackage	Components.admin
 * @copyright	Copyright (C) 2009 - 2012 corejoomla.com, Inc. All rights reserved.
 * @author		Maverick
 * @link		http://www.corejoomla.com/
 * @license		License GNU General Public License version 2 or later
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

JHtml::_('behavior.tooltip');
$user = JFactory::getUser();
?>
<form action="<?php echo JRoute::_('index.php?option='.CJBLOG.'&view=pointrules');?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
	<div class="control-group">
		<label class="control-label"><?php echo JText::_('COM_CJBLOG_TITLE')?><sup>*</sup>:</label>
		<div class="controls"><input type="text" size="40" class="span6 required" name="rule_title" value="<?php echo $this->escape($this->rule->title);?>"></div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo JText::_('COM_CJBLOG_DESCRIPTION')?><sup>*</sup>:</label>
		<div class="controls"><textarea class="span6 required" rows="5" cols="40" name="rule_description"><?php echo $this->escape($this->rule->description);?></textarea></div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo JText::_('COM_CJBLOG_POINTS')?><sup>*</sup>:</label>
		<div class="controls"><input type="text" class="required" name="rule_points" value="<?php echo $this->escape($this->rule->points);?>"/></div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo JText::_('COM_CJBLOG_PUBLISHED')?>:</label>
		<div class="controls">
			<select name="rule_state" size="1" class="required">
				<option value="0"<?php echo ($this->rule->published == 0) ? ' selected="selected"' : '';?>><?php echo JText::_('COM_CJBLOG_UNPUBLISHED');?></option>
				<option value="1"<?php echo ($this->rule->published == 1) ? ' selected="selected"' : '';?>><?php echo JText::_('COM_CJBLOG_PUBLISHED');?></option>
			</select>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo JText::_('COM_CJBLOG_AUTOAPPROVE')?>:</label>
		<div class="controls">
			<select name="rule_auto_approve" size="1" class="required">
				<option value="1"<?php echo ($this->rule->auto_approve == 1) ? ' selected="selected"' : '';?>><?php echo JText::_('COM_CJBLOG_YES');?></option>
				<option value="0"<?php echo ($this->rule->auto_approve == 0) ? ' selected="selected"' : '';?>><?php echo JText::_('COM_CJBLOG_NO');?></option>
			</select>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo JText::_('COM_CJBLOG_ACCESS_LEVEL')?>:</label>
		<div class="controls">
			<?php echo JHTML::_('access.assetgrouplist', 'access', $this->rule->access); ?>
		</div>
	</div>
	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="rule_id" value="<?php echo $this->rule->id;?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>