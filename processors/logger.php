<?php

	class KLogger
	{
		
		const DEBUG 	= 1;	// Most Verbose
		const INFO 		= 2;	// ...
		const WARN 		= 3;	// ...
		const ERROR 	= 4;	// ...
		const FATAL 	= 5;	// Least Verbose
		const OFF 		= 6;	// Nothing at all.
		
		const LOG_OPEN 		= 1;
		const OPEN_FAILED 	= 2;
		const LOG_CLOSED 	= 3;
		
		/* Public members: Not so much of an example of encapsulation, but that's okay. */
		public $Log_Status 	= KLogger::LOG_CLOSED;
		public $DateFormat	= "Y-m-d G:i:s";
		public $MessageQueue;
	
		private $log_file;
		private $priority = KLogger::INFO;
		
		private $file_handle;
		
		public function __construct( $filepath , $priority )
		{
			if ( $priority == KLogger::OFF ) return;
			
			$this->log_file = $filepath;
			$this->MessageQueue = array();
			$this->priority = $priority;
			
			if ( file_exists( $this->log_file ) )
			{
				if ( !is_writable($this->log_file) )
				{
					$this->Log_Status = KLogger::OPEN_FAILED;
					$this->MessageQueue[] = "The file exists, but could not be opened for writing. Check that appropriate permissions have been set.";
					return;
				}
			}
			
			if ( $this->file_handle = fopen( $this->log_file , "a" ) )
			{
				$this->Log_Status = KLogger::LOG_OPEN;
				$this->MessageQueue[] = "The log file was opened successfully.";
			}
			else
			{
				$this->Log_Status = KLogger::OPEN_FAILED;
				$this->MessageQueue[] = "The file could not be opened. Check permissions.";
			}
			
			return;
		}
		
		public function __destruct()
		{
			if ( $this->file_handle )
				fclose( $this->file_handle );
		}
		
		public function logInfo($line)
		{
			$this->Log( $line , KLogger::INFO );
		}
		
		public function logDebug($line)
		{
			$this->Log( $line , KLogger::DEBUG );
		}
		
		public function logWarn($line)
		{
			$this->Log( $line , KLogger::WARN );	
		}
		
		public function logError($line)
		{
			$this->Log( $line , KLogger::ERROR );		
		}

		public function logFatal($line)
		{
			$this->Log( $line , KLogger::FATAL );
		}
		
		public function log($line, $priority)
		{
			if ( $this->priority <= $priority )
			{
				$status = $this->getTimeLine( $priority );
				$this->WriteFreeFormLine ( "$status $line \n" );
			}
		}
		
		public function WriteFreeFormLine( $line )
		{
			if ( $this->Log_Status == KLogger::LOG_OPEN && $this->priority != KLogger::OFF )
			{
			    if (fwrite( $this->file_handle , $line ) === false) {
			        $this->MessageQueue[] = "The file could not be written to. Check that appropriate permissions have been set.";
			    }
			}
		}
		
		private function getTimeLine( $level )
		{
			$time = date( $this->DateFormat );
		
			switch( $level )
			{
				case KLogger::INFO:
					return "$time - INFO  -->";
				case KLogger::WARN:
					return "$time - WARN  -->";				
				case KLogger::DEBUG:
					return "$time - DEBUG -->";				
				case KLogger::ERROR:
					return "$time - ERROR -->";
				case KLogger::FATAL:
					return "$time - FATAL -->";
				default:
					return "$time - LOG   -->";
			}
		}
		
	}


?>