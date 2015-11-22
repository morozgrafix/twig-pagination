[![Build Status](https://travis-ci.org/bertramakers/twig-pagination.svg?branch=master)](https://travis-ci.org/bertramakers/twig-pagination)
[![Coverage Status](https://coveralls.io/repos/bertramakers/twig-pagination/badge.svg?branch=master&service=github)](https://coveralls.io/github/bertramakers/twig-pagination?branch=master)

# Introduction

This pagination extension provides a way to determine what pagination links to
display depending on the total amount of pages, the current page, and whatever
behaviour you choose.

It will not generate any HTML for you, but simply provide you with an array of
page numbers to display as links and optionally where any chunks of omitted
pages would be.

For example:

* **Total pages**: 50
* **Current page**: 30
* **Omitted pages indicator**: -1
* **Behaviour**: `FixedLength`, 11
* **Result**: `[1, 2, 3, -1, 29, 30, 31, -1, 48, 49, 50];`

It is then up to you to display this data using a Twig template.

# Setup

When setting up the PaginationExtension, you can configure as many pagination
functions as you like. 

Each function can have a custom name, and the behaviour that you choose. The 
only provided behaviour at this point is `FixedLength`, but you can easily add 
your own by implementing `PaginationBehaviourInterface`.

## Plain PHP

Documentation: [http://twig.sensiolabs.org/doc/api.html](http://twig.sensiolabs.org/doc/api.html)

```php
use DevotedCode\Twig\Pagination\PaginationExtension;
use DevotedCode\Twig\Pagination\Behaviour\FixedLength;
use \Twig_Environment;
use \Twig_Loader_Filesystem;

$loader = new Twig_Loader_Filesystem('/path/to/templates');
$twig = new Twig_Environment($loader);

$paginationExtension = (new PaginationExtension())
    ->withFunction('small', new FixedLength(7))
    ->withFunction('wide', new FixedLength(21));
    
$twig->addExtension($paginationExtension);
```

## Silex

Documentation: [http://silex.sensiolabs.org/doc/providers/twig.html](http://silex.sensiolabs.org/doc/providers/twig.html)

```php
use DevotedCode\Twig\Pagination\PaginationExtension;
use DevotedCode\Twig\Pagination\Behaviour\FixedLength;
use Silex\Provider\TwigServiceProvider;

$app->register(new TwigServiceProvider(), array(
    'twig.path' => '/path/to/templates',
));

$app['twig'] = $app->share(
    $app->extend(
        'twig', 
        function($twig, $app) {
            $paginationExtension = (new PaginationExtension())
                ->withFunction('small', new FixedLength(7))
                ->withFunction('wide', new FixedLength(21));
        
            $twig->addExtension($paginationExtension)
        
            return $twig;
        }
    )
);
```

# Usage in Twig

In the setup section above we set up two pagination functions: `small` and `wide`.
These functions are now available in Twig as `small_pagination` and `wide_pagination`.

**Every pagination function takes the following arguments**:
* Total pages
* Current page
* Omitted pages indicator (defaults to `-1`)

**Examples**:
```twig
{% set pageLinks = wide_pagination(20, 7) %}
```

```twig
{% set pageLinks = small_pagination(20, 7, '...') %}
```
