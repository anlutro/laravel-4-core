# L4 Core

This is my development boilerplate for Laravel 4. It includes user and authentication controllers and views as well as a bunch of base classes you can use.

I can't document every feature here so I recommend you just read the source code instead.

I also can't recommend using these exact classes in your project, instead try to draw inspiration and make your own "core" package :)

All classes reside in the "c\" namespace (except controllers which are in the global namespace) for the sake of convenience. If you don't like this, don't use the package.

## Installation

`composer require anlutro/l4-core`

Add `c\CoreServiceProvider` to the list of providers in `app/config/app.php`.

If you want the new and improved password reset/reminder functionality, remove the default ReminderServiceProvider from the providers array and replace it with `c\Auth\Reminders\ReminderServiceProvider`.

If you want access to activation, add `c\Auth\Activation\ActivationServiceProvider` as well. Optionally, add the alias `'Activation' => 'c\Auth\Activation\Activation'` to your list of aliases.

## Assumptions about your app

You have two layouts available: `layout.main` and `layout.fullwidth`. Fullwidth is used by non-logged in users (on routes like login, reset password etc.).

Your layouts define the sections 'title', 'content' and 'scripts' (for javascript).

You have a Bootstrap 3-derived stylesheet.