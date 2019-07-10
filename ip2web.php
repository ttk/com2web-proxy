<?php

require __DIR__ . '/vendor/autoload.php';

try {
  $tcp_port = 8081;
  $ws_port = 8080;

  if(isset($argv[1])) {
    if($argv[1] == '-h') throw new Exception("Help");
    else if(preg_match('#^\d+$#',$argv[1],$match)) $tcp_port = intval($match[0]);
    else throw new Exception("Invalid specified TCP port");
  }

  if(isset($argv[2])) {
    if(preg_match('#^\d+$#',$argv[2],$match)) $tcp_port = intval($match[0]);
    else throw new Exception("Invalid specified WebSocket port");
  }

  $loop = React\EventLoop\Factory::create();
  $pusher = new MyApp\Pusher;

  $server = stream_socket_server("tcp://127.0.0.1:$tcp_port");
  if (!$server) throw new Exception("Can't open port $tcp_port for reading");

  if (!stream_set_blocking($server, false)) throw new Exception("Can't set socket to non-blocking mode");

  $loop->addReadStream($server, function ($server) use ($loop, $pusher) {
    $conn = stream_socket_accept($server);

    print "ACCEPTED com2ip CONNECTION\n";

    $loop->addReadStream($conn, function ($conn) use ($loop, $pusher) {
      $data = fread($conn, 1);
      if (!$data) {
        print "CLOSED com2ip CONNECTION\n";
        fclose($conn);
        $loop->removeReadStream($conn);
      }
      else {
        $pusher->onComMessage($data);
      }
    });
  });


  // Run the server application through the WebSocket protocol on port 8080
  $app = new Ratchet\App('127.0.0.1', $ws_port, '127.0.0.1', $loop);
  $app->route('/com1', $pusher, array('*'));
  $app->route('/echo', new Ratchet\Server\EchoServer, array('*'));

  print "Listening for CONNECTIONS\n";
  $app->run();
}
catch(Exception $e) {
  print "Error: " . $e->getMessage() . "\n";
  print "Usage ip2web TCPPORT WSPORT\n";
  print "  TCPPORT - The tcp port of the incoming data stream (default: 8081)\n";
  print "  WSPORT  - The tcp port of the WebSocket server (default: 8080)\n";
}