<?php
namespace App\Exports;

use App\Models\User;
use App\Models\Users_detail;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MasterDataExport implements Responsable
{
    protected $user;
    protected $users_detail;

    public function __construct($users, $usersDetail)
    {
        $this->user = $users;
        $this->users_detail = $usersDetail;
    }

    public function export()
    {
        $templatePath = public_path('template_masterdata.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();


        // Set up the starting row and column for the data
        $startRow = 8;
        $startCol = 2;

        // Tulis data dari tabel user
        $row = $startRow;
        foreach ($this->user as $user) {
            $departmentName = $user->users_detail->department->department_name ?? '';
            $positionName = $user->users_detail->position->position_name ?? '';
            $gender = $user->users_detail->usr_gender ?? '';
            switch ($gender) {
                case 'M':
                    $genderLabel = 'Male';
                    break;
                case 'F':
                    $genderLabel = 'Female';
                    break;
                default:
                    $genderLabel = 'Unknown Gender';
                    break;
            }

            $marital = $user->users_detail->usr_merital_status ?? '';
            switch ($marital){
                case 'S';
                    $maritalLabel = 'Single';
                    break;
                case 'M';
                    $maritalLabel = 'Married';
                    break;
                case 'Widow';
                    $maritalLabel = 'Widow / Janda';
                    break;
                case 'Widower';
                    $maritalLabel = 'Widower / Duda';
                    break;
                case 'Divorced';
                    $maritalLabel = 'Divorced';
                    break;
                default:
                    $maritalLabel = ' Unknown Merital Status';
                    break;

            }

            $sheet->setCellValue('A' . $row, $user->users_detail->employee_id);
            $sheet->setCellValue('B' . $row, $user->id);
            $sheet->setCellValue('C' . $row, $user->name);
            $sheet->setCellValue('D' . $row, $user->email);
            $sheet->setCellValue('E' . $row, $positionName);
            $sheet->setCellValue('F' . $row, $departmentName);
            $sheet->setCellValue('G' . $row, $user->users_detail->employee_status);
            $sheet->setCellValue('H' . $row, $user->users_detail->status_active);
            $sheet->setCellValue('I' . $row, $user->users_detail->hired_date);
            $sheet->setCellValue('J' . $row, $user->users_detail->resignation_date);
            $sheet->setCellValue('K' . $row, $genderLabel);
            $sheet->setCellValue('L' . $row, $user->users_detail->usr_dob);
            $sheet->setCellValue('M' . $row, $user->users_detail->usr_birth_place);
            $sheet->setCellValue('N' . $row, $user->users_detail->usr_religion);
            $sheet->setCellValue('O' . $row, $maritalLabel);
            $sheet->setCellValue('P' . $row, $user->users_detail->usr_children);
            $sheet->setCellValue('Q' . $row, $user->users_detail->usr_id_type);
            $sheet->setCellValue('R' . $row, $user->users_detail->usr_id_no);
            $sheet->setCellValue('S' . $row, $user->users_detail->usr_id_expiration);
            $sheet->setCellValue('T' . $row, $user->users_detail->usr_phone_home);
            $sheet->setCellValue('U' . $row, $user->users_detail->usr_phone_mobile);
            $sheet->setCellValue('V' . $row, $user->users_detail->usr_npwp);
            $sheet->setCellValue('W' . $row, $user->users_detail->usr_address);
            $sheet->setCellValue('X' . $row, $user->users_detail->current_address);
            $sheet->setCellValue('Y' . $row, $user->users_detail->usr_address_city);
            $sheet->setCellValue('Z' . $row, $user->users_detail->usr_address_postal);
            $sheet->setCellValue('AA' . $row, $user->users_detail->usr_bank_name);
            $sheet->setCellValue('AB' . $row, $user->users_detail->usr_bank_account);
            $sheet->setCellValue('AC' . $row, $user->users_detail->usr_bank_account_name);
            $sheet->setCellValue('AD' . $row, $user->users_detail->usr_bank_branch);
            $row++;
        }

        // Simpan file Excel
        $writer = new Xlsx($spreadsheet);
        $filename = 'Master Data Employee.xlsx';
        $writer->save($filename);

        return $filename;
    }

    public function toResponse($request)
    {
        return response()->download($this->export())->deleteFileAfterSend(true);
    }
}
