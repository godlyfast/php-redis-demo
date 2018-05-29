<?php
class Process
{
  private $payload;
  private $redis;
  private $response;
  public $error;

  function __construct($redis, $body)
  {
    $this->redis = $redis;
    $this->payload = json_decode($body);
    if ($this->validateReq()) {
      $this->process();
    }
  }

  private function validateReq()
  {
    if (!isset($this->payload->country)) {
      $this->error = [
        'code' => '400',
        'msg' => 'country is required'
      ];
      return false;
    }
    if (!isset($this->payload->event)) {
      $this->error = [
        'code' => '400',
        'msg' => 'event is required'
      ];
      return false;
    }
    return true;
  }

  private function process()
  {
    $stat = date('Y-m-d') . ' '
    . $this->payload->country . ' '
    . $this->payload->event;
    if ($this->redis->exists($stat))
    {
      $this->redis->incr($stat);
    } else {
      $this->redis->set($stat, 1);
    }

    $this->redis->zIncrBy('countries', 1, $this->payload->country);
  }

  public function response()
  {
    $r = [];
    // foreach ($this->redis->keys('*') as $key) {
      // $r[$key] = $this->redis->get($key);
    // };
    // $r['scores'] = $this->redis->zRevRange('countries', 0, 4, true);
    return [
      'body' => json_encode($this->error ?? $r)
    ];
  }
}
