<?php

namespace App\Tests\Badge\Model\UseCase;

use App\Badge\Model\Badge;
use App\Badge\Model\UseCase\CreateErrorBadge;
use GuzzleHttp\Exception\BadResponseException;
use PHPUnit\Framework\TestCase;

class CreateErroreBadgeTest extends TestCase
{
    public function testCreateAGenericErrorBadge(): void
    {
        $error = new \Exception('msg');
        $format = 'svg';

        $createErrorBadge = new CreateErrorBadge();

        $badge = $createErrorBadge->createErrorBadge($error, $format)->getBadge();

        $this->assertEquals(new Badge((string) $error, 'generic', 'e05d44', $format), $badge);
    }

    public function testCreateABadClientResponseErrorBadge(): void
    {
        $badResponseEx = $this->getMockBuilder(BadResponseException::class)
            ->disableOriginalConstructor()
            ->getMock();
        $format = 'svg';

        $createErrorBadge = new CreateErrorBadge();

        $badge = $createErrorBadge->createErrorBadge($badResponseEx, $format)->getBadge();

        $this->assertEquals(new Badge((string) $badResponseEx, 'not found?', 'e05d44', $format), $badge);
    }

    public function testCreateAClientExceptionErrorBadge(): void
    {
        $unexpectedValueEx = new \UnexpectedValueException('msg');
        $format = 'svg';

        $createErrorBadge = new CreateErrorBadge();

        $badge = $createErrorBadge->createErrorBadge($unexpectedValueEx, $format)->getBadge();

        $this->assertEquals(new Badge((string) $unexpectedValueEx, 'connection', 'e05d44', $format), $badge);
    }
}
