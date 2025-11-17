<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

// Регистрируем namespace
JLoader::registerNamespace('JohnSmith\\Component\\Admission', JPATH_ADMINISTRATOR . '/components/com_admission/src');

// Подключаем CSS стили
$document = Factory::getDocument();
$document->addStyleSheet(JURI::root() . 'administrator/components/com_admission/media/css/admission.css');

$app = Factory::getApplication();
$input = $app->input;

// Определяем view и task
$view = $input->get('view', 'admission');
$task = $input->get('task', 'display');

// Проверяем, если task пришел из POST запроса (массовые операции)
$postTask = $app->input->post->get('task', '', 'cmd');
if (!empty($postTask) && strpos($postTask, '.') !== false) {
    $task = $postTask;
}

// Если есть task с точкой (например, item.add), используем контроллер или обрабатываем вручную
if (strpos($task, '.') !== false) {
    try {
        $controller = BaseController::getInstance('Admission', ['base_path' => JPATH_ADMINISTRATOR . '/components/com_admission']);
        $controller->execute($task);
        $controller->redirect();
    } catch (Exception $e) {
        // Если контроллер не сработал, пробуем обработать задачу вручную
        handleTaskManually($task);
    }
} else {
    // Для прямых view загружаем напрямую
    loadViewDirectly($view);
}

/**
 * Обработка задач вручную
 */
function handleTaskManually($task)
{
    $app = Factory::getApplication();
    $input = $app->input;
    
    list($controllerName, $taskName) = explode('.', $task);
    
    if ($controllerName === 'item') {
        switch ($taskName) {
            case 'add':
                $input->set('view', 'item');
                $input->set('layout', 'default');
                loadViewDirectly('item');
                break;
                
            case 'edit':
                // Получаем ID из разных источников (cid для массового выбора, id для одиночного)
                $ids = $input->get('cid', array(), 'array');
                $singleId = $input->getInt('id', 0);
                
                if (!empty($ids)) {
                    // Используем первый ID из массива (массовое выделение)
                    $id = (int) $ids[0];
                } elseif ($singleId > 0) {
                    // Используем одиночный ID
                    $id = $singleId;
                } else {
                    $app->enqueueMessage('Не выбрана заявка для редактирования', 'warning');
                    $app->redirect('index.php?option=com_admission&view=items');
                    return;
                }
                
                $input->set('view', 'item');
                $input->set('layout', 'default');
                $input->set('id', $id);
                loadViewDirectly('item');
                break;
                
            case 'save':
            case 'save2new':
            case 'apply':
                handleSaveItem($taskName);
                break;
                
            case 'cancel':
                $app->redirect('index.php?option=com_admission&view=items');
                break;
                
            default:
                $app->enqueueMessage('Неизвестная задача: ' . $task, 'error');
                $app->redirect('index.php?option=com_admission');
        }
    } elseif ($controllerName === 'items') {
        switch ($taskName) {
            case 'delete':
                handleDeleteItems();
                break;
                
            case 'publish':
                handlePublishItems(1); // Публикация
                break;
                
            case 'unpublish':
                handlePublishItems(0); // Снятие с публикации
                break;
                
            default:
                $app->redirect('index.php?option=com_admission&view=items');
        }
    } else {
        $app->enqueueMessage('Неизвестный контроллер: ' . $controllerName, 'error');
        $app->redirect('index.php?option=com_admission');
    }
}

/**
 * Обработка сохранения заявки
 */
