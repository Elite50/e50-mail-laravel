<?php
namespace Elite50\E50MailLaravel\Facades;

use Illuminate\Support\Facades\Facade;

class E50Mail extends Facade
{
    public static function getFacadeAccessor() { return 'e50mail'; }
}
