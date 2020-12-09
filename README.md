# Framekit

[![Latest Stable Version](https://poser.pugx.org/mr-luke/framekit/v)](//packagist.org/packages/mr-luke/framekit)
[![Total Downloads](https://poser.pugx.org/mr-luke/framekit/downloads)](//packagist.org/packages/mr-luke/framekit)
[![Build Status](https://scrutinizer-ci.com/g/mr-luke/framekit/badges/build.png?b=master)](https://scrutinizer-ci.com/g/mr-luke/framekit/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mr-luke/framekit/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mr-luke/framekit/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/mr-luke/framekit/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/mr-luke/framekit/?branch=master)

[[_TOC_]]

## Getting Started

Framekit is Laravel package built to speed up DDD modeling with EventSourcing. It implements 
CQRS architecture. This version will be replaced by `2.0.0` where some concepts will be re-written.

## Installation

To install through composer, simply put the following in your composer.json file and run `composer update`

```json
{
    "require": {
        "mr-luke/framekit": "~1.0"
    }
}
```
Or use the following command

```bash
composer require "mr-luke/framekit"
```

## Configuration

To use `Framekit` we need to set up some `env` variables. To see all of them just run command:
```bash
php artisan vendor:publish
```

## Base Components

### `\Framekit\AggregateRoot`
It's main build up component. Due to DDD building blocks it's our root or Model tree. The heart 
of an aggregate is our Entity model.

### `\Framekit\State`
This is our Entity abstract, we call it State.

### `\Framekit\Event`
Every Aggregate uses Events as state flow control blocks.

### `\Framekit\Projection`
Since we work with CQRS, it's an instruction how to decompose our model to Query side model.

### `\Framekit\Retrospection`
EventSourcing gives us an ability to walk along the stream to make a retrospection of Events.

There are many more elements to explore...
