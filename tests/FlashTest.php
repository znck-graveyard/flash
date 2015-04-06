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

        $this->assertSessionHas('znck.flash.notifications');

        $this->getSession()->flush();
    }

    public function test_it_loads_messages_from_session()
    {
        $flash1 = $this->getFlash();
        $flash1->message('Test Message');

        $flash2 = $this->getFlash();
        assertEquals(1, count($flash2->get()));
    }

    public function test_if_can_flash_error_message()
    {
        $message = 'Error Message';
        $level = 'error';

        $key = md5($message . $level);

        $flash = $this->getFlash();
        $flash->error($message);

        $notifications = $flash->get('error');


        assertEquals(1, count($notifications));

        assertArrayHasKey($key, $notifications->toArray());

        $notification = $notifications->get($key);

        assertEquals($message, $notification['message']);

    }

    public function test_it_can_flash_warning_message()
    {
        $message = 'Warning Message';
        $level = 'warning';

        $key = md5($message . $level);

        $flash = $this->getFlash();
        $flash->warning($message);

        $notifications = $flash->get('warning');


        assertEquals(1, count($notifications));

        assertArrayHasKey($key, $notifications->toArray());

        $notification = $notifications->get($key);

        assertEquals($message, $notification['message']);

    }

    public function test_it_can_flash_many_messages()
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

        $notifications = $flash->get();

        assertEquals(count($messages), count($notifications));
    }

    public function test_it_flashes_message_only_once()
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

        $notifications = $flash->get();

        assertEquals(count(array_unique($messages)), count($notifications));
    }

    public function test_it_returns_filtered_messages()
    {
        $messages = [
            ['level' => 'info', 'message' => 'message text 1'],
            ['level' => 'error', 'message' => 'message text 2'],
            ['level' => 'error', 'message' => 'message text 3'],
            ['level' => 'warning', 'message' => 'message text 4'],
            ['level' => 'warning', 'message' => 'message text 5'],
            ['level' => 'warning', 'message' => 'message text 6'],
            ['level' => 'success', 'message' => 'message text 7'],
            ['level' => 'success', 'message' => 'message text 8'],
            ['level' => 'success', 'message' => 'message text 9'],
            ['level' => 'success', 'message' => 'message text 10'],
        ];

        $flash = $this->getFlash();

        foreach ($messages as $message) {
            $flash->message($message['message'], $message['level']);
        }

        assertEquals(1, count($flash->get('info')));
        assertEquals(2, count($flash->get('error')));
        assertEquals(3, count($flash->get('warning')));
        assertEquals(4, count($flash->get('success')));

        assertEquals(3, count($flash->get('info|error')));
        assertEquals(4, count($flash->get('info|warning')));
        assertEquals(5, count($flash->get('info|success')));

        assertEquals(5, count($flash->get('error|warning')));
        assertEquals(6, count($flash->get('error|success')));

        assertEquals(7, count($flash->get('warning|success')));

        assertEquals(6, count($flash->get('info|error|warning')));
        assertEquals(7, count($flash->get('info|error|success')));

        assertEquals(8, count($flash->get('info|warning|success')));

        assertEquals(10, count($flash->get('info|error|warning|success')));
    }

    public function test_it_can_accept_invalid_level_value()
    {
        $flash = $this->getFlash();

        $flash->message('message', 'random level');

        assertEquals(1, count($flash->get()));
    }

    public function test_it_creates_overlay_message()
    {
        $flash = $this->getFlash();

        $message = [
            'message' => 'overlaid message',
            'title'   => 'message title',
            'level'   => 'info',
            'overlay' => true,
        ];

        $flash->overlay($message['message'], $message['title'], $message['level'], $message['overlay']);

        $notifications = $flash->get();

        assertEquals(1, count($notifications));

        $key = md5(implode('', array_values($message)));
        assertArrayHasKey($key, $notifications->toArray());

        assertEquals($message['message'], $notifications->get($key)['message']);
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
