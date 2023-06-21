<?php

namespace Tests\Feature;

use Framekit\Contracts\Projector as ProjectorContract;
use Framekit\Contracts\Store as StoreContract;
use Framekit\Drivers\EventStore as RealStore;
use Framekit\Drivers\Projector as RealProjector;
use Framekit\Facades\EventStore;
use Framekit\Facades\Projector;
use Framekit\Testing\EventStore as FakeStore;
use Framekit\Testing\Projector as FakeProjector;
use Tests\AppCase;

/**
 * Facades feature tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
class FakeFacadesTest extends AppCase
{
    public function testFakingEventStore()
    {
        EventStore::fake();

        $this->assertInstanceOf(
            FakeStore::class,
            $this->app->make(StoreContract::class)
        );

        $this->assertNotInstanceOf(
            RealStore::class,
            $this->app->make(StoreContract::class)
        );
    }

    public function testSwitchToFakeStore()
    {
        $this->assertInstanceOf(
            RealStore::class,
            $this->app->make(StoreContract::class)
        );

        EventStore::fake();

        $this->assertInstanceOf(
            FakeStore::class,
            $this->app->make(StoreContract::class)
        );
    }

    public function testFakingProjector()
    {
        Projector::fake();

        $this->assertInstanceOf(
            FakeProjector::class,
            $this->app->make(ProjectorContract::class)
        );

        $this->assertNotInstanceOf(
            RealProjector::class,
            $this->app->make(ProjectorContract::class)
        );
    }

    public function testSwitchToFakeProjector()
    {
        $this->assertInstanceOf(
            RealProjector::class,
            $this->app->make(ProjectorContract::class)
        );

        Projector::fake();

        $this->assertInstanceOf(
            FakeProjector::class,
            $this->app->make(ProjectorContract::class)
        );
    }
}
