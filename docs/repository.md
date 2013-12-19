# Base repository

Location: src/c/EloquentRepository.php

A repository is a class that lies between the controller and the model to make the controller more lightweight and allows you to more easily re-use database logic in your application.

The repository comes with some standard methods already, like getByKey and getAll. You may add as many custom methods you want to the repository. Overwrite the constructor method to inject your own model and validator. The methods available by default are:

- getAll()
- getByKey($key)
- update($model, $attributes) - does update validation
- delete($model)
- getNew($attributes) - no validation, simply a new model instance
- makeNew($attributes) - does create validation, doesn't save to database
- create($attributes) - does create validation, saves to database

You can toggle pagination on and off by using paginate(false) or paginate(20). You can toggle exceptions with the toggleExceptions methd - this will make sure that firstOrFail is called instead of first - useful for an API or where you otherwise have a generic error handler for the ModelNotFound exception. These methods can be chained, so you can for example do paginate(20)->getAll().

The repository utilizes the fetchSingle and fetchMany methods to run queries. Before the query is ran, prepareQuery is called with the query builder instance - this way, you can add default behaviour to all queries being ran through the repository - this can be used to limit results to rows related to the logged in user, utilize search or filtering and so on.

In addition to prepareQuery there is also prepareModel, prepareCollection and preparePaginator, which are called after the query is ran. This is useful if you have some operations you want to run in prepareQuery but can't because it'll mess up how Eloquent or the paginator works, or if what you want to do can't be done until the data has actually been fetched.

The repository also utilizes validation. If methods like update() and create() return false, validation errors are available via the errors() method.

There's also a raw database repository which only makes use of the query builder, but it's not fully developed yet.