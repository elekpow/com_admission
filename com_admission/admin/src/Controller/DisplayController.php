<?php
namespace JohnSmith\Component\Admission\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;

class DisplayController extends BaseController
{
    public function display($cachable = false, $urlparams = [])
    {
        // Устанавливаем default view
        $view = $this->input->get('view', 'admission');
        $this->input->set('view', $view);
        
        parent::display($cachable, $urlparams);
        
        return $this;
    }
}