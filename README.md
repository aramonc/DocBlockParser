DocBlockParser
==============

Parses strings for docBlock like portions and then extracts the annotations, descriptions, and optional document content. This should not be used as an annotation parser for PHP code, at least not on it's own. If you're looking to do something with the docBlocks you might want to use something like https://github.com/schmittjoh/metadata better. This is more for if you're trying to get metadata from a plain text file. Look through the tests for examples.
