<?php

namespace Framekit;

use Framekit\Contracts\VersionMap as Contract;
use Framekit\Exceptions\MethodUnknown;

/**
 * Version map abstract class.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
abstract class VersionMap implements Contract
{
    /**
     * @inheritDoc
     */
    public function translate(array $payload, int $from, int $to, array $upstream): array
    {
        if ($from === $to || $from > $to) {
            return $payload;
        }

        foreach ($this->buildTranslationsStack($from, $to) as $step) {
            $payload = $this->{$step}($payload, $upstream);
        }

        return $payload;
    }

    /**
     * Build the shortest way from version A to version B.
     *
     * @param int $from
     * @param int $to
     * @return array
     * @throws \Framekit\Exceptions\MethodUnknown
     */
    protected function buildTranslationsStack(int $from, int $to): array
    {
        $stack = [];

        $i = $from;
        while ($i < $to) {
            if ($this->hasTranslation($i, $to)) {
                $stack[] = $this->buildTranslationStepMethod($i, $to);
                break;
            }

            $nextStep = $this->findNextStep($i, $to);
            $stack[] = $this->buildTranslationStepMethod($i, $nextStep);

            $i = $nextStep;
        }

        return $stack;
    }

    /**
     * Build translation method.
     *
     * @param int $from
     * @param int $to
     * @return string
     */
    protected function buildTranslationStepMethod(int $from, int $to): string
    {
        return sprintf('stepFrom%dto%d', $from, $to);
    }

    /**
     * Determine where this step ends.
     *
     * @param int $from
     * @param int $to
     * @return int
     * @throws \Framekit\Exceptions\MethodUnknown
     */
    protected function findNextStep(int $from, int $to): int
    {
        for ($i = $to; $i > $from; $i--) {
            if ($this->hasTranslation($from, $i)) {
                return $i;
            }
        }

        throw new MethodUnknown(
            sprintf(
                'There\'s no translation method from [%d] to [%d] in [%s]',
                $from,
                $from + 1,
                get_class($this)
            )
        );
    }

    /**
     * Determine if there's a direct way to translate.
     *
     * @param int $from
     * @param int $to
     * @return bool
     */
    protected function hasTranslation(int $from, int $to): bool
    {
        return method_exists($this, $this->buildTranslationStepMethod($from, $to));
    }
}
