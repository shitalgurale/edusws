<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

// 🔥 Handle Raw POST from Device Before Anything Else
//file_put_contents('face_device_test.log', date('Y-m-d H:i:s') . " - HIT\n", FILE_APPEND);
//file_put_contents('face_device_test.log', "HEADERS:\n" . print_r(getallheaders(), true), FILE_APPEND);
//file_put_contents('face_device_test.log', "BODY:\n" . file_get_contents('php://input') . "\n\n", FILE_APPEND);

	define("DB", "siliconcpanel_dbedusws");
    define("TBL_DEVICE", DB.".face_device");
	define("TBL_LOG", DB.".face_log_data");
	
    $dbh = new PDO('mysql:host=localhost;port=3306;dbname=siliconcpanel_dbedusws', 'siliconcpanel_edusws', 'edusws@123', array( PDO::ATTR_PERSISTENT => false));

	$header = apache_request_headers();	
    $command = null;
    $data = null;

    // Convert the headers array to a readable string format for writing to a file
    //$headerString = print_r($header, true);
	// Specify the file path where you want to save the headers
    //$filePath = 'debug_file.txt';
    // Write the headers to the file
    //file_put_contents($filePath, $headerString, FILE_APPEND);
    

    if ($header["X-Dev-Id"])
    {	
    	    //die("Not a Silicon Wireless Systems Face Device, No X-Dev-Id found in header");
    
    	$trans_data = new silicon_data;	
        $trans_data->appendfile = $header["X-Dev-Id"]."_tmp.dat";//".$header["trans_id"]."
    	
    	if (!$header["X-Blk-No"]) 
    	    $header["X-Blk-No"] = 0;
    
        if ($header["X-Blk-No"] == 0) //if the data in request from device not needed to append
        {
            //get the attendance data
        	$input_valus = $trans_data->get();
        
            // Write the values to the file
            //DeviceName �user_id� �verify_mode� �io_mode� �io_time� �log_image�
            //file_put_contents($filePath, print_r($input_valus, true), FILE_APPEND);
         	
        	$device_id = $header["X-Dev-Id"];
        	
        	switch ($header["X-Request-Code"]) 
        	{
            	case "realtime_glog" : 
            		insert_log_data($device_id, $input_valus);//!!
            		break;
            	case "realtime_enroll_data" : 
            		break;
            	case "receive_cmd" : 
                    set_device($device_id, $input_valus); // If ping received from new device, update it in DB
            		break;
            	default:
            		break;
        	}
        	
            if (file_exists($trans_data->appendfile)) 
                unlink($trans_data->appendfile);
        }
        else{ // append if the resonse for command from the device has data to be appened
            
                $trans_data->append($header["X-Blk-No"]);
        } 
        
    
        //respond to the device 
    	$trans_data->set($command, $data);
    	
    }

    class silicon_data
    {
        public $tmpfile = 'tmpfile.dat'; 
        public $appendfile = ''; 

        function append($blk_no) { 
			$input_handler = fopen('php://input', 'r'); 
			$temp = tmpfile(); 
			$realSize = stream_copy_to_stream($input_handler, $temp); 
			fclose($input_handler); 

			if ($blk_no == 1){
				$out_handler = fopen($this->appendfile, 'w'); 
			} 
			if ($blk_no > 1) $out_handler = fopen($this->appendfile, 'a'); 
			fseek($temp, 0, SEEK_SET); 
			stream_copy_to_stream($temp, $out_handler); 
			fclose($out_handler);
		}
    			
        function get() 
        { 
			$input_handler = fopen('php://input', 'r'); 
			$temp = tmpfile(); 
			$realSize = stream_copy_to_stream($input_handler, $temp); 
			fclose($input_handler); 

			$out_handler = fopen($this->tmpfile, 'w'); 
			fseek($temp, 0, SEEK_SET); 
			stream_copy_to_stream($temp, $out_handler); 
			fclose($out_handler);
			
			$contents = ""; 

			if (file_exists($this->appendfile))
			{
				$contents = file_get_contents($this->appendfile); 
			}
			$contents .= file_get_contents($this->tmpfile); 

			$i = 0;
			$j = -1;
			$input_valus = array();
			$tmp = array();
			while ( $i < strlen($contents) )
			{
				$j++;
				$blklen = substr($contents, $i, 4); 				$i += 4;
				$array = unpack("Lint", $blklen);		$len = $array["int"];

				$t = substr($contents, $i, $len);				$i += $len;
				if ($j>0){
					$tmp["BIN_".$j] = bin2hex($t);
				}else{
					$t = trim($t);
					$input_valus = json_decode($t, true);
				}
			}

			$input_valus = array_merge($input_valus, $tmp);
	
			return $input_valus;
        } 
        
        function set($command, $data) 
        {
        
        	if (!$command["response_code"]) 
        	    $command["response_code"] = "OK";
        
 			$fp = fopen($this->tmpfile, 'w');
 			if (count((array)$data) > 0)
 			{
    	    	$main_data = array();
    	    	$bin_data = array();
    	    	foreach($data as $key => $val)
    	    	{
    	    		if (substr($key, 0, 4) == "BIN_")
    					{
    					  $bin_data[$key] = $val;
    	    			continue;
    					} 
    					$main_data[$key] = $val;
    	    	}
	    
	 			$contents = json_encode($main_data); 
	 			$len = strlen($contents) + 1;
				$lenstr = pack("L",$len);
				$nulstr = pack("c",0);
				
				fwrite($fp, $lenstr);
				fwrite($fp, $contents);
				fwrite($fp, $nulstr);
				
				foreach($bin_data as $key => $val)
				{
		 			$len = strlen($val)/2;
					$lenstr = pack("L",$len);
					fwrite($fp, $lenstr);
					fwrite($fp, hex2bin($val));
				}
 			}
			fclose($fp);
      
      		header("Cache-Control: private");
    	    header('Connection: close');
    	    header('Content-Type: application/octet-stream');
    	    header('Expires: 0');
    	    if (filesize($this->tmpfile)) 
    	        header('Content-Length: ' . filesize($this->tmpfile));
    		header("response_code:".$command["response_code"]);
    		if ($command["id"]) 
    		    header("trans_id:".$command["id"]);
    		if ($command["command"]) 
    		    header("cmd_code:".$command["command"]);
    	    readfile($this->tmpfile);
    	    exit;
        }
    }

	function set_device($device_id, $input_valus)
	{
		global $dbh;
		if (!$device_id) return;
		
		try {
			$stmt = $dbh->prepare("SELECT * FROM ".TBL_DEVICE." WHERE name='".$device_id."'");
			$stmt->execute();
			
			$note = json_encode($input_valus);
			$sql = "INSERT INTO ".TBL_DEVICE." (name, note, regtime) VALUES ('".$device_id."',?, NOW())";
			if ($stmt->fetchColumn()>0)                                                                     
			{                                                                                               
				$sql = "UPDATE ".TBL_DEVICE." SET note = ?, regtime=NOW() WHERE name='".$device_id."'";       
			}                                                                                               
			$stmt = $dbh->prepare($sql);
			$stmt->bindValue(1, $note, PDO::PARAM_STR);  
			$stmt->execute();

		} catch (PDOException $e) {
	    $str .= "Error!: " . $e->getMessage() . "\r\n";
		}
	
		return $str;
	}
    

	function insert_log_data($device_id, $input_valus)
	{
		if (!$input_valus["user_id"] || !$input_valus["verify_mode"] || !$input_valus["io_time"]) {
			return;
		}
	
		DB::beginTransaction();
	
		try {
			$user_id = $input_valus["user_id"];
			$verifymode = $input_valus["verify_mode"];
			$iomode = isset($input_valus["io_mode"]) ? $input_valus["io_mode"] : 0;
			$school_id = substr($device_id, 0, 5);
			$punchStatus = $verifymode; // Adjust this if needed
	
			// Format the datetime
			$regtime = substr($input_valus["io_time"], 0, 4) . "-" .
					   substr($input_valus["io_time"], 4, 2) . "-" .
					   substr($input_valus["io_time"], 6, 2) . " " .
					   substr($input_valus["io_time"], 8, 2) . ":" .
					   substr($input_valus["io_time"], 10, 2) . ":" .
					   substr($input_valus["io_time"], 12, 2);
	
			// Insert into log table
			DB::table('face_log_data')->insert([
				'user_id'    => $user_id,
				'iomode'     => $iomode,
				'verifymode' => $verifymode,
				'regtime'    => $regtime,
				'device_id'  => $device_id
			]);
	
			$dateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $regtime);
			if (!$dateTime) {
				Log::error("❌ Failed to parse PunchTime for BioID $user_id. Received date: $regtime");
				DB::rollBack();
				return;
			}
	
			$formattedDate = $dateTime->format('Y-m-d H:i:s');
			$attendanceDate = $dateTime->format('Y-m-d');
	
			// Fetch active session
			$active_session = DB::table('sessions')
								->where('school_id', $school_id)
								->where('status', 1)
								->first();
	
			if (!$active_session) {
				Log::error("❌ No active session found for School ID: $school_id");
				DB::rollBack();
				return;
			}
	
			// Check in Enrollments table
			$enrollment = DB::table('enrollments')
							->where('stu_bioid', $user_id)
							->where('school_id', $school_id)
							->first();
	
			if ($enrollment) {
				$attendance = DB::table('daily_attendances')
								->where('school_id', $school_id)
								->where('student_id', $enrollment->user_id)
								->whereDate('stu_intime', $attendanceDate)
								->first();
	
				if (!$attendance) {
					DB::table('daily_attendances')->insert([
						'school_id'   => $school_id,
						'section_id'  => $enrollment->section_id,
						'student_id'  => $enrollment->user_id,
						'class_id'    => $enrollment->class_id,
						'device_id'   => $device_id,
						'stu_intime'  => $formattedDate,
						'stu_outtime' => null,
						'punchstatus' => $punchStatus,
						'iomode'      => $iomode,
						'verifymode'  => $verifymode,
						'session_id'  => $active_session->id,
						'created_at'  => now(),
						'updated_at'  => now()
					]);
				} else {
					DB::table('daily_attendances')
					  ->where('id', $attendance->id)
					  ->update([
						  'stu_outtime' => $formattedDate,
						  'device_id'   => $device_id,
						  'punchstatus' => $punchStatus,
						  'iomode'      => $iomode,
						  'verifymode'  => $verifymode,
						  'updated_at'  => now()
					  ]);
				}
			} else {
				// Check in hr_user_list table
				$hrUser = DB::table('hr_user_list')
							->where('emp_bioid', $user_id)
							->where('school_id', $school_id)
							->first();
	
				if ($hrUser) {
					$attendance = DB::table('hr_daily_attendences')
									->where('school_id', $school_id)
									->where('user_id', $hrUser->id)
									->whereDate('emp_intime', $attendanceDate)
									->first();
	
					if (!$attendance) {
						DB::table('hr_daily_attendences')->insert([
							'school_id'   => $school_id,
							'role_id'     => $hrUser->role_id,
							'emp_intime'  => $formattedDate,
							'emp_outtime' => null,
							'device_id'   => $device_id,
							'punchstatus' => $punchStatus,
							'session_id'  => $active_session->id,
							'user_id'     => $hrUser->id,
							'iomode'      => $iomode,
							'verifymode'  => $verifymode,
							'created_at'  => now(),
							'updated_at'  => now()
						]);
					} else {
						DB::table('hr_daily_attendences')
						  ->where('id', $attendance->id)
						  ->update([
							  'emp_outtime' => $formattedDate,
							  'device_id'   => $device_id,
							  'punchstatus' => $punchStatus,
							  'iomode'      => $iomode,
							  'verifymode'  => $verifymode,
							  'updated_at'  => now()
						  ]);
					}
				} else {
					Log::error("❌ BioID $user_id not found in students or employees.");
				}
			}
	
			DB::commit();
			Log::info("✅ Attendance processing completed successfully.");
			return response()->json(['DeviceResponse' => 0], 200);
	
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error("❌ Error!: " . $e->getMessage());
			return response()->json(['DeviceResponse' => 1], 500);
		}
	}
	


// Laravel's Development Server Logic (if using built-in PHP server)
$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

// Allow direct file serving from /public when using PHP's built-in server
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

// Pass everything else to Laravel's public/index.php
require_once __DIR__.'/public/index.php';
