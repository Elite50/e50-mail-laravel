<?php
namespace Elite50\E50MailLaravel;

use Illuminate\Support\Facades\Facade;

class E50MailFacade extends Facade
{
    public static function getFacadeAccessor() { return 'e50mail'; }
}
