<?php

declare(strict_types = 1);

namespace Bakabot\Action;

interface SendFilesInterface extends ActionInterface
{
    public function getFiles(): array;
}
