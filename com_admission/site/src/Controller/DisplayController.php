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
        if (in_array($viewName, ['admin', 'edit']) || in_array($task, ['approve', 'reject', 'delete'])) {
            if (!$this->canManage()) {
                Factory::getApplication()->enqueueMessage('You do not have permission to manage applications', 'error');
                $this->setRedirect('index.php?option=com_admission');
                return $this;
            }
        }
        
        // Проверяем права для создания заявок
        if ($viewName === 'form' || $task === 'submit') {
            if (!$this->canSubmit()) {
                Factory::getApplication()->enqueueMessage('You do not have permission to submit applications', 'error');
                $this->setRedirect('index.php?option=com_admission');
                return $this;
            }
        }
        
        // Проверяем права для просмотра
        if (!$this->canView()) {
            Factory::getApplication()->enqueueMessage('You do not have permission to view applications', 'error');
            return $this;
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
        } elseif ($viewName === 'form') {
            $this->showForm();
        } else {
            $this->showItems();
        }
        
        return $this;
    }
    
    /**
     * Проверяем права на управление заявками
     */
    private function canManage()
    {
        $user = Factory::getUser();
        return $user->authorise('core.admin', 'com_admission') || 
               $user->authorise('core.manage', 'com_admission') ||
               $user->authorise('admission.manage', 'com_admission');
    }
    
    /**
     * Проверяем права на подачу заявок
     */
    private function canSubmit()
    {
        $user = Factory::getUser();
        return $user->authorise('core.create', 'com_admission') || 
               $user->authorise('admission.submit', 'com_admission') ||
               !$user->guest; // Разрешаем всем зарегистрированным пользователям
    }
    
    /**
     * Проверяем права на просмотр заявок
     */
    private function canView()
    {
        $user = Factory::getUser();
        return $user->authorise('core.view', 'com_admission') || 
               $user->authorise('admission.view', 'com_admission') ||
               !$user->guest; // Разрешаем всем зарегистрированным пользователям
    }
    
    /**
     * Проверяем является ли пользователь суперадмином (для обратной совместимости)
     */
    private function isAdmin()
    {
        return $this->canManage();
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
                
            case 'submit':
                $this->submitApplication();
                break;
        }
        
        // Редирект в зависимости от задачи
        if ($task === 'submit') {
            $app->redirect('index.php?option=com_admission');
        } else {
            $app->redirect('index.php?option=com_admission&view=admin');
        }
    }
    
    /**
     * Показ формы подачи заявки
     */
    private function showForm()
    {
        echo '<div class="container mt-4">';
        
        // Кнопка назад
        echo '<a href="index.php?option=com_admission" class="btn btn-outline-secondary mb-4">← Back to Applications List</a>';
        
        echo '<div class="row justify-content-center">';
        echo '<div class="col-md-8">';
        echo '<div class="card shadow">';
        echo '<div class="card-header bg-success text-white">';
        echo '<h2 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Submit New Application</h2>';
        echo '</div>';
        echo '<div class="card-body">';
        
        echo '<form action="index.php?option=com_admission&task=submit" method="post" id="admissionForm">';
        
        // CSRF защита
        echo '<input type="hidden" name="' . \JSession::getFormToken() . '" value="1">';
        
        // Поле заголовка
        echo '<div class="mb-3">';
        echo '<label for="title" class="form-label">Application Title *</label>';
        echo '<input type="text" class="form-control form-control-lg" id="title" name="title" required maxlength="255">';
        echo '<div class="form-text">Enter a descriptive title for your application</div>';
        echo '</div>';
        
        // Поле описания
        echo '<div class="mb-3">';
        echo '<label for="description" class="form-label">Description *</label>';
        echo '<textarea class="form-control" id="description" name="description" rows="5" required placeholder="Please describe your application in detail..."></textarea>';
        echo '</div>';
        
        // Контактная информация
        echo '<div class="row">';
        echo '<div class="col-md-6">';
        echo '<div class="mb-3">';
        echo '<label for="email" class="form-label">Email Address *</label>';
        echo '<input type="email" class="form-control" id="email" name="email" required>';
        echo '</div>';
        echo '</div>';
        echo '<div class="col-md-6">';
        echo '<div class="mb-3">';
        echo '<label for="phone" class="form-label">Phone Number</label>';
        echo '<input type="tel" class="form-control" id="phone" name="phone" placeholder="+1 (555) 123-4567" maxlength="20">';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        
        // Кнопки
        echo '<div class="d-grid gap-2 d-md-flex justify-content-md-end">';
        echo '<a href="index.php?option=com_admission" class="btn btn-secondary me-md-2">Cancel</a>';
        echo '<button type="submit" class="btn btn-success btn-lg">';
        echo '<i class="fas fa-paper-plane me-2"></i>Submit Application';
        echo '</button>';
        echo '</div>';
        
        echo '</form>';
        echo '</div>'; // card-body
        echo '</div>'; // card
        
        // Информация о процессе
        echo '<div class="mt-4">';
        echo '<div class="alert alert-info">';
        echo '<h5><i class="fas fa-info-circle me-2"></i>Application Process</h5>';
        echo '<ul class="mb-0">';
        echo '<li>Your application will be reviewed within 3-5 business days</li>';
        echo '<li>You will receive email notifications about status changes</li>';
        echo '<li>You can check your application status on this page</li>';
        echo '</ul>';
        echo '</div>';
        echo '</div>';
        
        echo '</div>'; // col-md-8
        echo '</div>'; // row
        echo '</div>'; // container
    }
    
    /**
     * Сохранение новой заявки
     */
    private function submitApplication()
    {
        $app = Factory::getApplication();
        $db = Factory::getDbo();
        $user = Factory::getUser();
        
        // Проверка CSRF токена
        if (!\JSession::checkToken()) {
            $app->enqueueMessage('Invalid security token', 'error');
            $app->redirect('index.php?option=com_admission&view=form');
            return;
        }
        
        // Получаем данные из формы
        $title = $app->input->getString('title', '');
        $description = $app->input->getString('description', '');
        $email = $app->input->getString('email', '');
        $phone = $app->input->getString('phone', '');
        
        // Валидация
        if (empty($title) || empty($description) || empty($email)) {
            $app->enqueueMessage('Please fill all required fields', 'error');
            $app->redirect('index.php?option=com_admission&view=form');
            return;
        }
        
        // Проверка email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $app->enqueueMessage('Please enter a valid email address', 'error');
            $app->redirect('index.php?option=com_admission&view=form');
            return;
        }
        
        try {
            // Сохраняем заявку
            $query = $db->getQuery(true)
                ->insert($db->quoteName('#__admission_items'))
                ->set($db->quoteName('title') . ' = ' . $db->quote($title))
                ->set($db->quoteName('description') . ' = ' . $db->quote($description))
                ->set($db->quoteName('email') . ' = ' . $db->quote($email))
                ->set($db->quoteName('phone') . ' = ' . $db->quote($phone))
                ->set($db->quoteName('status') . ' = ' . $db->quote('pending'))
                ->set($db->quoteName('state') . ' = 1')
                ->set($db->quoteName('created') . ' = NOW()')
                ->set($db->quoteName('created_by') . ' = ' . (int)$user->id)
                ->set($db->quoteName('ordering') . ' = 0');
                
            $db->setQuery($query);
            $db->execute();
            
            $app->enqueueMessage('Your application has been submitted successfully! We will review it soon.', 'success');
            
        } catch (Exception $e) {
            $app->enqueueMessage('Error submitting application: ' . $e->getMessage(), 'error');
            $app->redirect('index.php?option=com_admission&view=form');
        }
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
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__admission_items'))
            ->where($db->quoteName('state') . ' = 1')
            ->order('created DESC');
        
        $db->setQuery($query);
        $items = $db->loadObjectList();
        
        echo '<div class="container mt-4">';
        
        // Заголовок и кнопки
        echo '<div class="d-flex justify-content-between align-items-center mb-4">';
        echo '<h1 class="text-primary">Admission Applications</h1>';
        echo '<div>';
        // Кнопка для админов
        if ($this->canManage()) {
            echo '<a href="index.php?option=com_admission&view=admin" class="btn btn-warning me-2">Manage Applications</a>';
        }
        // Кнопка добавления для всех, у кого есть права
        if ($this->canSubmit()) {
            echo '<a href="index.php?option=com_admission&view=form" class="btn btn-success">';
            echo '<i class="fas fa-plus me-2"></i>Submit New Application';
            echo '</a>';
        }
        echo '</div>';
        echo '</div>';
        
        // Информация для пользователей
        if (!$this->canManage()) {
            echo '<div class="row mb-4">';
            echo '<div class="col-12">';
            echo '<div class="alert alert-info">';
            echo '<h5><i class="fas fa-info-circle me-2"></i>Welcome to Admission Portal</h5>';
            echo '<p class="mb-0">Submit your application and track its status here. Our team will review it promptly.</p>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        
        if (empty($items)) {
            echo '<div class="text-center py-5">';
            echo '<div class="alert alert-info">';
            echo '<h4>No Applications Yet</h4>';
            if ($this->canSubmit()) {
                echo '<p>Be the first to submit an application!</p>';
                echo '<a href="index.php?option=com_admission&view=form" class="btn btn-success btn-lg mt-2">';
                echo '<i class="fas fa-plus me-2"></i>Submit First Application';
                echo '</a>';
            } else {
                echo '<p>There are no applications to display at this time.</p>';
            }
            echo '</div>';
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