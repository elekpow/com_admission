<?php
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen', 'select');

?>

<form action="<?php echo JRoute::_('index.php?option=com_admission&layout=edit&id=' . (int) $this->item->id); ?>" 
      method="post" name="adminForm" id="item-form" class="form-validate">
    
    <div class="form-horizontal">
        <div class="row-fluid">
            <div class="span9">
                <div class="form-vertical">
                    <?php echo $this->form->renderField('title'); ?>
                    <?php echo $this->form->renderField('description'); ?>
                    <?php echo $this->form->renderField('email'); ?>
                    <?php echo $this->form->renderField('phone'); ?>
                </div>
            </div>
            <div class="span3">
                <div class="well">
                    <?php echo $this->form->renderField('status'); ?>
                    <?php echo $this->form->renderField('state'); ?>
                    <?php echo $this->form->renderField('created'); ?>
                    <?php echo $this->form->renderField('id'); ?>
                    <?php echo $this->form->renderField('created_by'); ?>
                </div>
            </div>
        </div>
    </div>
    
    <input type="hidden" name="task" value="" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>

<script>
Joomla.submitbutton = function(task) {
    if (task == 'item.cancel' || document.formvalidator.isValid(document.getElementById('item-form'))) {
        Joomla.submitform(task, document.getElementById('item-form'));
    }
}
</script>