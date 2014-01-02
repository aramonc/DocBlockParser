<?php

namespace Ils;


class AnnotationParserTest extends \PHPUnit_Framework_TestCase
{
    public $parser;
    public $docBlock;
    
    public function setUp()
    {
        $this->parser = new AnnotationParser();
        $this->docBlock = <<<TST
/**
 * This is the description
 *
 * @tags test, another test, joy
 * @category test
 */

This is the content of the test.

#It contains some MarkDown#

* list 1
* list 2
* list 3


TST;
    }

    public function tearDown()
    {
        $this->parser = null;
        $this->docBlock = null;
    }
    
    public function testStringHasDocBlock()
    {

        // Test for incorrect inputs
        $this->assertFalse($this->parser->hasDocBlock(null), "Null inputs don't have docBlocks");

        // Test for empty strings
        $this->assertFalse($this->parser->hasDocBlock(''), "Empty strings don't have docBlocks");

        // Test for non docBlock strings
        $this->assertFalse($this->parser->hasDocBlock('Test'), "Strings without docBlocks have no docBlocks");

        // Test an empty docBlock
        $this->assertTrue($this->parser->hasDocBlock('/***/'), "A string with an empty docBlock has a docBlock");

        // Test the sample docBlock
        $this->assertTrue($this->parser->hasDocBlock($this->docBlock), "A string with full docBlock has a docBlock");

    }

    public function testRetrieveAnnotationsFromString()
    {
        $this->assertEquals(array(), $this->parser->getAnnotations(''), "An empty string should return an empty array");
        $this->assertEquals(array(), $this->parser->getAnnotations('/***/'), "An empty docBlock should return an empty array");

        $annotationExpected = array(
            "category" => "test",
            "tags" => "test, another test, joy"
        );

        $result = $this->parser->getAnnotations($this->docBlock);

        $this->assertArrayHasKey("tags", $result, "Using the example docBlock, there should be a tags array key");
        $this->assertEquals($annotationExpected, $result, "Using the example docBlock, there should be two keys with values");

        $annotationExpected = null;
        $result = null;
    }

    public function testRetrieveDescriptionFromString()
    {
        $this->assertEmpty($this->parser->getDescription(''), "An empty string should return an empty string");
        $this->assertEmpty($this->parser->getDescription('/***/'), "An empty docBlock should return an empty string");

        $descriptionExpected = "This is the description";

        $result = $this->parser->getDescription($this->docBlock);

        $this->assertEquals($descriptionExpected, $result, "Using the example docBlock, there should be a description string");

        $descriptionExpected = null;
        $result = null;
    }

    public function testExtractAnnotationsAndContent()
    {
        $this->assertEquals(array('meta' => '', 'content' => ''), $this->parser->extractDocBlock(''), "An empty string should return an empty array");
        $this->assertEquals(array('meta' => '/***/', 'content' => ''), $this->parser->extractDocBlock('/***/'), "An empty docBlock should return an empty array");

        $annotationExpected = array(
            "meta" => "/**\n * This is the description\n *\n * @tags test, another test, joy\n * @category test\n */",
            "content" => "This is the content of the test.\n\n#It contains some MarkDown#\n\n* list 1\n* list 2\n* list 3"
        );

        $result = $this->parser->extractDocBlock($this->docBlock);

        $this->assertArrayHasKey("meta", $result, "Using the example docBlock, there should be a meta array key");
        $this->assertEquals($annotationExpected, $result, "Using the example docBlock, there should be two keys with values");

        $annotationExpected = null;
        $result = null;
    }

    public function testParseFunctionIntegration()
    {
        $expected = array(
            'meta' => array(
                'tags' => "test, another test, joy",
                'category' => "test",
                'description' => "This is the description",
            ),
            'content' => "This is the content of the test.\n\n#It contains some MarkDown#\n\n* list 1\n* list 2\n* list 3",
        );

        $this->assertEquals($expected, $this->parser->parse($this->docBlock), "Using the example docBlock, the result should equal the expected array");
    }
} 