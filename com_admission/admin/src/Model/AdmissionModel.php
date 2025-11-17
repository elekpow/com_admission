<?php
namespace JohnSmith\Component\Admission\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Factory;

class AdmissionModel extends AdminModel
{
    public function getForm($data = [], $loadData = true)
    {
        // Форма для редактирования заявки в админке
        $form = $this->loadForm(
            'com_admission.admission',
            'admission',
            ['control' => 'jform', 'load_data' => $loadData]
        );
        
        if (empty($form)) {
            return false;
        }
        
        return $form;
    }
    
    protected function loadFormData()
    {
        // Загрузка данных для формы
        $data = Factory::getApplication()->getUserState(
            'com_admission.edit.admission.data',
            []
        );
        
        if (empty($data)) {
            $data = $this->getItem();
        }
        
        return $data;
    }
}