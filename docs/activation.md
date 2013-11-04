# Activation

Similar to the Password:: functionality, Activation:: is a simple way to require your users to activate their accounts.

`Activation::generate($user)` generates an activation code for $user and sends an email with instructions on how to activate their account.

`Activation::activate($code)` activates the user which $code belongs to.