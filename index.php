<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

    define("DB", "siliconcpanel_dbedusws");
    define("TBL_DEVICE", DB.".face_device");
    define("TBL_LOG", DB.".face_log_data");
	
    $dbh = new PDO('mysql:host=localhost;port=3306;dbname=siliconcpanel_dbedusws', 'siliconcpanel_edusws', 'edusws@123', array(PDO::ATTR_PERSISTENT => false));

	$header = apache_request_headers();	
    $command = null;
    $data = null;

    // Convert the headers array to a readable string format for writing to a file
    $headerString = print_r($header, true);
	// Specify the file path where you want to save the headers
    $filePath = 'debug_file.txt';
    // Write the headers to the file
    file_put_contents($filePath, $headerString, FILE_APPEND);
    

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
            //DeviceName “user_id” “verify_mode” “io_mode” “io_time” “log_image”
            file_put_contents($filePath, print_r($input_valus, true), FILE_APPEND);
         	
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
        global $dbh;
    
        file_put_contents('face_device_debug.log', "🚀 insert_log_data called\n", FILE_APPEND);
    
        if (!$input_valus["user_id"]) {
            file_put_contents('face_device_debug.log', "❌ Missing user_id\n", FILE_APPEND);
            return;
        }
        if (!$input_valus["verify_mode"]) {
            file_put_contents('face_device_debug.log', "❌ Missing verify_mode\n", FILE_APPEND);
            return;
        }
        if (!$input_valus["io_time"]) {
            file_put_contents('face_device_debug.log', "❌ Missing io_time\n", FILE_APPEND);
            return;
        }
    
        $str = ""; // For debug errors.
    
        try {
            file_put_contents('face_device_debug.log', "🛠 Formatting regtime...\n", FILE_APPEND);
    
            $regtime = substr($input_valus["io_time"], 0, 4) . "-" .
                       substr($input_valus["io_time"], 4, 2) . "-" .
                       substr($input_valus["io_time"], 6, 2) . " " .
                       substr($input_valus["io_time"], 8, 2) . ":" .
                       substr($input_valus["io_time"], 10, 2) . ":" .
                       substr($input_valus["io_time"], 12, 2);
    
            $user_id = $input_valus["user_id"];
            $iomode = $input_valus["io_mode"] ?? 0;
            $verifymode = $input_valus["verify_mode"];
            $punchStatus = $verifymode;
            $school_id = substr($device_id, 0, 5);
            $attendanceDate = substr($regtime, 0, 10); // Extract date part
    
            file_put_contents('face_device_debug.log', "🔍 Processing: user_id = $user_id, regtime = $regtime, school_id = $school_id\n", FILE_APPEND);
    
    
                // To store data directly to face_log_data table need to comment below to 290 line code
    
            // Count how many logs exist for user/date
            $sql = "SELECT id FROM face_log_data WHERE user_id = ? AND DATE(regtime) = ?";
            $stmt = $dbh->prepare($sql);
            $stmt->execute([$user_id, $attendanceDate]);
            $existingLogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $count = count($existingLogs);
    
            file_put_contents('face_device_debug.log', "ℹ️ Existing log count for user_id $user_id on $attendanceDate: $count\n", FILE_APPEND);
    
            if ($count == 0 || $count == 1) {
                file_put_contents('face_device_debug.log', "📝 Inserting new log\n", FILE_APPEND);
                $sql = "INSERT INTO face_log_data (user_id, iomode, verifymode, regtime, device_id) VALUES (?, ?, ?, ?, ?)";
                $stmt = $dbh->prepare($sql);
                $stmt->execute([$user_id, $iomode, $verifymode, $regtime, $device_id]);
                file_put_contents('face_device_debug.log', "✅ Log inserted for user_id: $user_id, entry count: " . ($count + 1) . "\n", FILE_APPEND);
            } else {
                file_put_contents('face_device_debug.log', "♻️ Updating second log\n", FILE_APPEND);
                $secondLogId = $existingLogs[1]['id'];
                $sql = "UPDATE face_log_data SET iomode = ?, verifymode = ?, regtime = ?, device_id = ? WHERE id = ?";
                $stmt = $dbh->prepare($sql);
                $stmt->execute([$iomode, $verifymode, $regtime, $device_id, $secondLogId]);
                file_put_contents('face_device_debug.log', "♻️ Updated second log for user_id: $user_id\n", FILE_APPEND);
            }
    
            // ✅ Correct session_id fetch based on regtime year
            file_put_contents('face_device_debug.log', "🔍 Fetching session based on regtime year...\n", FILE_APPEND);
            
            // Extract year from regtime
            $year = substr($regtime, 0, 4);
            
            // Fetch session_id from sessions table based on year
            $sql = "SELECT id FROM sessions WHERE school_id = ? AND session_title LIKE ? LIMIT 1";
            $stmt = $dbh->prepare($sql);
            $stmt->execute([$school_id, $year . '%']);
            $session = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($session) {
                $session_id = $session['id'];
                file_put_contents('face_device_debug.log', "✅ Found session_id: " . $session_id . " for year: " . $year . "\n", FILE_APPEND);
            } else {
                $session_id = null;
                file_put_contents('face_device_debug.log', "❌ No session found for year: " . $year . "\n", FILE_APPEND);
                return;
            }
    
            // Check student
            file_put_contents('face_device_debug.log', "🔍 Checking if user is student...\n", FILE_APPEND);
            $sql = "SELECT * FROM enrollments WHERE stu_bioid = ? AND school_id = ? LIMIT 1";
            $stmt = $dbh->prepare($sql);
            $stmt->execute([$user_id, $school_id]);
            $enrollment = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($enrollment) {
                file_put_contents('face_device_debug.log', "✅ Student found: user_id = " . $enrollment['user_id'] . "\n", FILE_APPEND);
    
                // Check attendance for student
                $sql = "SELECT * FROM daily_attendances WHERE school_id = ? AND student_id = ? AND DATE(stu_intime) = ? LIMIT 1";
                $stmt = $dbh->prepare($sql);
                $stmt->execute([$school_id, $enrollment['user_id'], $attendanceDate]);
                $attendance = $stmt->fetch(PDO::FETCH_ASSOC);
                
                
                if (!$attendance) {
                    file_put_contents('face_device_debug.log', "📝 Inserting new student attendance\n", FILE_APPEND);
                    $sql = "INSERT INTO daily_attendances 
                            (school_id, section_id, student_id, class_id, device_id, stu_intime, timestamp, stu_outtime, punchstatus, iomode, verifymode, session_id, created_at, updated_at) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, NULL, ?, ?, ?, ?, ?, ?)";
                    $stmt = $dbh->prepare($sql);
                    $stmt->execute([
                        $school_id, $enrollment['section_id'], $enrollment['user_id'], $enrollment['class_id'],
                        $device_id, $regtime, $regtime, $punchStatus, $iomode, $verifymode,
                        $session_id, date('Y-m-d H:i:s'), date('Y-m-d H:i:s')
                    ]);
                    file_put_contents('face_device_debug.log', "✅ New student attendance inserted.\n", FILE_APPEND);
                } else {
                    file_put_contents('face_device_debug.log', "♻️ Updating student attendance\n", FILE_APPEND);
                    $sql = "UPDATE daily_attendances SET stu_outtime = ?, device_id = ?, punchstatus = ?, iomode = ?, verifymode = ?, updated_at = ? WHERE id = ?";
                    $stmt = $dbh->prepare($sql);
                    $stmt->execute([
                        $regtime, $device_id, $punchStatus, $iomode, $verifymode, date('Y-m-d H:i:s'), date('Y-m-d')
                    ]);
                    file_put_contents('face_device_debug.log', "♻️ Student attendance updated.\n", FILE_APPEND);
                }
            } else {
        file_put_contents('face_device_debug.log', "🔍 Checking if user is employee...\n", FILE_APPEND);
    
        // Check employee
        $sql = "SELECT * FROM hr_user_list WHERE emp_bioid = ? AND school_id = ? LIMIT 1";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$user_id, $school_id]);
        $hrUser = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($hrUser) {
            file_put_contents('face_device_debug.log', "✅ Employee found: emp_bioid = " . $user_id . ", hr_user_list.id = " . $hrUser['id'] . "\n", FILE_APPEND);
    
            // Check if already punched today
            $sql = "SELECT * FROM hr_daily_attendences WHERE school_id = ? AND user_id = ? AND DATE(emp_intime) = ? LIMIT 1";
            $stmt = $dbh->prepare($sql);
            $stmt->execute([$school_id, $hrUser['id'], $attendanceDate]);
            $attendance = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (!$attendance) {
                file_put_contents('face_device_debug.log', "📝 No existing punch found. Inserting new employee attendance\n", FILE_APPEND);
    
                $sql = "INSERT INTO hr_daily_attendences 
                        (school_id, role_id, hr_roles_role_id, emp_intime, emp_outtime, device_id, punchstatus, session_id, user_id, iomode, verifymode, created_at, updated_at) 
                        VALUES (?, ?, ?, ?, NULL, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $dbh->prepare($sql);
                $stmt->execute([
                    $school_id,
                    $hrUser['role_id'],
                    $hrUser['hr_roles_role_id'],
                    $regtime,           // Insert regtime into emp_intime
                    $device_id,
                    $punchStatus,
                    $session_id,
                    $hrUser['id'],
                    $iomode,
                    $verifymode,
                    $regtime,
                    //date('Y-m-d H:i:s'),
                    date('Y-m-d H:i:s')
                ]);
    
                file_put_contents('face_device_debug.log', "✅ New employee attendance inserted with emp_intime.\n", FILE_APPEND);
            } else {
                file_put_contents('face_device_debug.log', "♻️ Existing punch found. Updating emp_outtime\n", FILE_APPEND);
    
                $sql = "UPDATE hr_daily_attendences 
                        SET emp_outtime = ?, device_id = ?, punchstatus = ?, iomode = ?, verifymode = ?, updated_at = ? 
                        WHERE id = ?";
                $stmt = $dbh->prepare($sql);
                $stmt->execute([
                    $regtime,           // Update regtime into emp_outtime
                    $device_id,
                    $punchStatus,
                    $iomode,
                    $verifymode,
                    date('Y-m-d H:i:s'),
                    $attendance['id']
                ]);
    
                file_put_contents('face_device_debug.log', "✅ Updated emp_outtime with latest regtime.\n", FILE_APPEND);
            }
        } else {
            file_put_contents('face_device_debug.log', "❌ user_id $user_id not found in enrollments or hr_user_list\n", FILE_APPEND);
        }
    }
    
            file_put_contents('face_device_debug.log', "✅ Data processing complete for user_id: $user_id\n", FILE_APPEND);
    
        } catch (PDOException $e) {
            $str .= "❌ PDO Error!: " . $e->getMessage() . "\r\n";
            file_put_contents('face_device_debug.log', $str, FILE_APPEND);
        }
    
        return $str;
    
        exit; // Ensure no Laravel involvement
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
