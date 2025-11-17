<?php
defined('_JEXEC') or die;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        // Пустой провайдер - Joomla будет использовать автозагрузку
    }
};