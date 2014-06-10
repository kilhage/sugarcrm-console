sugarcrm-console
===============

About
---------------------
 * __Author:__ Emil Kilhage
 * __Date Created:__ 2014-03-24
 * __License:__ MIT

Idea
--------------------

 * To provide developers a full command line interface to develop SugarCRM
 * To simplify continious integration

Pre requirements
---------------------

Installation
---------------------

### Install as global console

#### Checkout
```sh
git clone https://github.com/kilhage/sugarcrm-console sugarcrm-console
cd sugarcrm-console
```

#### Install dependencies

```sh
curl -sS https://getcomposer.org/installer | php
php composer.phar install
```

#### Install binary globally

##### With rake
```sh
rake install:binary
```

##### Manually
```sh
ln -s bin/sugarcrm /usr/local/bin/sugarcrm
```

### Install inside project

#### In project managed by composer

```sh
    .....
    "repositories": [
        ....
        {
            "url": "git@github.com:kilhage/sugarcrm-console.git",
            "type": "git"
        }
        .....
    ],
    ......
    "require": {
        .....
        "kilhage/sugarcrm-console": "dev-master",
        .....
    }
    ....
```

#### As a submodule

```sh
git submodule add https://github.com/kilhage/sugarcrm-console sugarcrm-console
cd sugarcrm-console
```

##### Install dependencies

```sh
curl -sS https://getcomposer.org/installer | php
php composer.phar install
```

Usage
---------------------

### Commands

Extend
---------------------

Bugs
---------------------

Contribute
---------------------
