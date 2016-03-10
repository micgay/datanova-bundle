<?php

namespace Laposte\DatanovaBundle\Tests;

use Laposte\DatanovaBundle\LaposteDatanovaBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 *
 */
class LaposteDatanovaBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $bundle = new LaposteDatanovaBundle();
        $container = new ContainerBuilder();

        $bundle->build($container);
    }
}
