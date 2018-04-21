# Composer synchronizer

[![Build Status](https://travis-ci.org/composer-synchronizer/composer-synchronizer.svg?branch=master)](https://travis-ci.org/composer-synchronizer/composer-synchronizer)

- Composer synchronizer is a composer plugin that synchronizes files from vendor and simplifies package installation and configuration.
- It allows you to configure any package for any framework and synchronize or configure it automatically during the package installation
- **Note** Actually synchronizer contains only one synchronizer for [Nette Framework](https://nette.org/en/).
**HOWEVER** synchronizer is flexible and easy to extend so please if there is no synchronizer for your
favorite framework create it according to the [docs](https://github.com/composer-synchronizer/composer-synchronizer/blob/master/docs/Creating%20Synchronizer.md).

## Ready for projects (aliases)
- [Nette Framework](https://nette.org/en/) (`nette`, `nette2`)


## Installation
Install composer plugin
````
composer require machy8/composer-synchronizer
````

and add the type of your project into the `composer-synchronizer` section
into the `extra section` in your project `composer.json` file.
````JSON
....
"extra": {
    "composer-synchronizer": {
        "project-type": "SYNCHRONIZER ALIAS GOES HERE"
    }
}
....
````

Next steps are based on which framework you are using.

## Configuration for specific project types
**Common**

Configuration sections:
- resources
- gitignore

Available paths placeholders:
- projectDir => ''

**Nette Framework**

- Load `composer-synchronizer.neon` in your bootstrap.php before local configuration.

````PHP
$configurator->addConfig(__DIR__ . '/config/composer-synchronizer.neon');
````
Configuration sections
- includes

Available paths placeholders:
- 'appDir' => 'app'
- configDir => 'app/config'
- logDir => 'log'
- tempDir => 'temp'
- wwwDir => 'www'

## Installing packages
Packages are synchronized during installation and `only those packages that contains configuration for composer-synchronizer with the type of your project are synchronized`. Eventually composer synchronizer will look to the packages repository, if the package is configured here (THIS IS IN TODO).
