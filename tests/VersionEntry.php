<?php

namespace Andrey\StuffVersioned\Tests;

use Andrey\StuffVersioned\VersionEntryInterface;

class VersionEntry implements VersionEntryInterface
{
    public int $index;
    public bool $successful = false;

    public function __construct(
        public string $versionId {
            get {
                return $this->versionId;
            }
        }
    ) {
    }
}