<?php
class Report {
  private $redis;

  public function __construct($redis) {
    $this->redis = $redis;
    $this->process();
  }

  private function getLastNDays($days, $format = 'Y-m-d'){
      $m = date("m"); $de= date("d"); $y= date("Y");
      $dateArray = array();
      for($i=0; $i<=$days-1; $i++){
          $dateArray[] = date($format, mktime(0,0,0,$m,($de-$i),$y));
      }
      return array_reverse($dateArray);
  }

  private function process() {
    $r = [];
    foreach ($this->redis->zRevRange('countries', 0, 4, true) as $country => $score) {
      $r[$country] = [
        'click' => 0,
        'play' => 0,
        'view' => 0
      ];
      foreach ($this->getLastNDays(7) as $day) {
        foreach (['click', 'play', 'view'] as $event) {
          $r[$country][$event] += (int)$this->redis->get($day.' '.$country.' '.$event);
        }
      }
    }
    return $r;
  }

  public function response()
  {
    return [
      'body' => json_encode($this->process())
    ];
  }
}
