<?php

namespace Guzzle\Http\Event;

use Guzzle\Http\Adapter\TransactionInterface;
use Guzzle\Http\Exception\RequestException;

/**
 * Contains a collection of request events and methods used to manage the
 * request event lifecycle (before, after, error).
 */
final class RequestEvents
{
    /**
     * Event emitted before a request is sent
     *
     * The event emitted is a {@see \Guzzle\Http\Event\RequestBeforeSendEvent} object
     */
    const BEFORE_SEND = 'request.before_send';

    /**
     * Event emitted when a request has finished sending
     *
     * The event emitted is a {@see \Guzzle\Http\Event\RequestAfterSendEvent} object
     */
    const AFTER_SEND = 'request.after_send';

    /**
     * Event emitted when an error occurs for a given request
     *
     * The event emitted is a {@see \Guzzle\Http\Event\RequestErrorEvent} object
     */
    const ERROR = 'request.error';

    /**
     * Event emitted after receiving all of the headers of a non-information response.
     *
     * The event context contains 'request' and 'response' keys.
     */
    const RESPONSE_HEADERS = 'request.response_headers';

    /**
     * Emits the before send event for a request and emits an error
     * event if an error is encountered during the before send.
     *
     * @param TransactionInterface $transaction
     *
     * @throws RequestException
     */
    public static function emitBeforeSendEvent(TransactionInterface $transaction) {
        $request = $transaction->getRequest();
        try {
            $request->getEventDispatcher()->dispatch(
                RequestEvents::BEFORE_SEND,
                new RequestBeforeSendEvent($transaction)
            );
        } catch (RequestException $e) {
            self::emitErrorEvent($transaction, $e);
        }
    }

    /**
     * Emits the after send event for a request and emits an error
     * event if an error is encountered during the after send.
     *
     * @param TransactionInterface $transaction Transaction to emit for
     * @param array                $stats       Transfer stats
     *
     * @throws RequestException
     */
    public static function emitAfterSend(
        TransactionInterface $transaction,
        array $stats = []
    ) {
        try {
            $transaction->getRequest()->getEventDispatcher()->dispatch(
                RequestEvents::AFTER_SEND,
                new RequestAfterSendEvent($transaction, $stats)
            );
        } catch (RequestException $e) {
            self::emitErrorEvent($transaction, $e, $stats);
        }
    }

    /**
     * Emits an error event for a request and accounts for the propagation
     * of an error event being stopped to prevent the exception from being
     * thrown.
     *
     * @param TransactionInterface $transaction
     * @param \Exception           $e
     * @param array                $stats
     *
     * @throws \Guzzle\Http\Exception\RequestException
     */
    public static function emitErrorEvent(
        TransactionInterface $transaction,
        \Exception $e,
        array $stats = []
    ) {
        $request = $transaction->getRequest();

        // Convert non-request exception to a wrapped exception
        if (!($e instanceof RequestException)) {
            $e = new RequestException($e->getMessage(), $request, null, $e);
        }

        // Dispatch an event and allow interception
        if (!$request->getEventDispatcher()->dispatch(
            RequestEvents::ERROR,
            new RequestErrorEvent($transaction, $e, $stats)
        )->isPropagationStopped()) {
            throw $e;
        }
    }
}
