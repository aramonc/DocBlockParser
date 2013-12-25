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
*
* The description
* Another description
*
* @category test
* @tags test one two three
*
*/
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
            "tags" => "test one two three"
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

        $descriptionExpected = "The description Another description";

        $result = $this->parser->getDescription($this->docBlock);

        $this->assertEquals($descriptionExpected, $result, "Using the example docBlock, there should be a description string");

        $descriptionExpected = null;
        $result = null;
    }
} 