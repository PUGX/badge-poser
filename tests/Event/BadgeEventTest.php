<?php

namespace App\Tests\Event;

use App\Badge\Model\Badge;
use App\Badge\Model\BadgeInterface;
use App\Event\BadgeEvent;
use PHPUnit\Framework\TestCase;

final class BadgeEventTest extends TestCase
{
    public function testGetData(): void
    {
        $badge = $this->getMockBuilder(BadgeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $badgeEvent = new BadgeEvent($badge);

        self::assertIsArray($badgeEvent->getData());
        self::assertArrayHasKey('subject', $badgeEvent->getData());
        self::assertArrayHasKey('status', $badgeEvent->getData());
        self::assertArrayHasKey('color', $badgeEvent->getData());
        self::assertArrayHasKey('format', $badgeEvent->getData());
    }

    public function testGetDataContent(): void
    {
        $badge = new Badge('subjectFake', 'statusFake', 'FFF000', 'formatFake');
        $badgeEvent = new BadgeEvent($badge);
        $data = $badgeEvent->getData();

        self::assertEquals('subjectFake', $data['subject']);
        self::assertEquals('statusFake', $data['status']);
        self::assertEquals('#FFF000', $data['color']);
        self::assertEquals('formatFake', $data['format']);
    }
}
