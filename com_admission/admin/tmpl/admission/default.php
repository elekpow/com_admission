<?php
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
?>

<div class="container-fluid admission-dashboard">
    <div class="row">
        <div class="col-md-12">
            <h1>Admission Dashboard</h1>
            
            <!-- Обновленная панель действий -->
            <div class="action-buttons">
                <div class="main-actions">
                    <a href="<?php echo Route::_('index.php?option=com_admission&task=item.add'); ?>" 
                       class="btn btn-primary">
                        <i class="icon-plus"></i> Добавить заявку
                    </a>
                </div>
                
                <div class="filter-actions">
                    <!-- Кнопка "Все заявки" справа -->
                    <a href="<?php echo Route::_('index.php?option=com_admission&view=items'); ?>" 
                       class="btn btn-outline-primary">
                        <i class="icon-list"></i> Все заявки
                    </a>
                    
                    <!-- Кнопки-фильтры в виде значков -->
                    <a href="<?php echo Route::_('index.php?option=com_admission&view=items&filter_status=pending'); ?>" 
                       class="btn btn-warning btn-icon" title="Ожидающие решения">
                        <i class="icon-clock"></i>
                    </a>
                    
                    <a href="<?php echo Route::_('index.php?option=com_admission&view=items&filter_state=1'); ?>" 
                       class="btn btn-success btn-icon" title="Опубликованные">
                        <i class="icon-eye-open"></i>
                    </a>
                    
                    <a href="<?php echo Route::_('index.php?option=com_admission&view=items&filter_state=0'); ?>" 
                       class="btn btn-secondary btn-icon" title="Скрытые">
                        <i class="icon-eye-close"></i>
                    </a>
                </div>
            </div>

            <!-- Статистика в виде карточек -->
            <div class="quick-stats">
                <div class="stat-card">
                    <div class="stat-number text-primary"><?php echo $this->stats['total']; ?></div>
                    <div class="stat-label">Всего заявок</div>
                </div>
                <div class="stat-card success">
                    <div class="stat-number text-success"><?php echo $this->stats['published']; ?></div>
                    <div class="stat-label">Опубликовано</div>
                </div>
                <div class="stat-card secondary">
                    <div class="stat-number text-secondary"><?php echo $this->stats['unpublished']; ?></div>
                    <div class="stat-label">Не опубликовано</div>
                </div>
                <div class="stat-card warning">
                    <div class="stat-number text-warning"><?php echo $this->stats['pending']; ?></div>
                    <div class="stat-label">Ожидают решения</div>
                </div>
            </div>

            <!-- Последние заявки -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Последние заявки</h5>
                    <a href="<?php echo Route::_('index.php?option=com_admission&view=items'); ?>" class="btn btn-sm btn-outline-primary">
                        Все заявки
                    </a>
                </div>
                <div class="card-body">
                    <?php if (!empty($this->items)) : ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Название</th>
                                        <th>Email</th>
                                        <th>Статус</th>
                                        <th>Дата создания</th>
                                        <th width="120">Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($this->items as $item) : ?>
                                    <tr>
                                        <td><strong>#<?php echo $item->id; ?></strong></td>
                                        <td>
                                            <div class="font-weight-bold"><?php echo htmlspecialchars($item->title); ?></div>
                                            <?php if (!empty($item->description)) : ?>
                                                <small class="text-muted"><?php echo htmlspecialchars(mb_substr($item->description, 0, 60)) . '...'; ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($item->email); ?></td>
                                        <td>
                                            <?php
                                            $status = isset($item->status) ? $item->status : 'pending';
                                            $statusClasses = [
                                                'pending' => 'warning',
                                                'approved' => 'success', 
                                                'rejected' => 'danger',
                                                'in_review' => 'info'
                                            ];
                                            $class = isset($statusClasses[$status]) ? $statusClasses[$status] : 'secondary';
                                            ?>
                                            <span class="badge badge-<?php echo $class; ?>">
                                                <?php 
                                                $statusText = [
                                                    'pending' => 'Ожидает',
                                                    'approved' => 'Одобрено',
                                                    'rejected' => 'Отклонено',
                                                    'in_review' => 'На рассмотрении'
                                                ];
                                                echo isset($statusText[$status]) ? $statusText[$status] : $status;
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted"><?php echo date('d.m.Y H:i', strtotime($item->created)); ?></small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo Route::_('index.php?option=com_admission&task=item.edit&id=' . $item->id . '&return=dashboard'); ?>" 
                                                   class="btn btn-outline-primary btn-icon" title="Редактировать">
                                                    <i class="icon-edit"></i>
                                                </a>
                                                <?php if ($item->state == 1) : ?>
                                                    <a href="<?php echo Route::_('index.php?option=com_admission&task=items.unpublish&id=' . $item->id . '&return=dashboard'); ?>" 
                                                       class="btn btn-success btn-icon" title="Снять с публикации">
                                                        <i class="icon-eye-open"></i>
                                                    </a>
                                                <?php else : ?>
                                                    <a href="<?php echo Route::_('index.php?option=com_admission&task=items.publish&id=' . $item->id . '&return=dashboard'); ?>" 
                                                       class="btn btn-secondary btn-icon" title="Опубликовать">
                                                        <i class="icon-eye-close"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <a href="<?php echo Route::_('index.php?option=com_admission&task=items.delete&id=' . $item->id . '&return=dashboard'); ?>" 
                                                   class="btn btn-outline-danger btn-icon" 
                                                   onclick="return confirm('Вы уверены, что хотите удалить эту заявку?')"
                                                   title="Удалить">
                                                    <i class="icon-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else : ?>
                        <div class="text-center py-4">
                            <div class="text-muted mb-3">
                                <i class="icon-inbox icon-3x"></i>
                            </div>
                            <p>Нет заявок для отображения.</p>
                            <a href="<?php echo Route::_('index.php?option=com_admission&task=item.add'); ?>" class="btn btn-primary">
                                Добавить первую заявку
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>


        </div>
    </div>
</div>