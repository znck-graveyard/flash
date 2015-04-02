<?php
/**
 * This file belongs to flash.
 *
 * Author: Rahul Kadyan, <hi@znck.me>
 */

namespace Znck\Flash;

use Illuminate\Support\Collection;

class LaravelCollection
{

    /**
     * The message container.
     *
     * @var \Illuminate\Support\Collection
     */
    private $messages;

    function __construct(SessionStore $sessionStore)
    {
        $messages = session('flash_notification', null);
        if (!$messages) {
            $messages = new Collection;
        }
        $this->messages = $messages;
    }

    function add($value)
    {
        $this->messages->push($value);
    }

    function messages()
    {
        return $this->messages;
    }
}