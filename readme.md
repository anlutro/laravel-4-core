# L4 Core

This is my development boilerplate for Laravel 4. It includes user and authentication controllers and views as well as a bunch of base classes you can use.

I can't document every feature here so I recommend you just read the source code instead.

I also can't recommend using these exact classes in your project, instead try to draw inspiration and make your own "core" package :)

All classes reside in the "c\" namespace (except controllers which are in the global namespace) for the sake of convenience. If you don't like this, don't use the package.

Also check out the following repositories, which contains classes that this package uses:

- https://github.com/anlutro/laravel-repository
- https://github.com/anlutro/laravel-validation
- https://github.com/anlutro/laravel-controller
- https://github.com/anlutro/laravel-testing

## Installation

`composer require anlutro/l4-core`

Add `c\CoreServiceProvider` to the list of providers in `app/config/app.php`.

If you want the new and improved password reset/reminder functionality, remove the default ReminderServiceProvider from the providers array and replace it with `c\Auth\Reminders\ReminderServiceProvider`.

If you want access to activation, add `c\Auth\Activation\ActivationServiceProvider` as well. Optionally, add the alias `'Activation' => 'c\Auth\Activation\Activation'` to your list of aliases.

## Assumptions about your app

You have two layouts available: `layout.main` and `layout.fullwidth`. Fullwidth is used by non-logged in users (on routes like login, reset password etc.).

Your layouts define the sections 'title', 'content' and 'scripts' (for javascript).

You have a Bootstrap 3-derived stylesheet.

## Activation

Similar to the Password:: functionality, Activation:: is a simple way to require your users to activate their accounts.

`Activation::generate($user)` generates an activation code for $user and sends an email with instructions on how to activate their account.

`Activation::activate($code)` activates the user which $code belongs to.

## Improved password reminders

The functions on the Password:: class have changed.

`Password::requestReset($user)` generates a reset token for $user and sends said user an email with instructions. It returns false or true depending on whether or not the mail was successfully sent.

`Password::resetUser($user, $token, $newPassword)` makes an attempt at resetting $user's password. Will return false if $token does not belong to said user.

## Contact
Open an issue on GitHub if you have any problems or suggestions.

## License
The contents of this repository is released under the [MIT license](http://opensource.org/licenses/MIT).