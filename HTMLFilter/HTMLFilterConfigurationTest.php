<?php

namespace HTMLFilter;

class HTMLFilterConfigurationTest extends \PHPUnit_Framework_TestCase
{
    private $filter_config;

    public function setUp()
    {
        $this->filter_config = new HTMLFilterConfiguration();
    }

    public function testTagsCanBeAllowedOrDisallowed()
    {
        $this->filter_config->allowTag("img");

        $this->assertTagAllowed("img");

        $this->assertTagDisallowed("script");
    }

    private function assertTagAllowed($tag_name)
    {
        $this->assertTrue($this->filter_config->isAllowedTag($tag_name));
    }

    private function assertTagDisallowed($tag_name)
    {
        $this->assertFalse($this->filter_config->isAllowedTag($tag_name));
    }

    public function testAttributesCanBeRestrictedWithRegularExpression()
    {
        $this->filter_config->allowAttribute("img", "src", "/^hello\$/") ;

        $this->assertAttributeAllowed("img", "src", "hello");

        $this->assertAttributeDisallowed("img", "src", "world");
        $this->assertAttributeDisallowed("img", "alt", "hello");
        $this->assertAttributeDisallowed("script", "src", "hello");
    }

    private function assertAttributeAllowed($tag_name, $attr_name, $attr_value)
    {
        $this->assertTrue(
            $this->filter_config->isAllowedAttribute(
                $tag_name,
                $attr_name,
                $attr_value
            )
        );
    }

    private function assertAttributeDisallowed(
        $tag_name,
        $attr_name,
        $attr_value
    ) {
        $this->assertFalse(
            $this->filter_config->isAllowedAttribute(
                $tag_name,
                $attr_name,
                $attr_value
            )
        );
    }

    public function testAllowedAttributeImpliesAllowedTag()
    {
        $this->filter_config->allowAttribute("img", "src") ;

        $this->assertTagAllowed("img");
    }

    public function testAllowingAnAlreadyAllowedTagKeepsAttributeRestrictions()
    {
        $this->filter_config->allowAttribute("img", "src", "/^hello\$/")
                            ->allowTag("img");

        $this->assertAttributeAllowed("img", "src", "hello");

        $this->assertAttributeDisallowed("img", "src", "world");
    }

    public function testMultipleTagsCanBeAllowed()
    {
        $this->filter_config->allowTag("pre")
                            ->allowTag("blockquote")
                            ->allowTag("strong");

        $this->assertTagAllowed("pre");
        $this->assertTagAllowed("blockquote");
        $this->assertTagAllowed("strong");

        $this->assertTagDisallowed("table");
    }

    public function testMultipleAttributesCanBeAllowed()
    {
        $this->filter_config->allowAttribute("a", "href", "/^hello\$/")
                            ->allowAttribute("a", "title", "/^lorem\$/");

        $this->assertAttributeAllowed("a", "href", "hello");
        $this->assertAttributeAllowed("a", "title", "lorem");

        $this->assertAttributeDisallowed("a", "title", "hello");
        $this->assertAttributeDisallowed("a", "href", "lorem");
    }
}
