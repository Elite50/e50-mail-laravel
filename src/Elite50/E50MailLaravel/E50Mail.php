<?php
namespace Elite50\E50MailLaravel;

use Illuminate\Support\SerializableClosure;
use Queue;

class E50Mail {

    /**
     * Queues an email to be sent later
     *
     * @param string $domain
     * @param array|string $view
     * @param array $data
     * @param Closure $callback
     */
    public function queue($domain, $view, $data, $callback)
    {
        Queue::push('E50MailWorker', [
            $domain,
            $view,
            $data,
            serialize(new SerializableClosure($callback))
        ]);
    }
}