function handleSaveItem($taskName)
{
    $app = Factory::getApplication();
    $input = $app->input;
    
    // Получаем данные из формы
    $data = [
        'id' => $input->getInt('id', 0),
        'title' => $input->getString('title', ''),
        'email' => $input->getString('email', ''),
        'phone' => $input->getString('phone', ''),
        'status' => $input->getString('status', 'pending'),
        'state' => $input->getInt('state', 1),
        'description' => $input->getString('description', '')
    ];
    
    // Валидация обязательных полей
    if (empty($data['title'])) {
        $app->enqueueMessage('Пожалуйста, введите название заявки', 'error');
        showItemForm($data['id'], $data);
        return;
    }
    
    if (empty($data['email'])) {
        $app->enqueueMessage('Пожалуйста, введите email адрес', 'error');
        showItemForm($data['id'], $data);
        return;
    }
    
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $app->enqueueMessage('Пожалуйста, введите корректный email адрес', 'error');
        showItemForm($data['id'], $data);
        return;
    }
    
    // Сохранение в базу данных
    try {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        $currentDate = Factory::getDate()->toSql();
        $user = Factory::getUser();
        
        if ($data['id'] > 0) {
            // Обновление существующей записи
            $fields = [
                $db->quoteName('title') . ' = ' . $db->quote($data['title']),
                $db->quoteName('description') . ' = ' . $db->quote($data['description']),
                $db->quoteName('email') . ' = ' . $db->quote($data['email']),
                $db->quoteName('phone') . ' = ' . $db->quote($data['phone']),
                $db->quoteName('status') . ' = ' . $db->quote($data['status']),
                $db->quoteName('state') . ' = ' . $db->quote($data['state']),
                $db->quoteName('modified') . ' = ' . $db->quote($currentDate),
                $db->quoteName('modified_by') . ' = ' . (int)$user->id
            ];
            
            $query->update($db->quoteName('#__admission_items'))
                  ->set($fields)
                  ->where($db->quoteName('id') . ' = ' . $data['id']);
                  
            $message = 'Заявка успешно обновлена!';
        } else {
            // Вставка новой записи
            $fields = [
                $db->quoteName('title') . ' = ' . $db->quote($data['title']),
                $db->quoteName('description') . ' = ' . $db->quote($data['description']),
                $db->quoteName('email') . ' = ' . $db->quote($data['email']),
                $db->quoteName('phone') . ' = ' . $db->quote($data['phone']),
                $db->quoteName('status') . ' = ' . $db->quote($data['status']),
                $db->quoteName('state') . ' = ' . $db->quote($data['state']),
                $db->quoteName('created') . ' = ' . $db->quote($currentDate),
                $db->quoteName('created_by') . ' = ' . (int)$user->id
            ];
            
            $query->insert($db->quoteName('#__admission_items'))
                  ->set($fields);
                  
            $message = 'Заявка успешно создана!';
        }
        
        $db->setQuery($query);
        $db->execute();
        
        // Получаем ID новой записи
        $newId = $data['id'] > 0 ? $data['id'] : $db->insertid();
        
        $app->enqueueMessage($message, 'success');
        
        // Редирект в зависимости от задачи
        if ($taskName === 'apply') {
            $app->redirect('index.php?option=com_admission&task=item.edit&id=' . $newId);
        } elseif ($taskName === 'save2new') {
            $app->redirect('index.php?option=com_admission&task=item.add');
        } else {
            $app->redirect('index.php?option=com_admission&view=items');
        }
        
    } catch (Exception $e) {
        $app->enqueueMessage('Ошибка при сохранении заявки: ' . $e->getMessage(), 'error');
        showItemForm($data['id'], $data);
    }
}

/**
 * Показать форму заявки с данными
 */
function showItemForm($id, $data = [])
{
    $app = Factory::getApplication();
    $input = $app->input;
    
    $input->set('view', 'item');
    $input->set('layout', 'default');
    $input->set('id', $id);
    
    // Сохраняем данные в сессии для повторного заполнения формы
    $app->setUserState('com_admission.edit.item.data', $data);
    
    loadViewDirectly('item');
}

/**
 * Обработка удаления заявок
 */
