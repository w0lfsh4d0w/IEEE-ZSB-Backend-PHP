<?php

class AppleDevice
{
    public $ram;
    public $inch;
    public $space;
    // constant 
    const CHIP = 'A9';
    const MINCHAR = 5;

    public function setOwnerName($name)
    {
        if (strlen($name) < self::MINCHAR) {
            echo "Your name must be grater than 3  char " . "<br>";
        } else {
            echo "Your name has been sent ";
        }
    }
}
$iphone=new AppleDevice();
$iphone->setOwnerName('mooooooooooooo');

// pseudo variable $this   refer to object properties 
