# Configuring package for synchronizer
Package can be configured for synchronizer very easily.

- First decide for which frameworks you want to support.
- Then choose alias or name of the synchronizer given for selected framework.
- Another step is to create a `composer-synchronizer` section in the `extra section` in your package `composer.json` file
under the framework synchronizer label.

**Example for Nette framework**
````JSON
"extra": {
    "composer-synchronizer": {
        "nette": {
            "resources": {
                "synchronizer/nette/config": "%configDir%/myPackage"
                "synchronizer/nette/assets": "%wwwDir%/assets",
            },
            "includes": [
                "myPackage/extension.neon"
            ]
            "gitignore": [
                ".travis-yml",
                ".somefile"
            ]
        }
    }
}
````
