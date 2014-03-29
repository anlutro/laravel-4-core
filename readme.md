# L4 Core [![Build Status](https://travis-ci.org/anlutro/laravel-4-core.png?branch=master)](https://travis-ci.org/anlutro/laravel-4-core)

This is my personal development boilerplate for Laravel 4. It includes user management and authentication controllers, views, language files and more.

As this is a repository mostly for my personal (re-)use, I do not recommend you ever install this into your project as I will never bother to document everything. Instead, draw inspiration from it, pick up tricks here and there from reading source code.

Also check out the following repositories, which contains classes that this package uses. These are suited to be included in your own projects and are more thoroughly documented.

- https://github.com/anlutro/laravel-repository
- https://github.com/anlutro/laravel-validation
- https://github.com/anlutro/laravel-controller
- https://github.com/anlutro/laravel-testing

## Installation

`composer require anlutro/l4-core`

Add `c\CoreServiceProvider` to the list of providers in `app/config/app.php`.

If you want the new and improved password reset/reminder functionality, remove the default ReminderServiceProvider from the providers array and replace it with `c\Auth\Reminders\ReminderServiceProvider`. If you want access to activation, add `c\Auth\Activation\ActivationServiceProvider` as well. Ideally you should never touch any of these but just let the `c\Auth\UserManager` class do its stuff.

The package does not use default config/migration paths, so in order to publish those you have to use the following commands:

	php artisan config:publish --package=anlutro/l4-core --path=vendor/anlutro/l4-core/resources/config
	cp ./vendor/anlutro/l4-core/resources/migrations/* ./app/database/migrations

## Assumptions about your app

You have one layout view available: `layout.main`.

Your layout defines the sections 'title', 'content' and 'scripts' (at the end of the body tag, for javascript).

You have a Bootstrap 3-derived stylesheet.

## Contact

Open an issue on GitHub if you have any questions.

## License

The contents of this repository is released under the [MIT license](http://opensource.org/licenses/MIT).