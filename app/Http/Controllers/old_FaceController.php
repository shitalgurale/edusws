<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDO;

class FaceController extends Controller
{
    // Constants for database and tables
    const DB = 'siliconcpanel_edusws';
    const TBL_DEVICE = self::DB . '.face_device';
    const TBL_LOG = self::DB . '.face_log_data';

    private $dbh;

    public function __construct()
    {
        $this->dbh = new PDO(
            'mysql:host=localhost;port=3306;dbname=siliconcpanel_dbedusws',
            'siliconcpanel_edusws',
            'edusws@123',
            [PDO::ATTR_PERSISTENT => false]
        );
    }

    public function faceattendance()
    {
     //   error_reporting(1);

        $header = apache_request_headers();  
        $command = null;
        $data = null;
       
        // Fallback using $_SERVER for custom headers
        $request_code    = $header["request_code"]    ?? $_SERVER['HTTP_REQUEST_CODE']    ?? null;
        $cmd_return_code = $header["cmd_return_code"] ?? $_SERVER['HTTP_CMD_RETURN_CODE'] ?? null;
        $dev_id          = $header["dev_id"]          ?? $_SERVER['HTTP_DEV_ID']          ?? null;
        $trans_id        = $header["trans_id"]        ?? $_SERVER['HTTP_TRANS_ID']        ?? null;
        $blk_no          = $header["blk_no"]          ?? $_SERVER['HTTP_BLK_NO']          ?? null;
        $blk_len         = $header["blk_len"]         ?? $_SERVER['HTTP_BLK_LEN']         ?? null;
        
        // Log the incoming headers for debugging
        Log::info('Received headers:', $header);

        $headerString = print_r($header, true);
        $filePath = 'debug_file.txt';
        file_put_contents($filePath, $headerString, FILE_APPEND);

        // if (!$header["dev_id"]) {
        //     Log::error("No dev_id found in header");
        //     die("Not a Silicon Wireless Systems Face Device, No dev_id found in header");
        // }

        if (!$dev_id) {
            Log::error("❌ dev_id missing from headers");
            die("Not a Silicon Wireless Systems Face Device, No dev_id found in header");
        }
        
        Log::info('Processing device ID: ' . $header["dev_id"]);

        $trans_data = new silicon_data;
        $trans_data->appendfile = $header["dev_id"] . "_tmp.dat";

        if (!$header["blk_no"]) 
            $header["blk_no"] = 0;

        if ($header["blk_no"] == 0) {
            $input_valus = $trans_data->get();
            Log::info('Input values received:', $input_valus);
            file_put_contents($filePath, print_r($input_valus, true), FILE_APPEND);

            $device_id = $header["dev_id"];

            switch ($header["request_code"]) {
                case "realtime_glog": 
                    $this->insert_log_data($device_id, $input_valus);
                    break;
                case "realtime_enroll_data": 
                    break;
                case "receive_cmd": 
                    $this->set_device($device_id, $input_valus);
                    break;
                default:
                    break;
            }

            if (file_exists($trans_data->appendfile)) 
                unlink($trans_data->appendfile);
        } else {
            $trans_data->append($header["blk_no"]);
        }

        // Log the response sent to the device
        $trans_data->set($command, $data);
        Log::info('Response sent to device: ', ['command' => $command, 'data' => $data]);
        
    }

    // Function for inserting device data
    private function set_device($device_id, $input_valus)
    {
        if (!$device_id) return;

        try {
            $stmt = $this->dbh->prepare("SELECT * FROM " . self::TBL_DEVICE . " WHERE name='" . $device_id . "'");
            $stmt->execute();

            $note = json_encode($input_valus);
            $sql = "INSERT INTO " . self::TBL_DEVICE . " (name, note, regtime) VALUES ('" . $device_id . "',?, NOW())";
            if ($stmt->fetchColumn() > 0) {       
                $sql = "UPDATE " . self::TBL_DEVICE . " SET note = ?, regtime=NOW() WHERE name='" . $device_id . "'";
            }
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(1, $note, PDO::PARAM_STR);  
            $stmt->execute();

            Log::info('Device data updated successfully for device: ' . $device_id);
        } catch (PDOException $e) {
            Log::error('Error updating device data: ' . $e->getMessage());
            return "Error!: " . $e->getMessage() . "\r\n";
        }

        return "";
    }

    // Function for inserting log data
    private function insert_log_data($device_id, $input_valus)
    {
        if (!$input_valus["user_id"] || !$input_valus["verify_mode"] || !$input_valus["io_mode"] || !$input_valus["io_time"]) {
            Log::warning('Missing required data for log insertion.');
            return;
        }

        try {
            $regtime = substr($input_valus["io_time"], 0, 4);
            $regtime .= "-" . substr($input_valus["io_time"], 4, 2);
            $regtime .= "-" . substr($input_valus["io_time"], 6, 2);
            $regtime .= " " . substr($input_valus["io_time"], 8, 2);
            $regtime .= ":" . substr($input_valus["io_time"], 10, 2);
            $regtime .= ":" . substr($input_valus["io_time"], 12, 2);

            $sql = "INSERT INTO " . self::TBL_LOG . " (user_id, iomode, verifymode, regtime, device_id)";
            $sql .= " VALUES ('" . $input_valus["user_id"] . "', '" . $input_valus["io_mode"] . "', '" . $input_valus["verify_mode"] . "', '" . $regtime . "', '" . $device_id . "')";
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();

            Log::info('Log data inserted successfully for device: ' . $device_id);
        } catch (PDOException $e) {
            Log::error('Error inserting log data: ' . $e->getMessage());
            return "Error!: " . $e->getMessage() . "\r\n";
        }

        return "";
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

        if ($blk_no == 1) {
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

        if (file_exists($this->appendfile)) {
            $contents = file_get_contents($this->appendfile); 
        }
        $contents .= file_get_contents($this->tmpfile); 

        $i = 0;
        $j = -1;
        $input_valus = array();
        $tmp = array();
        while ($i < strlen($contents)) {
            $j++;
            $blklen = substr($contents, $i, 4);                
            $i += 4;
            $array = unpack("Lint", $blklen);        
            $len = $array["int"];

            $t = substr($contents, $i, $len);                
            $i += $len;
            if ($j > 0) {
                $tmp["BIN_" . $j] = bin2hex($t);
            } else {
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
            foreach ($data as $key => $val)
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
            $lenstr = pack("L", $len);
            $nulstr = pack("c", 0);

            fwrite($fp, $lenstr);
            fwrite($fp, $contents);
            fwrite($fp, $nulstr);

            foreach ($bin_data as $key => $val)
            {
                $len = strlen($val) / 2;
                $lenstr = pack("L", $len);
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
        header("response_code:" . $command["response_code"]);
        if ($command["id"]) 
            header("trans_id:" . $command["id"]);
        if ($command["command"]) 
            header("cmd_code:" . $command["command"]);
        readfile($this->tmpfile);
        exit;
    }
}
