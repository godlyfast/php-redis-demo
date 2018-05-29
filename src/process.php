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
    switch ($this->payload->event) {
      case 'click':
        return true;
      case 'view':
        return true;
      case 'play':
        return true;
      default:
        $this->error = [
          'code' => '400',
          'msg' => 'event should be click, view, play'
        ];
        return false;
        break;
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
    $r = [
      'status' => true
    ];
    return [
      'body' => json_encode($this->error ?? $r)
    ];
  }
}
