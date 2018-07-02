<?php

namespace flipbox\hubspot\tests;

use Codeception\Test\Unit;
use flipbox\craft\psr3\Logger;
use flipbox\hubspot\HubSpot as HubSpotPlugin;
use flipbox\hubspot\services\Cache;
use flipbox\hubspot\services\Connections;
use flipbox\hubspot\services\ObjectAssociations;
use flipbox\hubspot\services\Resources;
use flipbox\hubspot\services\ObjectsField;
use flipbox\hubspot\services\Transformers;

class HubSpotTest extends Unit
{
    /**
     * @var HubSpotPlugin
     */
    private $module;

    /**
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * phpcs:disable PSR2.Methods.MethodDeclaration.Underscore
     */
    protected function _before()
    {
        $this->module = new HubSpotPlugin('hubspot');
    }

    /**
     * Test the component is set correctly
     */
    public function testCacheComponent()
    {
        $this->assertInstanceOf(
            Cache::class,
            $this->module->getCache()
        );

        $this->assertInstanceOf(
            Cache::class,
            $this->module->cache
        );
    }

    /**
     * Test the component is set correctly
     */
    public function testConnectionsComponent()
    {
        $this->assertInstanceOf(
            Connections::class,
            $this->module->getConnections()
        );

        $this->assertInstanceOf(
            Connections::class,
            $this->module->connections
        );
    }

    /**
     * Test the component is set correctly
     */
    public function testPSR3Component()
    {
        $this->assertInstanceOf(
            Logger::class,
            $this->module->getPsrLogger()
        );

        $this->assertInstanceOf(
            Logger::class,
            $this->module->psr3Logger
        );
    }

    /**
     * Test the component is set correctly
     */
    public function testResourcesComponent()
    {
        $this->assertInstanceOf(
            Resources::class,
            $this->module->getResources()
        );

        $this->assertInstanceOf(
            Resources::class,
            $this->module->resources
        );
    }

    /**
     * Test the component is set correctly
     */
    public function testObjectAssociationsComponent()
    {
        $this->assertInstanceOf(
            ObjectAssociations::class,
            $this->module->getObjectAssociations()
        );

        $this->assertInstanceOf(
            ObjectAssociations::class,
            $this->module->objectAssociations
        );
    }

    /**
     * Test the component is set correctly
     */
    public function testObjectFieldsComponent()
    {
        $this->assertInstanceOf(
            ObjectsField::class,
            $this->module->getObjectsField()
        );

        $this->assertInstanceOf(
            ObjectsField::class,
            $this->module->objectsField
        );
    }

    /**
     * Test the component is set correctly
     */
    public function testTransformersComponent()
    {
        $this->assertInstanceOf(
            Transformers::class,
            $this->module->getTransformers()
        );

        $this->assertInstanceOf(
            Transformers::class,
            $this->module->transformers
        );
    }
}
