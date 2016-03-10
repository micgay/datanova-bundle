<?php

namespace Laposte\DatanovaBundle\Tests\DependencyInjection;

use Laposte\DatanovaBundle\DependencyInjection\LaposteDatanovaExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Florian Ajir <florianajir@gmail.com>
 */
class LaposteDatanovaExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LaposteDatanovaExtension
     */
    private $extension;

    /**
     * Root name of the configuration
     *
     * @var string
     */
    private $root;

    /**
     * @return LaposteDatanovaExtension
     */
    protected function getExtension()
    {
        return new LaposteDatanovaExtension();
    }

    /**
     * @return ContainerBuilder
     */
    private function getContainer()
    {
        $container = new ContainerBuilder();

        return $container;
    }

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();

        $this->extension = $this->getExtension();
        $this->root = 'data_nova';
    }

    /**
     *
     */
    public function testGetConfigWithDefaultValues()
    {
        $this->extension->load(array(), $container = $this->getContainer());

        $this->assertTrue($container->has('data_nova.manager.records'));
    }
}
