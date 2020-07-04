# Very short description of the package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/webfactor/:package_name.svg?style=flat-square)](https://packagist.org/packages/webfactor/:package_name)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/webfactor/:package_name/master.svg?style=flat-square)](https://travis-ci.org/webfactor/:package_name)
[![StyleCI](https://styleci.io/repos/123456/shield?branch=master)](https://styleci.io/repos/123456)
[![Total Downloads](https://img.shields.io/packagist/dt/webfactor/:package_name.svg?style=flat-square)](https://packagist.org/packages/webfactor/:package_name)

**Note:** Replace ```:author_name``` ```:author_username``` ```:author_email``` ```:package_name``` ```:package_description``` with their correct values in [README.md](README.md), [CHANGELOG.md](CHANGELOG.md), and [composer.json](composer.json) files, then delete this line.

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
