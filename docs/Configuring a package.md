# Configuring a package for synchronizer #
Package can be configured for synchronizer very easily.

- First decide which frameworks you want to support.
- Then choose an alias or name of the synchronizer given for selected framework.
- Another step is to create a `composer-synchronizer` section in the `extra section` in your package `composer.json` file
under the framework synchronizer label (in this example `nette`).
- The last step is to add a custom directory in your package, where the configuration files will be stored and from where the synchronizer will copy them.

**Example for Nette framework (local)**
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

**Example for Nette framework (remote)**
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

## Common configuration sections ##
- Resources - files and directories in this section are copied into selected paths.
- Gitignore - array of files and directories that are added into the .gitignore file in the project root into the package section.
On desynchronization this section is removed.

## Configuration sections for frameworks ##
**Nette**

In the first initialization creates a `composer-synchronizer.neon` file in the `app/config` directory
- Includes - Appends path to extension file into the composer-synchronizer.neon file that is included in the bootstrap.php.
On desynchronization of some package, the path to that package extension file is removed.
