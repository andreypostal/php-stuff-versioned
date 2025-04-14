<?php

namespace Andrey\StuffVersioned\Tests;

use Andrey\StuffVersioned\BackendInterface;
use Andrey\StuffVersioned\VersionEntryInterface;

class InMemoryBackend implements BackendInterface
{
    /** @var VersionEntry[] */
    private array $versions = [];

    public function getCurrentVersionId(): ?string
    {
        $r = null;
        foreach ($this->versions as $version) {
            if ($version->successful) {
                $r = $version;
            }
        }

        return $r?->versionId;
    }

    public function markVersionAsProcessing(string $versionId): VersionEntryInterface
    {
        $this->versions[] = new VersionEntry(versionId: $versionId);
        end($this->versions)->index = count($this->versions) - 1;

        return end($this->versions);
    }

    public function markVersionAsSuccessful(VersionEntryInterface $version): void
    {
        $this->versions[$version->index]->successful = true;
    }

    public function abortVersionProcessing(VersionEntryInterface $version, string $message): void
    { }

    public function getVersionList(): array
    {
        return array_filter($this->versions, static fn (VersionEntryInterface $entry) => $entry->successful);
    }
}