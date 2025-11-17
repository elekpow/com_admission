<?php
namespace JohnSmith\Component\Admission\Site\View\Item;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class HtmlView extends BaseHtmlView
{
    protected $item;
    
    public function display($tpl = null)
    {
        // Получаем данные через модель
        $this->item = $this->get('Item');
        
        if (empty($this->item)) {
            Factory::getApplication()->enqueueMessage('Application not found', 'error');
            return;
        }
        
        parent::display($tpl);
    }
    
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