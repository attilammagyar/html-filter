HTMLFilter
==========

Remove tags or attributes based on a whitelist from a snippet of somewhat
well-formatted HTML text using PHP's DOM library.

Example:

```php
<?php

$config = new HTMLFilter\Configuration();
$config->allowTag("p")
       ->allowAttribute("a", "title")
       ->allowAttribute("a", "href", "|^https?://.*\$|");

$filter = new HTMLFilter\HTMLFilter();

$html = <<<HTML
Lorem ipsum <em>dolor</em> sit amet
<p>
    Consectetur <a href="http://example.com" title="hey!">adipisicing</a>
    <a href="javascript:alert(42)" onclick="alert(42)">elit</a>.
</p>
HTML;

print $filter->filter($config, $html);
```

Output:

```html
Lorem ipsum dolor sit amet
<p>
    Consectetur <a href="http://example.com" title="hey!">adipisicing</a>
    <a>elit</a>.
</p>
```

Installation
------------

Installation is possible via [Composer][composer]. Create a file named
`composer.json` in your project directory with the following contents:

  [composer]: http://getcomposer.org/

    {
        "require": {
            "athoshun/html-filter": "1.0.*"
        }
    }

Then as a normal user, issue the following commands:

    $ curl http://getcomposer.org/installer | php
    $ php composer.phar install
