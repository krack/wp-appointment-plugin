<?php


define('SCOPES_APPOINTEMENT_PLUGIN', implode(' ', array(
	Google_Service_Calendar::CALENDAR) // CALENDAR_READONLY
));
putenv('GOOGLE_APPLICATION_CREDENTIALS='. __DIR__ .'/credentials/service-account.json');

class GoogleCalendarAbstractManager
{
  
    public function __construct()
    {
        
    }

    protected function getCalandarId(){
        $client = $this->getClient();
        $service = new Google_Service_Calendar($client);
        $calendarList = $service->calendarList->listCalendarList();
        // if not exist, create calendar
        if(count($calendarList->getItems()) == 0){
            $calendar = new Google_Service_Calendar_Calendar();
            $calendar->setSummary('Planning de Ongles et beauté');
            $calendar->setTimeZone('Europe/Paris');
            
            $calendarId = $service->calendars->insert($calendar);
        }else{
            $calendarId = $calendarList->getItems()[0]->getId();
        }
        return $calendarId;
    
    }
    
    
    /**
     * Returns an authorized API client.
     * @return Google_Client the authorized client object
     */
    protected function getClient() {
        $client = new Google_Client();
        $client->useApplicationDefaultCredentials();
        $client->setScopes(SCOPES_APPOINTEMENT_PLUGIN);
      
        return $client;
      }

}

