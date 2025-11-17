<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

// Get the application
$app = Factory::getApplication();

// Check if we are in the administrator application
if (!$app instanceof AdministratorApplication) {
    $app->enqueueMessage('Component only available in administrator application', 'error');
    return;
}

// Подключаем CSS и JavaScript
HTMLHelper::_('jquery.framework');
$document = Factory::getDocument();
$document->addStyleSheet('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

// Подключаем Bootstrap для пагинации
$document->addStyleSheet('https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css');
//$document->addScript('https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js');

// Простая и надежная загрузка компонента
try {
    // Определяем view и task
    $view = $app->input->get('view', 'admission');
    $task = $app->input->get('task', 'display');
    

// Обработка задач
if ($task === 'save') {
    handleSave();
} elseif ($task === 'delete') {
    handleDelete();
} elseif ($task === 'export_csv') {
    handleExportCSV();
} elseif ($task === 'export_excel') {
    handleExportExcel();
} elseif ($task === 'quick_approve') {
    handleQuickApprove();
} elseif ($task === 'quick_reject') {
    handleQuickReject();
} elseif ($task === 'bulk_action') {
    handleBulkAction();
}
    
    // Роутинг по view
    switch ($view) {
        case 'items':
            showItems();
            break;
        case 'item':
            showItemForm();
            break;
        case 'admission':
        default:
            showDashboard();
            break;
    }
    
} catch (Exception $e) {
    echo '<div class="container-fluid">
        <div class="alert alert-danger">
            <h2>Admission Component - Error</h2>
            <p>' . htmlspecialchars($e->getMessage()) . '</p>
        </div>
    </div>';
}

/**
 * Показать улучшенный дашборд
 */
function showDashboard()
{
    $db = Factory::getDbo();
    
    // Полная статистика
    $stats = getStatistics();
    $recentItems = getRecentItems(10);
    
    echo '<div class="container-fluid">';
    
    // Шапка дашборда
    echo '<div class="row">';
    echo '<div class="col-12">';
    echo '<div class="dashboard-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 2rem; border-radius: 10px; margin-bottom: 2rem;">';
    echo '<div class="row align-items-center">';
    echo '<div class="col-md-8">';
    echo '<h1 style="margin: 0; font-size: 2.5rem; font-weight: 300;"><i class="fas fa-graduation-cap me-3"></i>Admission Portal</h1>';
    echo '<p style="margin: 0.5rem 0 0 0; opacity: 0.9;">Manage student applications efficiently</p>';
    echo '</div>';
    echo '<div class="col-md-4 text-end">';
    echo '<a href="index.php?option=com_admission&view=item" class="btn btn-light btn-lg">';
    echo '<i class="fas fa-plus me-2"></i>New Application';
    echo '</a>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    
    // Статистика в карточках
    echo '<div class="row mb-4">';
    
    // Total Applications
    echo '<div class="col-xl-3 col-md-6 mb-4">';
    echo '<div class="card border-left-primary shadow h-100 py-2">';
    echo '<div class="card-body">';
    echo '<div class="row no-gutters align-items-center">';
    echo '<div class="col mr-2">';
    echo '<div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Applications</div>';
    echo '<div class="h5 mb-0 font-weight-bold text-gray-800">' . $stats['total'] . '</div>';
    echo '</div>';
    echo '<div class="col-auto">';
    echo '<i class="fas fa-clipboard-list fa-2x text-gray-300"></i>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    
    // Pending Review
    echo '<div class="col-xl-3 col-md-6 mb-4">';
    echo '<div class="card border-left-warning shadow h-100 py-2">';
    echo '<div class="card-body">';
    echo '<div class="row no-gutters align-items-center">';
    echo '<div class="col mr-2">';
    echo '<div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Review</div>';
    echo '<div class="h5 mb-0 font-weight-bold text-gray-800">' . $stats['pending'] . '</div>';
    echo '</div>';
    echo '<div class="col-auto">';
    echo '<i class="fas fa-clock fa-2x text-gray-300"></i>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    
    // Approved
    echo '<div class="col-xl-3 col-md-6 mb-4">';
    echo '<div class="card border-left-success shadow h-100 py-2">';
    echo '<div class="card-body">';
    echo '<div class="row no-gutters align-items-center">';
    echo '<div class="col mr-2">';
    echo '<div class="text-xs font-weight-bold text-success text-uppercase mb-1">Approved</div>';
    echo '<div class="h5 mb-0 font-weight-bold text-gray-800">' . $stats['approved'] . '</div>';
    echo '</div>';
    echo '<div class="col-auto">';
    echo '<i class="fas fa-check-circle fa-2x text-gray-300"></i>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    
    // Rejected
    echo '<div class="col-xl-3 col-md-6 mb-4">';
    echo '<div class="card border-left-danger shadow h-100 py-2">';
    echo '<div class="card-body">';
    echo '<div class="row no-gutters align-items-center">';
    echo '<div class="col mr-2">';
    echo '<div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Rejected</div>';
    echo '<div class="h5 mb-0 font-weight-bold text-gray-800">' . $stats['rejected'] . '</div>';
    echo '</div>';
    echo '<div class="col-auto">';
    echo '<i class="fas fa-times-circle fa-2x text-gray-300"></i>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    
    echo '</div>'; // End stats row
    
    // Основной контент - таблица заявок и быстрые действия
    echo '<div class="row">';
    
    // Таблица последних заявок
    echo '<div class="col-lg-8">';
    echo '<div class="card shadow mb-4">';
    echo '<div class="card-header py-3 d-flex justify-content-between align-items-center">';
    echo '<h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list me-2"></i>Recent Applications</h6>';
    echo '<a href="index.php?option=com_admission&view=items" class="btn btn-sm btn-primary">View All</a>';
    echo '</div>';
    echo '<div class="card-body">';
    
    if (empty($recentItems)) {
        echo '<div class="text-center py-4">';
        echo '<i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>';
        echo '<p class="text-muted">No applications found</p>';
        echo '</div>';
    } else {
        echo '<div class="table-responsive">';
        echo '<table class="table table-bordered table-hover" id="recentTable" width="100%" cellspacing="0">';
        echo '<thead class="thead-light">';
        echo '<tr>';
        echo '<th>Applicant</th>';
        echo '<th>Email</th>';
        echo '<th>Status</th>';
        echo '<th>Date</th>';
        echo '<th>Actions</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        foreach ($recentItems as $item) {
            $statusClass = getStatusClass($item->status);
            $statusIcon = getStatusIcon($item->status);
            
            echo '<tr>';
            echo '<td>';
            echo '<div class="font-weight-bold">' . htmlspecialchars($item->title) . '</div>';
            echo '<small class="text-muted">' . ($item->phone ? htmlspecialchars($item->phone) : 'No phone') . '</small>';
            echo '</td>';
            echo '<td>' . htmlspecialchars($item->email) . '</td>';
            echo '<td>';
            echo '<span class="badge badge-' . $statusClass . '">';
            echo '<i class="' . $statusIcon . ' me-1"></i>' . ucfirst($item->status);
            echo '</span>';
            echo '</td>';
            echo '<td>' . date('M j, Y', strtotime($item->created)) . '</td>';
            echo '<td>';
            echo '<div class="btn-group btn-group-sm">';
            echo '<a href="index.php?option=com_admission&view=item&id=' . $item->id . '" class="btn btn-primary btn-sm" title="Edit">';
            echo '<i class="fas fa-edit"></i>';
            echo '</a>';
            echo '<a href="index.php?option=com_admission&task=delete&id=' . $item->id . '" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm(\'Are you sure?\')">';
            echo '<i class="fas fa-trash"></i>';
            echo '</a>';
            echo '</div>';
            echo '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    }
    
    echo '</div>';
    echo '</div>';
    echo '</div>';
    
    // Боковая панель с быстрыми действиями
    echo '<div class="col-lg-4">';
    
    // Quick Actions
    echo '<div class="card shadow mb-4">';
    echo '<div class="card-header py-3">';
    echo '<h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-bolt me-2"></i>Quick Actions</h6>';
    echo '</div>';
    echo '<div class="card-body">';
    echo '<div class="d-grid gap-2">';
    echo '<a href="index.php?option=com_admission&view=item" class="btn btn-success btn-lg">';
    echo '<i class="fas fa-plus me-2"></i>New Application';
    echo '</a>';
    echo '<a href="index.php?option=com_admission&view=items" class="btn btn-primary btn-lg">';
    echo '<i class="fas fa-list me-2"></i>Manage All';
    echo '</a>';
    echo '<button class="btn btn-info btn-lg" onclick="exportData()">';
    echo '<i class="fas fa-download me-2"></i>Export Data';
    echo '</button>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    
    // Status Overview
    echo '<div class="card shadow">';
    echo '<div class="card-header py-3">';
    echo '<h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-pie me-2"></i>Status Overview</h6>';
    echo '</div>';
    echo '<div class="card-body">';
    
    $total = $stats['total'] ?: 1; // Avoid division by zero
    
    echo '<div class="mb-3">';
    echo '<div class="d-flex justify-content-between mb-1">';
    echo '<span>Pending</span>';
    echo '<span>' . $stats['pending'] . ' (' . round(($stats['pending'] / $total) * 100) . '%)</span>';
    echo '</div>';
    echo '<div class="progress" style="height: 10px;">';
    echo '<div class="progress-bar bg-warning" role="progressbar" style="width: ' . ($stats['pending'] / $total * 100) . '%"></div>';
    echo '</div>';
    echo '</div>';
    
    echo '<div class="mb-3">';
    echo '<div class="d-flex justify-content-between mb-1">';
    echo '<span>Approved</span>';
    echo '<span>' . $stats['approved'] . ' (' . round(($stats['approved'] / $total) * 100) . '%)</span>';
    echo '</div>';
    echo '<div class="progress" style="height: 10px;">';
    echo '<div class="progress-bar bg-success" role="progressbar" style="width: ' . ($stats['approved'] / $total * 100) . '%"></div>';
    echo '</div>';
    echo '</div>';
    
    echo '<div class="mb-3">';
    echo '<div class="d-flex justify-content-between mb-1">';
    echo '<span>Rejected</span>';
    echo '<span>' . $stats['rejected'] . ' (' . round(($stats['rejected'] / $total) * 100) . '%)</span>';
    echo '</div>';
    echo '<div class="progress" style="height: 10px;">';
    echo '<div class="progress-bar bg-danger" role="progressbar" style="width: ' . ($stats['rejected'] / $total * 100) . '%"></div>';
    echo '</div>';
    echo '</div>';
    
    echo '</div>';
    echo '</div>';
    
    echo '</div>'; // End sidebar
    echo '</div>'; // End main row
    
    echo '</div>'; // End container
    
    // JavaScript для интерактивности
    echo '
    <script>
    function exportData() {
        alert("Export feature will be implemented soon!");
    }
    
    // Simple search functionality
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("tableSearch");
        if (searchInput) {
            searchInput.addEventListener("keyup", function() {
                const filter = this.value.toLowerCase();
                const rows = document.querySelectorAll("#recentTable tbody tr");
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(filter) ? "" : "none";
                });
            });
        }
    });
    </script>
    
    <style>
    .card {
        border: none;
        border-radius: 10px;
    }
    .card-header {
        border-bottom: 1px solid #e3e6f0;
        background: white;
    }
    .table th {
        border-top: none;
        font-weight: 600;
        color: #6e707e;
    }
    .btn {
        border-radius: 6px;
    }
    .progress {
        border-radius: 10px;
    }
    .badge {
        border-radius: 6px;
        padding: 6px 12px;
        font-weight: 500;
    }
    </style>
    ';
}

