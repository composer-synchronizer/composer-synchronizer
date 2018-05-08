# Configuring a project
Configuring project for the synchronizer is pretty simple. Actually,
you can add `project-type` and `paths-placeholders` section.

**Project type (project-type)**

Depends on which project you use. If you use `Nette 2.0`, then the suitable
synchronizer is `nette2` or its alias `nette`.

````JSON
"extra": {
    "composer-synchronizer": {
        "project-type": "nette",
    }
}
````

**Paths placeholders (path-placeholders)**

In case you use Nette 2 with modified directory structure you can change the default
paths in path placeholders by adding this section.
For example if your temporary directory is not the `temp` but `tmp`
then you can change the `tempDir` placehorder path to `tmp`.

````JSON
"extra": {
    "composer-synchronizer": {
        "paths-placeholders": {
            "tempDir": "tmp"
       }
    }
}
````

# Disabling remote configurations
You can disable configurations downloading from [packages repository](https://github.com/composer-synchronizer/packages).
````JSON
"extra": {
    "composer-synchronizer": {
        "disable-remote-configurations": true
    }
}
````

# Keeping package unlocked (for development purposes)
If you develop a package configuration, you might want to keep the package unlocked
````JSON
"extra": {
    "composer-synchronizer": {
        "non-lockable-packages": [
            "some/package"
        ]
    }
}
````
