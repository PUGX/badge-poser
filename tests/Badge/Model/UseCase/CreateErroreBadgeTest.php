<?php

namespace App\Tests\Badge\Model\UseCase;

use App\Badge\Model\Badge;
use App\Badge\Model\CacheableBadge;
use App\Badge\Model\UseCase\CreateErrorBadge;
use GuzzleHttp\Exception\BadResponseException;
use PHPUnit\Framework\TestCase;

final class CreateErroreBadgeTest extends TestCase
{
    public function testCreateAGenericErrorBadge(): void
    {
        $error = new \Exception('msg');
        $format = 'svg';

        $createErrorBadge = new CreateErrorBadge();

        $badge = $createErrorBadge->createErrorBadge($error, $format);
        $expectedBadge = new CacheableBadge(new Badge((string) $error, 'generic', 'e05d44', $format), 0, 0);

        self::assertEquals($expectedBadge, $badge);
    }

    public function testCreateABadClientResponseErrorBadge(): void
    {
        $badResponseEx = $this->getMockBuilder(BadResponseException::class)
            ->disableOriginalConstructor()
            ->getMock();
        $format = 'svg';

        $createErrorBadge = new CreateErrorBadge();

        $badge = $createErrorBadge->createErrorBadge($badResponseEx, $format);
        $expectedBadge = new CacheableBadge(new Badge((string) $badResponseEx, 'not found?', 'e05d44', $format), 0, 0);

        self::assertEquals($expectedBadge, $badge);
    }

    public function testCreateAClientExceptionErrorBadge(): void
    {
        $unexpectedValueEx = new \UnexpectedValueException('msg');
        $format = 'svg';

        $createErrorBadge = new CreateErrorBadge();

        $badge = $createErrorBadge->createErrorBadge($unexpectedValueEx, $format);
        $expectedBadge = new CacheableBadge(new Badge((string) $unexpectedValueEx, 'connection', 'e05d44', $format), 0, 0);

        self::assertEquals($expectedBadge, $badge);
    }
}
