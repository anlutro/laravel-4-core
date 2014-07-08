# L4 Core [![Build Status](https://travis-ci.org/anlutro/laravel-4-core.png?branch=master)](https://travis-ci.org/anlutro/laravel-4-core) [![Latest Version](http://img.shields.io/github/tag/anlutro/laravel-4-core.svg)](https://github.com/anlutro/laravel-4-core/releases)

This is my personal development boilerplate for Laravel 4. It includes user management and authentication controllers, views, language files and more.

As this is a repository mostly for my personal use, I do not recommend you ever install this into your project as I will never bother to document everything. Instead, draw inspiration from it, pick up tricks here and there from reading source code.

Also check out the following repositories, which contains classes that this package uses. These are suited to be included in your own projects and are more thoroughly documented.

- https://github.com/anlutro/laravel-4-smart-errors
- https://github.com/anlutro/laravel-controller
- https://github.com/anlutro/laravel-repository
- https://github.com/anlutro/laravel-testing
- https://github.com/anlutro/laravel-validation
- https://github.com/anlutro/php-menu

## Prerequisites

- Bootstrap 3 stylesheet with the necessary Javascript installed and included
- Optional: The package anlutro/php-menu installed and its service provider registered
- Optional: The package anlutro/l4-smart-errors installed and its service provider registered

## Installation

`composer require anlutro/l4-core` - pick the latest minor release tag from github or packagist. For example, `0.16.*`

Add `anlutro\Core\CoreServiceProvider` to the list of providers in `app/config/app.php`.

Run `artisan core:publish config` to publish config files. You can do the same with "migration", "lang" and "view" if you want. Delete the published files you don't want to override.

Remove everything from app/start/global.php except the line that sets up the logger.

In app/config/auth.php, set the driver to "eloquent-exceptions".

In app/start/global.php or a serviceprovider's boot method, you need to add some code.

```php
// either one of these, depending on if you want a sidebar or not
View::alias('c::layout.main-nosidebar', 'c::layout.main');
View::alias('c::layout.main-sidebar', 'c::layout.main');

// register CSS and JS files to be included
View::composer('c::layout.main-generic', function($view) {
	$view->styles->add(URL::asset('css/app.min.css'));
	$view->headScripts->add(URL::asset('js/modernizr.min.js'));
	$view->bodyScripts->add(URL::asset('js/jquery.min.js'));
	$view->bodyScripts->add(URL::asset('js/bootstrap.min.js'));
	$view->bodyScripts->add(URL::asset('js/app.min.js'));
});
```

### Password reset

If you want the new and improved password reset/reminder functionality, remove the default ReminderServiceProvider from the providers array and replace it with `anlutro\Core\Auth\Reminders\ReminderServiceProvider`.

### User registration and activation

If you want access to activation, add `anlutro\Core\Auth\Activation\ActivationServiceProvider` as well.

### Improved form builder

Replace the default Laravel HtmlServiceProvider with `anlutro\Core\Form\ServiceProvider` in the providers array.

## Contact

Open an issue on GitHub if you have any questions.

## License

The contents of this repository is released under the [MIT license](http://opensource.org/licenses/MIT).