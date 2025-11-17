<?php
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');
?>

<form action="<?php echo Route::_('index.php?option=com_admission&view=items'); ?>" method="post" name="adminForm" id="adminForm">
    
    <div id="j-main-container" class="j-main-container">
        
        <!-- Простой поиск -->
        <div class="filter-search btn-group pull-left">
            <label for="filter_search" class="element-invisible"><?php echo Text::_('JSEARCH_FILTER'); ?></label>
            <input type="text" name="filter_search" id="filter_search" 
                   placeholder="<?php echo Text::_('JSEARCH_FILTER'); ?>" 
                   value="" class="input-medium" />
        </div>
        
        <div class="btn-group pull-left">
            <button type="submit" class="btn hasTooltip" title="<?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>">
                <i class="icon-search"></i>
            </button>
            <button type="button" class="btn hasTooltip" title="<?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?>" 
                    onclick="document.getElementById('filter_search').value='';this.form.submit();">
                <i class="icon-remove"></i>
            </button>
        </div>
        
        <!-- Таблица заявок -->
        <table class="table table-striped" id="itemList">
            <thead>
                <tr>
                    <th width="1%" class="nowrap center">
                        <?php echo HTMLHelper::_('grid.checkall'); ?>
                    </th>
                    <th width="1%" class="nowrap center">
                        <?php echo Text::_('JSTATUS'); ?>
                    </th>
                    <th>
                        <?php echo Text::_('COM_ADMISSION_HEADING_TITLE'); ?>
                    </th>
                    <th width="15%">
                        <?php echo Text::_('COM_ADMISSION_HEADING_EMAIL'); ?>
                    </th>
                    <th width="10%">
                        <?php echo Text::_('COM_ADMISSION_HEADING_STATUS'); ?>
                    </th>
                    <th width="15%">
                        <?php echo Text::_('COM_ADMISSION_HEADING_CREATED'); ?>
                    </th>
                    <th width="1%" class="nowrap">
                        <?php echo Text::_('JGRID_HEADING_ID'); ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($this->items)) : ?>
                    <tr>
                        <td colspan="7" class="center">
                            <div class="alert alert-no-items">
                                <?php echo Text::_('COM_ADMISSION_NO_ITEMS_FOUND'); ?>
                            </div>
                        </td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($this->items as $i => $item) : ?>
                        <tr class="row<?php echo $i % 2; ?>">
                            <td class="center">
                                <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                            </td>
                            <td class="center">
                                <?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'items.', true); ?>
                            </td>
                            <td>
                                <a href="<?php echo Route::_('index.php?option=com_admission&task=item.edit&id=' . (int) $item->id); ?>" 
                                   title="<?php echo Text::_('JACTION_EDIT'); ?>">
                                    <?php echo $this->escape($item->title); ?>
                                </a>
                            </td>
                            <td>
                                <?php echo $this->escape($item->email); ?>
                            </td>
                            <td>
                                <?php
                                $statusClass = 'warning';
                                if ($item->status == 'approved') $statusClass = 'success';
                                if ($item->status == 'rejected') $statusClass = 'important';
                                ?>
                                <span class="label label-<?php echo $statusClass; ?>">
                                    <?php echo Text::_('COM_ADMISSION_STATUS_' . strtoupper($item->status)); ?>
                                </span>
                            </td>
                            <td>
                                <?php echo HTMLHelper::_('date', $item->created, Text::_('DATE_FORMAT_LC4')); ?>
                            </td>
                            <td class="center">
                                <?php echo (int) $item->id; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
    </div>
    
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>