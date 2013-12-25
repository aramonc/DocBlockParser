<?php

namespace Ils;

class AnnotationParser
{
    const DOCBLOCK_PATTERN = '%^\/\*\*.*\*\/$%s';
    const ANNOTATION_PATTERN = '/^(?:\*\s*\@)(?P<tag>[a-zA-Z]+)\s(?P<value>.+)$/m';
    const DESCRIPTION_PATTERN = '/^(?:\*\s*)(?P<description>[^@\/\s\*].+)$/m';

    /**
     * Verify if the string has a docBlock in it.
     *
     * @param string $block
     * @return bool
     */
    public function hasDocBlock($block)
    {
        if(!is_string($block) || $block == '') {
            return false;
        }

        return (bool) preg_match(self::DOCBLOCK_PATTERN, $block);
    }

    /**
     * Extract annotations from a docBlock
     * @param string $text
     * @return array
     */
    public function getAnnotations($text)
    {
        $annotations = array();

        if(!is_string($text) || !$this->hasDocBlock($text)) {
            return $annotations;
        }

        preg_match_all(self::ANNOTATION_PATTERN, $text, $matches);

        foreach($matches['tag'] as $index => $tag) {
            $annotations[$tag] = $matches['value'][$index];
        }

        return $annotations;
    }

    /**
     * Retrieves any text that is not a * follow by a space or an annotation
     * @param string $text
     * @return string
     */
    public function getDescription($text)
    {
        $description = '';

        if(!is_string($text) || !$this->hasDocBlock($text)) {
            return $description;
        }

        preg_match_all(self::DESCRIPTION_PATTERN, $text, $matches);

        return implode(" ", $matches['description']);
    }
} 