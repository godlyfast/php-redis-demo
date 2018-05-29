<?php
require '../src/process.php';
require '../src/report.php';

$redis = new Redis();
$redis->connect('redis', 6379);
$err = [];
$res;
$reqAccept = $_SERVER['HTTP_ACCEPT'];

if ($_SERVER['REQUEST_URI'] === '/data') {
  switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
      $body = file_get_contents('php://input');
      $process = new Process($redis, $body);
      $resp = $process->response();
      $err = $process->error;
      break;
    case 'GET':
      $report = new Report($redis);
      $resp = $report->response($reqAccept);
      $err = $report->error;
      break;
    default:
      $err['code'] = 400;
      $resp = ['body' => '{"error": "Method not allowed for this request"}'];
      break;
  }
} else {
  $err['code'] = 404;
  $resp = ['body' => '404'];
}

header($req['Content-Type'] ?? 'Content-Type: application/json');
http_response_code($err ? $err['code'] : 200);
echo $resp['body'];
