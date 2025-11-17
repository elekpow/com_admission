<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

// Простая проверка
$app = Factory::getApplication();
$view = $app->input->get('view', 'items');
$id = $app->input->getInt('id');

echo "<!-- Debug: view=$view, id=$id -->";

// Подключаем контроллер
require_once JPATH_COMPONENT . '/src/Controller/DisplayController.php';

$controller = new JohnSmith\Component\Admission\Site\Controller\DisplayController();
$controller->display();