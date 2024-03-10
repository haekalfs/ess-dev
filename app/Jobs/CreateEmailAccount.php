<?php

namespace App\Jobs;

use App\Mail\CancelLeaveRequest;
use App\Mail\NotifyNewsEmployees;
use App\Models\User;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class CreateEmailAccount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userName;
    protected $password;

    public function __construct(String $userName, String $password)
    {
        $this->userName = $userName;
        $this->password = $password;
    }

    public function handle()
    {
        // Replace these with your cPanel API token and server details
        $cpanelUsername = 'perdanac';
        $cpanelAPIToken = '7OBRNMX34QWP0ANM9OUREPL0MSPCIJPM';
        $cpanelHostname = 'perdana.co.id'; // e.g., example.com

        // Authenticate with cPanel API
        $client = new Client(['base_uri' => "https://$cpanelHostname:2083"]);
        try {
            // Define the email account details
            $email = $this->userName;
            $password = $this->password;
            $quota = 1000; // Quota in MB

            // Send a request to create the email account using the API token
            $response = $client->request('GET', "/execute/Email/add_pop?email=$email&password=$password&quota=$quota", [
                'headers' => [
                    'Authorization' => "cpanel $cpanelUsername:$cpanelAPIToken",
                ],
            ]);

            // Check if the request was successful
            if ($response->getStatusCode() === 200) {
                // Email account created successfully
                return response()->json(['success' => true, 'message' => 'Email account created successfully']);
            } else {
                // Failed to create email account
                return response()->json(['success' => false, 'message' => 'Failed to create email account']);
            }
        } catch (Exception $e) {
            // Handle exceptions
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
