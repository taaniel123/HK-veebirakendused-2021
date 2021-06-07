<?php

class Test {
    private $secret = 7;
    public $nonsecret = 3;
    private $received_secret;

    function __construct($received) {
        echo "Klass on laetud!";
        $this->received_secret = $received;
        echo "Saabunud salajane number on " .$this->received_secret .". ";
        $this->multiply();
    }

    function __destruct() {
        echo "Klass lõpetas";
    }

    public function reveal() {
        echo "Täiesti salajane number on " .$this->secret .". ";
    }

    private function multiply() {
        echo " Korrutis on: " .$this->secret * $this->nonsecret * $this->received_secret;
    }
}


























