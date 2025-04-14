<?php

namespace Andrey\StuffVersioned;

interface VersionInterface
{
    /**
     * check will be called before the actual run and before
     * checking for inconsistencies.
     *
     * If check is false, it will skip both operations - the warning for
     * inconsistency and the actual run.
     *
     * If no check is needed, you can simply return true.
     */
    public function check(): bool;

    /**
     * run is the desired behavior to be attached to this version.
     */
    public function run(): void;

    /**
     * rollback is executed if something went wrong while performing
     * the run process. It should aim to revert the progress done by the run process.
     */
    public function rollback(): void;

    /**
     * getId should return a unique identifier for this version within the
     * entire manager session.
     *
     * @return string
     */
    public function getId(): string;
}