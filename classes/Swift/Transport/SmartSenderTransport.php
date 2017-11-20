<?php

class Swift_Transport_SmartSenderTransport implements Swift_Transport
{
    private $_apiId;
    private $_secretKey;
    private $_endpoint;

    private $_debug = false;
    private $_response;

    protected $_eventDispatcher;

    public function __construct(Swift_Events_EventDispatcher $eventDispatcher)
    {
        $this->_eventDispatcher = $eventDispatcher;
    }

    public function setApiId($val)
    {
        $this->_apiId = $val;

        return $this;
    }

    public function getApiId()
    {
        return $this->_apiId;
    }

    public function setSecretKey($val)
    {
        $this->_secretKey = $val;

        return $this;
    }

    public function getSecretKey()
    {
        return $this->_secretKey;
    }

    public function setEndpoint($val)
    {
        $this->_endpoint = $val;

        return $this;
    }

    public function getEndpoint()
    {
        return $this->_endpoint;
    }

    public function setDebug($val)
    {
        $this->_debug = (bool) $val;

        return $this;
    }

    public function getResponse()
    {
        return $this->_response;
    }

    public function send(Swift_Mime_Message $message, &$failedRecipients = null)
    {
        $sent = 0;

        if ($event = $this->_eventDispatcher->createSendEvent($this, $message)) {
            $this->_eventDispatcher->dispatchEvent($event, 'beforeSendPerformed');

            if ($event->bubbleCancelled()) {
                return 0;
            }
        }

        $from    = (array) $message->getFrom();
        $to      = (array) $message->getTo();
        $replyTo = (array) $message->getReplyTo();

        reset($from);

        $data = [
            'key'     => $this->_apiId,
            'secret'  => $this->_secretKey,
            'message' => [
                'from_email' => key($from),
                'from_name'  => $from[key($from)],
                'reply_to'   => array_map(function($name, $email) {
                    return compact('name', 'email');
                }, $replyTo, array_keys($replyTo)),
                'subject'    => $message->getSubject(),
                'to'         => array_map(function($name, $email) {
                    return compact('name', 'email');
                }, $to, array_keys($to)),
                'text'       => $message->getBody(),
                'html'       => $message->getBody(),
            ],
        ];

        $this->_response = $this->_doRequest($data);

        if ($this->_debug) {
            echo "--- service response ---", PHP_EOL;
            echo print_r($this->_response);
            echo "--- service response ---", PHP_EOL;
        }

        $success = ($this->_response->result == 1);

        if ($event) {
            $event->setResult($success ? Swift_Events_SendEvent::RESULT_SUCCESS : Swift_Events_SendEvent::RESULT_FAILED);
            $this->_eventDispatcher->dispatchEvent($event, 'sendPerformed');
        }

        if ($success) {
            $sent = count($to);
        }

        return $sent;
    }

    protected function _doRequest(array $data)
    {
        $data = json_encode($data);
        $ch = curl_init($this->_endpoint);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, $this->_debug);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ]);

        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result);
    }

    public function registerPlugin(Swift_Events_EventListener $plugin)
    {
        $this->_eventDispatcher->bindEventListener($plugin);
    }

    public function isStarted() {}
    public function start() {}
    public function stop() {}
}

Swift_DependencyContainer::getInstance()
    ->register('transport.sndmart')
    ->withDependencies(['transport.eventdispatcher']);
