<?php

class SmartSenderTransport extends Swift_Transport_SmartSenderTransport
{
    public function isStarted()
    {
        // TODO: Implement isStarted() method.
    }

    public function start()
    {
        // TODO: Implement start() method.
    }

    public function stop()
    {
        // TODO: Implement stop() method.
    }

    public function send(Swift_Mime_Message $message, &$failedRecipients = null)
    {
        echo 'send message via smartsender transport...';
    }

    public function registerPlugin(Swift_Events_EventListener $plugin)
    {
        // TODO: Implement registerPlugin() method.
    }

}