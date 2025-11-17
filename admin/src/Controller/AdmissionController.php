<?php
namespace JohnSmith\Component\Admission\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

class AdmissionController extends BaseController
{
    /**
     * Добавление новой заявки
     */
    public function add()
    {
        $this->setRedirect(Route::_('index.php?option=com_admission&view=item&layout=edit', false));
    }

    /**
     * Редактирование заявки
     */
    public function edit()
    {
        $input = Factory::getApplication()->input;
        $ids = $input->get('cid', array(), 'array');
        
        if (!empty($ids)) {
            $id = (int) $ids[0];
            $this->setRedirect(Route::_('index.php?option=com_admission&view=item&layout=edit&id=' . $id, false));
        } else {
            $this->setMessage(Text::_('COM_ADMISSION_NO_ITEM_SELECTED'), 'warning');
            $this->setRedirect(Route::_('index.php?option=com_admission&view=items', false));
        }
    }

    /**
     * Сохранение заявки
     */
    public function save()
    {
        $this->saveRecord();
    }

    /**
     * Применение изменений
     */
    public function apply()
    {
        $id = $this->saveRecord();
        if ($id) {
            $this->setRedirect(Route::_('index.php?option=com_admission&view=item&layout=edit&id=' . $id, false));
        }
    }

    /**
     * Сохранение и создание новой
     */
    public function save2new()
    {
        $this->saveRecord();
        $this->setRedirect(Route::_('index.php?option=com_admission&view=item&layout=edit', false));
    }

    /**
     * Общий метод сохранения
     */
    private function saveRecord()
    {
        // Check for request forgeries
        $this->checkToken();

        $app = Factory::getApplication();
        $input = $app->input;

        // Get the data from the form
        $data = $input->get('jform', array(), 'array');

        // Валидация обязательных полей
        if (empty($data['title'])) {
            $app->enqueueMessage(Text::_('COM_ADMISSION_ERR_TITLE_EMPTY'), 'error');
            $this->setRedirect(Route::_('index.php?option=com_admission&view=item&layout=edit', false));
            return false;
        }

        if (empty($data['email'])) {
            $app->enqueueMessage(Text::_('COM_ADMISSION_ERR_EMAIL_EMPTY'), 'error');
            $this->setRedirect(Route::_('index.php?option=com_admission&view=item&layout=edit', false));
            return false;
        }

        // Сохраняем в базу данных
        try {
            $db = Factory::getDbo();
            $query = $db->getQuery(true);
            
            // Подготавливаем данные
            $currentDate = Factory::getDate()->toSql();
            $user = Factory::getUser();
            
            $fields = [
                $db->quoteName('title') . ' = ' . $db->quote($data['title']),
                $db->quoteName('description') . ' = ' . $db->quote($data['description'] ?? ''),
                $db->quoteName('email') . ' = ' . $db->quote($data['email']),
                $db->quoteName('phone') . ' = ' . $db->quote($data['phone'] ?? ''),
                $db->quoteName('status') . ' = ' . $db->quote($data['status'] ?? 'pending'),
                $db->quoteName('state') . ' = ' . (int)($data['state'] ?? 1),
                $db->quoteName('modified') . ' = ' . $db->quote($currentDate),
                $db->quoteName('modified_by') . ' = ' . (int)$user->id
            ];

            // Если это новая запись
            if (empty($data['id'])) {
                $fields[] = $db->quoteName('created') . ' = ' . $db->quote($currentDate);
                $fields[] = $db->quoteName('created_by') . ' = ' . (int)$user->id;
                
                $query->insert($db->quoteName('#__admission_items'))
                      ->set($fields);
            } else {
                // Обновление существующей записи
                $query->update($db->quoteName('#__admission_items'))
                      ->set($fields)
                      ->where($db->quoteName('id') . ' = ' . (int)$data['id']);
            }

            $db->setQuery($query);
            $db->execute();

            // Получаем ID новой записи
            $id = empty($data['id']) ? $db->insertid() : $data['id'];

            $app->enqueueMessage(Text::_('COM_ADMISSION_ITEM_SAVED'), 'success');
            $this->setRedirect(Route::_('index.php?option=com_admission&view=items', false));

            return $id;

        } catch (Exception $e) {
            $app->enqueueMessage(Text::_('COM_ADMISSION_ERR_SAVING') . ': ' . $e->getMessage(), 'error');
            $this->setRedirect(Route::_('index.php?option=com_admission&view=item&layout=edit', false));
            return false;
        }
    }

    /**
     * Отмена редактирования
     */
    public function cancel()
    {
        $this->setRedirect(Route::_('index.php?option=com_admission&view=items', false));
    }

    /**
     * Удаление заявок
     */
    public function delete()
    {
        $this->checkToken();

        $app = Factory::getApplication();
        $input = $app->input;
        $ids = $input->get('cid', array(), 'array');

        if (empty($ids)) {
            $app->enqueueMessage(Text::_('COM_ADMISSION_NO_ITEM_SELECTED'), 'warning');
        } else {
            try {
                $db = Factory::getDbo();
                $query = $db->getQuery(true);
                
                $query->delete($db->quoteName('#__admission_items'))
                      ->where($db->quoteName('id') . ' IN (' . implode(',', array_map('intval', $ids)) . ')');
                
                $db->setQuery($query);
                $db->execute();

                $app->enqueueMessage(Text::_('COM_ADMISSION_ITEMS_DELETED'), 'success');
            } catch (Exception $e) {
                $app->enqueueMessage(Text::_('COM_ADMISSION_ERR_DELETING') . ': ' . $e->getMessage(), 'error');
            }
        }

        $this->setRedirect(Route::_('index.php?option=com_admission&view=items', false));
    }
}