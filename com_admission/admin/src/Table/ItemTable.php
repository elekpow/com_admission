<?php
namespace JohnSmith\Component\Admission\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class ItemTable extends Table
{
    public function __construct(\JDatabaseDriver $db)
    {
        $this->typeAlias = 'com_admission.item';
        parent::__construct('#__admission_items', 'id', $db);
    }

    public function check()
    {
        try {
            parent::check();
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }

        // Проверка обязательных полей
        if (trim($this->title) == '') {
            $this->setError(Text::_('COM_ADMISSION_ERR_TITLE_EMPTY'));
            return false;
        }

        if (trim($this->email) == '') {
            $this->setError(Text::_('COM_ADMISSION_ERR_EMAIL_EMPTY'));
            return false;
        }

        return true;
    }

    public function store($updateNulls = false)
    {
        $date = Factory::getDate();
        $user = Factory::getUser();

        if ($this->id) {
            // Existing item
            $this->modified = $date->toSql();
            $this->modified_by = $user->id;
        } else {
            // New item
            if (!(int) $this->created) {
                $this->created = $date->toSql();
            }
            if (empty($this->created_by)) {
                $this->created_by = $user->id;
            }
        }

        return parent::store($updateNulls);
    }
}