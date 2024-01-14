<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Response;

class HolidayController extends Controller
{
    public function fetchAndFormatHolidays()
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

            // Write JSON data to a file
            $file_path = public_path("holidays_indonesia.json");
            file_put_contents($file_path, $json_data);

            // Provide a downloadable link
            return Response::download($file_path, "holidays_indonesia.json");

            return "JSON file created successfully at {$file_path}!";
        } catch (\Exception $e) {
            return "Error fetching or formatting holidays: " . $e->getMessage();
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
