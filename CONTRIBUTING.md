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

## Configure your `.env` file

```bash
$ cp .env.dist .env
```

Follow [this link](https://github.com/settings/tokens) for generate your GitHub "Personal access token" and put them
 in `.env` file as value of `GITHUB_USERNAME`.
 
Follow [this link](https://bitbucket.org/account/settings/app-passwords/new) for generate your Bitbucket "App Password" and put them
 in `.env` file as value of `BITBUCKET_SECRET`, add to `BITBUCKET_TOKEN` your username and into `BITBUCKET_AUTH_METHOD` value `http_password`

Follow [this link](https://circleci.com/account/api) for generate your CircleCI "Personal API Token" and put them
 in `.env` file as value of `CIRCLE_CI_TOKEN`.

## Manage App with docker-compose

### Start App
```bash
$ make run
```
Add in `/etc/hosts`
```console
127.0.0.1 poser.local
```
and now you can see the app on [http://poser.local:8001](http://poser.local:8001)

### Go away
```bash
$ make down
```

### Build assets and remaining in watch
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
