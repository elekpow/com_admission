<?php
namespace JohnSmith\Component\Admission\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

class ItemTable extends Table
{
    public function __construct(DatabaseDriver $db)
    {
        $this->typeAlias = 'com_admission.item';
        
        parent::__construct('#__admission_items', 'id', $db);
    }
    
    public function store($updateNulls = false)
    {
        $date = JFactory::getDate();
        $user = JFactory::getUser();
        
        if ($this->id) {
            // Existing item
            $this->modified = $date->toSql();
            $this->modified_by = $user->get('id');
        } else {
            // New item
            if (!(int) $this->created) {
                $this->created = $date->toSql();
            }
            if (empty($this->created_by)) {
                $this->created_by = $user->get('id');
            }
        }
        
        return parent::store($updateNulls);
    }
}