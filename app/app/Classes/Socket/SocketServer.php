<?php

namespace App\Classes\Socket;

use App\Classes\Socket\Base\BaseSocket;

Class SocketServer extends BaseSocket
{
    protected $clients;
    protected $connections;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(\Ratchet\ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        $this->connections[$conn->resourceId] = ['resourceId' => $conn->resourceId, 'authId' => ''];
        echo "New connection ({$conn->resourceId})\n";
    }

    public function onMessage(\Ratchet\ConnectionInterface $conn, $msg)
    {
        $numRecv = count($this->clients) - 1;

        $data = json_decode($msg, true);
        if(isset($data['auth']))
            $this->connections[$conn->resourceId]['authId'] = $data['auth'];
        if(isset($data['message']))
            $msg = $data['message'];

        echo sprintf('Connection %d sending message "%s" to %d other connection'."\n",
            $conn->resourceId, $msg, $numRecv
        );


        if(isset($data['auth']) && !isset($data['message']))
            $msg = '';
            //$msg = 'В систему вошёл новый пользователь';
        if($msg)
            foreach($this->clients as $itemclient)
            {
                if(
                    $conn !== $itemclient &&
                    $this->connections[$itemclient->resourceId]['authId'] !== $this->connections[$conn->resourceId]['authId']
                )
                    $itemclient->send($msg);
            }
    }

    public function onClose(\Ratchet\ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        unset($this->connections[$conn->resourceId]);
        echo "Connection {$conn->resourceId} has disconnected \n";
    }

    public function onError(\Ratchet\ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occured: {$e->getMessage()} \n";
        $conn->close();
    }
}
