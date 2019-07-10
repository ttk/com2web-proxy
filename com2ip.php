<?php

try {

  if(count($argv)<2) throw new Exception("Invalid number of arguments");

  if(preg_match('#^(?:COM)?(\d+)$#',$argv[1],$match)) $com = intval($match[1]);
  else throw new Exception("Invalid COM port");

  $tcp_port = 8081;
  if(isset($argv[2])) {
    if (preg_match('#^\d+$#', $argv[2], $match)) $tcp_port = intval($match[0]);
    else throw new Exception("Invalid specified TCP port");
  }

  system("mode COM{$com} BAUD=9600 PARITY=N DATA=8 STOP=1");

  $comPath = "\\\\.\\COM{$com}";

  $file = dio_open($comPath, O_RDWR);
  if (!$file) throw new Exception("Can't open COM$com port");

  $fp = stream_socket_client("tcp://127.0.0.1:$tcp_port");
  if(!$fp) throw new Exception("Can't open TCP port $tcp_port for writing");

  print "All ports OPEN.  Proxying data.  Press CTRL-C to quit.\n";

  while (true) {
    $data = dio_read($file,1);
    if ($data===false) break;
    $data = preg_replace('#\s#','',$data);
    if($data == '0' || $data == '') continue;  // Filter out 0, since we aren't interested in those
    fwrite($fp, $data);
    print $data;
  }

  fclose($file);
  fclose($fp);
}
catch(Exception $e) {
  print "Error: " . $e->getMessage() . "\n";
  print "Usage: com2ip COMPORT IPPORT\n";
  print "  COMPORT - The windows COM port to open.  Format: integer or COM#\n";
  print "  IPPORT  - The socket tcp port to relay data to.  This port must be already listening for connections (default: 8081)\n";
}