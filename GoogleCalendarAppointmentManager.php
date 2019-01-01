<?php

require_once 'GoogleCalendarAbstractManager.php'; // Options page class

class GoogleCalendarAppointmentManager extends GoogleCalendarAbstractManager
{
  
    public function __construct()
    {
        
    }

    public function getAppointments(){
        $client = $this->getClient();
        $service = new Google_Service_Calendar($client);
        $calendarId = $this->getCalandarId();
        $events = $service->events->listEvents($calendarId);
        $data = array();
        foreach ($events->getItems() as $event) {
            array_push($data, array(
                'start' =>(new DateTime($event->start->dateTime))->format(DateTime::ISO8601),
                'end' =>(new DateTime($event->end->dateTime))->format(DateTime::ISO8601),
                'free' => ($event->summary == $GLOBALS["FREE_APPOINTMENT"])
            ));
        }
    
        return $data;
    }


}

