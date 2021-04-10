<?php

namespace App\Tests\Contributors\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class UpdateCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        $kernel = self::createKernel();
        $kernel->boot();

        $application = new Application($kernel);

        $command = $application->find('app:contributors:update');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
        ]);

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $numbers = $this->getNumbers($output);
        $count = $numbers[0] ?? 0;
        self::assertGreaterThanOrEqual(30, $count);
    }

    /**
     * @return array<int, array<int, string>>
     */
    private function getNumbers(string $str): array
    {
        \preg_match_all('/\d+/', $str, $matches);

        return $matches[0];
    }
}
