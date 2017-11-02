<?php
namespace Elite50\E50MailLaravel;

use Queue;

class E50Mail
{
    /**
     * Queues an email to be sent later
     *
     * @param string $domain
     * @param array|string $view
     * @param array $data
     * @param array $messageData
     * @param string|null $driver
     * @param string|null $queue
     * @param array|null $config
     */
    public function queue(
        $domain,
        $view,
        $data,
        $messageData,
        $driver = null,
        $queue = null,
        $config = null
    ) {
        Queue::push(
            'E50MailWorker',
            [
                $domain,
                $view,
                $data,
                $messageData,
                $driver,
                $config
            ],
            $queue
        );
    }
}
