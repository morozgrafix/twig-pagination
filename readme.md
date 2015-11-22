[![Build Status](https://travis-ci.org/bertramakers/twig-pagination.svg?branch=master)](https://travis-ci.org/bertramakers/twig-pagination)
[![Coverage Status](https://coveralls.io/repos/bertramakers/twig-pagination/badge.svg?branch=master&service=github)](https://coveralls.io/github/bertramakers/twig-pagination?branch=master)

# Introduction

This pagination extension helps you determine what pagination links to on a 
give page, depending on the total amount of pages and some configurable logic.

It will not generate any HTML for you, but simply provide you with an array of
page numbers to display as links and if necessary where any chunks of omitted
pages are.

For example:

* **Total pages**: 50
* **Current page**: 30
* **Omitted pages indicator**: -1
* **Behaviour**: `FixedLength`, 11
* **Result**: `[1, 2, 3, -1, 29, 30, 31, -1, 48, 49, 50];`

It is then up to you to display this data using a Twig template. 
(See [Usage in Twig](#usage-in-twig))

It's important to note that this extension **will not help you help you load or
filter the data you want to paginate**. It's simply meant to help you decide 
what links to other pages you should display on a given page.

# Installation

Using [Composer](http://getcomposer.org):

```bash
composer require bertramakers/twig-pagination
```

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

// Configure two fixed length pagination functions, a small one and a wide one.
// The names are completely up to you to choose.
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
            // Configure two fixed length pagination functions, a small one and
            // a wide one. The names are completely up to you to choose.
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

**Important!** The current page value uses regular numbering starting from 1,
so if you use 0-based numbering make sure to add 1 to the current page value.

## Examples:

**Standard usage**:

```twig
{% set totalPages = 20 %}
{% set currentPage = 8 %}
```

```twig
{% set paginationData = wide_pagination(totalPages, currentPage) %}
```

**Custom indicator for omitted pages**:

```twig
{% set paginationData = small_pagination(totalPages, currentPage, '...') %}
```

**Looping over the data**:

This template is just a simple example you could start from, or you could write
one from scratch depending on your requirements.

```twig
<ul class="pagination">

{% if currentPage > 1 %}
    <li><a href="...">Previous</a></li>
{% else %}
    <li class="disabled">Previous</li>
{% endif %}

{% for paginationItem in paginationData %}
    {% if paginationItem == -1 %} {# The value you chose for omitted chunks #}
        <li class="disabled">...</li>
    {% elseif paginationItem == currentPage %}
        <li class="active"><a href="...">{{ paginationItem }}</a></li>
    {% else %}
        <li><a href="...">{{ paginationItem }}</a></li>
    {% endif %}
{% endfor %}

{% if currentPage < totalPages %}
    <li><a href="...">Next</a></li>
{% else %}
    <li class="disabled">Next</li>
{% endif %}

</ul>
```

# FAQ

### Why not just put everything inside a Twig template?

Using this extension, you can keep the logic for determining which page links
to display and the actual markup for the list of links separated. This makes it
possible to re-use the template across your application, even in places where
the logic for which links to display is different. It also makes it a lot 
easier to test the actual display logic.

You could even use the behaviour classes outside of Twig, and use them to
render pagination links in something else than HTML.

### Can you add a zero-based numbering option?

This extension's purpose is to decide what page numbers should be shown to your
end users, so it always starts counting from 1.

However if you want to use zero-based numbering for the page parameter in your 
URLs, you can easily do so by subtracting 1 from each page number you get back.

So you would still print "1" for the first page, but subtract 1 and use "0" as 
the page parameter in the URL to the first page.

### Where are the previous / next links?

You should include those in your template, as they have no effect on what page
numbers to display and which to exclude. See the template example above for an
example on how to add previous and next links.

### How do I know which page link should be disabled for the current page?

You should simply loop over the page numbers you get back, and if a given page
number equals the current page value you used when generating the pagination
data, that's the current page that you could disable if you wanted to.

# Contributing

Run a full test (lint check, coding standards check and unit tests) with 
`./vendor/bin/phing test`.

Check code coverage with `./vendor/bin/phpunit --coverage-html build` and open `./build/index.html`.

In order to automatically run a full test when committing to git, install the
included git hooks hook with `./vendor/bin/phing githooks`.
