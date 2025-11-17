<?php
namespace JohnSmith\Component\Admission\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class DisplayController extends BaseController
{
    protected $default_view = 'admission';
    
    public function display($cachable = false, $urlparams = [])
    {
        $view = $this->input->get('view', $this->default_view);
        $layout = $this->input->get('layout', 'default');
        $id = $this->input->getInt('id');

        // Устанавливаем view
        $this->input->set('view', $view);

        parent::display($cachable, $urlparams);

        return $this;
    }
}