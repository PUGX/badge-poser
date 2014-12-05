Badge-Poser
===========

Use shields for you packagist.org repository, that shows how many times your project has been downloaded from Packagist.org
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

Not only because all the other languages already had it, but having the latest stable release in the readme could save time.

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

- Run phpunit:

``` bash
./bin/phpunit
```

- Production

For Production purpose you need Redis.

## Donation
Badge Poser serves billion of images per month. We really dream to move all our code on Amazon AWS but is up to you.

If you use Badge Poser and you like it, pleas consider to donate. **Thank you!**

<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top" class="centered-content">
	<input type="hidden" name="cmd" value="_s-xclick">
    <input type="hidden" name="hosted_button_id" value="ETT4JRJARLTSC">
    <input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
    <img alt="" border="0" src="https://www.paypalobjects.com/it_IT/i/scr/pixel.gif" width="1" height="1">
</form>

## This project is HHVM approved :)

Try:

``` bash
hhvm ./bin/phpunit
```

