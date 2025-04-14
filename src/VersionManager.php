<?php

namespace Andrey\StuffVersioned;

use Psr\Log\LoggerInterface;
use Throwable;

class VersionManager implements VersionManagerInterface
{
    /** @var VersionInterface[] */
    protected array $versions = [];

    public function __construct(
        private readonly BackendInterface $backend,
        private readonly ?LoggerInterface $logger = null,
    ) {
    }

    public function addVersion(VersionInterface $v): void
    {
        $this->versions[] = clone $v;
    }

    public function withVersion(VersionInterface $v): self
    {
        $new = clone $this;
        $new->addVersion($v);
        return $new;
    }

    /** @return int representing the counter of versions executed */
    public function run(): int
    {
        $currentVersion = $this->backend->getCurrentVersionId();
        $versionRuns = $this->backend->getVersionList();

        $indexForLastVersionExecuted = -1;
        foreach ($this->versions as $i => $version) {
            if ($version->getId() === $currentVersion) {
                $indexForLastVersionExecuted = $i;
                break;
            }

            if ($version->check() === false) {
                continue;
            }

            if (isset($versionRuns[$i]) && $versionRuns[$i]->versionId !== $version->getId()) {
                $this->logger?->warning("Inconsistent versioning, found {$version->getId()} expected {$versionRuns[$i]->versionId} as {$i}th run");
            }
        }

        // Move to next version
        $currentVersionToExecute = $indexForLastVersionExecuted + 1;

        $versionsAvailable = count($this->versions);

        // Already on latest version
        if ($currentVersionToExecute === $versionsAvailable) {
            $this->logger?->debug(
                'Already on latest version',
            );
            return 0;
        }

        // Invalid configuration
        if ($currentVersionToExecute > $versionsAvailable) {
            $this->logger?->warning('Number of versions executed exceeds available ones');
            return 0;
        }

        $counter = 0;
        for (; $currentVersionToExecute < $versionsAvailable; $currentVersionToExecute++) {
            $version = $this->versions[$currentVersionToExecute];
            if ($version->check() === false) {
                continue;
            }

            $entry = $this->backend->markVersionAsProcessing($version->getId());

            try {
                $version->run();
                $this->backend->markVersionAsSuccessful($entry);
                $counter++;
            } catch (Throwable $exception) {
                $this->logger?->warning(
                    "Version run failed with exception {$exception->getMessage()}",
                );

                $version->rollback();
                $this->backend->abortVersionProcessing($entry, $exception->getMessage());
                break;
            }
        }

        return $counter;
    }
}