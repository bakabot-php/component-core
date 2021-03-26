<?php

declare(strict_types = 1);

namespace Bakabot\Action;

use Bakabot\Chat\Channel\ChannelInterface;
use Bakabot\Chat\User\UserInterface;
use Bakabot\Payload\PayloadInterface;
use LogicException;

final class Builder
{
    private ?ChannelInterface $channel = null;
    private ?array $embed = null;
    private ?array $files = null;
    private ?string $message = null;
    private bool $pingRecipient = false;
    private ?UserInterface $recipient = null;
    private bool $shouldDeleteTriggerMessage = false;

    public static function fromPayload(PayloadInterface $payload): self
    {
        return (new self())->sendTo($payload->getChannel(), $payload->getMessage()->getAuthor());
    }

    private function setContents(?string $message = null, ?array $embed = null, ?array $files = null): self
    {
        if (is_array($files) && ($message !== null || $embed !== null)) {
            throw new LogicException('Invalid combination of contents.');
        }

        $copy = clone $this;
        $copy->embed = $embed;
        $copy->files = $files;
        $copy->message = $message;

        return $copy;
    }

    public function build(): ActionInterface
    {
        assert($this->channel !== null);

        if (is_array($this->files)) {
            if ($this->channel->isPrivate()) {
                assert($this->recipient !== null);

                $action = new SendFilesToDirectMessage(
                    $this->recipient,
                    $this->files,
                );
            } else {
                $action = new SendFilesToChannel(
                    $this->channel,
                    $this->files,
                );
            }
        } else {
            if ($this->channel->isPrivate()) {
                assert($this->recipient !== null);

                $action = new SendDirectMessage(
                    $this->recipient,
                    $this->message,
                    $this->embed
                );
            } else {
                $action = new SendMessage(
                    $this->channel,
                    $this->message,
                    $this->embed,
                    $this->recipient,
                    $this->pingRecipient,
                );
            }
        }

        $action->shouldDeleteTriggerMessage($this->shouldDeleteTriggerMessage);

        return $action;
    }

    public function pingRecipient(bool $ping = true): self
    {
        if ($this->recipient === null) {
            $ping = false;
        }

        $copy = clone $this;
        $copy->pingRecipient = $ping;

        return $copy;
    }

    public function sendTo(ChannelInterface $channel, ?UserInterface $recipient = null, ?bool $ping = null): self
    {
        $copy = clone $this;
        $copy->channel = $channel;
        $copy->recipient = $recipient;

        if ($ping !== null) {
            $copy = $copy->pingRecipient($ping);
        }

        return $copy;
    }

    public function shouldDeleteTriggerMessage(bool $deleteTriggerMessage = true): self
    {
        $copy = clone $this;
        $copy->shouldDeleteTriggerMessage = $deleteTriggerMessage;

        return $copy;
    }

    public function withContents(?string $message = null, ?array $embed = null, ?array $files = null): self
    {
        if (is_array($files) && count($files) === 0) {
            $files = null;
        }

        return $this->setContents($message, $embed, $files);
    }
}
