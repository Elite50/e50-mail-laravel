<?php
namespace Elite50\E50MailLaravel;

use Illuminate\Support\ServiceProvider;

class E50MailServiceProvider extends ServiceProvider {
    public function register() {
        $app = $this->app;

        $app->bind('e50mail', function() {
            return new E50Mail;
        });
    }
}
