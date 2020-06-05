<?php

declare(strict_types=1);

namespace App\Tests\Badge\ValueObject;

use App\Badge\ValueObject\Repository;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

class RepositoryTest extends TestCase
{
    public function testItShouldCreateFromRepositoryUrl(): void
    {
        $repository = Repository::createFromRepositoryUrl('https://github.com/username/repository');

        $this->assertEquals('github.com', $repository->getSource());
        $this->assertEquals('username', $repository->getUsername());
        $this->assertEquals('repository', $repository->getName());
    }

    public function testItThrowExceptionIfUrlNotValid(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Impossible to fetch package by "https://google.it" repository.');

        Repository::createFromRepositoryUrl('https://google.it');
    }

    public function testItShouldCreateRepository(): void
    {
        $repository = Repository::create('github.com', 'username', 'repository');

        $this->assertEquals('github.com', $repository->getSource());
        $this->assertEquals('username', $repository->getUsername());
        $this->assertEquals('repository', $repository->getName());
    }
}
