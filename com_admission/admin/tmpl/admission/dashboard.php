<?php
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
?>
<div class="admin-dashboard">
    <h1><?php echo Text::_('COM_ADMISSION_DASHBOARD_TITLE'); ?></h1>
    
    <div class="alert alert-info">
        <h4><?php echo Text::_('COM_ADMISSION_WELCOME'); ?></h4>
        <p><?php echo Text::_('COM_ADMISSION_DASHBOARD_DESC'); ?></p>
    </div>
    
    <div class="row-striped">
        <div class="row-fluid">
            <div class="span4">
                <div class="well well-small">
                    <h3><?php echo Text::_('COM_ADMISSION_QUICK_STATS'); ?></h3>
                    <ul class="unstyled">
                        <li><strong>Total Applications:</strong> <?php echo $this->total; ?></li>
                        <li><strong>Published:</strong> <?php echo $this->published; ?></li>
                        <li><strong>Pending:</strong> <?php echo $this->pending; ?></li>
                    </ul>
                </div>
            </div>
            <div class="span4">
                <div class="well well-small">
                    <h3><?php echo Text::_('COM_ADMISSION_QUICK_ACTIONS'); ?></h3>
                    <div class="btn-group-vertical" style="width: 100%;">
                        <a href="<?php echo Route::_('index.php?option=com_admission&view=items'); ?>" 
                           class="btn btn-primary">
                            <i class="icon-list"></i> <?php echo Text::_('COM_ADMISSION_MANAGE_APPLICATIONS'); ?>
                        </a>
                        <a href="<?php echo Route::_('index.php?option=com_admission&task=item.add'); ?>" 
                           class="btn btn-success">
                            <i class="icon-plus"></i> <?php echo Text::_('COM_ADMISSION_ADD_APPLICATION'); ?>
                        </a>
                    </div>
                </div>
            </div>
            <div class="span4">
                <div class="well well-small">
                    <h3><?php echo Text::_('COM_ADMISSION_RECENT_ACTIVITY'); ?></h3>
                    <p><?php echo Text::_('COM_ADMISSION_NO_RECENT_ACTIVITY'); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>