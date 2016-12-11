<?php
require_once("CustomLog.php");


/******************** SAVE CUSTOM LOG TO FILE ********************/

$log = new CustomLog("custom.log"); // new CustomLog(); or change target with input string

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

$logdb = new CustomLog(); // new CustomLog(); or change target with: new CustomLog("another.log");

$logdb->startToDB("","root","","test"); // provide the right credentials

$logdb->clearDB(); // Comment out to force appending log messages to database table on each run

$logdb->writeToDB("Start"); // A simple Start label, difference equals 0 since is the first message to log

sleep(1); // This is a simple test, write YOUR OWN PIECE OF CODE here

$logdb->writeToDB("Step1 completed", $calc_difference = true);

sleep(2);

$logdb->writeToDB("Step2 completed", $calc_difference = true);

sleep(3);

$logdb->writeToDB("Step3 completed", $calc_difference = true);

$logdb->writeToDB("End"); // A simple End label

require_once("CustomLog1.php");

$logdb->stopToDB();

?>
