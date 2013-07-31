<?php

namespace Guzzle\Stream;

use Guzzle\Common\Event;
use Guzzle\Common\HasDispatcher;
use Guzzle\Common\HasDispatcherInterface;

/**
 * Stream decorator that emits events for read and write methods
 */
class IoEmittingStream implements StreamInterface, HasDispatcherInterface
{
    use StreamDecorator;
    use HasDispatcher;

    public function read($length)
    {
        $event = ['stream' => $this, 'length' => $length, 'data' => $this->stream->read($length)];
        $this->dispatch('stream.read', $event);

        return $event['data'];
    }

    public function write($string)
    {
        $event = ['stream' => $this, 'length' => $this->stream->write($string), 'data' => $string];
        $this->dispatch('stream.write', $event);

        return $event['length'];
    }
}
