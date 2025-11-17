<?php
namespace JohnSmith\Component\Admission\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;

class DisplayController extends BaseController
{
    protected $default_view = 'admission';

    public function display($cachable = false, $urlparams = array())
    {
        $view = $this->input->get('view', $this->default_view);
        $layout = $this->input->get('layout', 'default');
        
        $this->input->set('view', $view);
        $this->input->set('layout', $layout);

        return parent::display($cachable, $urlparams);
    }
}