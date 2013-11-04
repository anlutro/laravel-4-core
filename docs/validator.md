# Validator service class

Location: src/c/Validator.php

This validation class is a layer on top of Laravel's own Validation class (the one you create by calling Validator::make), meant to be injected into a repository or controller.

Create one Validator for each model or purpose. Overwrite the constructor to inject the correct model type, and store it in the class as `$this->model`.

Define $commonRules as an array of rules that should always be ran when querying the validator. In addition, you can optionally define rules like $updateRules, $createRules, $myCallRules etc.

`$validator->validCreate($attributes)` will merge $commonRules and $createRules. If $createRules doesn't exist, it'll just use $commonRules.

The class will automatically replace `<table>` with the model's table, and if you've set a key using `$validator->setKey(123)`, `<key>` will be replaced with what you provided (if you don't, it'll replace it with `null`). Very useful for exists and unique rules.