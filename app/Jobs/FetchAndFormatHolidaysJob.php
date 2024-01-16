<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use GuzzleHttp\Client;

class FetchAndFormatHolidaysJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $client = new Client();
        $currentYear = date('Y');
        $yearsToFetch = 4;

        try {
            $formattedHolidays = [];

            // Loop through the last 4 years
            for ($i = 1; $i <= $yearsToFetch; $i++) {
                $year = $currentYear - $i;

                // Replace this URL with the appropriate API endpoint
                $url = 'https://dayoffapi.vercel.app/api?year=' . $year;

                $response = $client->get($url);
                $data = json_decode($response->getBody(), true);

                // Merge the holiday data for the current year into the result array
                $formattedHolidays = array_merge($formattedHolidays, $this->formatHolidays($data, $year));
            }

            // Convert data to JSON
            $json_data = json_encode($formattedHolidays, JSON_PRETTY_PRINT);

            // Write JSON data to a file in the public folder
            $file_path = public_path("holidays_indonesia.json");
            file_put_contents($file_path, $json_data);

            // You can perform additional actions if needed

        } catch (\Exception $e) {
            // Handle exception or log error
            report($e);
        }
    }

    private function formatHolidays($data, $year)
    {
        $formattedHolidays = [];

        foreach ($data as $holiday) {
            $date = date('Y-m-d', strtotime($holiday['tanggal']));
            $formattedHolidays[$date] = [
                'description' => ['Hari libur nasional'],
                'holiday' => true,
                'summary' => [$holiday['keterangan']],
                'year' => $year, // Add the year to the formatted data
            ];
        }

        return $formattedHolidays;
    }
}
