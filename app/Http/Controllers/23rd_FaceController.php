<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class FaceController extends Controller
{
    public function receiveAttendance(Request $request)
    {
        Log::info("📥 Face device POST received");

        // Extract headers with full fallback (header → server → input)
        $dev_id = $request->header('x-dev-id');
               // ?? $request->server('HTTP_DEV_ID') 
            //    ?? $request->input('dev_id');
        
        $blk_no = $request->header('x-blk-no');
              //  ?? $request->server('HTTP_BLK_NO') 
            //    ?? $request->input('blk_no', 0);
        
        $blk_len = $request->header('x-blk-len');
              //  ?? $request->server('HTTP_BLK_LEN') 
            //    ?? $request->input('blk_len', 0);
                
        $request_code = $request->header('x-request-code');
              //       ?? $request->server('HTTP_REQUEST_CODE') 
            //          ?? $request->input('request_code');
                
        $cmd_return_code = $request->header('x-cmd-return-code');
              //           ?? $request->server('HTTP_CMD_RETURN_CODE') 
            //             ?? $request->input('cmd_return_code');
        
        $trans_id = $request->header('x-trans-id');
              //    ?? $request->server('HTTP_TRANS_ID') 
            //      ?? $request->input('trans_id');

// $dev_id = 0;
// $blk_no = 0;
// $blk_len = 0;
// $request_code = 0;
// $cmd_return_code = 0;
// $trans_id = 0;

        // Log headers
        $logData = [
            'dev_id' => $dev_id,
            'blk_no' => $blk_no,
            'blk_len' => $blk_len,
            'request_code' => $request_code,
            'cmd_return_code' => $cmd_return_code,
            'trans_id' => $trans_id,
        ];
        Log::info("📋 Headers: ", $logData);
        file_put_contents(storage_path('app/debug_log.txt'), json_encode($logData, JSON_PRETTY_PRINT), FILE_APPEND);

        if (!$dev_id) {
            Log::error("❌ dev_id not found");
            return response("❌ No dev_id found", 400);
        }

        $trans_data = new SiliconData($dev_id);

        if ((int) $blk_no === 0) {
            $input_values = $trans_data->get();
            file_put_contents(storage_path('app/debug_log.txt'), print_r($input_values, true), FILE_APPEND);

            switch ($request_code) {
                case "receive_cmd":
                    $this->setDevice($dev_id, $input_values);
                    return $trans_data->set(null, null);

                case "realtime_glog":
                    $this->insertLogData($dev_id, $input_values);
                    return $trans_data->set(null, null);

                default:
                    Log::warning("🚫 Unknown request_code: $request_code");
                    return $trans_data->set(null, null);
            }
        } else {
            $trans_data->append($blk_no);
            return $trans_data->set(null, null);
        }
    }

    private function insertLogData($device_id, $input_lines)
    {
        foreach ($input_lines as $line) {
            $parts = explode(" ", trim($line));
            if (count($parts) < 6) {
                Log::warning("⚠️ Skipping malformed attendance line: $line");
                continue;
            }

            list($user_id, $verifymode, $iomode, $timestamp, $valid, $device_name) = $parts;

            DB::table('face_log_data')->insert([
                'user_id' => $user_id,
                'iomode' => $iomode,
                'verifymode' => $verifymode,
                'regtime' => Carbon::createFromTimestamp($timestamp),
                'valid' => $valid,
                'device_id' => $device_name
            ]);

            Log::info("📝 Attendance logged: user_id=$user_id, device=$device_name, time=$timestamp");
        }
    }

    private function setDevice($device_id, $input_lines)
    {
        DB::table('face_device')->updateOrInsert(
            ['name' => $device_id],
            [
                'note' => 'Device Registered',
                'regtime' => now(),
                'last_com' => time()
            ]
        );

        Log::info("✅ Device registered: $device_id");
    }
}

class SiliconData
{
    public $tmpfile = 'tmpfile.dat';
    public $appendfile = '';

    public function __construct($dev_id)
    {
        $this->appendfile = storage_path("app/public/" . $dev_id . "_" . $this->tmpfile);
    }

    public function append($blk_no)
    {
        $input = fopen('php://input', 'r');
        $temp = tmpfile();
        stream_copy_to_stream($input, $temp);
        fclose($input);

        $meta = stream_get_meta_data($temp);
        file_put_contents($this->appendfile, file_get_contents($meta['uri']), FILE_APPEND);
        fclose($temp);

        Log::info("📎 Appended blk_no=$blk_no to {$this->appendfile}");
    }

    public function get()
    {
        if (!file_exists($this->appendfile)) return [];

        $data = file_get_contents($this->appendfile);
        unlink($this->appendfile);
        return explode("\n", trim($data));
    }

    public function set($command, $data)
    {
        Log::info("📨 Responded OK to face device");
        return response("OK", 200);
    }
}
