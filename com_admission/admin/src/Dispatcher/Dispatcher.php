<?php
namespace JohnSmith\Component\Admission\Administrator\Dispatcher;

defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcher;

class Dispatcher extends ComponentDispatcher
{
    public function dispatch()
    {
        // Устанавливаем представление по умолчанию
        if (!$this->input->get('view')) {
            $this->input->set('view', 'admission');
        }

        parent::dispatch();
    }
}