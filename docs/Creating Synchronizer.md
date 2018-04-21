# Creating synchronizer
If you want to create a synchronizer for your favorite framework, you need to send a pull request.
Creating a new synchronizer plugin is pretty simple and all you need to do is
extend the `AbstractSynchronizer` class and implement `SynchronizerInterface` interface and register the
synchronizer in the `SYNCHRONIZER_REGISTER` constant in the `SynchronizersMaster` class

## init()
- This method is intended for some preparations before synchronization like
creating directories and files, configuring variables etc.

## getPathsPlaceholders()
- Returns paths placeholders used in the AbstractSynchronizer class for synchronizing resources,
and gitignore file.
- Paths returned in this method are always against the project root directory.

## getConfigurationSections()
- Returns an array of key value pairs where the key is the name of the section in the composer.json and
the function callback is responsible for its processing.

## getAliases()
- The array returned from this function contains aliases for the synchronizer. It is because of the versioned name.
- When you have versioned name for example `nette2` and `nette2` is the latest version, then the alias can be `nette`.

## getVersionedName()
- Returns the versioned name for the synchronizer.
- For example for Nette 2.0 => `nette2`. For `Nette 3.0` it would be `nette3`.

## Creating synchronizing and desynchronizing methods
- Synchronizing and desynchronizing methods should be `protected`
- Only one parameter is passed to these methods and its type depends on the one that is used in the composer.json
configuration section (`{}` => stdClass, `[]` => array, `""` => string);
- During creating the synchronizer and its methods you can use `Helpers` class. It contains methods
for working with files and `consoleMessage` method for adding some output to the console while the package is processed.

