<p align="center">
<a href="https://travis-ci.org/ynievesdotnet/adminpanel-hooks"><img src="https://travis-ci.org/ynievesdotnet/adminpanel-hooks.svg?branch=master" alt="Build Status"></a>
<a href="https://styleci.io/repos/76975411/shield?style=flat"><img src="https://styleci.io/repos/76975411/shield?style=flat" alt="Build Status"></a>
<a href="https://packagist.org/packages/ynievesdotnet/adminpanel-hooks"><img src="https://poser.pugx.org/ynievesdotnet/adminpanel-hooks/downloads.svg?format=flat" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/ynievesdotnet/adminpanel-hooks"><img src="https://poser.pugx.org/ynievesdotnet/adminpanel-hooks/v/stable.svg?format=flat" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/ynievesdotnet/adminpanel-hooks"><img src="https://poser.pugx.org/ynievesdotnet/adminpanel-hooks/license.svg?format=flat" alt="License"></a>
</p>

Made with ❤️ by [Mark Topper](https://marktopper.com)
Maintained by [Yoinier Hernández](http://www.ynieves.net)

# AdminPanel Hooks

[Hooks](https://github.com/larapack/hooks) system integrated into [AdminPanel](https://github.com/ynievesdotnet/adminpanel).

# Installation

Install using composer:

```
composer require ynievesdotnet/adminpanel-hooks
```

Then add the service provider to the configuration (optional on Laravel 5.5+):

```php
'providers' => [
    YnievesDotNet\AdminPanelHooks\AdminPanelHooksServiceProvider::class,
],
```

In order for AdminPanel to automatically check for updates of hooks, add the following to your console kernel:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('hook:check')->sundays()->at('03:00');
}
```

That's it! You can now visit your AdminPanel admin panel and see a new menu item called `Hooks` have been added.
