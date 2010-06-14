<?php
require 'hCal.php';

$cal_obj = new hCal(file_get_contents('basic3.ics'));

$events = $cal_obj->getEvents();

$event_by_time = array();
foreach($events as $event)
{
  $time = $event->getTime();

  $event_by_time[$time[0]] = array('name'=>$event->getTitle(), 'time'=>$time, 'description' => $event->getDescription()); 
}
krsort($event_by_time);

var_dump($event_by_time);
