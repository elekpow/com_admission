<?php
namespace JohnSmith\Component\Admission\Administrator\View\Admission;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

class HtmlView extends BaseHtmlView
{
    protected $items;
    protected $stats;

    /**
     * Execute and display a template script.
     */
    public function display($tpl = null)
    {
        // Загружаем данные из базы данных
        $this->loadDataFromDatabase();
        
        // Загружаем статистику
        $this->loadStatistics();

        $this->addToolbar();
        parent::display($tpl);
    }

    /**
     * Загружаем данные из базы данных
     */
    protected function loadDataFromDatabase()
    {
        $db = Factory::getDbo();
        
        try {
            $query = $db->getQuery(true);
            
            $query->select('*')
                  ->from($db->quoteName('#__admission_items'))
                  ->order('created DESC')
                  ->setLimit(10);
            
            $db->setQuery($query);
            $this->items = $db->loadObjectList();
            
        } catch (Exception $e) {
            // Если возникает ошибка, используем пустой массив
            $this->items = array();
            Factory::getApplication()->enqueueMessage('Ошибка загрузки данных: ' . $e->getMessage(), 'warning');
        }
    }

    /**
     * Загружаем статистику
     */
    protected function loadStatistics()
    {
        $db = Factory::getDbo();
        
        try {
            // Общее количество
            $query = $db->getQuery(true);
            $query->select('COUNT(*)')
                  ->from($db->quoteName('#__admission_items'));
            $db->setQuery($query);
            $total = $db->loadResult();

            // Опубликованные
            $query = $db->getQuery(true);
            $query->select('COUNT(*)')
                  ->from($db->quoteName('#__admission_items'))
                  ->where('state = 1');
            $db->setQuery($query);
            $published = $db->loadResult();

            // Неопубликованные
            $query = $db->getQuery(true);
            $query->select('COUNT(*)')
                  ->from($db->quoteName('#__admission_items'))
                  ->where('state = 0');
            $db->setQuery($query);
            $unpublished = $db->loadResult();

            // Ожидающие решения (проверяем наличие колонки status)
            $pending = 0;
            try {
                $query = $db->getQuery(true);
                $query->select('COUNT(*)')
                      ->from($db->quoteName('#__admission_items'))
                      ->where('status = ' . $db->quote('pending'));
                $db->setQuery($query);
                $pending = $db->loadResult();
				
				$this->stats = [
					'total' => $total,
					'published' => $published,
					'unpublished' => $unpublished,
					'pending' => $pending
				];
				
            } catch (Exception $e) {
                // Колонка status не существует
                $pending = 0;
            }

            $this->stats = [
                'total' => $total,
                'published' => $published,
                'unpublished' => $unpublished,
                'pending' => $pending
            ];
            
        } catch (Exception $e) {
            // Если возникает ошибка, используем нулевые значения
            $this->stats = [
                'total' => 0,
                'published' => 0,
                'unpublished' => 0,
                'pending' => 0
            ];
        }
    }

    /**
     * Add the page title and toolbar.
     */
    protected function addToolbar()
    {
        ToolbarHelper::title(Text::_('Admission Dashboard'), 'address admission');
        
        // Кнопка добавления в тулбар
        ToolbarHelper::addNew('item.add');
        
        ToolbarHelper::preferences('com_admission');
    }
}