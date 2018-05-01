# Configuring project
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
            "tempDir" => "tmp"
       }
    }
}
````