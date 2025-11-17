<?php
namespace JohnSmith\Component\Admission\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Factory;

class ItemsModel extends ListModel
{
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'a.id',
                'title', 'a.title',
                'email', 'a.email',
                'status', 'a.status',
                'state', 'a.state',
                'created', 'a.created',
                'ordering', 'a.ordering',
            ];
        }

        parent::__construct($config);
    }

    protected function getListQuery()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        
        // Select required fields
        $query->select([
            'a.*',
            $db->quoteName('u.name', 'author_name')
        ])
        ->from($db->quoteName('#__admission_items', 'a'))
        ->leftJoin(
            $db->quoteName('#__users', 'u') . 
            ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('a.created_by')
        );
        
        // Filter by published state
        $published = $this->getState('filter.state');
        if (is_numeric($published)) {
            $query->where($db->quoteName('a.state') . ' = ' . (int) $published);
        } elseif ($published === '') {
            $query->where($db->quoteName('a.state') . ' IN (0, 1)');
        }
        
        // Filter by status
        $status = $this->getState('filter.status');
        if ($status && $status !== '*') {
            $query->where($db->quoteName('a.status') . ' = ' . $db->quote($status));
        }
        
        // Search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where($db->quoteName('a.id') . ' = ' . (int) substr($search, 3));
            } else {
                $search = $db->quote('%' . $db->escape($search, true) . '%');
                $query->where($db->quoteName('a.title') . ' LIKE ' . $search);
            }
        }
        
        // Ordering
        $orderCol = $this->getState('list.ordering', 'a.created');
        $orderDirn = $this->getState('list.direction', 'DESC');
        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));
        
        return $query;
    }

    protected function populateState($ordering = 'a.created', $direction = 'DESC')
    {
        $app = Factory::getApplication();
        
        // Load parameters
        $params = $app->getParams();
        $this->setState('params', $params);
        
        // Filter state
        $state = $app->getUserStateFromRequest(
            $this->context . 'filter.state', 
            'filter_state', 
            '', 
            'string'
        );
        $this->setState('filter.state', $state);
        
        // Filter status
        $status = $app->getUserStateFromRequest(
            $this->context . 'filter.status', 
            'filter_status', 
            '', 
            'string'
        );
        $this->setState('filter.status', $status);
        
        // Search
        $search = $app->getUserStateFromRequest(
            $this->context . 'filter.search', 
            'filter_search', 
            '', 
            'string'
        );
        $this->setState('filter.search', $search);
        
        parent::populateState($ordering, $direction);
    }
}