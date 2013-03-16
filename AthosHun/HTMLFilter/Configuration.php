<?php

namespace AthosHun\HTMLFilter;

class Configuration
{
    private $allowed_tags_with_attributes;

    public function __construct()
    {
        $this->allowed_tags_with_attributes = array();
    }

    public function allowTag($tag_name)
    {
        if (!$this->isAllowedTag($tag_name)) {
            $this->allowed_tags_with_attributes[(string)$tag_name] = array();
        }

        return $this;
    }

    public function allowAttribute(
        $tag_name,
        $attribute_name,
        $attribute_regexp = "/.*/"
    ) {
        $this->allowTag($tag_name);

        $tag = (string)$tag_name;
        $attr = (string)$attribute_name;
        $this->allowed_tags_with_attributes[$tag][$attr] = $attribute_regexp;

        return $this;
    }

    public function isAllowedTag($tag_name)
    {
        return array_key_exists((string)$tag_name,
                                $this->allowed_tags_with_attributes);
    }

    public function isAllowedAttribute(
        $tag_name,
        $attribute_name,
        $attribute_value
    ) {
        if (!$this->isAllowedTag($tag_name)) {
            return false;
        }

        $tag = (string)$tag_name;
        $attr = (string)$attribute_name;

        $allowed_attributes = $this->allowed_tags_with_attributes[$tag];
        if (!array_key_exists($attr, $allowed_attributes)) {
            return false;
        }

        $restriction = $allowed_attributes[$attr];

        return 1 === preg_match($restriction, $attribute_value);
    }
}
