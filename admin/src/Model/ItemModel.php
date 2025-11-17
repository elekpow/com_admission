<?php
namespace JohnSmith\Component\Admission\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;

class ItemModel extends AdminModel
{
    public $typeAlias = 'com_admission.item';

    public function getTable($name = 'Item', $prefix = 'Administrator', $options = array())
    {
        return Table::getInstance($name, $prefix, $options);
    }

    public function getForm($data = array(), $loadData = true)
    {
        $form = $this->loadForm('com_admission.item', 'item', array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    protected function loadFormData()
    {
        $data = Factory::getApplication()->getUserState('com_admission.edit.item.data', array());

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    public function getItem($pk = null)
    {
        $item = parent::getItem($pk);

        // Если это новая запись, устанавливаем значения по умолчанию
        if ($item->id == 0) {
            $item->created = Factory::getDate()->toSql();
            $item->created_by = Factory::getUser()->id;
        }

        return $item;
    }

    protected function prepareTable($table)
    {
        $date = Factory::getDate();
        $user = Factory::getUser();

        if (empty($table->id)) {
            // New record
            $table->created = $date->toSql();
            $table->created_by = $user->id;
        } else {
            // Existing record
            $table->modified = $date->toSql();
            $table->modified_by = $user->id;
        }
    }

    public function save($data)
    {
        // Устанавливаем значения по умолчанию для новых записей
        if (empty($data['id'])) {
            $data['created'] = Factory::getDate()->toSql();
            $data['created_by'] = Factory::getUser()->id;
        }

        return parent::save($data);
    }
}