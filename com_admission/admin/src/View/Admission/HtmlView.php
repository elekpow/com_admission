<?php
namespace JohnSmith\Component\Admission\Administrator\View\Admission;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
    public function display($tpl = null)
    {
        // Получаем статистику
        $stats = $this->getStats();
        
        // Передаем данные в шаблон
        $this->stats = $stats;
        
        // Устанавливаем тулбар
        $this->addToolbar();
        
        parent::display($tpl);
    }
    
    protected function getStats()
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
    
    protected function addToolbar()
    {
        ToolbarHelper::title(Text::_('COM_ADMISSION_DASHBOARD_TITLE'), 'dashboard admission');
        
        // Кнопки
        ToolbarHelper::preferences('com_admission');
    }
}