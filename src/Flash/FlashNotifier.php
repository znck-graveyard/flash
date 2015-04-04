<?php namespace Znck\Flash;

use Illuminate\Session\Store;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class FlashNotifier
{
    /**
     * The message container.
     *
     * @type \Illuminate\Support\Collection
     */
    private $messages;


    private $session;

    /**
     * Create a new flash notifier instance.
     *
     * @param \Illuminate\Session\Store      $session
     * @param \Illuminate\Support\Collection $messages
     */
    function __construct(Store $session, Collection $messages)
    {
        $this->session = $session;
        $this->messages = $messages;

        $messages = $session->get('flash_notification', null);
        if (!is_null($messages)) {
            foreach ($messages as $key => $message) {
                $this->messages->put($key, $message);
            }
        }
    }

    /**
     * Flash an information message.
     *
     * @param string $message
     *
     * @return $this
     */
    public function info($message)
    {
        $this->message($message, 'info');

        return $this;
    }

    /**
     * Flash a success message.
     *
     * @param  string $message
     *
     * @return $this
     */
    public function success($message)
    {
        $this->message($message, 'success');

        return $this;
    }

    /**
     * Flash an error message.
     *
     * @param  string $message
     *
     * @return $this
     */
    public function error($message)
    {
        $this->message($message, 'danger');

        return $this;
    }

    /**
     * Flash a warning message.
     *
     * @param  string $message
     *
     * @return $this
     */
    public function warning($message)
    {
        $this->message($message, 'warning');

        return $this;
    }

    /**
     * Flash an overlay modal.
     *
     * @param  string $message
     * @param  string $title
     *
     * @param string  $level
     * @param bool    $overlay
     *
     * @return $this
     *
     */
    public function overlay($message, $title = 'Notice', $level = 'info', $overlay = true)
    {
        $this->messages->put(md5($message . $level . $title . $overlay), compact('message', 'level', 'title', 'overlay'));

        $this->session->flash('flash_notification', $this->messages);

        return $this;
    }

    /**
     * Flash a general message.
     *
     * @param  string $message
     * @param  string $level
     *
     * @return $this
     */
    public function message($message, $level = 'info')
    {
        $this->messages->put(md5($message . $level), compact('message', 'level'));

        $this->session->flash('flash_notification', $this->messages);

        return $this;
    }

}
