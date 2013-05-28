Badge-Poser
===========

Use shields for you packagist.org repository, that shows how many times your project has been downloaded from Packagist.org
or its latest stable version.

is still in ALPHA and don't use it (the protocol and the domain will change).

[![Downloads](http://poser.pagodabox.com/symfony/symfony/d/total.png)](https://packagist.org/packages/pugx/badge-poser)
[![Latest Version](version.png)](https://packagist.org/packages/pugx/badge-poser)
[![Build Status](https://secure.travis-ci.org/PUGX/badge-poser.png)](http://travis-ci.org/PUGX/badge-poser)

(Only the `version` badge is not real)

## Usage

Total downloads [![Downloads](http://poser.pagodabox.com/symfony/symfony/d/total.png)](https://packagist.org/packages/symfony/symfony)
```md
[![Total Downloads](http://poser.pagodabox.com/symfony/symfony/d/total.png)](https://packagist.org/packages/symfony/symfony)
```

Monthly downloads [![Downloads](http://poser.pagodabox.com/symfony/symfony/d/monthly.png)](https://packagist.org/packages/symfony/symfony)
```md
[![Monthly Downloads](http://poser.pagodabox.com/symfony/symfony/d/monthly.png)](https://packagist.org/packages/symfony/symfony)
```

Daily downloads  [![Downloads](http://poser.pagodabox.com/symfony/symfony/d/daily.png)](https://packagist.org/packages/symfony/symfony)
```md
[![Daily Downloads](http://poser.pagodabox.com/symfony/symfony/d/daily.png)](https://packagist.org/packages/symfony/symfony)
```

Latest Version (to do)
```md
[![Latest Version](http://poser.pagodabox.com/symfony/symfony/version.png](https://packagist.org/packages/symfony/symfony)
```
## Why a composer badge?

Not only because all the other languages already had it, but having the latest stable release in the readme could save developer time.


## Todo

0. Documentation and homepage.

1. ~~badge for download monthly and daily~~

2. badge for current stable version.

3. gather feedback about destroy this project or find a domain

4. ~~some love to the image templates~~

5. cache


## Contribution

Active contribution and patches are very welcome.
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
php composer.phar install
```

- This will give you proper results:

``` bash
phpunit
```

