<?php
namespace Elite50\E50MailLaravel;

use Config;
use Exception;
use Illuminate\Mail\Transport\MailgunTransport;
use Log;
use Mail;
use Swift_Mailer;
use Swift_RfcComplianceException;
use Swift_TransportException;

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
        $messageData = $params[3];
        $driver = isset($params[4]) ? $params[4] : null;

        // If using a specific driver, set it now
        if (!is_null($driver)) {
            Config::set('mail.driver', $driver);
        }

        // If not using the mailgun driver, send normally ignoring the domain
        if (Config::get('mail.driver') !== 'mailgun') {
            $this->send($view, $data, $messageData);

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
            $this->send($view, $data, $messageData);

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
     * @param array $messageData
     * @param int $attempt
     */
    private function send($view, $data, $messageData, $attempt = 0)
    {
        try {
            Mail::send($view, $data, function ($message) use ($messageData) {
                $message->to(
                    $messageData['toEmail'],
                    isset($messageData['toName']) ? $messageData['toName'] : null
                );
                $message->from(
                    $messageData['fromEmail'],
                    isset($messageData['fromName']) ? $messageData['fromName'] : null
                );
                $message->subject($messageData['subject']);
                // Add headers if included
                if (isset($messageData['headers'])) {
                    foreach ($messageData['headers'] as $headerName => $headerValue) {
                        $message->getHeaders()->addTextHeader($headerName, $headerValue);
                    }
                }
            });
        } catch (Exception $e) {
            if ($e instanceof Swift_RfcComplianceException) {
                Log::error($e->getMessage());
            } elseif ($e instanceof Swift_TransportException) {
                if (strpos($e->getMessage(), 'unroutable domain') !== false) {
                    Log::warning($e->getMessage());
                } else {
                    if ($attempt < 4) {
                        sleep($attempt * 10 + 1);
                        $this->send($view, $data, $messageData, $attempt + 1);
                    } else {
                        throw $e;
                    }
                }
            } else {
                throw $e;
            }
        }
    }
}
