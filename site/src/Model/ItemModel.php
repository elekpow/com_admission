<?php
namespace JohnSmith\Component\Admission\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ItemModel;
use Joomla\CMS\Factory;

class ItemModel extends ItemModel
{
    public function getItem($pk = null)
    {
        $app = Factory::getApplication();
        $pk = $app->input->getInt('id', 0);
        
        if (!$pk) {
            return null;
        }

        try {
            $db = Factory::getDbo();
            $query = $db->getQuery(true)
                ->select('*')
                ->from($db->quoteName('#__admission_items'))
                ->where($db->quoteName('id') . ' = ' . (int) $pk)
                ->where($db->quoteName('state') . ' = 1');
            
            $db->setQuery($query);
            $item = $db->loadObject();
            
            return $item;
            
        } catch (\Exception $e) {
            return null;
        }
    }
}