# Composer synchronizer

## Note
Actually synchronizer contains only one synchronizer for [Nette Framework](https://nette.org/en/).
**HOWEVER** synchronizer is flexible and easy to extend so please if there is no synchronizer for your
favorite framework create it according to the docs.

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

## Configuration for specific types
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
