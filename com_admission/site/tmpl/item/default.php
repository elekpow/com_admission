<?php
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

?>
<div class="com-admission-item container mt-4">
    <div class="row">
        <div class="col-12">
            <!-- Кнопка назад -->
            <div class="mb-4">
                <a href="index.php?option=com_admission" class="btn btn-secondary">
                    ← <?php echo Text::_('COM_ADMISSION_BACK_TO_LIST'); ?>
                </a>
            </div>
            
            <?php if ($this->item) : ?>
                <!-- Детали заявки -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0"><?php echo htmlspecialchars($this->item->title); ?></h2>
                    </div>
                    <div class="card-body">
                        <!-- Описание -->
                        <?php if (!empty($this->item->description)) : ?>
                            <div class="mb-4">
                                <h4><?php echo Text::_('COM_ADMISSION_DESCRIPTION'); ?></h4>
                                <p class="fs-5"><?php echo htmlspecialchars($this->item->description); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Контактная информация -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h4><?php echo Text::_('COM_ADMISSION_CONTACT_INFO'); ?></h4>
                                <?php if (!empty($this->item->email)) : ?>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($this->item->email); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($this->item->phone)) : ?>
                                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($this->item->phone); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Статус и даты -->
                        <div class="row">
                            <div class="col-md-6">
                                <h4><?php echo Text::_('COM_ADMISSION_APPLICATION_INFO'); ?></h4>
                                <p>
                                    <strong>Status:</strong>
                                    <span class="badge bg-<?php echo $this->getStatusClass($this->item->status); ?> ms-2">
                                        <?php echo Text::_('COM_ADMISSION_STATUS_' . strtoupper($this->item->status)); ?>
                                    </span>
                                </p>
                                <p><strong>Created:</strong> <?php echo HTMLHelper::_('date', $this->item->created, Text::_('DATE_FORMAT_LC3')); ?></p>
                                
                                <?php if (!empty($this->item->modified)) : ?>
                                    <p><strong>Last Updated:</strong> <?php echo HTMLHelper::_('date', $this->item->modified, Text::_('DATE_FORMAT_LC3')); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <!-- Сообщение об ошибке -->
                <div class="alert alert-danger">
                    <h4><?php echo Text::_('COM_ADMISSION_ITEM_NOT_FOUND'); ?></h4>
                    <p>The requested application could not be found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>