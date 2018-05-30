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

## Configuration sections ##
Visit [list of available synchronizers](https://github.com/composer-synchronizer/composer-synchronizer/blob/master/docs/Available%20synchronizers.md).
