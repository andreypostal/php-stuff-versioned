<?php

namespace Andrey\StuffVersioned;

interface BackendInterface
{
    public function getCurrentVersionId(): ?string;

    public function markVersionAsProcessing(string $versionId): VersionEntryInterface;

    public function markVersionAsSuccessful(VersionEntryInterface $version): void;

    public function abortVersionProcessing(VersionEntryInterface $version, string $message): void;

    /** @return VersionEntryInterface[] a list containing every version successfully executed */
    public function getVersionList(): array;
}