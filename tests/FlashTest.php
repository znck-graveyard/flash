<?php

use Mockery as m;

class FlashTest extends Orchestra\Testbench\TestCase
{

    protected function getPackageProviders($app)
    {
        return ['Znck\Flash\FlashServiceProvider'];
    }


    public function test_it_sets_session_key()
    {

        $flash = $this->getFlash();

        $flash->error('error message');

        $this->assertSessionHas('flash_notification');

        $this->getSession()->flush();
    }

    public function test_error_message()
    {
        $message = 'Error Message';
        $level = 'danger';

        $key = md5($message . $level);

        $flash = $this->getFlash();
        $flash->error($message);

        $session = $this->getSession();
        $this->assertSessionHas('flash_notification');
        $notifications = $session->get('flash_notification');


        assertEquals(1, count($notifications));

        assertArrayHasKey($key, $notifications->toArray());

        $notification = $notifications->get($key);

        assertEquals($message, $notification['message']);

        assertEquals($level, $notification['level']);
    }

    public function test_warning_message()
    {
        $message = 'Warning Message';
        $level = 'warning';

        $key = md5($message . $level);

        $flash = $this->getFlash();
        $flash->warning($message);

        $session = $this->getSession();
        $this->assertSessionHas('flash_notification');
        $notifications = $session->get('flash_notification');


        assertEquals(1, count($notifications));

        assertArrayHasKey($key, $notifications->toArray());

        $notification = $notifications->get($key);

        assertEquals($message, $notification['message']);

        assertEquals($level, $notification['level']);
    }

    public function test_many_messages()
    {

        $messages = [
            'message 1',
            'message 2',
            'message 3',
            'message 4',
            'message 5',
            'message 6',
            'message 7',
        ];

        $flash = $this->getFlash();
        foreach ($messages as $message) {
            $flash->error($message);
        }

        $session = $this->getSession();
        $this->assertSessionHas('flash_notification');
        $notifications = $session->get('flash_notification');

        assertEquals(count($messages), count($notifications));
    }

    public function test_many_messages_with_repeating_messages()
    {

        $messages = [
            'message 1',
            'message 2',
            'message 3',
            'message 2',
            'message 5',
            'message 2',
            'message 7',
        ];

        $flash = $this->getFlash();
        foreach ($messages as $message) {
            $flash->error($message);
        }

        $session = $this->getSession();
        $this->assertSessionHas('flash_notification');
        $notifications = $session->get('flash_notification');

        assertEquals(count(array_unique($messages)), count($notifications));
    }

    /**
     * @return \Illuminate\Session\Store
     */
    private function getSession()
    {
        return $this->app['session'];
    }

    /**
     * @return \Znck\Flash\FlashNotifier
     */
    private function getFlash()
    {
        return $this->app->make('\Znck\Flash\FlashNotifier');
    }
}
