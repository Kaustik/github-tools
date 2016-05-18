<?php

namespace Test;

use Kaustik\AppBundle\Command\UpsertImageLinkToPullRequestDescription;

class UpsertImageTest extends \PHPUnit_Framework_TestCase
{
    public function testReplaceExisting()
    {
        $body = <<<EOD
Github pull request
![uml](https://example.com/test-uml/123.png)
test
EOD;
        $url = 'https://example.com/test-uml/456.png';
        $actualNewBody = UpsertImageLinkToPullRequestDescription::getNewBody($body, $url);
        $expecedNewBody = <<<EOD
Github pull request
![uml](https://example.com/test-uml/456.png)
test
EOD;
        $this->assertEquals($expecedNewBody, $actualNewBody);
    }
    
    public function testAddUrl()
    {
        $body = "Github pull request";
        $url = 'https://example.com/test-uml/123.png';
        $actualNewBody = UpsertImageLinkToPullRequestDescription::getNewBody($body, $url);
        $expecedNewBody = <<<EOD
Github pull request
![uml](https://example.com/test-uml/123.png)
EOD;
        $this->assertEquals($expecedNewBody, $actualNewBody);
    }

    public function testReplaceCorrectExisting()
    {
        $body = <<<EOD
Github pull request
![test](https://example.com/foo.png)
![uml](https://example.com/test-uml/123.png)
test
EOD;
        $url = 'https://example.com/test-uml/456.png';
        $actualNewBody = UpsertImageLinkToPullRequestDescription::getNewBody($body, $url);
        $expecedNewBody = <<<EOD
Github pull request
![test](https://example.com/foo.png)
![uml](https://example.com/test-uml/456.png)
test
EOD;
        $this->assertEquals($expecedNewBody, $actualNewBody);
    }
}