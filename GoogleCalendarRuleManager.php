<?php

require_once 'GoogleCalendarAbstractManager.php'; // Options page class

class GoogleCalendarRuleManager extends GoogleCalendarAbstractManager
{
  
    public function __construct()
    {
        
    }

    public function updateCalendarListTemplateList() {
        $emailsSave = get_option( 'wp-appointment-plugin_options' )['calendar_emails'];
        if($emailsSave == ""){
            $emails = array();
        }else{
            $emails = explode (',', $emailsSave);
            if(!$emails){
                $emails = array();
            }
        }
        $calendarId = $this->getCalandarId();
        $client = $this->getClient();
        $service = new Google_Service_Calendar($client);
        $this->cleanEmailMissing($service, $emails, $calendarId);
        if(count($emails) > 0){
            foreach ($emails as $email) {
                if(!$this->hasRole($service, $email, $calendarId)){
                    $this->addRole($service, $email, $calendarId);
                }
            }
        }
    }
    
    private function cleanEmailMissing($service, $emails, $calendarId){
        $acl = $service->acl->listAcl($calendarId);
        foreach ($acl->getItems() as $rule) {
            $found = false;
            
            if(strpos($rule->getScope()->getValue(), "@group.calendar.google.com") || strpos($rule->getScope()->getValue(), "gserviceaccount.com")){
                $found = true;
            }
            if(count($emails) > 0){
                foreach ($emails as $email) {
                    if($rule->getScope()->getValue() == $email){
                        $found = true;
                    }
                    
                }
            }
            if(!$found){
                $service->acl->delete($calendarId, $rule->getId());
            }
        }
        return false;
    }
    
    private function hasRole($service, $email, $calendarId){
        $acl = $service->acl->listAcl($calendarId);
        foreach ($acl->getItems() as $rule) {
            if($rule->getScope()->getValue() == $email){
                return true;
            }
        }
        return false;
    }
    
    private function addRole($service, $email, $calendarId){
        $rule = new Google_Service_Calendar_AclRule();
        $rule->setRole("writer");
        $scope = new Google_Service_Calendar_AclRuleScope();
        $scope->setType("user");
        $scope->setValue($email);
        $rule->setScope($scope);
    
        $createdRule = $service->acl->insert($calendarId, $rule);
    }

}

