<?php
namespace JohnSmith\Component\Admission\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\Access\Access;

class DisplayController extends BaseController
{
    public function display($cachable = false, $urlparams = [])
    {
        $viewName = $this->input->get('view', 'items');
        $id = $this->input->getInt('id');
        $task = $this->input->get('task', '');
        
        // Проверяем права для админских действий
        if (in_array($viewName, ['admin', 'edit']) || $task) {
            if (!$this->isAdmin()) {
                Factory::getApplication()->enqueueMessage('Access denied', 'error');
                $this->setRedirect('index.php?option=com_admission');
                return $this;
            }
        }
        
        // Обработка задач
        if ($task) {
            $this->handleTask($task, $id);
            return $this;
        }
        
        // Показ представлений
        if ($viewName === 'item' && $id) {
            $this->showItem($id);
        } elseif ($viewName === 'admin') {
            $this->showAdmin();
        } elseif ($viewName === 'edit' && $id) {
            $this->showEditForm($id);
        } else {
            $this->showItems();
        }
        
        return $this;
    }
    
	
	
	

    /**
     * Проверяем, является ли пользователь администратором
     */
    private function isAdmin()
    {
        $user = Factory::getUser();
        return $user->authorise('core.admin') || $user->authorise('core.manage', 'com_admission');
    }
    
    /**
     * Обработка задач (изменение статуса, удаление и т.д.)
     */
    private function handleTask($task, $id)
    {
        $app = Factory::getApplication();
        
        switch ($task) {
            case 'approve':
                $this->changeStatus($id, 'approved');
                break;
                
            case 'reject':
                $this->changeStatus($id, 'rejected');
                break;
                
            case 'pending':
                $this->changeStatus($id, 'pending');
                break;
                
            case 'delete':
                $this->deleteItem($id);
                break;
                
            case 'save':
                $this->saveItem();
                break;
        }
        
        $app->redirect('index.php?option=com_admission&view=admin');
    }
    
    /**
     * Изменение статуса заявки
     */
    private function changeStatus($id, $status)
    {
        if (!$id) return;
        
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->update($db->quoteName('#__admission_items'))
            ->set($db->quoteName('status') . ' = ' . $db->quote($status))
            ->set($db->quoteName('modified') . ' = NOW()')
            ->set($db->quoteName('modified_by') . ' = ' . Factory::getUser()->id)
            ->where($db->quoteName('id') . ' = ' . (int)$id);
            
        $db->setQuery($query);
        $db->execute();
        
        Factory::getApplication()->enqueueMessage("Status changed to $status", 'success');
    }
    
    /**
     * Удаление заявки
     */
    private function deleteItem($id)
    {
        if (!$id) return;
        
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->delete($db->quoteName('#__admission_items'))
            ->where($db->quoteName('id') . ' = ' . (int)$id);
            
        $db->setQuery($query);
        $db->execute();
        
        Factory::getApplication()->enqueueMessage('Application deleted', 'success');
    }
    
    /**
     * Показ админ-панели
     */
    private function showAdmin()
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__admission_items'))
            ->order('created DESC');
        
        $db->setQuery($query);
        $items = $db->loadObjectList();
        
        echo '<div class="container mt-4">';
        echo '<div class="d-flex justify-content-between align-items-center mb-4">';
        echo '<h1 class="text-primary">Admission Management</h1>';
        echo '<a href="index.php?option=com_admission" class="btn btn-outline-secondary">← Back to Public View</a>';
        echo '</div>';
        
        // Статистика
        $stats = $this->getStats();
        echo $this->renderStats($stats);
        
