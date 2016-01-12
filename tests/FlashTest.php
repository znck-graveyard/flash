<?php


class FlashTest extends Orchestra\Testbench\TestCase
{
    public function test_it_sets_session_key()
    {
        $flash = $this->getFlash();
        $flash->error('error message');
        $this->assertSessionHas('znck.flash.notifications');
        $this->getSession()->flush();
    }

    /**
     * @return \Znck\Flash\FlashNotifier
     */
    protected function getFlash()
    {
        return $this->app->make('\Znck\Flash\FlashNotifier');
    }

    /**
     * @return \Illuminate\Session\Store
     */
    protected function getSession()
    {
        return $this->app['session'];
    }

    public function test_it_loads_messages_from_session()
    {
        $flash1 = $this->getFlash();
        $flash1->message('Test Message');
        $flash2 = $this->getFlash();
        $this->assertEquals(1, count($flash2->get()), 'It is loading messages from session.');
    }

    public function test_if_can_flash_error_message()
    {
        $message = 'Error Message';
        $level = 'error';
        $key = md5($message.$level);
        $flash = $this->getFlash();
        $flash->error($message);
        $notifications = $flash->get('error');

        $this->assertEquals(1, count($notifications));
        $this->assertArrayHasKey($key, $notifications->toArray());

        $notification = $notifications->get($key);

        $this->assertEquals($message, $notification['message']);
    }

    public function test_it_can_flash_warning_message()
    {
        $message = 'Warning Message';
        $level = 'warning';
        $key = md5($message.$level);
        $flash = $this->getFlash();
        $flash->warning($message);
        $notifications = $flash->get('warning');

        $this->assertEquals(1, count($notifications));
        $this->assertArrayHasKey($key, $notifications->toArray());
        $notification = $notifications->get($key);

        $this->assertEquals($message, $notification['message']);
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

        $this->assertEquals(count($messages), count($notifications));
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
        $this->assertEquals(count(array_unique($messages)), count($notifications));
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

        $this->assertEquals(1, count($flash->get('info')));
        $this->assertEquals(2, count($flash->get('error')));
        $this->assertEquals(3, count($flash->get('warning')));
        $this->assertEquals(4, count($flash->get('success')));

        $this->assertEquals(3, count($flash->get('info|error')));
        $this->assertEquals(4, count($flash->get('info|warning')));
        $this->assertEquals(5, count($flash->get('info|success')));

        $this->assertEquals(5, count($flash->get('error|warning')));
        $this->assertEquals(6, count($flash->get('error|success')));

        $this->assertEquals(7, count($flash->get('warning|success')));

        $this->assertEquals(6, count($flash->get('info|error|warning')));
        $this->assertEquals(7, count($flash->get('info|error|success')));

        $this->assertEquals(8, count($flash->get('info|warning|success')));

        $this->assertEquals(10, count($flash->get('info|error|warning|success')));
    }

    public function test_it_can_accept_invalid_level_value()
    {
        $flash = $this->getFlash();

        $flash->message('message', 'random level');

        $this->assertEquals(1, count($flash->get()));
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

        $this->assertEquals(1, count($notifications));

        $key = md5(implode('', array_values($message)));
        $this->assertArrayHasKey($key, $notifications->toArray());

        $this->assertEquals($message['message'], $notifications->get($key)['message']);
    }

    protected function getPackageProviders($application)
    {
        return ['Znck\Flash\FlashServiceProvider'];
    }
}
