# Composer Synchronizer

[![Build Status](https://travis-ci.org/composer-synchronizer/composer-synchronizer.svg?branch=master)](https://travis-ci.org/composer-synchronizer/composer-synchronizer)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat)](https://github.com/phpstan/phpstan)

- Composer Synchronizer is a composer plugin that synchronizes files from vendor and simplifies package installation and configuration.
- It allows you to configure any package for any framework and synchronize or configure it automatically during the package installation.

## Docs
- [Configuring a package](https://github.com/composer-synchronizer/composer-synchronizer/blob/master/docs/Configuring%20a%20package.md)
- [Configuring a project](https://github.com/composer-synchronizer/composer-synchronizer/blob/master/docs/Configuring%20a%20project.md)
- [Creating a synchronizer](https://github.com/composer-synchronizer/composer-synchronizer/blob/master/docs/Creating%20a%20synchronizer.md)

## Usage - 3 steps (example with Nette framework) ##
- Install the Synchronizer plugin.

````
composer require composer-synchronizer/composer-synchronizer
````

- Add composer synchronizer configuration into your project composer.json file.
````
"extra": {
    "composer-synchronizer": {
        "project-type": "nette2"
    }
}
````

- Install package that contains composer synchronizer configuration for `nette2` or has the configuration in the [Github repository](https://github.com/composer-synchronizer/packages).
````
composer require machy8/webloader
````

- Composer synchronizer will create a webtemp directory, file for loading extensions files and it will copy
the webloader extension file into the configuration directory.
- To make it all works, just load the configuration file `composer-synchronizer.neon` in your bootstrap.php file.

## Using other framework? ##
- Actually there is only one synchronizer and that for [Nette Framework](https://nette.org/en/) (others comming soon).
- Is the synchronizer for your framework missing? Send a pull request. It is easy to create a custom synchronizer. Just take
a look at the [docs](https://github.com/composer-synchronizer/composer-synchronizer/blob/master/docs/Creating%20a%20synchronizer.md).
