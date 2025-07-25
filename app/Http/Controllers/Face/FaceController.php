<?php

namespace App\Http\Controllers\Face;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

define("DB", "siliconcpanel_dbedusws");
define("TBL_DEVICE", DB.".face_device");
define("TBL_LOG", DB.".face_log_data");
	
class FaceController extends Controller
{
    protected $dbh;

    public function __construct()
    {
        $this->dbh = new \PDO(
            'mysql:host=localhost;port=3306;dbname=siliconcpanel_dbedusws',
            'siliconcpanel_edusws',
            'edusws@123',
            array(\PDO::ATTR_PERSISTENT => false)
        );
        
        $create_tables = array(
        	"SET NAMES utf8",
        	"CREATE DATABASE IF NOT EXISTS ".DB,
            "CREATE TABLE IF NOT EXISTS ".TBL_DEVICE." (id int(5) primary key auto_increment, name varchar(255), note text, regtime datetime, last_com int(10))",
        	"CREATE TABLE IF NOT EXISTS ".TBL_LOG." (id int(10) primary key auto_increment, user_id varchar(50), iomode int(3), verifymode int(3), regtime datetime, valid int(3), device_id varchar(255))",
        );        
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
		if (!$input_valus["user_id"]) return;
		if (!$input_valus["verify_mode"]) return;
		//if (!$input_valus["io_mode"]) return;
		if (!$input_valus["io_time"]) return;
		 	
		try 
		{
			$regtime = substr($input_valus["io_time"], 0, 4);
			$regtime .= "-".substr($input_valus["io_time"], 4, 2);
			$regtime .= "-".substr($input_valus["io_time"], 6, 2);
			$regtime .= " ".substr($input_valus["io_time"], 8, 2);
			$regtime .= ":".substr($input_valus["io_time"], 10, 2);
			$regtime .= ":".substr($input_valus["io_time"], 12, 2);
			
			$sql = "INSERT INTO ".TBL_LOG."  (user_id, iomode, verifymode, regtime, device_id)";
			$sql .= " VALUES ('".$input_valus["user_id"]."', '".$input_valus["io_mode"]."', '".$input_valus["verify_mode"]."', '".$regtime."', '".$device_id."')";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();

		} catch (PDOException $e) {
	    $str .= "Error!: " . $e->getMessage() . "\r\n";
			return $str;
		}
		
		return $str;
	}
	
    public function faceattendance()
    {   
        
        //error_reporting(1); //creates error_log file and writes errors
    
    	//include "config.php";
    
        Log::info("Entered Face Controller");
    	$header = apache_request_headers();	
        $command = null;
        $data = null;
    
        // Convert the headers array to a readable string format for writing to a file
        $headerString = print_r($header, true);
    	// Specify the file path where you want to save the headers
        $filePath = 'debug_file.txt';
        // Write the headers to the file
        file_put_contents($filePath, $headerString, FILE_APPEND);
        
    
    	if (!$header["dev_id"]) 
    	    die("Not a Silicon Wireless Systems Face Device, No dev_id found in header");
    
    	$trans_data = new silicon_data;	
        $trans_data->appendfile = $header["dev_id"]."_tmp.dat";//".$header["trans_id"]."
    	
    	if (!$header["blk_no"]) 
    	    $header["blk_no"] = 0;
    
        if ($header["blk_no"] == 0) //if the data in request from device not needed to append
        {
            //get the attendance data
        	$input_valus = $trans_data->get();
        
            // Write the values to the file
            //DeviceName “user_id” “verify_mode” “io_mode” “io_time” “log_image”
            file_put_contents($filePath, print_r($input_valus, true), FILE_APPEND);
         	
        	$device_id = $header["dev_id"];
        	
        	switch ($header["request_code"]) 
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
            
                $trans_data->append($header["blk_no"]);
        } 
        
        //respond to the device 
    	$trans_data->set($command, $data);
    }	
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

?>