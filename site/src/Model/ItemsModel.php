<?php
namespace JohnSmith\Component\Admission\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;

class ItemsModel extends ListModel
{
    protected function getListQuery()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        
        $query->select('*')
              ->from($db->quoteName('#__admission_items'))
              ->where($db->quoteName('state') . ' = 1');
        
        return $query;
    }
}