<?php
defined('_JEXEC') or die;

$id = JFactory::getApplication()->input->getInt('id', 0);
echo "<h1>Testing Item View - ID: $id</h1>";
echo "<a href='index.php?option=com_admission'>Back to list</a>";