        if (empty($items)) {
            echo '<div class="alert alert-info">No applications found</div>';
        } else {
            echo '<div class="card shadow">';
            echo '<div class="card-header bg-dark text-white">';
            echo '<h4 class="mb-0">All Applications</h4>';
            echo '</div>';
            echo '<div class="card-body p-0">';
            echo '<div class="table-responsive">';
            echo '<table class="table table-striped table-hover mb-0">';
            echo '<thead class="table-light">';
            echo '<tr>';
            echo '<th>ID</th>';
            echo '<th>Title</th>';
            echo '<th>Email</th>';
            echo '<th>Status</th>';
            echo '<th>Created</th>';
            echo '<th>Actions</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            
            foreach ($items as $item) {
                $statusClass = 'warning';
                if ($item->status == 'approved') $statusClass = 'success';
                if ($item->status == 'rejected') $statusClass = 'danger';
                
                echo '<tr>';
                echo '<td>' . $item->id . '</td>';
                echo '<td><strong>' . htmlspecialchars($item->title) . '</strong></td>';
                echo '<td>' . htmlspecialchars($item->email) . '</td>';
                echo '<td><span class="badge bg-' . $statusClass . '">' . ucfirst($item->status) . '</span></td>';
                echo '<td>' . date('M j, Y', strtotime($item->created)) . '</td>';
                echo '<td>';
                echo '<div class="btn-group btn-group-sm">';
                echo '<a href="index.php?option=com_admission&view=item&id=' . $item->id . '" class="btn btn-outline-primary">View</a>';
                echo '<a href="index.php?option=com_admission&task=approve&id=' . $item->id . '" class="btn btn-outline-success">Approve</a>';
                echo '<a href="index.php?option=com_admission&task=reject&id=' . $item->id . '" class="btn btn-outline-danger">Reject</a>';
                echo '<a href="index.php?option=com_admission&task=delete&id=' . $item->id . '" class="btn btn-outline-dark" onclick="return confirm(\'Are you sure?\')">Delete</a>';
                echo '</div>';
                echo '</td>';
                echo '</tr>';
            }
            
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        
        echo '</div>';
    }
    
    /**
     * Получение статистики
     */
    private function getStats()
    {
        $db = Factory::getDbo();
        
        // Общее количество
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__admission_items'));
        $db->setQuery($query);
        $total = $db->loadResult();
        
        // По статусам
        $query = $db->getQuery(true)
            ->select('status, COUNT(*) as count')
            ->from($db->quoteName('#__admission_items'))
            ->group('status');
        $db->setQuery($query);
        $statusCounts = $db->loadObjectList();
        
        $stats = ['total' => $total];
        foreach ($statusCounts as $status) {
            $stats[$status->status] = $status->count;
        }
        
        return $stats;
    }
    
