<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Framekit\Contracts\Serializable;
use Framekit\Entity;
use Tests\Components\SumEntity;
use Tests\UnitCase;

/**
 * State unit tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
class EntityTest extends UnitCase
{
    public function testGetCreatedAt()
    {
        $date = Carbon::now();
        $entity = $this->getMockForAbstractClass(Entity::class, ['uuid', $date]);

        $this->assertEquals(
            $date,
            $entity->createdAt()
        );

        $this->assertEquals(
            null,
            $entity->deletedAt()
        );
    }

    public function testMarkAsRemoved()
    {
        $date = Carbon::now();
        $entity = $this->getMockForAbstractClass(Entity::class, ['uuid', $date]);

        $this->assertFalse($entity->isAlreadyDeleted());

        $entity->markAsRemoved();

        $this->assertEquals(
            $date->format('Y-m-d H:i'),
            $entity->deletedAt()->format('Y-m-d H:i')
        );

        $this->assertTrue($entity->isAlreadyDeleted());
    }

    public function testInitState()
    {
        $date = Carbon::now();
        $entity = SumEntity::createWithCurrentTime('uuid');

        $this->assertEquals(
            $date->format('Y-m-d H:i'),
            $entity->createdAt()->format('Y-m-d H:i')
        );

        $this->assertEquals(
            'uuid',
            $entity->id
        );
    }
}
