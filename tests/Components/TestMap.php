<?php

namespace Tests\Components;

use Framekit\Contracts\VersionMap;

class TestMap implements VersionMap
{
    public function translate(array $payload, int $from, int $to, array $upstream): array
    {
        return array_merge($payload, ['added' => 1]);
    }
}
