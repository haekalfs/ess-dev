<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Checkinout;
use Exception;

include_once(app_path('Helper.php'));
class ProcessAttendanceData implements ShouldQueue
{
    public $timeout = 1200;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle()
    {
        $IP = "192.168.1.113";
        $Key = "2907";

        $maxAttempts = 10;
        $attempts = 0;
        $validation = false;

        if ($IP) {
            while (!$validation && $attempts < $maxAttempts) {
                try {
                    $Connect = fsockopen($IP, "80", $errno, $errstr, 1);
                    if ($Connect) {
                        $soap_request = "<GetAttLog><ArgComKey xsi:type=\"xsd:integer\">" . $Key . "</ArgComKey><Arg><PIN xsi:type=\"xsd:integer\">All</PIN></Arg></GetAttLog>";
                        $newLine = "\r\n";
                        fputs($Connect, "POST /iWsService HTTP/1.0" . $newLine);
                        fputs($Connect, "Content-Type: text/xml" . $newLine);
                        fputs($Connect, "Content-Length: " . strlen($soap_request) . $newLine . $newLine);
                        fputs($Connect, $soap_request . $newLine);
                        $buffer = "";
                        while ($Response = fgets($Connect, 1024)) {
                            $buffer .= $Response;
                            $validation = true;
                        }
                    } else {
                        $validation = false; // Set validation explicitly to false
                    }
                } catch (Exception $e) {
                    // Handle exception or log error
                    sleep(5); // Wait for 5 seconds before retrying
                    $attempts++;
                }
            }

            $buffer = Parse_Data($buffer, "<GetAttLogResponse>", "</GetAttLogResponse>");
            $buffer = explode("\r\n", $buffer);

            $currentDate = date("Y-m-d");
            $yesterdayDate = date("Y-m-d", strtotime("-1 day"));

            foreach ($buffer as $dataRow) {
                $data = Parse_Data($dataRow, "<Row>", "</Row>");
                $PIN = Parse_Data($data, "<PIN>", "</PIN>");
                $DateTime = Parse_Data($data, "<DateTime>", "</DateTime>");
                $Verified = Parse_Data($data, "<Verified>", "</Verified>");
                $Status = Parse_Data($data, "<Status>", "</Status>");

                $dateTimeParts = explode(" ", $DateTime);
                $date = $dateTimeParts[0];
                $time = isset($dateTimeParts[1]) ? $dateTimeParts[1] : null;

                // Check if the date matches today or yesterday
                if ($date === $currentDate || $date === $yesterdayDate) {
                    $checkinout = new Checkinout();
                    $checkinout->user_id = $PIN;
                    $checkinout->date = $date;
                    $checkinout->time = $time;
                    $checkinout->verify = $Verified;
                    $checkinout->status = $Status;

                    if ($checkinout->save()) {
                        // Log success in Laravel logs
                        // \Log::info("New record created successfully");
                    } else {
                        // Log any errors in saving to the database
                        // \Log::error("Error saving the record");
                    }
                }
            }
        }
    }
}
