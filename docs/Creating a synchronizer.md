# Creating a synchronizer
If you want to create a synchronizer for your favorite framework, you need to send a pull request.
Creating a new synchronizer plugin is very easy and all you need to do is
extend the `AbstractSynchronizer` class and register the synchronizer in the
`SYNCHRONIZERS_REGISTER` constant in the `SynchronizersManager` class.
The name of the synchronizer should be a camelcase version of the project type with the Synchronizer suffix => `nette2` - `Nette2Synchronizer`.

## init()
This method is intended for some preparations before the synchronization itself like
creating directories and files, configuring variables etc.

## getPathsPlaceholders()
Returns paths placeholders used in the AbstractSynchronizer class for synchronizing resources,
and a gitignore file. Paths returned in this method are always against the project root directory.

## getConfigurationSections()
Returns an array of key value pairs where the key is the name of the section in the composer.json and
the function callback is responsible for its processing.

## getAliases()
The array returned from this function contains aliases for the synchronizer. It is because of the versioned name.
When you have versioned name for example `nette2` and `nette2` is the "latest" version of the framework, then the alias can be `nette`.

## getVersionedName()
Returns the versioned name for the synchronizer. For example for Nette 2.0.* => `nette2`. For `Nette 3.0` it would be `nette3` (if the synchronizer exists).

## Adding synchronizing and desynchronizing methods
- Synchronizing and desynchronizing methods should be `protected`.
- Only one parameter is passed to these methods and its type depends on [which data type](https://www.w3schools.com/js/js_json_datatypes.asp)
is passed as value for the section in the composer.json configuration section.
- During development you can use `Helpers` class. It contains methods for working with
files, directories and `consoleMessage` method for adding some output to the console while
the package is processed.
