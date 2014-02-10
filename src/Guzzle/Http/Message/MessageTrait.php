<?php

namespace Guzzle\Http\Message;

use Guzzle\Http\Mimetypes;
use Guzzle\Stream\MetadataStreamInterface;
use Guzzle\Stream\StreamInterface;

/**
 * HTTP request/response message trait
 * @internal This should not be relied upon directly but rather in Request or Response objects
 */
trait MessageTrait
{
    use HasHeadersTrait;

    /** @var StreamInterface Message body */
    private $body;

    /** @var string HTTP protocol version of the message */
    private $protocolVersion = '1.1';

    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setBody(StreamInterface $body = null)
    {
        if ($body === null) {
            // Setting a null body will remove the body of the request
            $this->body = null;
            $this->removeHeader('Content-Length');
            $this->removeHeader('Transfer-Encoding');
        } else {

            $this->body = $body;

            // Set the Content-Length header if it can be determined
            $size = $this->body->getSize();
            if ($size !== null && !$this->hasHeader('Content-Length')) {
                $this->setHeader('Content-Length', $size);
            }

            // Add the content-type if possible based on the stream URI
            if ($body instanceof MetadataStreamInterface && !$this->hasHeader('Content-Type')) {
                if ($uri = $body->getMetadata('uri')) {
                    if ($contentType = Mimetypes::getInstance()->fromFilename($uri)) {
                        $this->setHeader('Content-Type', $contentType);
                    }
                }
            }
        }

        return $this;
    }
}
