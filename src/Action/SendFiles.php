<?php

declare(strict_types = 1);

namespace Bakabot\Action;

use Bakabot\Chat\TargetInterface;

final class SendFiles extends AbstractAction implements SendFilesInterface
{
    /** @var string[] */
    private array $files;

    /**
     * @param string[] $files
     */
    public function __construct(TargetInterface $target, array $files, bool $pingRecipient = false)
    {
        parent::__construct($target, $pingRecipient);

        $this->files = array_values($files);
    }

    /**
     * @return string[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }
}
