<?php

session_start();
start();

function start(){
    session_destroy();
    header('location: index.html');

}

?>