<?php
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;  // ДОБАВИТЬ эту строку

HTMLHelper::_('behavior.core');

// Функция помощник должна быть определена ДО использования
function getStatusClass($status) {
    switch ($status) {
        case 'approved': return 'success';
        case 'rejected': return 'danger';
        case 'pending': 
        default: return 'warning';
    }
}
?>
<div class="com-admission-items">
    <h1 class="component-heading"><?php echo Text::_('COM_ADMISSION_ITEMS_TITLE'); ?></h1>
    
    <?php if (empty($this->items)) : ?>
        <div class="alert alert-info">
            <?php echo Text::_('COM_ADMISSION_NO_ITEMS_FOUND'); ?>
        </div>
    <?php else : ?>
        <div class="alert alert-success">
            Found <strong><?php echo count($this->items); ?></strong> admission applications
        </div>
        
        <div class="items-grid">
            <?php foreach ($this->items as $item) : ?>
                <div class="card admission-item mb-4">
                    <div class="card-header">
                        <h3 class="card-title mb-0"><?php echo $this->escape($item->title); ?></h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($item->description)) : ?>
                            <p class="card-text"><?php echo $this->escape($item->description); ?></p>
                        <?php endif; ?>
                        
                        <div class="item-meta mt-3">
                            <?php if (!empty($item->email)) : ?>
                                <div class="meta-item">
                                    <strong>Email:</strong> 
                                    <?php echo $this->escape($item->email); ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($item->phone)) : ?>
                                <div class="meta-item">
                                    <strong>Phone:</strong> 
                                    <?php echo $this->escape($item->phone); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="meta-item">
                                <strong>Status:</strong>
                                <span class="badge bg-<?php echo getStatusClass($item->status); ?>">
                                    <?php echo Text::_('COM_ADMISSION_STATUS_' . strtoupper($item->status)); ?>
                                </span>
                            </div>
                            
                            <div class="meta-item">
                                <strong>Created:</strong>
                                <?php echo HTMLHelper::_('date', $item->created, Text::_('DATE_FORMAT_LC3')); ?>
                            </div>
                        </div>
                    </div>
<a href="index.php?option=com_admission&view=item&id=<?php echo (int) $item->id; ?>" 
   class="btn btn-sm btn-primary">
    <?php echo Text::_('COM_ADMISSION_VIEW_DETAILS'); ?> ц
</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>