function handleDeleteItems()
{
    $app = Factory::getApplication();
    $input = $app->input;
    
    // Получаем ID из разных источников (cid для массового выбора, id для одиночного)
    $ids = $input->get('cid', array(), 'array');
    $singleId = $input->getInt('id', 0);
    
    // Собираем все ID для удаления
    $allIds = [];
    
    if (!empty($ids)) {
        $allIds = array_map('intval', $ids);
    } elseif ($singleId > 0) {
        $allIds = [$singleId];
    }

    if (empty($allIds)) {
        $app->enqueueMessage('Не выбраны заявки для удаления', 'warning');
    } else {
        try {
            $db = Factory::getDbo();
            $query = $db->getQuery(true);
            
            $query->delete($db->quoteName('#__admission_items'))
                  ->where($db->quoteName('id') . ' IN (' . implode(',', $allIds) . ')');
            
            $db->setQuery($query);
            $db->execute();

            $count = count($allIds);
            $message = $count == 1 ? 'Заявка успешно удалена!' : "Успешно удалено {$count} заявок!";
            $app->enqueueMessage($message, 'success');
        } catch (Exception $e) {
            $app->enqueueMessage('Ошибка при удалении заявок: ' . $e->getMessage(), 'error');
        }
    }

    $app->redirect('index.php?option=com_admission&view=items');
}

/**
 * Обработка публикации/снятия с публикации заявок
 */
/**
 * Обработка публикации/снятия с публикации заявок
 */
function handlePublishItems($state)
{
    $app = Factory::getApplication();
    $input = $app->input;
    
    // Получаем ID из разных источников (cid для массового выбора, id для одиночного)
    $ids = $input->get('cid', array(), 'array');
    $singleId = $input->getInt('id', 0);
    
    // Собираем все ID для изменения статуса
    $allIds = [];
    
    if (!empty($ids)) {
        $allIds = array_map('intval', $ids);
    } elseif ($singleId > 0) {
        $allIds = [$singleId];
    }

    if (empty($allIds)) {
        $app->enqueueMessage('Не выбраны заявки для изменения статуса', 'warning');
    } else {
        try {
            $db = Factory::getDbo();
            $query = $db->getQuery(true);
            
            $query->update($db->quoteName('#__admission_items'))
                  ->set($db->quoteName('state') . ' = ' . (int)$state)
                  ->where($db->quoteName('id') . ' IN (' . implode(',', $allIds) . ')');
            
            $db->setQuery($query);
            $db->execute();

            $count = count($allIds);
            $message = $state == 1 
                ? ($count == 1 ? 'Заявка опубликована!' : "Опубликовано {$count} заявок!")
                : ($count == 1 ? 'Заявка снята с публикации!' : "Снято с публикации {$count} заявок!");
                
            $app->enqueueMessage($message, 'success');
        } catch (Exception $e) {
            $app->enqueueMessage('Ошибка при изменении статуса заявок: ' . $e->getMessage(), 'error');
        }
    }

    // Определяем, откуда пришел запрос и делаем соответствующий редирект
    $referer = $input->server->getString('HTTP_REFERER', '');
    
    // Если запрос пришел со страницы дашборда, возвращаем на дашборд
    if (strpos($referer, 'option=com_admission&view=items') === false && 
        strpos($referer, 'task=item.') === false) {
        // Вероятно, запрос с дашборда
        $app->redirect('index.php?option=com_admission');
    } else {
        // Запрос со страницы списка заявок или редактирования
        $app->redirect('index.php?option=com_admission&view=items');
    }
}

/**
 * Загрузка View напрямую
 */
function loadViewDirectly($viewName)
{
    $app = Factory::getApplication();
    $input = $app->input;
    
    $input->set('view', $viewName);
    $layout = $input->get('layout', 'default');
    $input->set('layout', $layout);

    try {
        $viewClass = 'JohnSmith\\Component\\Admission\\Administrator\\View\\' . ucfirst($viewName) . '\\HtmlView';
        
        if (class_exists($viewClass)) {
            $view = new $viewClass();
            $view->display();
        } else {
            throw new Exception('View class not found: ' . $viewClass);
        }
    } catch (Exception $e) {
        echo '<div class="container-fluid">';
        echo '<h2>Admission Component Error</h2>';
        echo '<div class="alert alert-danger">';
        echo '<p><strong>Error:</strong> ' . $e->getMessage() . '</p>';
        echo '</div>';
        echo '</div>';
    }
}