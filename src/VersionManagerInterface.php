<?php

namespace Andrey\StuffVersioned;

interface VersionManagerInterface
{
    public function addVersion(VersionInterface $v): void;
    public function withVersion(VersionInterface $v): self;

    /** @return int representing the counter of versions executed */
    public function run(): int;
}