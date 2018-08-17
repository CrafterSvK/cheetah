# Cheetah
Small, modulable microframework. With minimal features to enable highly optimized webpages. Highly experimental. Not recommended for a production.

## Currently extremely experimental

## Features (todo)
* Simple router with parameters, `$_GET` & `$_POST` handling ✓ (still experimental)
* MVC enabled maybe other design patterns.
* Preload frontend friendly
* Plugins friendly
* Automated install

## Installation
* clone repository using `git clone`
* create `routes.json` with some routes
* create an `index.php` file and call
`require_once "vendor/autoload.php";
use cheetah\essentials\Router;
new Router('routes.json');`
* add some routes e.g. `{"index": {"route": "/", "view": "index.php"}}`
* create `view/` folder with an `index.php` file
That's a lot of creation isn't. I am working on automated installation process.

## Why?
Other framework didn't satisfy my simple mind. I had no idea what was I coding after 30 minutes of spent time.
I was actually learning Symfony for work. It's quite nice but too hard for me. I am not a new developer but not an experienced one.

## Rules
* PSR-1, PSR-4 (autoloading)
* `declare(strict_types=1);` and usage of type hinting
* Write documentation for every method and class (if it's not self explanatory)
* We are in experimental development. Any idea around technologies and architecture is welcome.
* Keep things simple. Features should be elegant and simple.
e.g. Login and Registration should be made by a plugin so it can have it's developers.
* Featured plugins should follow this rules but don't need to.

## Joining development
* You can join. If this framework will reach a good state it will receive separate github profile and a team
* If you want to feature a plugin just pull a request :) (Experimental dev only features can vary in a future)
