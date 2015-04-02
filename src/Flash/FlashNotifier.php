<?php namespace Znck\Flash;

class FlashNotifier
{
    /**
     * The message container.
     *
     * @var LaravelCollection
     */
    private $messages;

    /**
     * The session writer.
     *
     * @var SessionStore
     */
    private $session;

    /**
     * Create a new flash notifier instance.
     *
     * @param SessionStore                  $session
     * @param \Znck\Flash\LaravelCollection $messages
     */
    function __construct(SessionStore $session, LaravelCollection $messages)
    {
        $this->session = $session;
        $this->messages = $messages;
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
        $this->messages->add(compact('message', 'level', 'title', 'overlay'));

        $this->session->flash('flash_notification', $this->messages->messages());

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
        $this->messages->add(compact('message', 'level'));

        $this->session->flash('flash_notification', $this->messages->messages());

        return $this;
    }

}
