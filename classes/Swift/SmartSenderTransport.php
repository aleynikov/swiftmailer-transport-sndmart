<?php

/**
 * Sends Messages over Service SmartSender (https://blog.sndmart.com/doc/api-documentation/).
 *
 * @author Maxim Aleynikov
 *
 */
class Swift_SmartSenderTransport extends Swift_Transport_SmartSenderTransport
{
    public function __construct($apiId = null, $secretKey = null, $endpoint = 'https://api.sndmart.com/send')
    {
        call_user_func_array(
            [$this, 'Swift_Transport_SmartSenderTransport::__construct'],
            Swift_DependencyContainer::getInstance()
                ->createDependenciesFor('transport.sndmart')
        );

        $this->setApiId($apiId);
        $this->setSecretKey($secretKey);
        $this->setEndpoint($endpoint);
    }

    public static function newInstance($apiId, $secretKey)
    {
        return new self($apiId, $secretKey);
    }
}