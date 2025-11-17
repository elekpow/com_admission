<?php
namespace JohnSmith\Component\Admission\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Factory;

class AdmissionModel extends ListModel
{
    /**
     * Method to get items for dashboard
     */
    public function getItems()
    {
        // Простая реализация - возвращаем тестовые данные
        $items = array();
        
        // Тестовые данные
        $items[] = (object) array(
            'id' => 1,
            'title' => 'Sample Admission Item 1',
            'state' => 1,
            'created' => Factory::getDate()->toSql()
        );
        
        $items[] = (object) array(
            'id' => 2,
            'title' => 'Sample Admission Item 2', 
            'state' => 1,
            'created' => Factory::getDate()->toSql()
        );
        
        return $items;
    }

    /**
     * Method to get pagination (заглушка)
     */
    public function getPagination()
    {
        return null;
    }

    /**
     * Method to get model state (заглушка)
     */
    public function getState()
    {
        return null;
    }
}