/**
 * Получить статистику
 */
function getStatistics()
{
    $db = Factory::getDbo();
    
    $stats = [];
    
    // Общее количество
    $query = $db->getQuery(true)
        ->select('COUNT(*)')
        ->from($db->quoteName('#__admission_items'));
    $db->setQuery($query);
    $stats['total'] = $db->loadResult();
    
    // Опубликованные
    $query = $db->getQuery(true)
        ->select('COUNT(*)')
        ->from($db->quoteName('#__admission_items'))
        ->where($db->quoteName('state') . ' = 1');
    $db->setQuery($query);
    $stats['published'] = $db->loadResult();
    
    // В ожидании
    $query = $db->getQuery(true)
        ->select('COUNT(*)')
        ->from($db->quoteName('#__admission_items'))
        ->where($db->quoteName('status') . ' = ' . $db->quote('pending'))
        ->where($db->quoteName('state') . ' = 1');
    $db->setQuery($query);
    $stats['pending'] = $db->loadResult();
    
    // Одобренные
    $query = $db->getQuery(true)
        ->select('COUNT(*)')
        ->from($db->quoteName('#__admission_items'))
        ->where($db->quoteName('status') . ' = ' . $db->quote('approved'))
        ->where($db->quoteName('state') . ' = 1');
    $db->setQuery($query);
    $stats['approved'] = $db->loadResult();
    
    // Отклоненные
    $query = $db->getQuery(true)
        ->select('COUNT(*)')
        ->from($db->quoteName('#__admission_items'))
        ->where($db->quoteName('status') . ' = ' . $db->quote('rejected'))
        ->where($db->quoteName('state') . ' = 1');
    $db->setQuery($query);
    $stats['rejected'] = $db->loadResult();
    
    return $stats;
}

