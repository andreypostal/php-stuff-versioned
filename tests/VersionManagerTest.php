<?php

namespace Andrey\StuffVersioned\Tests;

use Andrey\StuffVersioned\VersionManager;
use PHPUnit\Framework\TestCase;

class VersionManagerTest extends TestCase
{
    public function testVersioning(): void
    {
        $manager = new VersionManager(new InMemoryBackend());

        $manager->addVersion(new MyBestVersion('A'));

        $executed = $manager->run();
        $this->assertEquals(1, $executed);

        $executed = $manager->run();
        $this->assertEquals(0, $executed);

        $manager->addVersion(new MyBestVersion('B'));
        $manager->addVersion(new MyBestVersion('C'));

        $executed = $manager->run();
        $this->assertEquals(2, $executed);

        $executed = $manager->run();
        $this->assertEquals(0, $executed);

        $manager = $manager
            ->withVersion(new MyBestVersion('D'))
            ->withVersion(new MyBestVersion('E', true));

        $executed = $manager->run();
        $this->assertEquals(1, $executed);

        $executed = $manager->run();
        $this->assertEquals(0, $executed);
    }
}