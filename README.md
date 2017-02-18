# Introduction

This installer enables Preacher to support plugins using Symfony Bundles and by
introducing the `preacher-plugin` package type to Composer.

You can enable your Symfony Bundle inside Preacher by setting the following in
your package:

```json
{
  "type": "preacher-plugin",
  "require": {
    "zero-config/preacher-installer": "^1.0"
  },
  "extra": {
    "class": "\\My\\Preacher\\Plugin\\AwesomeBundle"
  }
}
```
