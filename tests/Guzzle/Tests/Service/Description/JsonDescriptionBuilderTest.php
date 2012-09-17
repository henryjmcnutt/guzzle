<?php

namespace Guzzle\Tests\Service\Description;

use Guzzle\Service\Description\JsonDescriptionBuilder;

/**
 * @covers Guzzle\Service\Description\JsonDescriptionBuilder
 */
class JsonDescriptionBuilderTest extends \Guzzle\Tests\GuzzleTestCase
{
    /**
     * @expectedException Guzzle\Service\Exception\JsonException
     */
    public function testThrowsErrorsOnOpenFailure()
    {
        $j = new JsonDescriptionBuilder();
        $b = @$j->build('/foo.does.not.exist');
    }

    public function testBuildsServiceDescriptions()
    {
        $j = new JsonDescriptionBuilder();
        $description = $j->build(__DIR__ . '/../../TestData/test_service.json');
        $this->assertTrue($description->hasOperation('test'));
        $test = $description->getOperation('test');
        $this->assertEquals('/path', $test->getUri());
        $test = $description->getOperation('concrete');
        $this->assertEquals('/abstract', $test->getUri());
    }
}
