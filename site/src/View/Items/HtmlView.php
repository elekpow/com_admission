<?php
namespace JohnSmith\Component\Admission\Site\View\Items;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

class HtmlView extends BaseHtmlView
{
    protected $items;
    
    public function display($tpl = null)
    {
        // Получаем данные напрямую из БД
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__admission_items'))
            ->where($db->quoteName('state') . ' = 1')
            ->order('created DESC');
        
        $db->setQuery($query);
        $this->items = $db->loadObjectList();
        
        parent::display($tpl);
    }
    
    // Публичный метод для использования в шаблоне
    public function getStatusClass($status)
    {
        switch ($status) {
            case 'approved': return 'success';
            case 'rejected': return 'danger';
            case 'pending': 
            default: return 'warning';
        }
    }
}