<?php

class Swift_SmartSenderTransport extends Swift_Transport_SmartSenderTransport
{
    private $key;
    private $secret;
    private $endpoint;

    private $response;

    public function __construct($key = null, $secret = null, $endpoint = 'https://api.sndmart.com/send')
    {
        $this->key      = $key;
        $this->secret   = $secret;
        $this->endpoint = $endpoint;
    }

    public static function newInstance($key, $secret)
    {
        return new self($key, $secret);
    }

    public function setKey($val)
    {
        $this->key = $val;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setSecret($val)
    {
        $this->secret = $val;
    }

    public function getSecret()
    {
        return $this->secret;
    }

    public function setEndpoint($val)
    {
        $this->endpoint = $val;
    }

    public function getEndpoint()
    {
        return $this->endpoint;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function send(Swift_Mime_Message $message, &$failedRecipients = null)
    {
        $data = [
            'key'     => $this->key,
            'secret'  => $this->secret,
            'message' => [
                'from_email' => key($message->getFrom()),
                'reply_to'   => array_map(function($name, $email) {
                    return compact('name', 'email');
                }, $message->getReplyTo(), array_keys($message->getReplyTo())),
                'subject'    => $message->getSubject(),
                'to'         => array_map(function($name, $email) {
                    return compact('name', 'email');
                }, $message->getTo(), array_keys($message->getTo())),
                'text'       => $message->getBody(),
                'html'       => $message->getBody(),
            ],
        ];

        $this->response = $this->_doRequest($data);
    }

    protected function _doRequest(array $data)
    {
        $data = json_encode($data);
        $ch = curl_init($this->endpoint);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch, CURLOPT_HTTPHEADER,
            [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            ]
        );

        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}