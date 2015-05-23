"Clean" command for Composer
==================

With Composer being used often to build a package to deploy, it makes sense that there should be functionality to strip out certain things (like README or a `tests/` directory) that don't need to be deployed. With this addition you'll be given a command to "clean" the packages in your repositories and make it ready for deploy.

## Usage

To use the package, you'll need two things. First, on your application you need to make the "clean" command available. First, install the latest version of the `ComposerClean` package:

```
composer require enygma/composerclean:dev-master
```

Then you update your `composer.json` file to make it a command:

```
{
	"scripts": {
		"clean": "ComposerClean\\Clean::exec"
	}
}
```

Then you can fire off the cleaning process with a call to:

```
composer.phar clean
```

This will go through your installed repositories and remove the items marked in the project's "clean" list. To define this list in your own project, you put the list of directories or files in the `composer.json` configuration in the `extras` section:

```
{
	"extras": {
		"clean": [
			"tests/",
			"README.md",
			"LICENSE"
		]
	}
}
```

The command will do its best to remove the files and directories (recursing down) you've specified. The paths start from the root of the project so `tests/` would relate to something like `vendor/enygma/composerclean/tests` and everything under it.
