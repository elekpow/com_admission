<?php
defined('_JEXEC') or die;

use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container->registerServiceProvider(new MVCFactory('\\JohnSmith\\Component\\Admission'));
        
        $container->set(
            MVCFactoryInterface::class,
            function (Container $container) {
                $factory = $container->get(MVCFactory::class);
                return $factory;
            }
        );
    }
}