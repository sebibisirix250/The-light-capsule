<?php

if (session_status() === PHP_SESSION_NONE) { //SESSION ISOLATION VERIFICATION

    ini_set('session.cookie_httponly', 1); //RESTRICT JS ACCES

    ini_set('session.use_only_cookies', 1); //FORCE STRCIT COOKIE DELIVERY

    ini_set('session.cookie_samesite', 'Lax'); //CROSS-SITE REQUEST DEFENSE 

    session_start(); //MEMORY INITIALIZER
}

//FIRST CONTACT TIMESTAMP 
if (!isset($_SESSION['last_regeneration'])) {
    session_regenerate_id(true);                //IDENTITY CARD PURGE
    $_SESSION['last_regeneration'] = time();
} else {
    $interval = 60 * 30; //30 MINUTES - IDENITY ROTATION COUNTDOWN
    if (time() - $_SESSION['last_regeneration'] >= $interval) {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}
