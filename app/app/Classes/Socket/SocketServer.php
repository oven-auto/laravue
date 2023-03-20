<?php

namespace App\Classes\Socket;

use App\Classes\Socket\Base\BaseSocket;

Class SocketServer extends BaseSocket
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(\Ratchet\ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "New connection ({$conn->resourceId})\n";
    }

    public function onMessage(\Ratchet\ConnectionInterface $conn, $msg)
    {
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection'."\n",
            $conn->resourceId, $msg, $numRecv
        );
        foreach($this->clients as $itemclient)
        {
            if($conn !== $itemclient)
                $itemclient->send($msg);
        }
    }

    public function onClose(\Ratchet\ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected \n";
    }

    public function onError(\Ratchet\ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occured: {$e->getMessage()} \n";
        $conn->close();
    }
}
