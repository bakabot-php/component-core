<?php

declare(strict_types = 1);

namespace Bakabot\Action;

/** @psalm-suppress PropertyNotSetInConstructor */
final class SendTemplatedMessage extends AbstractAction
{
    public /* readonly */ array $context;
    public /* readonly */ string $template;

    public function withTemplate(string $template, array $context): self
    {
        assert(trim($template) !== '');

        return $this->copy(template: $template, context: $context);
    }
}
