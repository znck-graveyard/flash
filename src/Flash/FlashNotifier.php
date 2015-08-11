<?php namespace Znck\Flash;

use Illuminate\Session\Store;
use Illuminate\Support\Collection;

/**
 * Class FlashNotifier
 *
 * @package Znck\Flash
 */
class FlashNotifier
{
    private $levels  = [
        'info'    => 100,
        'success' => 200,
        'warning' => 300,
        'error'   => 400,
    ];
    private $classes = [
        'info'    => 'info',
        'success' => 'success',
        'warning' => 'warning',
        'error'   => 'danger',
    ];
    /**
     * The message container.
     *
     * @type \Illuminate\Support\Collection
     */
    private $messages;

    /**
     * The session store.
     *
     * @type \Illuminate\Session\Store
     */
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

        $this->loadConfig();
        $this->loadOldNotifications($session);
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
        $this->message($message, $this->classes['info']);

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
        $this->message($message, 'error');

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
        $sort = array_get($this->levels, $level, 0);
        $level = array_get($this->classes, $level, 'info');

        $this->messages->put(md5($message . $title . $level . $overlay),
            compact('message', 'level', 'title', 'overlay', 'sort'));

        $this->session->flash($this->getFlashSessionKey(), $this->messages);

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
        $key = md5($message . $level);
        $sort = array_get($this->levels, $level, 0);
        $level = array_get($this->classes, $level, 'info');
        $this->messages->put($key, compact('message', 'level', 'sort'));

        $this->session->flash($this->getFlashSessionKey(), $this->messages);

        return $this;
    }

    /**
     * @param string $level
     *
     * @return \Illuminate\Support\Collection
     */
    public function get($level = '*')
    {
        $this->messages->sort(function ($a, $b) {
            return $b['sort'] - $a['sort'];
        });

        return $level !== '*' ? $this->filterNotifications($level) : $this->messages;
    }

    /**
     * @return string
     */
    protected function getFlashSessionKey()
    {
        return 'znck.flash.notifications';
    }

    /**
     * @param \Illuminate\Session\Store $session
     */
    protected function loadOldNotifications(Store $session)
    {
        $messages = $session->get($this->getFlashSessionKey(), null);
        if (!is_null($messages)) {
            foreach ($messages as $key => $message) {
                $this->messages->put($key, $message);
            }
        }
    }

    protected function loadConfig()
    {
        $this->levels = array_merge($this->levels, config('znck::flash.levels', []));
        $this->classes = array_merge($this->classes, config('znck::flash.classes', []));
    }

    /**
     * @param $level
     *
     * @return \Illuminate\Support\Collection|static
     */
    protected function filterNotifications($level)
    {
        if (is_string($level)) {
            $level = explode('|', $level);
        }

        if (is_array($level)) {
            $levels = [0 => 0];
            foreach ($level as $l) {
                $l = array_get($this->levels, $l, 0);
                $levels[$l] = $l;
            }
            $notifications = $this->messages->reject(function ($notification) use ($levels) {
                return !array_key_exists($notification['sort'], $levels);
            });

            return $notifications;
        }

        return new Collection;
    }

}
