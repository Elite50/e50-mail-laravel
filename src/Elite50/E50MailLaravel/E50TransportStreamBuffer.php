<?php
namespace Elite50\E50MailLaravel;

use ReflectionProperty;
use Swift_Transport_StreamBuffer;

class E50TransportStreamBuffer extends Swift_Transport_StreamBuffer
{
    public function startTLS()
    {
        $ref = new ReflectionProperty("Swift_Transport_StreamBuffer", "_stream");
        $ref->setAccessible(true);

        return stream_socket_enable_crypto($ref->getValue($this), true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT);
    }
}
