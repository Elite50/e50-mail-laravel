<?php
namespace Elite50\E50MailLaravel;

use Config;
use Illuminate\Mail\Transport\MailgunTransport;
use Mail;
use Swift_Mailer;

class E50MailWorker {

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
        list($domain, $view, $data, $callback) = $params;
        $callback = unserialize($callback)->getClosure();

        // If not using the mailgun driver, send normally ignoring the domain
        if (Config::get('mail.driver') !== 'mailgun') {
            Mail::send($view, $data, $callback);

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
            Mail::send($view, $data, $callback);

            // Restore the default mailer instance
            Mail::setSwiftMailer($backup);
        }

        $sqsJob->delete();
    }
}
