<?php

declare(strict_types=1);

namespace Framekit\Extentions;

use ReflectionClass;
use Illuminate\Foundation\Application;

/**
 * Resolve classes based on constructor.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
trait ClassResolver
{
    /**
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * @var array
     */
    private $resolved = [];

    /**
     * @param \Illuminate\Foundation\Application
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Resolve class based on constructor.
     *
     * @param string $className
     * @return mixed
     *
     * @throws \ReflectionException
     */
    public function resolveClass(string $className)
    {
        if (! isset($this->resolved[$className])) {
            $reflection  = new ReflectionClass($className);

            $dependencies = [];
            if ($constructor = $reflection->getConstructor()) {
                foreach ($constructor->getParameters() as $p) {
                    $dependencies[] = $this->app->make($p->getClass()->getName());
                }
            }

            $this->resolved[$className] = empty($dependencies) ?
                new $className : $reflection->newInstanceArgs($dependencies);
        }

        return $this->resolved[$className];
    }
}
