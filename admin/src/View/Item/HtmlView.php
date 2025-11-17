<?php
namespace JohnSmith\Component\Admission\Administrator\View\Item;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;

class HtmlView extends BaseHtmlView
{
    protected $item;
    protected $isNew = true;

    public function display($tpl = null)
    {
        $this->item = $this->loadItem();
        
        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar()
    {
        $title = $this->isNew ? 'Добавить заявку' : 'Редактировать заявку';
        $document = Factory::getDocument();
        $document->setTitle($title . ' - Admission');
    }

    /**
     * Загрузка данных заявки
     */
    protected function loadItem()
    {
        $app = Factory::getApplication();
        $input = $app->input;
        $id = $input->getInt('id', 0);
        
        // Пробуем получить данные из сессии (при ошибках валидации)
        $data = $app->getUserState('com_admission.edit.item.data', []);
        
        if (!empty($data)) {
            // Очищаем данные сессии
            $app->setUserState('com_admission.edit.item.data', null);
            $this->isNew = empty($data['id']);
            return (object) $data;
        }
        
        if ($id > 0) {
            // Загружаем существующую запись из базы
            try {
                $db = Factory::getDbo();
                $query = $db->getQuery(true);
                
                $query->select('*')
                      ->from($db->quoteName('#__admission_items'))
                      ->where($db->quoteName('id') . ' = ' . $id);
                
                $db->setQuery($query);
                $item = $db->loadObject();
                
                if ($item) {
                    $this->isNew = false;
                    return $item;
                }
            } catch (Exception $e) {
                $app->enqueueMessage('Ошибка загрузки заявки: ' . $e->getMessage(), 'error');
            }
        }
        
        // Новая заявка или ошибка загрузки
        $this->isNew = true;
        return (object) [
            'id' => 0,
            'title' => '',
            'description' => '',
            'email' => '',
            'phone' => '',
            'status' => 'pending',
            'state' => 1
        ];
    }
}