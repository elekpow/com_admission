<?php
namespace JohnSmith\Component\Admission\Administrator\View\Items;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Router\Route;

class HtmlView extends BaseHtmlView
{
    protected $items;
    
    public function display($tpl = null)
    {
        $this->items = $this->getItems();
        
        // Устанавливаем тулбар
        $this->addToolbar();
        
        parent::display($tpl);
    }
    
    protected function getItems()
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__admission_items'))
            ->order('created DESC');
        
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    protected function addToolbar()
    {
        ToolbarHelper::title(Text::_('COM_ADMISSION_MANAGER_ITEMS'), 'list admission');
        
        $toolbar = Toolbar::getInstance('toolbar');
        
        // Кнопка добавления
        $toolbar->standardButton('new')
            ->text('JTOOLBAR_NEW')
            ->task('item.add')
            ->icon('icon-plus');
            
        // Кнопка настроек
        $toolbar->preferences('com_admission');
    }
}