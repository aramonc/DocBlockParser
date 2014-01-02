<?php

namespace IlsTest;

use Ils\AnnotationParser;

class AnnotationParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AnnotationParser
     */
    public $parser;

    /**
     * @var $this->docBlock
     */
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

    /**
     * @test
     * @dataProvider getValidInputProvider
     */
    public function hasdocblockShouldReturnTrueForStringsWithValidDocblocks($string, $errorMessage)
    {
        $this->assertTrue( $this->parser->hasDocBlock($string), $errorMessage );
    }

    /**
     * @test
     * @dataProvider getInvalidInputProvider
     */
    public function hasdocblockShouldReturnFalseForStringsWithInvalidDoclbocks($string, $errorMessage)
    {
        $this->assertFalse( $this->parser->hasDocBlock($string), $errorMessage );
    }

    /**
     * @test
     */
    public function getdescriptionShouldReturnDescriptionAsString()
    {
        $expected = "The description Another description";

        $result = $this->parser->getDescription($this->docBlock);

        $this->assertEquals( $expected, $result, "Using the example docBlock, there should be a description string");
    }

    /**
     * @test
     */
    public function getdescriptionShouldReturnEmptyStringWithInvalidInput()
    {
        $result = $this->parser->getDescription(array('foo' => 'bar'));
        $this->assertEmpty($result);
    }

    /**
     * @test
     */
    public function getannotationsShouldReturnEmptyArrayWithInvalidInput()
    {
        $result = $this->parser->getAnnotations('');
        $this->assertEmpty($result);
    }

    /**
     * @test
     */
    public function getannotationsShouldReturnEmptyArrayWithNoDocblocks()
    {
        $result = $this->parser->getAnnotations('just a string without docblocks');
        $this->assertEmpty($result);
    }

    /**
     * @test
     */
    public function getannotationsShouldReturnArrayWithTagsAsKeys()
    {
        $expected = array(
            "category" => "test",
            "tags" => "test one two three"
        );

        $result = $this->parser->getAnnotations($this->docBlock);
        $this->assertEquals($expected, $result);
    }

    public function getValidInputProvider()
    {
        return array(
            array('/***/', "A string with an empty docBlock"),
            array('/** @foo bar **/', "A string with an inline docBlock"),
            array("/**
* @tags test one two three
*/", "A string with full docBlock"),
        );
    }

    public function getInvalidInputProvider()
    {
        return array(
            array(null, "Null inputs don't have docBlocks"),
            array('', "Empty strings don't have docBlocks"),
            array('Test', "Strings without docBlocks have no docBlocks"),
            array('/* @covers foo */', "An invalid phpdoc."),
            array('/***** @foo bar', "An invalid docBlock."),
            array('/**
*
* @bar foo', "An invalid docBlock."),
        );
    }
} 