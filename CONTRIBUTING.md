# Contributing

Active contribution and patches are very welcome.
See the [github issues](https://github.com/PUGX/badge-poser/issues?state=open).
There are some tagged as [easy-pick](https://github.com/PUGX/badge-poser/issues?labels=easy-pick&page=1&state=open).
To keep things in shape we have a bunch of unit tests. If you're submitting pull requests please
make sure that they are still passing and if you add functionality please
take a look at the coverage as well, it should be pretty high. :)
Last, but not least, respect coding standards
(we use [php-cs-fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) for that);

## First, fork or clone the repository

```bash
git clone git://github.com/PUGX/badge-poser.git
cd badge-poser
```

## Configure your `.env.local` file

```bash
$ cp .env .env.local
```

Follow [this link](https://github.com/settings/tokens) to generate your GitHub "Personal access token" and put them
 in `.env.local` file as the value of `GITHUB_USERNAME`.

Follow [this link](https://bitbucket.org/account/settings/app-passwords/new) to generate your Bitbucket "App Password" and put them
 in `.env.local` file as the value of `BITBUCKET_SECRET`, add to `BITBUCKET_TOKEN` your username and into `BITBUCKET_AUTH_METHOD` value `http_password`

Follow [this link](https://circleci.com/account/api) to generate your CircleCI "Personal API Token" and put them
 in `.env.local` file as the value of `CIRCLE_CI_TOKEN`.

Make the same for the `.env.test.local` to run the test suite.

## Manage App with Docker Compose

### Show the help

```bash
$ make help
Use: make <target>

GENERIC
  init                  initialize app (For the first initialize of the app)
  run                   run app
  start                 start docker containers
  stop                  stop docker containers
  dc_build_prod         rebuild docker compose containers to production environment
  purge                 cleaning
  status                docker containers status

DEV
  install               install php and node dependencies
  build_dev             build assets for dev environment
  build_watch           build assets and watch
  phpunit               run suite of tests
  php_cs_fixer          run php-cs-fixer
  phpstan               run phpstan
  analyse               run php-cs-fixer and phpstan

PROD
  install_prod          install PHP and node dependencies for production environment
  build_prod            build assets for production environment
```

### For the first initialize of the app
```bash
$ make init
```
Add in `/etc/hosts`
```console
127.0.0.1 poser.local
```
and now you can see the app on [http://poser.local:8001](http://poser.local:8001)

### Otherwise, for the other days
```bash
$ make run
```

### Go away
```bash
$ make stop
```

### Build assets and remaining on watch
```bash
$ make build_watch
```

### Run phpunit
``` bash
$ make phpunit
```

### Analyzes code (php-cs-fixer and phpstan)
``` bash
$ make analyse
```

### For the others allowed action execute
``` bash
$ make help
```

## ENJOY
