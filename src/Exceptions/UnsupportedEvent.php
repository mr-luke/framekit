<?php

namespace Mrluke\Framekit\Exceptions;

/**
 * Exception thrown when Event has no Reducer that
 * can handle State mutation.
 */
class UnsupportedEvent extends Exception {}
