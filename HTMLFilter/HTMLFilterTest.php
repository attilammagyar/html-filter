<?php

namespace HTMLFilter;

class HTMLFilterTest extends \PHPUnit_Framework_TestCase
{
    private $filter;
    private $filter_config;

    public function setUp()
    {
        $this->filter_config = new Configuration();
        $this->filter = new HTMLFilter();
    }

    public function testPlainTextWithoutAnyHtmlRemainsUnchanged()
    {
        $this->assertFilteredHTML("", "");
        $this->assertFilteredHTML("hello", "hello");
    }

    private function assertFilteredHTML($expected_html, $html)
    {
        $this->assertSame(
            $expected_html,
            $this->filter->filter($this->filter_config, $html)
        );
    }

    public function testDisallowedTagsAreRemoved()
    {
        $this->assertFilteredHTML("lorem ipsum", "lorem <a>ipsum</a>");
    }

    public function testAllowedTagsAreKept()
    {
        $this->filter_config->allowTag("a");
        $html = "lorem <a>ipsum</a>";

        $this->assertFilteredHTML($html, $html);
    }

    public function testDisallowedAttributesAreRemoved()
    {
        $this->filter_config->allowTag("a");

        $this->assertFilteredHTML(
            "lorem <a>ipsum</a>",
            "lorem <a href=\"hello\">ipsum</a>"
        );
    }

    public function testAllowedAttributesAreKept()
    {
        $this->filter_config->allowTag("a")
                            ->allowAttribute("a", "href");

        $html = "lorem <a href=\"hello\">ipsum</a>";
        $this->assertFilteredHTML($html, $html);
    }

    public function testAllowedAttributesNotMatchingARegexpAreRemoved()
    {
        $this->filter_config->allowAttribute("a", "href", "/^hello\$/");

        $this->assertFilteredHTML(
            "lorem <a>ipsum</a>",
            "lorem <a href=\"world\">ipsum</a>"
        );
    }

    public function testHTMLEntitiesAreLeftUnchanged()
    {
        $this->filter_config->allowAttribute("a", "href");

        $quoted_special_chars_attr = "&lt;&quot;&amp;&gt;'";
        $quoted_special_chars_text = "&lt;\"&amp;&gt;'";
        $quoted_html = "<a href=\"$quoted_special_chars_attr\">"
                        . $quoted_special_chars_text
                     . "</a>";

        $this->assertFilteredHTML($quoted_html, $quoted_html);
    }

    public function testNodesAreCopiedRecursively()
    {
        $this->filter_config->allowTag("p")
                            ->allowTag("b")
                            ->allowAttribute("a", "href", "/^hello\$/");

        $this->assertFilteredHTML(
            "Lorem <p><b><a>ipsum</a> dolor sit</b> amet</p>",
            "Lorem <p><b><a>ipsum</a> <em>dolor</em> <em>sit</em></b> amet</p>"
        );
    }

    public function testHtmlCommentsArePreserved()
    {
        $html = "Lorem <!-- <Hello> --> Ipsum";

        $this->assertFilteredHTML($html, $html);
    }

    public function testInvalidMarkupIsIgnored()
    {
        $this->assertFilteredHTML(
            "hello world",
            "<nosuchtag>hello world</x></nosuchtag>"
        );
    }
}
