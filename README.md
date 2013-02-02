HTMLFilter
==========

Remove tags or attributes based on a whitelist from a snippet of
well-formatted HTML text using PHP's DOM library.

Example:

    $config = new HTMLFilterConfiguration();
    $config->allowTag("p")
           ->allowAttribute("a", "title")
           ->allowAttribute("a", "href", "|^https?://.*\$|");

    $filter = new HTMLFilter();

    $html = <<<HTML
    Lorem ipsum <em>dolor</em> sit amet
    <p>
        Consectetur <a href="http://example.com" title="hey!">adipiscing</a>
        <a href="javascript:alert(42)" onclick="alert(42)">elit</a>.
    </p>
    HTML;

    print $filter->filter($config, $html);

Output:

    Lorem ipsum dolor sit amet
    <p>
        Consectetur <a href="http://example.com" title="hey!">adipiscing</a>
        <a>elit</a>.
    </p>
