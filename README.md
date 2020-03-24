Badge-Poser
===========

Use shields for your packagist.org repository that shows how many times your project has been downloaded from packagist.org
or its latest stable version.

[![CircleCI](https://circleci.com/gh/PUGX/badge-poser/tree/release%2Fv3.0.0.svg?style=svg)](https://circleci.com/gh/PUGX/badge-poser/tree/release%2Fv3.0.0)
[![Latest Stable Version](https://poser.pugx.org/pugx/badge-poser/version.svg)](https://packagist.org/packages/pugx/badge-poser)
[![Latest Unstable Version](https://poser.pugx.org/pugx/badge-poser/v/unstable.svg)](https://packagist.org/packages/pugx/badge-poser)
[![License](https://poser.pugx.org/pugx/badge-poser/license.svg)](https://packagist.org/packages/pugx/badge-poser)
[![Downloads](https://poser.pugx.org/pugx/badge-poser/d/total.svg)](https://packagist.org/packages/pugx/badge-poser)
[![composer.lock available](https://poser.pugx.org/pugx/badge-poser/composerlock)](https://packagist.org/packages/pugx/badge-poser)

## How to create your own Badge
-  Go to the [Badge Poser](https://poser.pugx.org) website
-  Insert username/repository and click on `Show`
-  That's it!  Copy the Markdown into the README.md

## Why a composer badge?

Not only because all the other languages already have it, but having the latest stable release in the readme could save time.

## Contribution

Active contribution and patches are very welcome.
See the [github issues](https://github.com/PUGX/badge-poser/issues?state=open). There are some tagged as [easy-pick](https://github.com/PUGX/badge-poser/issues?labels=easy-pick&page=1&state=open).
To keep things in shape we have a bunch of unit tests. If you're submitting pull requests please
make sure that they are still passing and if you add functionality please
take a look at the coverage as well, it should be pretty high. :)
Last, but not least, respect coding standards
(we use [php-cs-fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) for that);

- First, fork or clone the repository:

```
git clone git://github.com/PUGX/badge-poser.git
cd badge-poser
```

- First Install App:

```bash
$ make init
```

now you can see the app on [http://localhost:8001](http://localhost:8001)

- Go away:

```bash
$ make down
```

- Return to work on App:

```bash
$ make run
```

- Run phpunit:

``` bash
$ make phpunit
```

- Analyzes code (php-cs-fixer and phpstan):

``` bash
$ make analyse
```

For the others allowed action execute:
``` bash
$ make help
```

A pre-commit git hook to run `phpunit`, `php-cs-fixer` and `phpstan` is automatically installed.
