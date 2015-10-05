<?php
namespace Elite50\E50MailLaravel;

use Config;
use Illuminate\Mail\Transport\MailgunTransport;
use Illuminate\Support\SerializableClosure;
use Log;
use Mail;
use Swift_Mailer;
use Swift_RfcComplianceException;

class E50MailWorker
{
    /**
     * Sends an email
     *
     * @param \Illuminate\Queue\Jobs\Job $sqsJob
     * @param array $data
     *
     * @return void
     */
    public function fire($sqsJob, $params)
    {
        // Get the needed parameters
        $domain = $params[0];
        $view = $params[1];
        $data = $params[2];
        $callback = unserialize($params[3])->getClosure();
        $driver = isset($params[4]) ? $params[4] : null;

        // If using a specific driver, set it now
        if (!is_null($driver)) {
            Config::set('mail.driver', $driver);
        }

        // If not using the mailgun driver, send normally ignoring the domain
        if (Config::get('mail.driver') !== 'mailgun') {
            $this->send($view, $data, $callback);

        // Otherwise, adjust the mailgun domain dynamically
        } else {
            // Backup your default mailer
            $backup = Mail::getSwiftMailer();

            // Setup your mailgun transport
            $transport = new MailgunTransport(Config::get('services.mailgun.secret'), $domain);
            $mailer = new Swift_Mailer($transport);

            // Set the new mailer with the domain
            Mail::setSwiftMailer($mailer);

            // Send your message
            $this->send($view, $data, $callback);

            // Restore the default mailer instance
            Mail::setSwiftMailer($backup);
        }

        $sqsJob->delete();
    }

    /**
     * Sends the email
     *
     * @param array|string $view
     * @param array $data
     * @param Closure $callback
     */
    private function send($view, $data, $callback)
    {
        try {
            Mail::send($view, $data, $callback);
        } catch (Swift_RfcComplianceException $e) {
            Log::error($e->getMessage());
        }
    }
}
