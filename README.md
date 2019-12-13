A Supervisor dashboard based on the Jaxon ajax library
======================================================

There are already several packages that allow to monitor multiple [Supervisor](http://supervisord.org) instances from a single dashboard.
However, all these packages are standalone applications, with all the constraints that this implies in terms of installation, configuration, authentication, etc.

This package allows to insert a dashboard for [Supervisor](http://supervisord.org) into an existing PHP application.
Thanks to the [Jaxon library](https://www.jaxon-php.org), it installs and runs in a page of the application, which can be loaded with an HTTP or an Ajax request.
All its operations are performed with Ajax requests.

Features
--------

- Show the processes on Supervisor servers with status and running time.
- Start, restart or stop a process on a server.
- Start, restart or stop all the processes on a server.
- Start or stop refresh timer.
- Trigger refresh.

Documentation
-------------

0. Install the jaxon library so it bootstraps from a config file and handles ajax requests. Here's the [documentation](https://www.jaxon-php.org/docs/v3x/advanced/bootstrap.html).

1. Install this package with Composer. If a [Jaxon plugin](https://www.jaxon-php.org/docs/v3x/plugins/frameworks.html) exists for your framework, you can also install it. It will automate the previous step.

2. Declare the package and the Supervisor servers in the `app` section of the [Jaxon configuration file](https://www.jaxon-php.org/docs/v3x/advanced/bootstrap.html).

```php
    'app' => [
        // Other config options
        // ...
        'packages' => [
            Lagdo\Supervisor\Package::class => [
                'servers' => [
                    'first_server' => [
                        'url' => 'http://192.168.1.10',
                        'port' => '9001',
                    ],
                    'second_server' => [
                        'url' => 'http://192.168.1.11',
                        'port' => '9001',
                    ],
                ],
            ],
        ],
    ],
```

3. Insert the CSS and javascript codes in the HTML pages of your application using calls to `jaxon()->getCss()` and `jaxon()->getScript(true)`.

4. In the page that displays the dashboard, insert its HTML code with a call to `jaxon()->package(\Lagdo\Supervisor\Package::class)->getHtml()`. Two cases are then possible.

    - If the dashboard is displayed on a dedicated page, make a call to `jaxon()->package(\Lagdo\Supervisor\Package::class)->ready()` when loading the page.

    - If the dashboard is loaded with an Ajax request in a page already displayed, execute the javascript code returned the call to `jaxon()->package(\Lagdo\Supervisor\Package::class)->getReadyScript()` when loading the page.

Notes
-----

The HTML code of the package uses the [Bootstrap](https://getbootstrap.com/) CSS framework, qui which must also be included in the page.
It is entirely contained in a `<div class="col-md-12">` tag.

Support for other frameworks will be added in future releases.

Contribute
----------

- Issue Tracker: github.com/lagdo/jaxon-supervisor/issues
- Source Code: github.com/lagdo/jaxon-supervisor

License
-------

The project is licensed under the BSD license.
