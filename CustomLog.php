<?php
/**
 * This class is implements a custom logging system, which allows keep log to both file and database
 */
class CustomLog {

	private $logFile = null;
	private $conn = null;
	private $logFileStr = null;
	private $lastLogTime = null;
  private $logtable = null;

	/**
	 * Constructor
	 *
	 * @param filename: string, default value is custom.log, free to provide another
	 */
	public function __construct($filename = "custom.log") {
    error_reporting(E_ALL); // or 0
    ini_set("log_errors", 1);
    ini_set("error_log", $filename);

		$this->lastLogTime = date("Y-m-d H:i:s");
		if ($filename != null) {
			$this->logFileStr = $filename;
		}
	}

	/**
	 * Open file to disk with write privileges
	 * Comment out in order to append new log messages instead of cleaning the file on every run
	 *
	 * @return void
	 */
	public function startToFile() {
		$this->logfile = fopen($this->logFileStr, "w") or die("Unable to open file!");
	}

	/**
	 * Connect to Database providing the appropriate credentials, before logging.
   * Change error handler to allow write access to database for Php errors
   * Error reporting set to inactive in order to hide messages from file log and the screen
	 *
	 * @param servername: string, default value is localhost
	 * @param username: string
	 * @param password: string
	 * @param database: string
	 * @return void
	 */
	public function startToDB($servername = "localhost", $username, $password, $database) {
		$this->conn = new mysqli($servername, $username, $password, $database);
		if ($this->conn->connect_error) {
			die("Connection failed: " . $this->conn->connect_error);
		}
    set_error_handler(array($this, 'customErrorHandler'));
    error_reporting(0);
	}

	/**
	 * Adds new log message with automatic time stamp, to the log file and optionally calculates gap from previous log message.
	 *
	 * @param string msg Give an appropriate message to Log. Time stamp will be added automatically.
	 * @param bool calc_difference Set to true to calculate the time gap from the previous log message.
	 * @return void
	 */
	public function writeToFile($msg, $calc_difference = false) {
		$now = date("d-M-Y H:i:s");
		if ($calc_difference) {
      error_log("(".abs((strtotime($now) - strtotime($this->lastLogTime)))." secs): ".$msg);
		} else {
      error_log($msg);
		}
		$this->lastLogTime = $now;
	}

	/**
	 * Adds new log message with automatic time stamp, to the log table in database.
	 *
	 * @param string msg Give an appropriate message to Log. Time stamp will be added automatically.
	 * @param bool calc_difference Set to true to calculate the time gap from the previous log message.
	 * @param string logtable Default value is log
	 * @return void
	 */
	public function writeToDB($msg, $calc_difference = false, $logtable = "log") {
		$now = date("d-M-Y H:i:s");
    if ($logtable != null) {
      $this->logtable = $logtable;
    }

    if ($calc_difference) {
			$this->insertToDB($logtable, "[".$now."] (".abs((strtotime($now) - strtotime($this->lastLogTime)))." secs): ".$msg."\n");
		} else {
			$this->insertToDB($logtable, "[".$now."] ".$msg."\n");
		}

		$this->lastLogTime = $now;

	}

	/**
	 * Clears log file from all content
	 *
	 * @return void
	 */
	public function clearFile() {
		file_put_contents($this->logfile, "");
	}

	/**
	 * Clears the log table in database, especially before new logs are added
	 * In order to save log to another table, type: $log->clearLogDB("log_table");
	 *
	 * @param string logtable Default value is log.
	 * @return void
	 */
	public function clearDB($logtable = "log") {
		$sql = "TRUNCATE TABLE ".$logtable;
		if (!$this->conn->query($sql) === TRUE) {
      die("Error: " . $sql . "<br>" . $this->conn->error);
		}
	}

	/**
	 * Closes the open file pointer
	 * Comment out this line to append new log messages instead of cleaning the file on every run
	 *
	 * @return void
	 */
	public function stopToFile() {
		fclose($this->logfile);
	}

	/**
	 * Closes the connection to the database
   * Error handler will be restored to normal and reporting to ALL types
	 *
	 * @return void
	 */
	public function stopToDB() {
    restore_error_handler();
    error_reporting(E_ALL);
		$this->conn->close();
	}

	/**
	 * Inserts a log message to the database table provided
	 *
	 * @param string logtable Default value is log.
	 * @param string msg The log message
	 * @return void
	 */
	public function insertToDB($logtable = "log", $msg) {
		$sql = "INSERT INTO ".$logtable." (log_descr) VALUES ('".$msg."')";
		if (!$this->conn->query($sql) === TRUE) {
      die("Error: " . $sql . "<br>" . $this->conn->error);
		}
	}

  /**
	 * All Php errors are inserted to database instead of file throw new handler
	 *
	 * @param int errno Lever of error
	 * @param string errstr Contains the error message
   * @param string errfile Contains the filename the error happened
   * @param string errline Contains the line number the error happened
	 * @return void
	 */
  public function customErrorHandler($errno, $errstr, $errfile, $errline) {
    $now = date("d-M-Y H:i:s");
    $sql = "INSERT INTO ".$this->logtable." (log_descr) VALUES ('[".$now."] ".$errstr." in ".$errfile." on line ".$errline."')";
    if (!$this->conn->query($sql) === TRUE) {
      die("Error: " . $sql . "<br>" . $this->conn->error);
    }
    return true;
  }
}
?>
