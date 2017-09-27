# Recurring relation for Laravel models.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/blessingdube/laravel-recurring.svg?style=flat-square)](https://packagist.org/packages/blessingdube/laravel-recurring)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/blessingdube/laravel-recurring/master.svg?style=flat-square)](https://travis-ci.org/blessingdube/laravel-recurring)
[![StyleCI](https://styleci.io/repos/276990480/shield?branch=master)](https://styleci.io/repos/276990480)
[![Total Downloads](https://img.shields.io/packagist/dt/blessingdube/laravel-recurring.svg?style=flat-square)](https://packagist.org/packages/blessingdube/laravel-recurring)

**Note:** This package is still in active development so breaking changes may be applied before v1 is released. Please proceed with caution.

This package adds a recurring relation to laravel models.

## Installation

You can install the package via composer:

```bash
composer require blessingdube/laravel-recurring
```

## Usage

``` php
use BlessingDube\Recurring\Contracts\IsRecurring;
use BlessingDube\Recurring\Traits\RecurringTrait;

class Event extends Model implements IsRecurring
{
    use RecurringTrait;

    public function getRecurringOptions()
    {
        return [
            'start_date' => 'starts_at',
            'end_date' => 'ends_at',
        ];
    }
}
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
composer test
```


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
