# Available Synchronizers
There is one common synchronizer (AbstractSynchronizer) that all
other synchronizers must extend and other synchronizers specific for each
framework and its version.

## Common configuration options (Abstract synchronizer) ##
These options are available for all projects types.

**Sections**
- Resources - files and directories in this section are copied into selected paths.
- Gitignore - array of files and directories that are added into the
.gitignore file in the project root into the package section. On desynchronization
this section is removed.
````
...
"resources": {
    "some.file": "%somePlaceholder%/somePackage/",
    "another.file": "%anotherPlaceholder%/anotherPath/"
},
"gitignore": [
    ".gitattributes"
]
...
````

## Specific configuration options ##

### CakePhp ()
#### CakePhp 3.x
- Versioned name: `cakePhp3`
- Alias: `cakePhp`
- During the first initialization creates a `composer-synchronizer-configs.php` and
`composer-synchronizer-plugins.php` file in the `config` directory.

**Paths placeholders**
- configDir => config
- webrootDir => webroot

**Sections**
- Configs - Appends a path into the `config/composer-synchronizer-configs.php`
file. On desynchronization of some package, the path is removed.
- Plugins - Appends a path into the `config/composer-synchronizer-plugins.php`
file. On desynchronization of some package, the path is removed.
- The `config/composer-synchronizer-configs.php` file can be loaded in
the `config/app.php` and variables from it can be used in appropriate sections.
- The `config/composer-synchronizer-plugins.php` file can be loaded on the end of the
`config/bootstrap.php`.

````
...
"configs": [
    "path/to/some/config.php"
],
"plugins": [
    "path/to/some/plugin.php"
]
....
````


### [Nette](https://nette.org/en/)
#### Nette 2.x
- Versioned name: `nette2`
- Alias: `nette`
- During the first initialization creates a `composer-synchronizer.neon`
file in the `app/config` directory.
- The file can be loaded in bootstrap.php before the `config.local.neon`.

**Paths placeholders**
- appDir => app
- configDir => app/config
- logDir => log
- tempDir => temp
- wwwDir => www

**Sections**
- Includes - Appends path to extension file into the composer-synchronizer.neon
file that is included in the bootstrap.php. On desynchronization of some package,
the path to that package extension file is removed.

````
...
"includes": [
    "somePackage/extension.neon"
]
....
````

### [Yii](https://www.yiiframework.com/)
#### Yii 2.x
- Versioned name: `yii2`
- Alias: `yii`
- During the first initialization creates a `composer-synchronizer.php` file in the `config` directory.
- The `config/composer-synchronizer.php` file can be loaded on the top of the
`config/web.php` and the variables from it can be used on appropriate places.
**Paths placeholders**
- commandsDir => commands
- configDir => config
- webDir => web

**Sections**
- Configs - Appends a variable name and a path to required configuration
file into the config/composer-synchronizer.php file. On desynchronization
of some package, the variable and the path is removed.

````
...
"configs": [
    "twig": "path/to/twig/config.php"
]
....
````
