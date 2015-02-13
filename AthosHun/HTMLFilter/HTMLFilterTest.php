<?php

namespace AthosHun\HTMLFilter;

class HTMLFilterTest extends \PHPUnit_Framework_TestCase
{
    private $filter;
    private $filter_config;
    private $mb_substitute_character;

    public function setUp()
    {
        $this->filter_config = new Configuration();
        $this->filter = new HTMLFilter();
        $this->mbstring_substitute_character = ini_get(
            "mbstring.substitute_character"
        );
        ini_set("mbstring.substitute_character", "none");
    }

    public function tearDown()
    {
        ini_set(
            "mbstring.substitute_character",
            $this->mbstring_substitute_character
        );
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

    public function testWorksWithCjkCharacters()
    {
        $zh = "格萊美紅毯大戰誰是贏家";
        $jp = "日本語です。";
        $ko = "빛나리의 타잔 주제가";

        $this->assertFilteredHTML(
            "$zh$jp$ko",
            "<p>$zh</p><p>$jp</p><p>$ko</p>"
        );
    }

    public function testIgnoresInvalidUtf8()
    {
        $this->filter_config->allowTag("p");

        $this->assertFilteredHTML(
            "prefix <p> onclick=alert(42)&gt;infix\n\nsuffix</p>",
            "prefix <p\xe6> onclick=alert(42)>infix\xe6\xff\n\nsuffix"
        );
    }
}
