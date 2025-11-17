<?php
if (!headers_sent()) {
    header('Content-Type: text/html; charset=utf-8');
}
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\HTML\HTMLHelper;

$app = Factory::getApplication();
$input = $app->input;
$filterState = $input->get('filter_state', '');
$filterStatus = $input->get('filter_status', '');
$filterSearch = $input->get('filter_search', '');
$token = Session::getFormToken();

// Подключаем Bootstrap JS если еще не подключен
HTMLHelper::_('bootstrap.framework');
?>

<form action="<?php echo Route::_('index.php?option=com_admission&view=items'); ?>" method="get" name="adminForm" id="adminForm">
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h1><?php echo Text::_('COM_ADMISSION_MANAGE_APPLICATIONS'); ?></h1>
            
            <!-- Компактная панель фильтров -->
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center py-2">
                    <h5 class="mb-0">
                        <span class="icon-filter" aria-hidden="true"></span>
                        <?php echo Text::_('COM_ADMISSION_FILTERS'); ?>
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="true" aria-controls="filterCollapse">
                        <span class="icon-chevron-up" aria-hidden="true"></span>
                        <span class="sr-only"><?php echo Text::_('JTOGGLE_FILTERS'); ?></span>
                    </button>
                </div>
                
                <div class="collapse show" id="filterCollapse">
                    <div class="card-body py-2">
                        <div class="row align-items-end">
                            <!-- Поиск -->
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-2">
                                <div class="form-group mb-0">
                                    <label for="filter_search" class="small font-weight-bold mb-1">
                                        <?php echo Text::_('COM_ADMISSION_SEARCH'); ?>
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="filter_search" id="filter_search" 
                                               class="form-control" 
                                               value="<?php echo htmlspecialchars($filterSearch, ENT_QUOTES, 'UTF-8'); ?>"
                                               placeholder="<?php echo Text::_('COM_ADMISSION_SEARCH_PLACEHOLDER'); ?>" />

                                    </div>
                                </div>
                            </div>
                            
                            <!-- Статус публикации -->
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-2">
                                <div class="form-group mb-0">
                                    <label for="filter_state" class="small font-weight-bold mb-1">
                                        <?php echo Text::_('JPUBLISHED'); ?>
                                    </label>
                                    <select name="filter_state" id="filter_state" class="form-control form-control-sm">
                                        <option value="">- <?php echo Text::_('JALL'); ?> -</option>
                                        <option value="1" <?php echo $filterState === '1' ? 'selected' : ''; ?>><?php echo Text::_('JPUBLISHED'); ?></option>
                                        <option value="0" <?php echo $filterState === '0' ? 'selected' : ''; ?>><?php echo Text::_('JUNPUBLISHED'); ?></option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Статус заявки -->
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-2">
                                <div class="form-group mb-0">
                                    <label for="filter_status" class="small font-weight-bold mb-1">
                                        <?php echo Text::_('JSTATUS'); ?>
                                    </label>
                                    <select name="filter_status" id="filter_status" class="form-control form-control-sm">
                                        <option value="">- <?php echo Text::_('JALL'); ?> -</option>
                                        <option value="pending" <?php echo $filterStatus === 'pending' ? 'selected' : ''; ?>><?php echo Text::_('COM_ADMISSION_STATUS_PENDING'); ?></option>
                                        <option value="in_review" <?php echo $filterStatus === 'in_review' ? 'selected' : ''; ?>><?php echo Text::_('COM_ADMISSION_STATUS_IN_REVIEW'); ?></option>
                                        <option value="approved" <?php echo $filterStatus === 'approved' ? 'selected' : ''; ?>><?php echo Text::_('COM_ADMISSION_STATUS_APPROVED'); ?></option>
                                        <option value="rejected" <?php echo $filterStatus === 'rejected' ? 'selected' : ''; ?>><?php echo Text::_('COM_ADMISSION_STATUS_REJECTED'); ?></option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Кнопки действий -->
                            <div class="col-xl-3 col-lg-2 col-md-4 mb-2">
                                <div class="form-group mb-0">
                                    <label class="small font-weight-bold mb-1 d-block">&nbsp;</label>
                                    <div class="btn-group btn-group-sm w-100">
                                        <button type="submit" class="btn btn-primary flex-fill">
                                            <span class="icon-search" aria-hidden="true"></span>
                                            <span class="d-none d-md-inline"><?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?></span>
                                        </button>
                                        <a href="<?php echo Route::_('index.php?option=com_admission&view=items'); ?>" 
                                           class="btn btn-secondary flex-fill">
                                            <span class="icon-remove" aria-hidden="true"></span>
                                            <span class="d-none d-md-inline"><?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Быстрые фильтры -->
                            <div class="col-xl-2 col-lg-12 mb-2">
                                <div class="form-group mb-0">
                                    <label class="small font-weight-bold mb-1 d-block">
                                        <?php echo Text::_('COM_ADMISSION_QUICK_ACTIONS'); ?>
                                    </label>
                                    <select class="form-control form-control-sm" onchange="if(this.value) window.location.href=this.value;">
                                        <option value=""><?php echo Text::_('COM_ADMISSION_QUICK_FILTERS'); ?></option>
                                        <option value="<?php echo Route::_('index.php?option=com_admission&view=items&filter_status=pending'); ?>">
                                            → <?php echo Text::_('COM_ADMISSION_STATUS_PENDING'); ?>
                                        </option>
                                        <option value="<?php echo Route::_('index.php?option=com_admission&view=items&filter_status=approved'); ?>">
                                            → <?php echo Text::_('COM_ADMISSION_STATUS_APPROVED'); ?>
                                        </option>
                                        <option value="<?php echo Route::_('index.php?option=com_admission&view=items&filter_status=rejected'); ?>">
                                            → <?php echo Text::_('COM_ADMISSION_STATUS_REJECTED'); ?>
                                        </option>
                                        <option value="<?php echo Route::_('index.php?option=com_admission&view=items&filter_state=1'); ?>">
                                            → <?php echo Text::_('JPUBLISHED'); ?>
                                        </option>
                                        <option value="<?php echo Route::_('index.php?option=com_admission&view=items&filter_state=0'); ?>">
                                            → <?php echo Text::_('JUNPUBLISHED'); ?>
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Активные фильтры (чипсы) -->
            <?php if (!empty($this->activeFilters)): ?>
            <div class="mb-3">
                <div class="d-flex align-items-center flex-wrap">
                    <small class="text-muted mr-2 mb-1">
                        <strong><?php echo Text::_('COM_ADMISSION_ACTIVE_FILTERS'); ?>:</strong>
                    </small>
                    <?php 
                    $filterChips = [];
                    if (isset($this->activeFilters['state'])) {
                        $stateText = $this->activeFilters['state'] == 1 ? Text::_('JPUBLISHED') : Text::_('JUNPUBLISHED');
                        $filterChips[] = '<span class="badge badge-info mr-1 mb-1">' . 
                            Text::_('JPUBLISHED') . ': ' . $stateText . 
                            ' <a href="' . Route::_('index.php?option=com_admission&view=items&filter_state=') . '" class="text-white ml-1" style="text-decoration: none;">×</a></span>';
                    }
                    if (isset($this->activeFilters['status'])) {
                        $statusLabels = [
                            'pending' => Text::_('COM_ADMISSION_STATUS_PENDING'),
                            'in_review' => Text::_('COM_ADMISSION_STATUS_IN_REVIEW'), 
                            'approved' => Text::_('COM_ADMISSION_STATUS_APPROVED'),
                            'rejected' => Text::_('COM_ADMISSION_STATUS_REJECTED')
                        ];
                        $statusText = $statusLabels[$this->activeFilters['status']] ?? $this->activeFilters['status'];
                        $filterChips[] = '<span class="badge badge-info mr-1 mb-1">' . 
                            Text::_('JSTATUS') . ': ' . $statusText . 
                            ' <a href="' . Route::_('index.php?option=com_admission&view=items&filter_status=') . '" class="text-white ml-1" style="text-decoration: none;">×</a></span>';
                    }
                    if (isset($this->activeFilters['search'])) {
                        $filterChips[] = '<span class="badge badge-info mr-1 mb-1">' . 
                            Text::_('JSEARCH_FILTER_LABEL') . ': "' . htmlspecialchars($this->activeFilters['search']) . '"' . 
                            ' <a href="' . Route::_('index.php?option=com_admission&view=items&filter_search=') . '" class="text-white ml-1" style="text-decoration: none;">×</a></span>';
                    }
                    echo implode('', $filterChips);
                    ?>
                    <a href="<?php echo Route::_('index.php?option=com_admission&view=items'); ?>" 
                       class="btn btn-sm btn-outline-danger mb-1 ml-auto">
                        <span class="icon-remove" aria-hidden="true"></span>
                        <?php echo Text::_('COM_ADMISSION_CLEAR_ALL_FILTERS'); ?>
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Остальная часть кода остается без изменений -->
            <!-- Панель действий -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <div class="mb-2">
                    <a href="<?php echo Route::_('index.php?option=com_admission&task=item.add'); ?>" 
                       class="btn btn-success btn-sm">
                        <span class="icon-plus" aria-hidden="true"></span>
                        <?php echo Text::_('COM_ADMISSION_ADD_APPLICATION'); ?>
                    </a>
                    <a href="<?php echo Route::_('index.php?option=com_admission'); ?>" 
                       class="btn btn-outline-primary btn-sm">
                        <span class="icon-dashboard" aria-hidden="true"></span>
                        <?php echo Text::_('COM_ADMISSION_TO_DASHBOARD'); ?>
                    </a>
                </div>
                
                <!-- Счетчик результатов -->
                <div class="mb-2">
                    <div class="alert alert-light py-1 px-3 mb-0 small">
                        <span class="font-weight-bold">
                            <?php echo Text::_('COM_ADMISSION_FOUND_APPLICATIONS'); ?>: 
                            <?php echo isset($this->pagination) ? $this->pagination->total : count($this->items); ?>
                        </span>
                        <?php if (!empty($this->activeFilters)): ?>
                            <span class="text-muted">
                                (<?php echo Text::_('COM_ADMISSION_FILTERED'); ?>)
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Основной контент -->
            <?php if (!empty($this->items)) : ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th width="5%" class="text-center">ID</th>
                                <th width="25%"><?php echo Text::_('COM_ADMISSION_TITLE'); ?></th>
                                <th width="15%"><?php echo Text::_('JGLOBAL_EMAIL'); ?></th>
                                <th width="12%"><?php echo Text::_('COM_ADMISSION_PHONE'); ?></th>
                                <th width="10%"><?php echo Text::_('JSTATUS'); ?></th>
                                <th width="8%"><?php echo Text::_('JPUBLISHED'); ?></th>
                                <th width="15%"><?php echo Text::_('JDATE'); ?></th>
                                <th width="10%" class="text-center"><?php echo Text::_('JACTION'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($this->items as $i => $item) : ?>
                            <tr>
                                <td class="text-center"><strong><?php echo $item->id; ?></strong></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?></strong>
                                    <?php if (!empty($item->description)) : ?>
                                        <br><small class="text-muted"><?php echo htmlspecialchars(mb_substr($item->description, 0, 50), ENT_QUOTES, 'UTF-8') . '...'; ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="mailto:<?php echo htmlspecialchars($item->email, ENT_QUOTES, 'UTF-8'); ?>" class="text-break">
                                        <?php echo htmlspecialchars($item->email, ENT_QUOTES, 'UTF-8'); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($item->phone, ENT_QUOTES, 'UTF-8'); ?></td>
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
                                            'pending' => Text::_('COM_ADMISSION_STATUS_PENDING'),
                                            'approved' => Text::_('COM_ADMISSION_STATUS_APPROVED'),
                                            'rejected' => Text::_('COM_ADMISSION_STATUS_REJECTED'),
                                            'in_review' => Text::_('COM_ADMISSION_STATUS_IN_REVIEW')
                                        ];
                                        echo isset($statusText[$status]) ? $statusText[$status] : $status;
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($item->state == 1) : ?>
                                        <span class="badge badge-success"><?php echo Text::_('JPUBLISHED'); ?></span>
                                    <?php else : ?>
                                        <span class="badge badge-secondary"><?php echo Text::_('JUNPUBLISHED'); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small><?php echo HTMLHelper::_('date', $item->created, Text::_('DATE_FORMAT_LC2')); ?></small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?php echo Route::_('index.php?option=com_admission&task=item.edit&id=' . $item->id); ?>" 
                                           class="btn btn-outline-primary" title="<?php echo Text::_('JACTION_EDIT'); ?>">
                                            <span class="icon-edit" aria-hidden="true"></span>
                                        </a>
                                        <?php if ($item->state == 1) : ?>
                                            <a href="<?php echo Route::_('index.php?option=com_admission&task=items.unpublish&' . $token . '=1&id=' . $item->id); ?>" 
                                               class="btn btn-outline-success" title="<?php echo Text::_('JACTION_UNPUBLISH'); ?>">
                                                <span class="icon-eye-open" aria-hidden="true"></span>
                                            </a>
                                        <?php else : ?>
                                            <a href="<?php echo Route::_('index.php?option=com_admission&task=items.publish&' . $token . '=1&id=' . $item->id); ?>" 
                                               class="btn btn-outline-secondary" title="<?php echo Text::_('JACTION_PUBLISH'); ?>">
                                                <span class="icon-eye-close" aria-hidden="true"></span>
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?php echo Route::_('index.php?option=com_admission&task=items.delete&' . $token . '=1&id=' . $item->id); ?>" 
                                           class="btn btn-outline-danger" 
                                           onclick="return confirm('<?php echo Text::_('COM_ADMISSION_CONFIRM_DELETE'); ?>')"
                                           title="<?php echo Text::_('JACTION_DELETE'); ?>">
                                            <span class="icon-trash" aria-hidden="true"></span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Пагинация -->
                <?php if (isset($this->pagination)): ?>
                <div class="com-admission-pagination mt-3">
                    <?php echo $this->pagination->getListFooter(); ?>
                </div>
                <?php endif; ?>
                
            <?php else : ?>
                <div class="alert alert-warning text-center py-5">
                    <div class="icon-warning icon-4x text-warning mb-3" aria-hidden="true"></div>
                    <h4><?php echo Text::_('COM_ADMISSION_NO_APPLICATIONS_FOUND'); ?></h4>
                    <p class="mb-3">
                        <?php if (!empty($this->activeFilters)): ?>
                            <?php echo Text::_('COM_ADMISSION_NO_APPLICATIONS_FILTER'); ?>
                        <?php else: ?>
                            <?php echo Text::_('COM_ADMISSION_NO_APPLICATIONS'); ?>
                        <?php endif; ?>
                    </p>
                    <div>
                        <a href="<?php echo Route::_('index.php?option=com_admission&task=item.add'); ?>" class="btn btn-success mr-2">
                            <span class="icon-plus" aria-hidden="true"></span> <?php echo Text::_('COM_ADMISSION_ADD_FIRST_APPLICATION'); ?>
                        </a>
                        <?php if (!empty($this->activeFilters)): ?>
                            <a href="<?php echo Route::_('index.php?option=com_admission&view=items'); ?>" class="btn btn-outline-primary">
                                <?php echo Text::_('COM_ADMISSION_SHOW_ALL'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<input type="hidden" name="option" value="com_admission" />
<input type="hidden" name="view" value="items" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<?php echo HTMLHelper::_('form.token'); ?>
</form>

<!-- JavaScript для улучшения UX --><script>
jQuery(document).ready(function($) {
    // Автофокус на поле поиска
    $('#filter_search').focus();
    
    // Простая реализация toggle без зависимостей от Bootstrap events
    $('[data-bs-target="#filterCollapse"]').on('click', function(e) {
        e.preventDefault();
        var $target = $($(this).data('bs-target'));
        var $icon = $(this).find('span[class*="icon-"]');
        
        if ($target.hasClass('show')) {
            $target.collapse('hide');
            $icon.removeClass('icon-chevron-down').addClass('icon-chevron-up');
            $(this).attr('aria-expanded', 'false');
        } else {
            $target.collapse('show');
            $icon.removeClass('icon-chevron-up').addClass('icon-chevron-down');
            $(this).attr('aria-expanded', 'true');
        }
    });
    
    // Быстрое применение фильтров при изменении селектов
    $('#filter_state, #filter_status').on('change', function() {
        $('#adminForm').submit();
    });
    
    // Сохранение состояния фильтров
    try {
        $('#filter_search, #filter_state, #filter_status').on('change input', function() {
            localStorage.setItem('com_admission_filters', JSON.stringify({
                search: $('#filter_search').val(),
                state: $('#filter_state').val(),
                status: $('#filter_status').val()
            }));
        });
        
        var savedFilters = localStorage.getItem('com_admission_filters');
        if (savedFilters) {
            var filters = JSON.parse(savedFilters);
            $('#filter_search').val(filters.search || '');
            $('#filter_state').val(filters.state || '');
            $('#filter_status').val(filters.status || '');
        }
    } catch(e) {
        // Игнорируем ошибки localStorage
    }
});
</script>