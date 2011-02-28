<?php

namespace Guzzle\Http\Pool;

use Guzzle\Http\Message\RequestInterface;
use Guzzle\Common\Subject\Subject;

/**
 * Execute a pool of {@see RequestInterface} objects in
 * parallel.
 *
 * @author  michael@guzzlephp.org
 */
interface PoolInterface extends Subject
{
    // Various states of the pool's request cycle
    const BEFORE_SEND = 'before_send';
    const POLLING = 'polling';
    const COMPLETE = 'complete';
    const ADD_REQUEST = 'add_request';
    const REMOVE_REQUEST = 'remove_request';
    const RESET = 'reset';

    const STATE_IDLE = 'idle';
    const STATE_SENDING = 'sending';
    const STATE_COMPLETE = 'complete';

    /**
     * Add a request to the pool.
     *
     * @param RequestInterface $request Returns the Request that was added
     */
    public function addRequest(RequestInterface $request);

    /**
     * Get an array of attached {@see RequestInterface}s.
     *
     * @return array Returns an array of attached requests.
     */
    public function getRequests();

    /**
     * Get the current state of the Pool
     *
     * @return string
     */
    public function getState();

    /**
     * Remove a request from the pool.
     *
     * @param RequestInterface $request Request to detach.
     *
     * @return RequestInterface Returns the Request object that was removed
     */
    public function removeRequest(RequestInterface $request);

    /**
     * Reset the state of the Pool and remove any attached Requests
     */
    public function reset();

    /**
     * Send a pool of {@see RequestInterface} requests.
     *
     * Calling this method more than once will return FALSE.
     *
     * @return array|bool Returns an array of attached Request objects on
     *      success FALSE on failure.
     */
    public function send();
}