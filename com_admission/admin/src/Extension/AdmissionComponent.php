<?php
namespace JohnSmith\Component\Admission\Administrator\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentInterface;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;

class AdmissionComponent implements ComponentInterface
{
    protected $componentDispatcherFactory;
    protected $mvcFactory;

    public function __construct(ComponentDispatcherFactoryInterface $dispatcherFactory)
    {
        $this->componentDispatcherFactory = $dispatcherFactory;
    }

    public function setMVCFactory(MVCFactoryInterface $mvcFactory): void
    {
        $this->mvcFactory = $mvcFactory;
    }

    public function getDispatcherFactory(): ComponentDispatcherFactoryInterface
    {
        return $this->componentDispatcherFactory;
    }

    public function getMVCFactory(): MVCFactoryInterface
    {
        return $this->mvcFactory;
    }
}