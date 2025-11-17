<?php
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
?>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            
            <h1><?php echo Text::_('COM_ADMISSION_DASHBOARD_TITLE'); ?></h1>
            
            <div class="alert alert-info">
                <h4><?php echo Text::_('COM_ADMISSION_WELCOME'); ?></h4>
                <p><?php echo Text::_('COM_ADMISSION_DASHBOARD_DESC'); ?></p>
            </div>
            
            <!-- Статистика -->
            <div class="row-striped">
                <div class="row-fluid">
                    
                    <!-- Карточка статистики -->
                    <div class="span3">
                        <div class="card bg-primary text-white p-3" style="background: #337ab7; color: white; padding: 15px; border-radius: 4px;">
                            <h3 style="margin: 0 0 10px 0; font-size: 24px;"><?php echo $this->stats['total']; ?></h3>
                            <p style="margin: 0;"><?php echo Text::_('COM_ADMISSION_TOTAL_APPLICATIONS'); ?></p>
                        </div>
                    </div>
                    
                    <div class="span3">
                        <div class="card bg-success text-white p-3" style="background: #5cb85c; color: white; padding: 15px; border-radius: 4px;">
                            <h3 style="margin: 0 0 10px 0; font-size: 24px;"><?php echo $this->stats['published']; ?></h3>
                            <p style="margin: 0;"><?php echo Text::_('COM_ADMISSION_PUBLISHED'); ?></p>
                        </div>
                    </div>
                    
                    <div class="span3">
                        <div class="card bg-warning text-dark p-3" style="background: #f0ad4e; color: #333; padding: 15px; border-radius: 4px;">
                            <h3 style="margin: 0 0 10px 0; font-size: 24px;"><?php echo $this->stats['pending']; ?></h3>
                            <p style="margin: 0;"><?php echo Text::_('COM_ADMISSION_PENDING'); ?></p>
                        </div>
                    </div>
                    
                    <div class="span3">
                        <div class="card bg-info text-white p-3" style="background: #5bc0de; color: white; padding: 15px; border-radius: 4px;">
                            <h3 style="margin: 0 0 10px 0; font-size: 24px;"><?php echo $this->stats['approved']; ?></h3>
                            <p style="margin: 0;"><?php echo Text::_('COM_ADMISSION_APPROVED'); ?></p>
                        </div>
                    </div>
                    
                </div>
            </div>
            
            <!-- Быстрые действия -->
            <div class="row-fluid" style="margin-top: 30px;">
                <div class="span6">
                    <div class="well well-small">
                        <h3><?php echo Text::_('COM_ADMISSION_QUICK_ACTIONS'); ?></h3>
                        <div class="btn-group-vertical" style="width: 100%;">
                            <a href="<?php echo Route::_('index.php?option=com_admission&view=items'); ?>" 
                               class="btn btn-primary btn-large" style="text-align: left; margin-bottom: 5px;">
                                <i class="icon-list"></i> <?php echo Text::_('COM_ADMISSION_MANAGE_APPLICATIONS'); ?>
                            </a>
                            <a href="<?php echo Route::_('index.php?option=com_admission&task=item.add'); ?>" 
                               class="btn btn-success btn-large" style="text-align: left; margin-bottom: 5px;">
                                <i class="icon-plus"></i> <?php echo Text::_('COM_ADMISSION_ADD_APPLICATION'); ?>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="span6">
                    <div class="well well-small">
                        <h3><?php echo Text::_('COM_ADMISSION_RECENT_ACTIVITY'); ?></h3>
                        <div class="alert alert-info">
                            <?php echo Text::_('COM_ADMISSION_NO_RECENT_ACTIVITY'); ?>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>