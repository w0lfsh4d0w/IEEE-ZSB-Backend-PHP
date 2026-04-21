<?php

// cannot be take object from abstract class 

// pseudo variable $this   refer to object properties 

// Polymorphism 
interface mobile
{
    public function pressHome();
}

class Iphone implements mobile
{
    public function pressHome() {

    echo " This is Iphone Mobile " ;
    }
}

class Sony implements mobile
{
    public function pressHome() {

    "This is fucking mobile " ;
    
    }
}