/**
 * Получить последние заявки
 */
function getRecentItems($limit = 10)
{
    $db = Factory::getDbo();
    $query = $db->getQuery(true)
        ->select('*')
        ->from($db->quoteName('#__admission_items'))
        ->where($db->quoteName('state') . ' = 1')
        ->order('created DESC')
        ->setLimit($limit);
    
    $db->setQuery($query);
    return $db->loadObjectList();
}

/**
 * Получить CSS класс для статуса
 */
function getStatusClass($status)
{
    switch ($status) {
        case 'approved': return 'success';
        case 'rejected': return 'danger';
        case 'pending': 
        default: return 'warning';
    }
}

/**
 * Получить иконку для статуса
 */
function getStatusIcon($status)
{
    switch ($status) {
        case 'approved': return 'fas fa-check-circle';
        case 'rejected': return 'fas fa-times-circle';
        case 'pending': 
        default: return 'fas fa-clock';
    }
}

// Остальные функции showItems(), showItemForm(), handleSave(), handleDelete() остаются
// но их также нужно обновить в современном стиле...

/**
 * Показать улучшенный список заявок с поиском
 */
/**
 * Показать улучшенный список заявок с поиском и пагинацией
 */
function showItems()
{
    $db = Factory::getDbo();
    $app = Factory::getApplication();
    
    // Параметры поиска и пагинации
    $search = $app->input->getString('search', '');
    $statusFilter = $app->input->getString('status', '');
    $limit = $app->input->getInt('limit', 20); // Количество элементов на странице
    $limitstart = $app->input->getInt('limitstart', 0); // Начальная позиция
    
    // Получаем общее количество записей
    $query = $db->getQuery(true)
        ->select('COUNT(*)')
        ->from($db->quoteName('#__admission_items'))
        ->where($db->quoteName('state') . ' IN (0,1)');
    
    // Применяем фильтры для подсчета общего количества
    if (!empty($search)) {
        $searchTerm = $db->quote('%' . $db->escape($search, true) . '%');
        $query->where('(title LIKE ' . $searchTerm . ' OR email LIKE ' . $searchTerm . ' OR description LIKE ' . $searchTerm . ')');
    }
    
    if (!empty($statusFilter) && $statusFilter !== 'all') {
        $query->where('status = ' . $db->quote($statusFilter));
    }
    
    $db->setQuery($query);
    $total = $db->loadResult();
    
    // Основной запрос с пагинацией
    $query = $db->getQuery(true)
        ->select('*')
        ->from($db->quoteName('#__admission_items'))
        ->order('created DESC');
    
    // Применяем фильтры
    if (!empty($search)) {
        $searchTerm = $db->quote('%' . $db->escape($search, true) . '%');
        $query->where('(title LIKE ' . $searchTerm . ' OR email LIKE ' . $searchTerm . ' OR description LIKE ' . $searchTerm . ')');
    }
    
    if (!empty($statusFilter) && $statusFilter !== 'all') {
        $query->where('status = ' . $db->quote($statusFilter));
    }
    
    $db->setQuery($query, $limitstart, $limit);
    $items = $db->loadObjectList();
    
    // Вычисляем пагинацию
    $totalPages = ceil($total / $limit);
    $currentPage = floor($limitstart / $limit) + 1;
    
    echo '<div class="container-fluid">';
    
    // Шапка страницы
    echo '<div class="d-sm-flex align-items-center justify-content-between mb-4">';
    echo '<h1 class="h3 mb-0 text-gray-800"><i class="fas fa-list me-2"></i>Admission Applications</h1>';
    echo '<a href="index.php?option=com_admission&view=item" class="d-none d-sm-inline-block btn btn-success shadow-sm">';
    echo '<i class="fas fa-plus me-2"></i>New Application';
    echo '</a>';
    echo '</div>';
    
    // Панель поиска, фильтров и управления пагинацией
    echo '<div class="card shadow mb-4">';
    echo '<div class="card-body">';
    echo '<div class="row">';
    
    // Поиск
    echo '<div class="col-md-5">';
    echo '<div class="input-group">';
    echo '<input type="text" class="form-control" placeholder="Search applications..." id="tableSearch" value="' . htmlspecialchars($search) . '">';
    echo '<button class="btn btn-primary" type="button" onclick="performSearch()">';
    echo '<i class="fas fa-search"></i>';
    echo '</button>';
    echo '</div>';
    echo '</div>';
    
    // Фильтр по статусу
    echo '<div class="col-md-3">';
    echo '<select class="form-select" id="statusFilter" onchange="performSearch()">';
    echo '<option value="all"' . ($statusFilter === '' ? ' selected' : '') . '>All Statuses</option>';
    echo '<option value="pending"' . ($statusFilter === 'pending' ? ' selected' : '') . '>Pending</option>';
    echo '<option value="approved"' . ($statusFilter === 'approved' ? ' selected' : '') . '>Approved</option>';
    echo '<option value="rejected"' . ($statusFilter === 'rejected' ? ' selected' : '') . '>Rejected</option>';
    echo '</select>';
    echo '</div>';
    
    // Количество элементов на странице
    echo '<div class="col-md-2">';
    echo '<select class="form-select" id="itemsPerPage" onchange="changeItemsPerPage()">';
    echo '<option value="10"' . ($limit == 10 ? ' selected' : '') . '>10 per page</option>';
    echo '<option value="20"' . ($limit == 20 ? ' selected' : '') . '>20 per page</option>';
    echo '<option value="50"' . ($limit == 50 ? ' selected' : '') . '>50 per page</option>';
    echo '<option value="100"' . ($limit == 100 ? ' selected' : '') . '>100 per page</option>';
    echo '</select>';
    echo '</div>';
    
    // Сброс фильтров и экспорт
    echo '<div class="col-md-2">';
    echo '<div class="btn-group w-100">';
    echo '<button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">';
    echo '<i class="fas fa-download me-1"></i>Export';
    echo '</button>';
    echo '<ul class="dropdown-menu">';
    echo '<li><a class="dropdown-item" href="index.php?option=com_admission&task=export_csv&search=' . urlencode($search) . '&status=' . urlencode($statusFilter) . '">';
    echo '<i class="fas fa-file-csv me-2"></i>Export as CSV';
    echo '</a></li>';
    echo '<li><a class="dropdown-item" href="index.php?option=com_admission&task=export_excel&search=' . urlencode($search) . '&status=' . urlencode($statusFilter) . '">';
    echo '<i class="fas fa-file-excel me-2"></i>Export as Excel';
    echo '</a></li>';
    echo '</ul>';
    echo '</div>';
    echo '</div>';
    
    echo '</div>';
    echo '</div>';
    echo '</div>';
    
    // Информация о пагинации
    echo '<div class="row mb-3">';
    echo '<div class="col-12">';
    echo '<div class="d-flex justify-content-between align-items-center">';
    echo '<div class="text-muted">';
    $startItem = $limitstart + 1;
    $endItem = min($limitstart + $limit, $total);
    echo 'Showing ' . $startItem . ' to ' . $endItem . ' of ' . $total . ' entries';
    if (!empty($search)) {
        echo ' (filtered from total)';
    }
    echo '</div>';
    echo '<div>';
    echo '<a href="index.php?option=com_admission&view=items" class="btn btn-outline-secondary btn-sm">Clear Filters</a>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    
    // Таблица заявок
    echo '<div class="card shadow">';
    echo '<div class="card-header py-3 d-flex justify-content-between align-items-center">';
    echo '<h6 class="m-0 font-weight-bold text-primary">Applications List</h6>';
    echo '<div class="btn-group">';
    echo '<button class="btn btn-outline-primary btn-sm" onclick="exportToCSV()">';
    echo '<i class="fas fa-download me-1"></i>Export';
    echo '</button>';
    echo '</div>';
    echo '</div>';
    echo '<div class="card-body">';
    
    if (empty($items)) {
        echo '<div class="text-center py-5">';
        echo '<i class="fas fa-inbox fa-4x text-gray-300 mb-3"></i>';
        echo '<h5 class="text-gray-500">No applications found</h5>';
        if (!empty($search) || !empty($statusFilter)) {
            echo '<p class="text-muted">Try adjusting your search criteria</p>';
            echo '<a href="index.php?option=com_admission&view=items" class="btn btn-primary mt-2">Clear Filters</a>';
        } else {
            echo '<p class="text-muted">Get started by creating your first application</p>';
            echo '<a href="index.php?option=com_admission&view=item" class="btn btn-success mt-2">';
            echo '<i class="fas fa-plus me-2"></i>Create First Application';
            echo '</a>';
        }
        echo '</div>';
    } else {
        echo '<div class="table-responsive">';
        echo '<table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">';
        echo '<thead class="thead-dark">';
        echo '<tr>';
        echo '<th width="30"><input type="checkbox" id="selectAll"></th>';
        echo '<th>Application</th>';
        echo '<th>Contact Info</th>';
        echo '<th width="100">Status</th>';
        echo '<th width="120">Date</th>';
        echo '<th width="150">Actions</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        foreach ($items as $item) {
            $statusClass = getStatusClass($item->status);
            $statusIcon = getStatusIcon($item->status);
            
            echo '<tr>';
            echo '<td><input type="checkbox" class="rowCheckbox" value="' . $item->id . '"></td>';
            echo '<td>';
            echo '<div class="font-weight-bold text-primary">' . htmlspecialchars($item->title) . '</div>';
            echo '<small class="text-muted">' . (strlen($item->description) > 100 ? substr(htmlspecialchars($item->description), 0, 100) . '...' : htmlspecialchars($item->description)) . '</small>';
            echo '</td>';
            echo '<td>';
            echo '<div><i class="fas fa-envelope me-2 text-muted"></i>' . htmlspecialchars($item->email) . '</div>';
            echo '<div><i class="fas fa-phone me-2 text-muted"></i>' . ($item->phone ? htmlspecialchars($item->phone) : '<em class="text-muted">No phone</em>') . '</div>';
            echo '</td>';
            echo '<td>';
            echo '<span class="badge badge-' . $statusClass . ' p-2">';
            echo '<i class="' . $statusIcon . ' me-1"></i>' . ucfirst($item->status);
            echo '</span>';
            echo '</td>';
            echo '<td>';
            echo '<div class="small">' . date('M j, Y', strtotime($item->created)) . '</div>';
            echo '<div class="small text-muted">' . date('g:i A', strtotime($item->created)) . '</div>';
            echo '</td>';
            echo '<td>';
            echo '<div class="btn-group btn-group-sm">';
            echo '<a href="index.php?option=com_admission&view=item&id=' . $item->id . '" class="btn btn-primary" title="Edit">';
            echo '<i class="fas fa-edit"></i>';
            echo '</a>';
            echo '<a href="index.php?option=com_admission&task=quick_approve&id=' . $item->id . '" class="btn btn-success" title="Approve" onclick="return confirm(\'Approve this application?\')">';
            echo '<i class="fas fa-check"></i>';
            echo '</a>';
            echo '<a href="index.php?option=com_admission&task=quick_reject&id=' . $item->id . '" class="btn btn-warning" title="Reject" onclick="return confirm(\'Reject this application?\')">';
            echo '<i class="fas fa-times"></i>';
            echo '</a>';
            echo '<a href="index.php?option=com_admission&task=delete&id=' . $item->id . '" class="btn btn-danger" title="Delete" onclick="return confirm(\'Delete this application?\')">';
            echo '<i class="fas fa-trash"></i>';
            echo '</a>';
            echo '</div>';
            echo '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
        
        // Пагинация
        echo '<div class="row mt-4">';
        echo '<div class="col-md-6">';
        // Массовые действия
        echo '<div class="d-flex align-items-center">';
        echo '<select class="form-select me-2" id="bulkAction" style="width: auto;">';
        echo '<option value="">Bulk Actions</option>';
        echo '<option value="approve">Approve Selected</option>';
        echo '<option value="reject">Reject Selected</option>';
        echo '<option value="delete">Delete Selected</option>';
        echo '</select>';
        echo '<button class="btn btn-primary" onclick="performBulkAction()">Apply</button>';
        echo '</div>';
        echo '</div>';
        echo '<div class="col-md-6">';
        echo generatePagination($total, $limit, $limitstart, $search, $statusFilter);
        echo '</div>';
        echo '</div>';
    }
    
    echo '</div>';
    echo '</div>';
    
    echo '</div>'; // End container
    
    // JavaScript для страницы списка
    echo '
    <script>
    function performSearch() {
        const search = document.getElementById("tableSearch").value;
        const status = document.getElementById("statusFilter").value;
        let url = "index.php?option=com_admission&view=items";
        
        if (search) url += "&search=" + encodeURIComponent(search);
        if (status && status !== "all") url += "&status=" + encodeURIComponent(status);
        
        window.location.href = url;
    }
    
    function changeItemsPerPage() {
        const limit = document.getElementById("itemsPerPage").value;
        const search = document.getElementById("tableSearch").value;
        const status = document.getElementById("statusFilter").value;
        let url = "index.php?option=com_admission&view=items&limit=" + limit;
        
        if (search) url += "&search=" + encodeURIComponent(search);
        if (status && status !== "all") url += "&status=" + encodeURIComponent(status);
        
        window.location.href = url;
    }
    
    function goToPage(page) {
        const limit = ' . $limit . ';
        const limitstart = (page - 1) * limit;
        const search = "' . addslashes($search) . '";
        const status = "' . addslashes($statusFilter) . '";
        
        let url = "index.php?option=com_admission&view=items&limitstart=" + limitstart + "&limit=" + limit;
        
        if (search) url += "&search=" + encodeURIComponent(search);
        if (status && status !== "all") url += "&status=" + encodeURIComponent(status);
        
        window.location.href = url;
    }
    
    // Select all functionality
    document.addEventListener("DOMContentLoaded", function() {
        const selectAll = document.getElementById("selectAll");
        if (selectAll) {
            selectAll.addEventListener("change", function() {
                const checkboxes = document.querySelectorAll(".rowCheckbox");
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
        }
    });
    
    function performBulkAction() {
        const action = document.getElementById("bulkAction").value;
        const selectedIds = Array.from(document.querySelectorAll(".rowCheckbox:checked"))
                                .map(checkbox => checkbox.value);
        
        if (!action) {
            alert("Please select an action");
            return;
        }
        
        if (selectedIds.length === 0) {
            alert("Please select at least one application");
            return;
        }
        
        if (confirm("Are you sure you want to " + action + " " + selectedIds.length + " application(s)?")) {
            // Создаем скрытую форму для отправки
            const form = document.createElement("form");
            form.method = "POST";
            form.action = "index.php?option=com_admission&task=bulk_action";
            
            // Добавляем action
            const actionInput = document.createElement("input");
            actionInput.type = "hidden";
            actionInput.name = "action";
            actionInput.value = action;
            form.appendChild(actionInput);
            
            // Добавляем IDs
            selectedIds.forEach(id => {
                const idInput = document.createElement("input");
                idInput.type = "hidden";
                idInput.name = "ids[]";
                idInput.value = id;
                form.appendChild(idInput);
            });
            
            // Добавляем токен безопасности Joomla
            const tokenInput = document.createElement("input");
            tokenInput.type = "hidden";
            tokenInput.name = "' . JSession::getFormToken() . '";
            tokenInput.value = "1";
            form.appendChild(tokenInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    }
    
    // Enter key for search
    document.getElementById("tableSearch").addEventListener("keypress", function(e) {
        if (e.key === "Enter") {
            performSearch();
        }
    });
    </script>
    ';
}

/**
 * Генерация HTML для пагинации
 */
function generatePagination($total, $limit, $limitstart, $search = '', $statusFilter = '')
{
    if ($total <= $limit) {
        return ''; // Не показывать пагинацию если элементов меньше лимита
    }
    
    $totalPages = ceil($total / $limit);
    $currentPage = floor($limitstart / $limit) + 1;
    
    $html = '<nav aria-label="Page navigation">';
    $html .= '<ul class="pagination pagination-sm justify-content-end mb-0">';
    
    // Кнопка "Назад"
    if ($currentPage > 1) {
        $prevStart = max(0, $limitstart - $limit);
        $html .= '<li class="page-item">';
        $html .= '<a class="page-link" href="' . buildPaginationUrl($prevStart, $limit, $search, $statusFilter) . '" aria-label="Previous">';
        $html .= '<span aria-hidden="true">&laquo;</span>';
        $html .= '</a>';
        $html .= '</li>';
    } else {
        $html .= '<li class="page-item disabled">';
        $html .= '<span class="page-link">&laquo;</span>';
        $html .= '</li>';
    }
    
    // Номера страниц
    $startPage = max(1, $currentPage - 2);
    $endPage = min($totalPages, $currentPage + 2);
    
    for ($i = $startPage; $i <= $endPage; $i++) {
        $pageStart = ($i - 1) * $limit;
        if ($i == $currentPage) {
            $html .= '<li class="page-item active">';
            $html .= '<span class="page-link">' . $i . '</span>';
            $html .= '</li>';
        } else {
            $html .= '<li class="page-item">';
            $html .= '<a class="page-link" href="' . buildPaginationUrl($pageStart, $limit, $search, $statusFilter) . '">' . $i . '</a>';
            $html .= '</li>';
        }
    }
    
    // Кнопка "Вперед"
    if ($currentPage < $totalPages) {
        $nextStart = $limitstart + $limit;
        $html .= '<li class="page-item">';
        $html .= '<a class="page-link" href="' . buildPaginationUrl($nextStart, $limit, $search, $statusFilter) . '" aria-label="Next">';
        $html .= '<span aria-hidden="true">&raquo;</span>';
        $html .= '</a>';
        $html .= '</li>';
    } else {
        $html .= '<li class="page-item disabled">';
        $html .= '<span class="page-link">&raquo;</span>';
        $html .= '</li>';
    }
    
    $html .= '</ul>';
    $html .= '</nav>';
    
    return $html;
}

/**
 * Построение URL для пагинации
 */
function buildPaginationUrl($limitstart, $limit, $search = '', $statusFilter = '')
{
    $url = 'index.php?option=com_admission&view=items&limitstart=' . $limitstart . '&limit=' . $limit;
    
    if (!empty($search)) {
        $url .= '&search=' . urlencode($search);
    }
    
    if (!empty($statusFilter) && $statusFilter !== 'all') {
        $url .= '&status=' . urlencode($statusFilter);
    }
    
    return $url;
}


/**
 * Показать современную форму заявки
 */
function showItemForm()
{
    $app = Factory::getApplication();
    $id = $app->input->getInt('id', 0);
    $db = Factory::getDbo();
    
    $item = null;
    if ($id > 0) {
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__admission_items'))
            ->where($db->quoteName('id') . ' = ' . $id);
        $db->setQuery($query);
        $item = $db->loadObject();
    }
    
    $isEdit = ($id > 0);
    
    echo '<div class="container-fluid">';
    
    // Шапка формы
    echo '<div class="d-sm-flex align-items-center justify-content-between mb-4">';
    echo '<h1 class="h3 mb-0 text-gray-800">';
    echo '<i class="' . ($isEdit ? 'fas fa-edit' : 'fas fa-plus') . ' me-2"></i>';
    echo ($isEdit ? 'Edit Application' : 'New Application');
    echo '</h1>';
    echo '<div class="btn-group">';
    echo '<a href="index.php?option=com_admission&view=items" class="btn btn-secondary">';
    echo '<i class="fas fa-arrow-left me-2"></i>Back to List';
    echo '</a>';
    echo '</div>';
    echo '</div>';
    
    // Основная форма
    echo '<div class="row justify-content-center">';
    echo '<div class="col-xl-8 col-lg-10">';
    echo '<div class="card shadow">';
    echo '<div class="card-header bg-primary text-white">';
    echo '<h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Application Details</h5>';
    echo '</div>';
    echo '<div class="card-body">';
    
    echo '<form action="index.php?option=com_admission&task=save" method="post" id="applicationForm" class="needs-validation" novalidate>';
    echo '<input type="hidden" name="id" value="' . ($item ? $item->id : 0) . '">';
    
    // Основная информация
    echo '<div class="row mb-4">';
    echo '<div class="col-12">';
    echo '<h6 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Basic Information</h6>';
    echo '</div>';
    echo '</div>';
    
    // Title
    echo '<div class="row mb-3">';
    echo '<div class="col-md-12">';
    echo '<label for="title" class="form-label">Application Title <span class="text-danger">*</span></label>';
    echo '<div class="input-group">';
    echo '<span class="input-group-text"><i class="fas fa-heading"></i></span>';
    echo '<input type="text" class="form-control" id="title" name="title" value="' . ($item ? htmlspecialchars($item->title) : '') . '" required placeholder="Enter application title">';
    echo '<div class="invalid-feedback">Please provide a title for the application.</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    
    // Contact Information
    echo '<div class="row mb-4">';
    echo '<div class="col-12">';
    echo '<h6 class="text-primary mb-3"><i class="fas fa-address-card me-2"></i>Contact Information</h6>';
    echo '</div>';
    echo '</div>';
    
    echo '<div class="row mb-3">';
    // Email
    echo '<div class="col-md-6 mb-3">';
    echo '<label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>';
    echo '<div class="input-group">';
    echo '<span class="input-group-text"><i class="fas fa-envelope"></i></span>';
    echo '<input type="email" class="form-control" id="email" name="email" value="' . ($item ? htmlspecialchars($item->email) : '') . '" required placeholder="applicant@example.com">';
    echo '<div class="invalid-feedback">Please provide a valid email address.</div>';
    echo '</div>';
    echo '</div>';
    
    // Phone
    echo '<div class="col-md-6 mb-3">';
    echo '<label for="phone" class="form-label">Phone Number</label>';
    echo '<div class="input-group">';
    echo '<span class="input-group-text"><i class="fas fa-phone"></i></span>';
    echo '<input type="tel" class="form-control" id="phone" name="phone" value="' . ($item ? htmlspecialchars($item->phone) : '') . '" placeholder="+1 (555) 123-4567">';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    
    // Description
    echo '<div class="row mb-4">';
    echo '<div class="col-12">';
    echo '<label for="description" class="form-label">Application Description <span class="text-danger">*</span></label>';
    echo '<textarea class="form-control" id="description" name="description" rows="6" required placeholder="Please provide detailed information about this application...">' . ($item ? htmlspecialchars($item->description) : '') . '</textarea>';
    echo '<div class="form-text">Describe the purpose and details of this application.</div>';
    echo '<div class="invalid-feedback">Please provide a description for the application.</div>';
    echo '</div>';
    echo '</div>';
    
    // Status and Settings
    echo '<div class="row mb-4">';
    echo '<div class="col-12">';
    echo '<h6 class="text-primary mb-3"><i class="fas fa-cog me-2"></i>Settings</h6>';
    echo '</div>';
    echo '</div>';
    
    echo '<div class="row mb-3">';
    // Status
    echo '<div class="col-md-6 mb-3">';
    echo '<label for="status" class="form-label">Application Status <span class="text-danger">*</span></label>';
    echo '<select class="form-select" id="status" name="status" required>';
    echo '<option value="pending" ' . ($item && $item->status == 'pending' ? 'selected' : '') . '>⏳ Pending Review</option>';
    echo '<option value="approved" ' . ($item && $item->status == 'approved' ? 'selected' : '') . '>✅ Approved</option>';
    echo '<option value="rejected" ' . ($item && $item->status == 'rejected' ? 'selected' : '') . '>❌ Rejected</option>';
    echo '</select>';
    echo '</div>';
    
    // State
    echo '<div class="col-md-6 mb-3">';
    echo '<label for="state" class="form-label">Publication Status</label>';
    echo '<select class="form-select" id="state" name="state">';
    echo '<option value="1" ' . ((!$item || $item->state == 1) ? 'selected' : '') . '>📢 Published</option>';
    echo '<option value="0" ' . ($item && $item->state == 0 ? 'selected' : '') . '>📭 Unpublished</option>';
    echo '</select>';
    echo '</div>';
    echo '</div>';
    
    // Кнопки действий
    echo '<div class="row mt-5">';
    echo '<div class="col-12">';
    echo '<div class="d-flex justify-content-between">';
    echo '<div>';
    echo '<a href="index.php?option=com_admission&view=items" class="btn btn-secondary">';
    echo '<i class="fas fa-times me-2"></i>Cancel';
    echo '</a>';
    echo '</div>';
    echo '<div class="btn-group">';
    if ($isEdit) {
        echo '<button type="submit" name="save_type" value="apply" class="btn btn-info">';
        echo '<i class="fas fa-save me-2"></i>Apply';
        echo '</button>';
    }
    echo '<button type="submit" name="save_type" value="save" class="btn btn-success">';
    echo '<i class="fas fa-check me-2"></i>' . ($isEdit ? 'Update' : 'Create');
    echo '</button>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    
    echo '</form>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    
    echo '</div>'; // End container
    
    // JavaScript для валидации формы
    echo '
    <script>
    // Form validation
    (function() {
        "use strict";
        const form = document.getElementById("applicationForm");
        
        form.addEventListener("submit", function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add("was-validated");
        }, false);
    })();
    
    // Character counter for description
    const description = document.getElementById("description");
    if (description) {
        const counter = document.createElement("div");
        counter.className = "form-text text-end";
        description.parentNode.appendChild(counter);
        
        description.addEventListener("input", function() {
            const length = this.value.length;
            counter.textContent = length + " characters" + (length > 500 ? " (Consider being more concise)" : "");
            counter.className = "form-text text-end " + (length > 1000 ? "text-warning" : "text-muted");
        });
        
        // Trigger initial count
        description.dispatchEvent(new Event("input"));
    }
    </script>
    
    <style>
    .form-control:focus, .form-select:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
    .input-group-text {
        background-color: #f8f9fc;
        border-color: #d1d3e2;
    }
    .card {
        border: none;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    .card-header {
        border-bottom: 1px solid #e3e6f0;
    }
    </style>
    ';
}


/**
 * Экспорт данных в CSV
 */
function handleExportCSV()
{
    $db = Factory::getDbo();
    $app = Factory::getApplication();
    
    // Параметры фильтрации (такие же как в списке)
    $search = $app->input->getString('search', '');
    $statusFilter = $app->input->getString('status', '');
    
    $query = $db->getQuery(true)
        ->select('*')
        ->from($db->quoteName('#__admission_items'))
        ->order('created DESC');
    
    // Применяем фильтры
    if (!empty($search)) {
        $search = $db->quote('%' . $db->escape($search, true) . '%');
        $query->where('(title LIKE ' . $search . ' OR email LIKE ' . $search . ' OR description LIKE ' . $search . ')');
    }
    
    if (!empty($statusFilter) && $statusFilter !== 'all') {
        $query->where('status = ' . $db->quote($statusFilter));
    }
    
    $db->setQuery($query);
    $items = $db->loadObjectList();
    
    // Устанавливаем заголовки для скачивания CSV
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=admission_applications_' . date('Y-m-d_H-i-s') . '.csv');
    
    // Создаем output stream
    $output = fopen('php://output', 'w');
    
    // Заголовки CSV
    $headers = [
        'ID',
        'Title',
        'Description', 
        'Email',
        'Phone',
        'Status',
        'State',
        'Created Date',
        'Modified Date',
        'Created By',
        'Modified By'
    ];
    fputcsv($output, $headers);
    
    // Данные
    foreach ($items as $item) {
        $state = $item->state == 1 ? 'Published' : 'Unpublished';
        
        $row = [
            $item->id,
            $item->title,
            $item->description,
            $item->email,
            $item->phone,
            ucfirst($item->status),
            $state,
            $item->created,
            $item->modified ?: 'Never',
            $item->created_by,
            $item->modified_by ?: 'Never'
        ];
        
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit;
}


/**
 * Экспорт данных в Excel (HTML формат)
 */
function handleExportExcel()
{
    $db = Factory::getDbo();
    $app = Factory::getApplication();
    
    // Параметры фильтрации
    $search = $app->input->getString('search', '');
    $statusFilter = $app->input->getString('status', '');
    
    $query = $db->getQuery(true)
        ->select('*')
        ->from($db->quoteName('#__admission_items'))
        ->order('created DESC');
    
    // Применяем фильтры
    if (!empty($search)) {
        $search = $db->quote('%' . $db->escape($search, true) . '%');
        $query->where('(title LIKE ' . $search . ' OR email LIKE ' . $search . ' OR description LIKE ' . $search . ')');
    }
    
    if (!empty($statusFilter) && $statusFilter !== 'all') {
        $query->where('status = ' . $db->quote($statusFilter));
    }
    
    $db->setQuery($query);
    $items = $db->loadObjectList();
    
    // Устанавливаем заголовки для скачивания Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename=admission_applications_' . date('Y-m-d_H-i-s') . '.xls');
    
    // Генерируем HTML таблицу (Excel понимает HTML)
    echo '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Admission Applications Export</title>
        <style>
            table { border-collapse: collapse; width: 100%; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; font-weight: bold; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            .status-pending { color: #856404; background-color: #fff3cd; }
            .status-approved { color: #155724; background-color: #d4edda; }
            .status-rejected { color: #721c24; background-color: #f8d7da; }
        </style>
    </head>
    <body>
        <h2>Admission Applications Export</h2>
        <p>Generated on: ' . date('F j, Y g:i A') . '</p>
        <p>Total records: ' . count($items) . '</p>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>State</th>
                    <th>Created Date</th>
                    <th>Last Modified</th>
                </tr>
            </thead>
            <tbody>';
    
    foreach ($items as $item) {
        $state = $item->state == 1 ? 'Published' : 'Unpublished';
        $statusClass = 'status-' . $item->status;
        
        echo '<tr>
                <td>' . $item->id . '</td>
                <td>' . htmlspecialchars($item->title) . '</td>
                <td>' . htmlspecialchars($item->description) . '</td>
                <td>' . htmlspecialchars($item->email) . '</td>
                <td>' . htmlspecialchars($item->phone) . '</td>
                <td class="' . $statusClass . '">' . ucfirst($item->status) . '</td>
                <td>' . $state . '</td>
                <td>' . $item->created . '</td>
                <td>' . ($item->modified ?: 'Never') . '</td>
              </tr>';
    }
    
    echo '</tbody>
        </table>
    </body>
    </html>';
    
    exit;
}


/**
 * Быстрое одобрение заявки
 */
function handleQuickApprove()
{
    $app = Factory::getApplication();
    $id = $app->input->getInt('id', 0);
    
    if ($id > 0) {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->update($db->quoteName('#__admission_items'))
            ->set($db->quoteName('status') . ' = ' . $db->quote('approved'))
            ->set($db->quoteName('modified') . ' = NOW()')
            ->where($db->quoteName('id') . ' = ' . $id);
            
        $db->setQuery($query);
        if ($db->execute()) {
            $app->enqueueMessage('Application approved successfully!', 'success');
        }
    }
    
    $app->redirect('index.php?option=com_admission&view=items');
}

/**
 * Быстрое отклонение заявки
 */
function handleQuickReject()
{
    $app = Factory::getApplication();
    $id = $app->input->getInt('id', 0);
    
    if ($id > 0) {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->update($db->quoteName('#__admission_items'))
            ->set($db->quoteName('status') . ' = ' . $db->quote('rejected'))
            ->set($db->quoteName('modified') . ' = NOW()')
            ->where($db->quoteName('id') . ' = ' . $id);
            
        $db->setQuery($query);
        if ($db->execute()) {
            $app->enqueueMessage('Application rejected successfully!', 'success');
        }
    }
    
    $app->redirect('index.php?option=com_admission&view=items');
}


/**
 * Обработка массовых действий
 */
function handleBulkAction()
{
    $app = Factory::getApplication();
    $db = Factory::getDbo();
    
    $action = $app->input->getString('action', '');
    $ids = $app->input->get('ids', [], 'array');
    
    if (empty($action) || empty($ids)) {
        $app->enqueueMessage('No action or items selected', 'warning');
        $app->redirect('index.php?option=com_admission&view=items');
        return;
    }
    
    $ids = array_map('intval', $ids);
    $count = 0;
    
    switch ($action) {
        case 'approve':
            $query = $db->getQuery(true)
                ->update($db->quoteName('#__admission_items'))
                ->set($db->quoteName('status') . ' = ' . $db->quote('approved'))
                ->set($db->quoteName('modified') . ' = NOW()')
                ->where($db->quoteName('id') . ' IN (' . implode(',', $ids) . ')');
            $db->setQuery($query);
            $count = $db->execute();
            $message = $count . ' application(s) approved successfully!';
            break;
            
        case 'reject':
            $query = $db->getQuery(true)
                ->update($db->quoteName('#__admission_items'))
                ->set($db->quoteName('status') . ' = ' . $db->quote('rejected'))
                ->set($db->quoteName('modified') . ' = NOW()')
                ->where($db->quoteName('id') . ' IN (' . implode(',', $ids) . ')');
            $db->setQuery($query);
            $count = $db->execute();
            $message = $count . ' application(s) rejected successfully!';
            break;
            
        case 'delete':
            $query = $db->getQuery(true)
                ->delete($db->quoteName('#__admission_items'))
                ->where($db->quoteName('id') . ' IN (' . implode(',', $ids) . ')');
            $db->setQuery($query);
            $count = $db->execute();
            $message = $count . ' application(s) deleted successfully!';
            break;
            
        default:
            $app->enqueueMessage('Unknown action', 'error');
            $app->redirect('index.php?option=com_admission&view=items');
            return;
    }
    
    if ($count > 0) {
        $app->enqueueMessage($message, 'success');
    } else {
        $app->enqueueMessage('No changes were made', 'warning');
    }
    
    $app->redirect('index.php?option=com_admission&view=items');
}