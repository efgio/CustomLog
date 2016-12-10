<?php

require_once("CustomLog.php");


/******************** SAVE CUSTOM LOG TO FILE ********************/

$log = new CustomLog(); 

$log->startToFile(); // Comment out to force appending log messages to file on each run

$log->writeToFile("Start"); // A simple Start label, difference equals 0 since is the first message to log

sleep(1); // This is a simple test, write YOUR OWN PIECE OF CODE here

$log->writeToFile("Step1 completed", $calc_difference = true);

sleep(2);

$log->writeToFile("Step2 completed", $calc_difference = true);

sleep(3);

$log->writeToFile("Step3 completed", $calc_difference = true);

$log->writeToFile("End"); // A simple End label

$log->stopToFile(); // Comment out to force appending log messages to file on each run



/******************** SAVE CUSTOM LOG TO DATABASE ********************/

$log->startToDB("","root","","test");

$log->clearDB(); // Comment out to force appending log messages to database table on each run

$log->writeToDB("Start"); // A simple Start label, difference equals 0 since is the first message to log

sleep(1); // This is a simple test, write YOUR OWN PIECE OF CODE here

$log->writeToDB("Step1 completed", $calc_difference = true);

sleep(2);

$log->writeToDB("Step2 completed", $calc_difference = true);

sleep(3);

$log->writeToDB("Step3 completed", $calc_difference = true);

$log->writeToDB("End"); // A simple End label

$log->stopToDB();

?>
