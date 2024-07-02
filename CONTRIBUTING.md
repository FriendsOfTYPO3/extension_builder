### Contributing

Feel free to create new pull-requests on GitHub.

#### Devbox

*Note: The devbox is not properly working at the moment.*

If you don't have a setup already, where you can do development, bugfixing etc. for the extension_builder, don't worry.

We have included a [ddev](https://www.ddev.com) devbox to help the development.

##### Prerequisites

* [DDEV](https://www.ddev.com)
* Docker

##### How to use the devbox?

```shell script
$ git clone git@github.com:FriendsOfTYPO3/extension_builder.git
$ cd .devbox
$ ddev start
```

Username/password: `admin`/`password`

And start working.

**INFO**
xdebug is disabled as default, to speed up the devbox when xdebug isn't needed.

This can be activated with `ddev xdebug on`.
