# Improved password reminders

The functions on the Password:: class have changed.

`Password::requestReset($user)` generates a reset token for $user and sends said user an email with instructions. It returns false or true depending on whether or not the mail was successfully sent.

`Password::resetUser($user, $token, $newPassword)` makes an attempt at resetting $user's password. Will return false if $token does not belong to said user.