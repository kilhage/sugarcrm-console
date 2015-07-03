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
git clone git@gitlab.dri-nordic.com:dri-nordic/sugarcrm-console.git /usr/local/share/sugarcrm-console
cd /usr/local/share/sugarcrm-console
```

#### Install dependencies

```sh
curl -sS https://getcomposer.org/installer | php
php composer.phar install
```

#### Install binary globally

##### Manually
```sh
chmod +x bin/sugarcrm
ln -s /usr/local/share/sugarcrm-console/bin/sugarcrm /usr/local/bin/sugarcrm
```

### Install inside project

#### In project managed by composer

```sh
    .....
    "repositories": [
        ....
        {
            "type": "composer",
            "url":  "https://packages.dricrm.com/"
        }
        .....
    ],
    ......
    "require": {
        .....
        "dri-nordic/sugarcrm-console": "dev-master",
        .....
    }
    ....
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