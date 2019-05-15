<?php

namespace Tests\Unit;

use Tests\UnitCase;

use Framekit\TransferObject;

/**
 * TransferObject unit tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
class TransferObjectTest extends UnitCase
{
    public function testThrowsWhenAttributeDoesntExist()
    {
        $this->expectException(\InvalidArgumentException::class);

        $transfer = new TransferObject([
            'first' => 'value'
        ]);
        $transfer->second;
    }

    public function testReturnCorrectAttribute()
    {
        $transfer = new TransferObject([
            'first' => 12.345
        ]);

        $this->assertEquals(
            12.345,
            $transfer->first
        );
    }
}
