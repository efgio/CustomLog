<?php 
class CustomLog { 
    	
	private $logFile = null;
	private $conn = null;
	private $logFileStr = null;
	private $lastLogTime = null;
		
	/** 
	 * Constructor
	 *
	 * @param filename: string, default value is custom.log, free to provide another	 
	 */
	public function __construct($filename = "custom.log") {
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
	}
	
	/**	 
	 * Adds new log message with automatic time stamp, to the log file and optionally calculates gap from previous log message.
	 *
	 * @param string msg Give an appropriate message to Log. Time stamp will be added automatically.
	 * @param bool calc_difference Set to true to calculate the time gap from the previous log message.
	 * @return void
	 */
	public function writeToFile($msg, $calc_difference = false) {			
		$now = date("d-m-Y H:i:s");		
		if ($calc_difference) {
			file_put_contents($this->logFileStr, $now." (".abs((strtotime($now) - strtotime($this->lastLogTime)))." secs): ".$msg."\n", FILE_APPEND);
		} else {
			file_put_contents($this->logFileStr, $now.": ".$msg."\n", FILE_APPEND);
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
		$now = date("d-m-Y H:i:s");		
		if ($calc_difference) {
			$this->insertToDB($logtable, $now." (".abs((strtotime($now) - strtotime($this->lastLogTime)))." secs): ".$msg."\n");
		} else {
			$this->insertToDB($logtable, $now.": ".$msg."\n");
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
			echo "Error: " . $sql . "<br>" . $this->conn->error;
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
	 *	  	
	 * @return void
	 */
	public function stopToDB() {
		$this->conn->close();
	}
	
	/**	 
	 * Inserts a log message to the database table provided
	 *	  	
	 * @param string logtable Default value is log.
	 * @param string msg The log message
	 * @return void
	 */
	public function insertToDB($logtable = "log", $msg){
		$sql = "INSERT INTO log (log_descr) VALUES ('".$msg."')";
		if (!$this->conn->query($sql) === TRUE) {			
			echo "Error: " . $sql . "<br>" . $this->conn->error;
		}
	}
} 
?>