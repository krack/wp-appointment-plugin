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
        $optParams = array(
            'timeMin' => date('c')
          );
        $events = $service->events->listEvents($calendarId, $optParams);
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
    public function createAppointment($selectedService, $order){

        $startDate=new DateTime($order['date']);
        $endDate=new DateTime($order['date']);
        $endDate->add(new DateInterval('PT'.$selectedService->during.'M'));
    
    
    
        $startCalendarDateTime = new \Google_Service_Calendar_EventDateTime();
        $startCalendarDateTime->setDateTime($startDate->format(\DateTime::RFC3339));
    
        $endCalendarDateTime = new \Google_Service_Calendar_EventDateTime();
        $endCalendarDateTime->setDateTime($endDate->format(\DateTime::RFC3339));
    
        $event = new Google_Service_Calendar_Event(array(
            'summary' => $order['name']." : ".$selectedService->name,
            'description' => 'RDV de '.$order['name']." pour ".$selectedService->name.". Numéro de téléphone : ".$order['phone'],
            'start' => $startCalendarDateTime,
            'end' => $endCalendarDateTime
        ));
    
        $calendarId = $this->getCalandarId();
        $client = $this->getClient();
        $service = new Google_Service_Calendar($client);
        $event = $service->events->insert($calendarId, $event);
    }

    public function existEventInSameTime($selectedService, $order){
        $startDate=new DateTime($order['date']);
        $endDate=new DateTime($order['date']);
        $endDate->add(new DateInterval('PT'.$selectedService->during.'M'));

        $startCalendarDateTime = new \Google_Service_Calendar_EventDateTime();
        $startCalendarDateTime->setDateTime($startDate->format(\DateTime::RFC3339));

        $endCalendarDateTime = new \Google_Service_Calendar_EventDateTime();
        $endCalendarDateTime->setDateTime($endDate->format(\DateTime::RFC3339));

        $client = $this->getClient();
        $service = new Google_Service_Calendar($client);
        $calendarId = $this->getCalandarId();
        $optParams = array(
            'timeMin' => $startCalendarDateTime->getDateTime(),
            'timeMax'=> $endCalendarDateTime->getDateTime()
          );
        $events = $service->events->listEvents($calendarId, $optParams);
        return !empty($events->getItems());
    }


}

