# Configuring a package for synchronizer #
Package can be configured for synchronizer very easily. See [examples](https://github.com/composer-synchronizer/examples).

- Choose an alias or name of the synchronizer given for selected framework.
- Create a `composer-synchronizer` section in the `extra section` in your package `composer.json` file
under the framework synchronizer label (in this example `nette`).
- Add a custom directory in your package, where the configuration files will be stored and from where the synchronizer will copy them.

**Example for Nette framework (local - directly in package)**
````JSON
"extra": {
    "composer-synchronizer": {
        "nette": {
            "resources": {
                "synchronizer/nette/config/": "%configDir%/myPackage/",
                "synchronizer/nette/assets/": "%wwwDir%/assets/",
            },
            "includes": [
                "myPackage/extension.neon"
            ],
            "gitignore": [
                ".somefile",
                "somedirectory/"
            ]
        }
    }
}
````

**Example for Nette framework (remote - stored in Github repository)**
- Create a config.json and add files into the [packages repository](https://github.com/composer-synchronizer/packages) in the directory with the following mask `<package name>/<version>/<project type>` => `machy8/webloader/1.2/nette2/config.json`.
- As when configuring a local package, create a directory where the configuration files will be stored.

````JSON
{
    "resources": {
        "synchronizer/nette/config/": "%configDir%/myPackage/",
        "synchronizer/nette/assets/": "%wwwDir%/assets/",
    },
    "includes": [
        "myPackage/extension.neon"
    ],
    "gitignore": [
        ".somefile",
        "somedirectory/"
    ]
}
````

## Common configuration options ##
These options are available for all projects types.

**Sections**
- Resources - files and directories in this section are copied into selected paths.
- Gitignore - array of files and directories that are added into the .gitignore file in the project root into the package section.
On desynchronization this section is removed.
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

### [Nette](https://nette.org/en/)
#### Nette 2.x
- Versioned name: `nette2`
- Alias: `nette`
- During the first initialization creates a `composer-synchronizer.neon` file in the `app/config` directory.

**Paths placeholders**
- appDir => app
- configDir => app/config
- logDir => log
- tempDir => temp
- wwwDir => www

**Sections**
- Includes - Appends path to extension file into the composer-synchronizer.neon file that is included in the bootstrap.php.
On desynchronization of some package, the path to that package extension file is removed.

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

**Paths placeholders**
- commandsDir => commands
- configDir => config
- webDir => web

**Sections**
- Configs - Appends a variable name and a path to required configuration file into the config/composer-synchronizer.php file.
On desynchronization of some package, the variable and the path is removed.

````
...
"configs": [
    "twig": "path/to/twig/config.php"
]
....
````
