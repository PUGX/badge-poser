Badge-Poser
===========

Use shields for you packagist.org repository, that shows how many times your project has been downloaded from Packagist.org
or its latest stable version.

This project is still in ALPHA and you shouldn't use it yet (protocol and domain will change).

[![Downloads](https://poser.pugx.org/symfony/symfony/d/total.png)](https://packagist.org/packages/pugx/badge-poser)
[![Latest Stable Version](https://poser.pugx.org/symfony/symfony/version.png)](https://packagist.org/packages/pugx/badge-poser)
[![Latest Unstable Version](https://poser.pugx.org/symfony/symfony/v/unstable.png)](https://packagist.org/packages/pugx/badge-poser)
[![Build Status](https://secure.travis-ci.org/PUGX/badge-poser.png)](http://travis-ci.org/PUGX/badge-poser)

## How to create your own Badge
-  Go to the [Badge Poser](https://poser.pugx.org) website
-  Insert username/repository and click on `Show`
-  That's it!  Copy the Markdown into the README.md

## Examples

Total downloads [![Downloads](https://poser.pugx.org/symfony/symfony/d/total.png)](https://packagist.org/packages/symfony/symfony)
```md
[![Total Downloads](https://poser.pugx.org/symfony/symfony/d/total.png)](https://packagist.org/packages/symfony/symfony)
```

Monthly downloads [![Downloads](https://poser.pugx.org/symfony/symfony/d/monthly.png)](https://packagist.org/packages/symfony/symfony)
```md
[![Monthly Downloads](https://poser.pugx.org/symfony/symfony/d/monthly.png)](https://packagist.org/packages/symfony/symfony)
```

Daily downloads  [![Downloads](https://poser.pugx.org/symfony/symfony/d/daily.png)](https://packagist.org/packages/symfony/symfony)
```md
[![Daily Downloads](https://poser.pugx.org/symfony/symfony/d/daily.png)](https://packagist.org/packages/symfony/symfony)
```

Latest Stable Version [![Stable Version](https://poser.pugx.org/symfony/symfony/version.png)](https://packagist.org/packages/symfony/symfony)
```md
[![Latest Stable Version](https://poser.pugx.org/symfony/symfony/version.png)](https://packagist.org/packages/symfony/symfony)
```

Latest Unstable Version [![Stable Version](https://poser.pugx.org/symfony/symfony/v/unstable.png)](https://packagist.org/packages/symfony/symfony)
```md
[![Latest Unstable Version](https://poser.pugx.org/symfony/symfony/v/unstable.png)](https://packagist.org/packages/symfony/symfony)
```

## Why a composer badge?

Not only because all the other languages already had it, but having the latest stable release in the readme could save developer time.


## Contribution

Active contribution and patches are very welcome.
See the [github issues](https://github.com/PUGX/badge-poser/issues?state=open) there are some tagged as [easy-pick](https://github.com/PUGX/badge-poser/issues?labels=easy-pick&page=1&state=open).
To keep things in shape we have quite a bunch of unit tests. If you're submitting pull requests please
make sure that they are still passing and if you add functionality please
take a look at the coverage as well it should be pretty high :)

- First fork or clone the repository

```
git clone git://github.com/PUGX/badge-poser.git
cd badge-poser
```

- Install vendors:

``` bash
php composer.phar self-update
php composer.phar install
```

- This will give you proper results:

``` bash
./bin/phpunit -c app
```

#### This project was inspired by [pypy](https://pypip.in/)


