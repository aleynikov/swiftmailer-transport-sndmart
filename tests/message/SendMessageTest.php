<?php
use PHPUnit\Framework\TestCase;

class SendMessageTest extends TestCase
{
    protected $mailer;

    public function setUp()
    {
        $transport = Swift_SmartSenderTransport::newInstance(
            $GLOBALS['API_ID'],
            $GLOBALS['SECRET_KEY']);

        $this->mailer = Swift_Mailer::newInstance($transport);
    }

    public function testSendMessageAsSuccess()
    {
        $message = Swift_Message::newInstance()
            ->setFrom('from@example.com')
            ->setTo('to@example.com')
            ->setSubject('Valid e-mail from')
            ->setBody('this a body');

        $this->assertEquals(1, $this->mailer->send($message));
    }

    public function testSendMessageAsFailure()
    {
        $message = Swift_Message::newInstance()
            ->setFrom('invalid@address')
            ->setTo('to@eample.com')
            ->setSubject('Invalid e-mail from')
            ->setBody('this is a body');

        $this->assertEquals(0, $this->mailer->send($message));
    }
}
