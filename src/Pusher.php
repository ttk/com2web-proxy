<?php


namespace MyApp;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Pusher implements MessageComponentInterface {
  /** @var \SplObjectStorage  */
  protected $clients;

  public function __construct() {
    $this->clients = new \SplObjectStorage;
  }

  public function onOpen(ConnectionInterface $conn) {
    $this->clients->attach($conn);
    print "Client connected\n";
  }

  public function onMessage(ConnectionInterface $from, $msg) {
    /*foreach ($this->clients as $client) {
      if ($from != $client) {
        $client->send($msg);
      }
    }*/
    print "WebSocket MSG: $msg\n";
  }

  public function onComMessage($data) {
    $this->broadcast($data);
  }

  public function onClose(ConnectionInterface $conn) {
    $this->clients->detach($conn);
  }

  public function onError(ConnectionInterface $conn, \Exception $e) {
    $conn->close();
  }

  private function broadcast($msg) {
    print "BROADCASTING: $msg\n";
    foreach ($this->clients as $client) {
      $client->send($msg);
    }
  }
}