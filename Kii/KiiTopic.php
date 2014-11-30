<?php

class KiiTopic {

    var $name = null;
    
    public static function KiiTopicWithName($name) {
        $topic = new KiiTopic();
        $topic->name = $name;
        return $topic;
    }

    public function subscribeCurrentUser() {

        $request = new KiiRequest();
        $request->path = "/topics/".$this->name."/push/subscriptions/users";
        $request->method = "POST";
        
        // make the request
        return $request->execute();
    }

    public function create() {

        $request = new KiiRequest();
        $request->path = "/topics/".$this->name;
        $request->method = "PUT";
        
        // make the request
        return $request->execute();
    }
    
};

