Badge-Poser
===========

Use shields for your packagist.org repository that shows how many times your project has been downloaded from packagist.org
or its latest stable version.

[![Latest Stable Version](https://poser.pugx.org/pugx/badge-poser/version.svg)](https://packagist.org/packages/pugx/badge-poser)
[![Latest Unstable Version](https://poser.pugx.org/pugx/badge-poser/v/unstable.svg)](https://packagist.org/packages/pugx/badge-poser)
[![Build Status](https://secure.travis-ci.org/PUGX/badge-poser.svg)](http://travis-ci.org/PUGX/badge-poser)
[![License](https://poser.pugx.org/pugx/badge-poser/license.svg)](https://packagist.org/packages/pugx/badge-poser)
[![Downloads](https://poser.pugx.org/pugx/badge-poser/d/total.svg)](https://packagist.org/packages/pugx/badge-poser)

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

- First, fork or clone the repository:

```
git clone git://github.com/PUGX/badge-poser.git
cd badge-poser
```

- Install vendors:

``` bash
php composer.phar self-update
php composer.phar install
```

- Run phpunit:

``` bash
./bin/phpunit
```

- Production

1. For Production purposes you need Redis.
2. Update the contributors `bin/extract_contributors.py  > src/PUGX/BadgeBundle/Resources/views/Page/contributors.html.twig`

## Donation
Badge Poser serves billion of images per month. We really dream to move all of our code on Amazon AWS but is up to you.

If you use Badge Poser and you like it, please consider to donate. **Thank you!**

<a href='https://pledgie.com/campaigns/27612'><img alt='Click here to lend your support to: Badge Poser needs your help and make a donation at pledgie.com !' src='https://pledgie.com/campaigns/27612.png?skin_name=chrome' border='0' ></a>

## This project is HHVM approved :)

Try:

``` bash
hhvm ./bin/phpunit
```

## Extract contributors

In order to update the contributors section:

1. install it running `pip install pygithub3 && pip install Jinja2`
2. `python bin/extract_contributors.py  > src/PUGX/BadgeBundle/Resources/views/Page/contributors.html.twig`