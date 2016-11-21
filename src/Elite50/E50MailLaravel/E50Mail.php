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
     */
    public function queue(
        $domain,
        $view,
        $data,
        $messageData,
        $driver = null
    ) {
        Queue::push('E50MailWorker', [
            $domain,
            $view,
            $data,
            $messageData,
            $driver
        ]);
    }
}
