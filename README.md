# Recurring relation for Laravel models.

[![Latest Version](https://img.shields.io/github/release/oss-tools/laravel-recurring.svg?style=flat-square)](https://github.com/oss-tools/laravel-recurring/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
![GitHub Workflow Status](https://img.shields.io/github/workflow/status/oss-tools/laravel-recurring/run-tests?label=tests)
![Check & fix styling](https://github.com/oss-tools/laravel-recurring/workflows/Check%20&%20fix%20styling/badge.svg)
[![Total Downloads](https://img.shields.io/packagist/dt/oss-tools/laravel-recurring.svg?style=flat-square)](https://packagist.org/packages/oss-tools/laravel-recurring)

**Note:** This package is still in active development so breaking changes may be applied before v1 is released. Please proceed with caution.

This package adds a recurring relation to laravel models.

## Installation

You can install the package via composer:

```bash
composer require oss-tools/laravel-recurring
```

## Usage

``` php
use OSSTools\Recurring\Contracts\IsRecurring;
use OSSTools\Recurring\Traits\RecurringTrait;

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

## Testing

``` bash
composer test
```


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
