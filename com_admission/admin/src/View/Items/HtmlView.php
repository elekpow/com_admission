<?php
namespace JohnSmith\Component\Admission\Administrator\View\Items;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;

class HtmlView extends BaseHtmlView
{
    protected $items;
    protected $state;
    protected $activeFilters;

    public function display($tpl = null)
    {
        // Загружаем данные
        $this->items = $this->loadItems();
        $this->state = $this->loadState();
        $this->activeFilters = $this->getActiveFilters();
        
        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar()
    {
        $document = Factory::getDocument();
        $document->setTitle('Управление заявками - Admission');
    }

    /**
     * Загрузка items с учетом фильтров
     */
    protected function loadItems()
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        $query->select('*')
              ->from($db->quoteName('#__admission_items'));
        
        // Применяем фильтры
        $app = Factory::getApplication();
        $stateFilter = $app->input->get('filter_state', '');
        $statusFilter = $app->input->get('filter_status', '');
        $searchFilter = $app->input->get('filter_search', '');
        
        // Фильтр по статусу публикации
        if ($stateFilter !== '') {
            if (is_numeric($stateFilter)) {
                $query->where('state = ' . (int)$stateFilter);
            }
        }
        
        // Фильтр по статусу заявки
        if ($statusFilter && $statusFilter !== '*') {
            $query->where('status = ' . $db->quote($statusFilter));
        }
        
        // Поиск
        if (!empty($searchFilter)) {
            $search = $db->quote('%' . $db->escape($searchFilter, true) . '%');
            $query->where('(title LIKE ' . $search . ' OR email LIKE ' . $search . ' OR description LIKE ' . $search . ')');
        }
        
        $query->order('created DESC');
        
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    /**
     * Загрузка состояния
     */
    protected function loadState()
    {
        $app = Factory::getApplication();
        return (object) [
            'get' => function($name, $default = null) use ($app) {
                switch ($name) {
                    case 'filter.search':
                        return $app->input->get('filter_search', $default);
                    case 'filter.state':
                        return $app->input->get('filter_state', $default);
                    case 'filter.status':
                        return $app->input->get('filter_status', $default);
                    default:
                        return $default;
                }
            }
        ];
    }

    /**
     * Получение активных фильтров
     */
    protected function getActiveFilters()
    {
        $filters = [];
        $app = Factory::getApplication();
        
        if ($app->input->get('filter_state') !== '') {
            $filters['state'] = $app->input->get('filter_state');
        }
        
        if ($app->input->get('filter_status') && $app->input->get('filter_status') !== '*') {
            $filters['status'] = $app->input->get('filter_status');
        }
        
        if ($app->input->get('filter_search')) {
            $filters['search'] = $app->input->get('filter_search');
        }
        
        return $filters;
    }
}