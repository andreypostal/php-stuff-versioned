<?php

namespace Andrey\StuffVersioned\Tests;

use Andrey\StuffVersioned\VersionInterface;
use Exception;

readonly class MyBestVersion implements VersionInterface
{
    public function __construct(
        private string $versionId,
        private bool $fail = false,
    ) {
    }

    /**
     * @throws Exception
     */
    public function run(): void
    {
        echo "Running {$this->versionId}\n";
        if ($this->fail) {
            throw new Exception('Sorry mom. I failed in my best version. :(');
        }
    }

    public function rollback(): void
    {
        echo "Oh no!!!!!!!!!!!\n";
    }

    public function getId(): string
    {
        return $this->versionId;
    }

    public function check(): bool
    {
        return true;
    }
}