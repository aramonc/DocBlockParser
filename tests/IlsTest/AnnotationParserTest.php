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
     * @var $this->docBlockWithContent
     */
    public $docBlockWithContent;

    /**
     * @var $this->docBlock
     */
    public $docBlock;

    public function setUp()
    {
        $this->parser = new AnnotationParser();
        $this->docBlockWithContent = <<<TST
/**
 *
 * The description.
 * Another description.
 *
 * @category test
 * @tags test one two three
 *
 */

This is some content

#It contains Some MarkDown#

 * List 1
 * List 2
 * List 3
 * List 4


TST;
        $this->docBlock = <<<DBK
/**
 *
 * The description.
 * Another description.
 *
 * @category test
 * @tags test one two three
 *
 */
DBK;

    }

    public function tearDown()
    {
        $this->parser = null;
        $this->docBlockWithContent = null;
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
        $expected = "The description. Another description.";

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

        $result = $this->parser->getAnnotations($this->docBlockWithContent);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function extractdocblockShouldReturnArrayWithContent()
    {
        $expected = array(
            'meta' => "/**\n *\n * The description.\n * Another description.\n *\n * @category test\n * @tags test one two three\n *\n */",
            'content' => "This is some content\n\n#It contains Some MarkDown#\n\n * List 1\n * List 2\n * List 3\n * List 4",
        );
        $result = $this->parser->extractDocBlock($this->docBlockWithContent);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function extractdocblockShouldReturnAnArrayWithOnlyContentFromInvalidInput()
    {
        $expected = array(
            'meta' => "",
            'content' => "This is some content",
        );
        $result = $this->parser->extractDocBlock("This is some content");
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function parseShouldReturnAnArrayContainingTheMetadataAndTheContent()
    {
        $expected = array(
            'meta' => array(
                "description" => "The description. Another description.",
                "category" => "test",
                "tags" => "test one two three"
            ),
            'content' => "This is some content\n\n#It contains Some MarkDown#\n\n * List 1\n * List 2\n * List 3\n * List 4",
        );

        $result = $this->parser->parse($this->docBlockWithContent);

        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function parseShouldReturnAnArrayWithOnlyContentFromInvalidInput()
    {
        $expected = array(
            'meta' => array(),
            'content' => "This is some content",
        );
        $result = $this->parser->parse("This is some content");
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
            array("This is some content", "Strings with multiple words but no docBlock have no docBlocks"),
            array('/* @covers foo */', "An invalid phpdoc."),
            array('/***** @foo bar', "An invalid docBlock."),
            array('/**
*
* @bar foo', "An invalid docBlock."),
        );
    }
} 