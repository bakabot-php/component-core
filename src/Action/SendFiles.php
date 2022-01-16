<?php

declare(strict_types = 1);

namespace Bakabot\Action;

/** @psalm-suppress PropertyNotSetInConstructor */
final class SendFiles extends AbstractAction
{
    /** @var string[] */
    public /* readonly */ array $files;

    public function withFiles(array $files): self
    {
        assert(count($files) > 0);

        return $this->copy(files: array_values($files));
    }
}