    /**
     * Отображение статистики
     */
    private function renderStats($stats)
    {
        $html = '<div class="row mb-4">';
        $html .= '<div class="col-md-3">';
        $html .= '<div class="card bg-primary text-white">';
        $html .= '<div class="card-body text-center">';
        $html .= '<h3>' . ($stats['total'] ?? 0) . '</h3>';
        $html .= '<p class="mb-0">Total Applications</p>';
        $html .= '</div></div></div>';
        
        $html .= '<div class="col-md-3">';
        $html .= '<div class="card bg-warning text-dark">';
        $html .= '<div class="card-body text-center">';
        $html .= '<h3>' . ($stats['pending'] ?? 0) . '</h3>';
        $html .= '<p class="mb-0">Pending</p>';
        $html .= '</div></div></div>';
        
        $html .= '<div class="col-md-3">';
        $html .= '<div class="card bg-success text-white">';
        $html .= '<div class="card-body text-center">';
        $html .= '<h3>' . ($stats['approved'] ?? 0) . '</h3>';
        $html .= '<p class="mb-0">Approved</p>';
        $html .= '</div></div></div>';
        
        $html .= '<div class="col-md-3">';
        $html .= '<div class="card bg-danger text-white">';
        $html .= '<div class="card-body text-center">';
        $html .= '<h3>' . ($stats['rejected'] ?? 0) . '</h3>';
        $html .= '<p class="mb-0">Rejected</p>';
        $html .= '</div></div></div>';
        $html .= '</div>';
        
        return $html;
    }
    
private function showItems()
{
	    // Проверяем права и показываем ссылку на админку
    if ($this->isAdmin()) {
        echo '<div class="alert alert-info mb-4">';
        echo '<div class="d-flex justify-content-between align-items-center">';
        echo '<span>You have administrative privileges</span>';
        echo '<a href="index.php?option=com_admission&view=admin" class="btn btn-warning btn-sm">Manage Applications</a>';
        echo '</div>';
        echo '</div>';
    }
    
	
	
    $db = Factory::getDbo();
    $query = $db->getQuery(true)
        ->select('*')
        ->from($db->quoteName('#__admission_items'))
        ->where($db->quoteName('state') . ' = 1')
        ->order('created DESC');
    
    $db->setQuery($query);
    $items = $db->loadObjectList();
    
    echo '<div class="container mt-4">';
    echo '<div class="d-flex justify-content-between align-items-center mb-4">';
    echo '<h1 class="text-primary">Admission Applications</h1>';
    echo '<span class="badge bg-secondary fs-6">' . count($items) . ' applications</span>';
    echo '</div>';
    
    if (empty($items)) {
        echo '<div class="alert alert-info">';
        echo '<h4>No Applications Found</h4>';
        echo '<p>There are no admission applications to display at this time.</p>';
        echo '</div>';
    } else {
        foreach ($items as $item) {
            // Определяем цвет статуса
            $statusClass = 'warning';
            if ($item->status == 'approved') $statusClass = 'success';
            if ($item->status == 'rejected') $statusClass = 'danger';
            
            echo '<div class="card mb-4 shadow-sm">';
            echo '<div class="card-body">';
            echo '<div class="row">';
            echo '<div class="col-md-8">';
            echo '<h3 class="card-title text-primary">' . htmlspecialchars($item->title) . '</h3>';
            if (!empty($item->description)) {
                echo '<p class="card-text">' . htmlspecialchars(mb_substr($item->description, 0, 150)) . 
                     (mb_strlen($item->description) > 150 ? '...' : '') . '</p>';
            }
            echo '<div class="mt-2">';
            echo '<span class="badge bg-' . $statusClass . ' me-2">' . ucfirst($item->status) . '</span>';
            echo '<small class="text-muted">Submitted: ' . date('M j, Y', strtotime($item->created)) . '</small>';
            echo '</div>';
            echo '</div>';
            echo '<div class="col-md-4 text-end">';
            echo '<a href="index.php?option=com_admission&view=item&id=' . $item->id . '" class="btn btn-primary btn-lg">';
            echo 'View Details →';
            echo '</a>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
    }
    
    echo '</div>';
}
    
private function showItem($id)
{
    $db = Factory::getDbo();
    $query = $db->getQuery(true)
        ->select('*')
        ->from($db->quoteName('#__admission_items'))
        ->where($db->quoteName('id') . ' = ' . (int)$id)
        ->where($db->quoteName('state') . ' = 1');
    
    $db->setQuery($query);
    $item = $db->loadObject();
    
    echo '<div class="container mt-4">';
    
    // Кнопка назад
    echo '<a href="index.php?option=com_admission" class="btn btn-outline-secondary mb-4">';
    echo '← Back to Applications List';
    echo '</a>';
    
    if ($item) {
        // Определяем цвет статуса
        $statusClass = 'warning';
        if ($item->status == 'approved') $statusClass = 'success';
        if ($item->status == 'rejected') $statusClass = 'danger';
        
        echo '<div class="card shadow">';
        echo '<div class="card-header bg-primary text-white">';
        echo '<h2 class="mb-0"><i class="fas fa-file-alt me-2"></i>' . htmlspecialchars($item->title) . '</h2>';
        echo '</div>';
        echo '<div class="card-body">';
        
        // Описание
        if (!empty($item->description)) {
            echo '<div class="mb-4">';
            echo '<h4 class="text-primary">Description</h4>';
            echo '<p class="fs-5">' . htmlspecialchars($item->description) . '</p>';
            echo '</div>';
        }
        
        echo '<div class="row">';
        
        // Контактная информация
        echo '<div class="col-md-6">';
        echo '<h4 class="text-primary mb-3">Contact Information</h4>';
        if (!empty($item->email)) {
            echo '<p class="mb-2">';
            echo '<strong>Email:</strong><br>';
            echo '<span class="text-muted">' . htmlspecialchars($item->email) . '</span>';
            echo '</p>';
        }
        if (!empty($item->phone)) {
            echo '<p class="mb-2">';
            echo '<strong>Phone:</strong><br>';
            echo '<span class="text-muted">' . htmlspecialchars($item->phone) . '</span>';
            echo '</p>';
        }
        echo '</div>';
        
        // Информация о заявке
        echo '<div class="col-md-6">';
        echo '<h4 class="text-primary mb-3">Application Details</h4>';
        echo '<p class="mb-2">';
        echo '<strong>Status:</strong><br>';
        echo '<span class="badge bg-' . $statusClass . ' fs-6">' . ucfirst($item->status) . '</span>';
        echo '</p>';
        echo '<p class="mb-2">';
        echo '<strong>Submitted:</strong><br>';
        echo '<span class="text-muted">' . date('F j, Y, g:i a', strtotime($item->created)) . '</span>';
        echo '</p>';
        
        if (!empty($item->modified) && $item->modified != '0000-00-00 00:00:00') {
            echo '<p class="mb-2">';
            echo '<strong>Last Updated:</strong><br>';
            echo '<span class="text-muted">' . date('F j, Y, g:i a', strtotime($item->modified)) . '</span>';
            echo '</p>';
        }
        echo '</div>';
        
        echo '</div>'; // end row
        echo '</div>'; // end card-body
        echo '</div>'; // end card
    } else {
        echo '<div class="alert alert-danger">';
        echo '<h4>Application Not Found</h4>';
        echo '<p>The requested application could not be found or is no longer available.</p>';
        echo '</div>';
    }
    
    echo '</div>'; // end container
}
}