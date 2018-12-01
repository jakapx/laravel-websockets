<?php

namespace BeyondCode\LaravelWebSockets\WebSockets\Messages;

use BeyondCode\LaravelWebSockets\Dashboard\DashboardLogger;
use BeyondCode\LaravelWebSockets\Events\ClientMessageSent;
use BeyondCode\LaravelWebSockets\WebSockets\Channels\ChannelManager;
use Ratchet\ConnectionInterface;
use stdClass;

class PusherClientMessage implements PusherMessage
{
    /** \stdClass */
    protected $payload;

    /** @var \Ratchet\ConnectionInterface */
    protected $connection;

    /** @var \BeyondCode\LaravelWebSockets\WebSockets\Channels\ChannelManager */
    protected $channelManager;

    public function __construct(stdClass $payload, ConnectionInterface $connection, ChannelManager $channelManager)
    {
        $this->payload = $payload;

        $this->connection = $connection;

        $this->channelManager = $channelManager;
    }

    public function respond()
    {
        if (!starts_with($this->payload->event, 'client-')) {
            return;
        }

        DashboardLogger::clientMessage($this->connection, $this->payload);

        $channel = $this->channelManager->find($this->connection->app->id, $this->payload->channel);

        optional($channel)->broadcast($this->payload);
    }
}