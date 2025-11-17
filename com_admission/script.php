<?php
defined('_JEXEC') or die;

class Com_AdmissionInstallerScript
{
    public function install($parent) {
        echo '<p>Admission component installed successfully</p>';
        $this->createTables();
    }
    
    public function update($parent) {
        $this->updateTables();
        return true;
    }
    
    private function createTables()
    {
        $db = \JFactory::getDbo();
        
        $tableName = $db->getPrefix() . 'admission_items';
        $tables = $db->getTableList();
        
        if (!in_array($tableName, $tables)) {
            $query = "CREATE TABLE IF NOT EXISTS `#__admission_items` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `title` varchar(255) NOT NULL,
                `description` text,
                `email` varchar(100),
                `phone` varchar(20),
                `status` varchar(50) DEFAULT 'pending',
                `state` tinyint(1) NOT NULL DEFAULT 1,
                `created` datetime NOT NULL,
                `created_by` int(11) NOT NULL,
                `modified` datetime,
                `modified_by` int(11),
                `ordering` int(11) NOT NULL DEFAULT 0,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
            
            $db->setQuery($query);
            $db->execute();
            
            echo '<p>Database table created successfully</p>';
            $this->addSampleData();
        } else {
            // Если таблица уже существует, проверяем наличие колонки status
            $this->updateTables();
        }
    }
    
    private function updateTables()
    {
        $db = \JFactory::getDbo();
        
        // Проверяем наличие колонки status
        $columns = $db->getTableColumns('#__admission_items');
        
        if (!isset($columns['status'])) {
            $query = "ALTER TABLE `#__admission_items` ADD COLUMN `status` VARCHAR(50) DEFAULT 'pending' AFTER `phone`";
            $db->setQuery($query);
            $db->execute();
            echo '<p>Added status column to admission_items table</p>';
        }
        
        // Добавляем тестовые данные если таблица пустая
        $query = $db->getQuery(true);
        $query->select('COUNT(*)')->from($db->quoteName('#__admission_items'));
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if ($count == 0) {
            $this->addSampleData();
        }
    }
    
    private function addSampleData()
    {
        $db = \JFactory::getDbo();
        $date = \JFactory::getDate()->toSql();
        
        $sampleData = array(
            array('Первая заявка на поступление', 'Описание первой заявки', 'applicant1@email.com', '+123456789', 'pending', $date),
            array('Вторая заявка на поступление', 'Описание второй заявки', 'applicant2@email.com', '+123456790', 'approved', $date),
            array('Третья заявка на поступление', 'Описание третьей заявки', 'applicant3@email.com', '+123456791', 'rejected', $date),
            array('Заявка на рассмотрении', 'Заявка находится на рассмотрении', 'applicant4@email.com', '+123456792', 'in_review', $date),
        );
        
        foreach ($sampleData as $data) {
            $query = $db->getQuery(true);
            $query->insert($db->quoteName('#__admission_items'))
                  ->columns('title, description, email, phone, status, created, created_by')
                  ->values(
                      $db->quote($data[0]) . ', ' . 
                      $db->quote($data[1]) . ', ' . 
                      $db->quote($data[2]) . ', ' . 
                      $db->quote($data[3]) . ', ' . 
                      $db->quote($data[4]) . ', ' . 
                      $db->quote($data[5]) . ', 1'
                  );
            $db->setQuery($query);
            $db->execute();
        }
        
        echo '<p>Sample data added</p>';
    }
}