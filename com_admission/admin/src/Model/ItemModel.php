<?php
namespace JohnSmith\Component\Admission\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;

class ItemModel extends AdminModel
{
    public function getForm($data = [], $loadData = true)
    {
        // Получаем форму
        $form = $this->loadForm(
            'com_admission.item',
            'item',
            ['control' => 'jform', 'load_data' => $loadData]
        );
        
        if (empty($form)) {
            return false;
        }
        
        return $form;
    }
    
    protected function loadFormData()
    {
        // Проверяем сессию на наличие ранее введенных данных формы
        $data = Factory::getApplication()->getUserState(
            'com_admission.edit.item.data',
            []
        );
        
        if (empty($data)) {
            $data = $this->getItem();
        }
        
        return $data;
    }
    
    public function getItem($pk = null)
    {
        if ($item = parent::getItem($pk)) {
            // Дополнительная обработка данных если нужно
            return $item;
        }
        
        return false;
    }
    
    public function getTable($name = 'Item', $prefix = 'Administrator', $options = [])
    {
        return parent::getTable($name, $prefix, $options);
    }
}