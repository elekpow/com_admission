<?php
namespace JohnSmith\Component\Admission\Administrator\View\Item;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Toolbar\Toolbar;

class HtmlView extends BaseHtmlView
{
    protected $form;
    protected $item;
    
    public function display($tpl = null)
    {
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        
        // Проверяем ошибки
        if (count($errors = $this->get('Errors'))) {
            throw new \Exception(implode("\n", $errors), 500);
        }
        
        // Устанавливаем тулбар
        $this->addToolbar();
        
        parent::display($tpl);
    }
    
    protected function addToolbar()
    {
        Factory::getApplication()->input->set('hidemainmenu', true);
        
        $isNew = ($this->item->id == 0);
        
        ToolbarHelper::title($isNew ? Text::_('COM_ADMISSION_MANAGER_ITEM_NEW') : 
            Text::_('COM_ADMISSION_MANAGER_ITEM_EDIT'), 'pencil-2 article');
        
        // Для существующих записей показываем кнопки сохранения
        if (!$isNew) {
            ToolbarHelper::apply('item.apply');
            ToolbarHelper::save('item.save');
            ToolbarHelper::save2new('item.save2new');
        }
        
        ToolbarHelper::save2copy('item.save2copy');
        ToolbarHelper::cancel('item.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
    }
}