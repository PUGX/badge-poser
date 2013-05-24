Badge-Poser
===========

Use shields for you packagist.org repository, that shows many times your project has been downloaded from Packagist.org
or it’s latest stable version.

is still in ALPHA and if you use it the domain will probably change.



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

## Todo

0. Documentation

1. badge for download monthly and daily

2. badge for current last version

3. gather feedback about destroy this project or find a domain

4. some love to the image templates

5. cache

6. test-issue

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
php composer.phar install --dev
```

- This will give you proper results:

``` bash
phpunit
```

