<?php

namespace App\Http\Controllers;

use App\Jobs\CreateEmailAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Users_detail;
use App\Models\Position;
use App\Models\Department;
use App\Models\Emp_leave_quota;
use App\Models\Emp_medical_balance;
use App\Models\Usr_role;
use App\Jobs\NotifyUserCreation;
use App\Models\Vendor_list;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;


class UserController extends Controller
{
    public function index()
    {
        $data = User::whereIn('id', function ($query) {
            $query->select('user_id')
                ->from('users_details')
                ->whereIn('employee_status', ['Permanent', 'Contract', 'Probation', 'MT']);
        })->get();
        $getFreelancer = User::whereIn('id', function ($query) {
            $query->select('user_id')
                ->from('users_details')
                ->where('employee_status', 'Freelance');
        })->get();
        return view('manage.users', ['data' => $data, 'dataFreelancer' => $getFreelancer]);
    }

    public function user_creation()
    {
        $dep_data = Department::all();
        $pos_data = Position::all();
        $latestForm = Users_detail::whereNull('deleted_at')->orderBy('id', 'desc')->pluck('employee_id')->max();
        $nextEmpID = $latestForm + 1;

        $response = Http::get('https://raw.githubusercontent.com/mul14/gudang-data/master/bank/bank.json');
        $banks = $response->json();

        // Mengubah name bank menjadi huruf kapital pada huruf pertama
        $bankNames = array_map(function ($bank) {
            $name = ucwords(strtolower($bank['name'])); // Mengubah name bank menjadi huruf kapital pada huruf pertama
            $words = explode(' ', $name); // Memisahkan kata dalam name bank

            // Mengecek panjang kata setelah kata "Bank" dan mengubahnya menjadi UPPERCASE jika kurang dari atau sama dengan 4 huruf
            foreach ($words as $key => $word) {
                if (strtolower($word) === 'bank' && isset($words[$key + 1])) {
                    $nextWord = $words[$key + 1];
                    if (strlen($nextWord) <= 4) {
                        $words[$key + 1] = strtoupper($nextWord);
                    }
                }
            }

            return implode(' ', $words); // Menggabungkan kata-kata kembali menjadi name bank
        }, $banks);

        return view('manage.users_creation', ['dep_data' => $dep_data, 'pos_data' => $pos_data, 'nextEmpID' => $nextEmpID, 'bankNames' => $bankNames]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'password' => 'required',
            'usr_id' => ['required', 'unique:users,id', 'regex:/^[a-z0-9]+$/'],
            'email' => 'required',
            'status' => 'required',
            'employee_status' => 'required',
            'position' => 'required',
            'department' => 'required',
            'hired_date' => 'required',
            'employee_id' => 'required',
            'profile_pic' => 'sometimes|file|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $lastId = Users_detail::whereNull('deleted_at')->orderBy('id', 'desc')->pluck('id')->first();
        $nextId = intval(substr($lastId, 4)) + 1;
        $hash_pwd = Hash::make($request->password);

        $employeeID = $request->employee_id;
        $emailAccount = $request->email."@perdana.co.id";

        // Memeriksa apakah file foto profil diunggah
        if ($request->hasFile('profile')) {
            $profile_file = $request->file('profile');
            $name_file_profile = $employeeID  . "_profile" . "." . $profile_file->getClientOriginalExtension();
            $upload_folder_profile = '/images_storage';
            $profile_file->move(public_path($upload_folder_profile, $name_file_profile));
        } else {
            // Tentukan nilai default untuk $name_file_profile jika file tidak diunggah
            $name_file_profile = null;
        }


        // Memeriksa apakah file CV diunggah
        if ($request->hasFile('cv')) {
            $cv_file = $request->file('cv');
            $name_file_cv = $employeeID . "_cv" . "." . $cv_file->getClientOriginalExtension();
            $upload_folder_cv = '/cv_storage';
            $cv_file->move(public_path($upload_folder_cv), $name_file_cv);
        } else {
            // Tentukan nilai default untuk $name_file_profile jika file tidak diunggah
            $name_file_cv = null;
        }

        User::create([
            'id' => $request->usr_id,
            'name' => $request->name,
            'email' => $emailAccount,
            'password' => $hash_pwd,
        ]);

        Users_detail::create([
            'id' => $nextId,
            'user_id' => $request->usr_id,
            'employee_id' => $employeeID,
            'position_id' => $request->position,
            'department_id' => $request->department,
            'status_active' => $request->status,
            'employee_status' => $request->employee_status,
            'hired_date' => $request->hired_date,
            'usr_address' => $request->usr_address,
            'current_address' => $request->current_address,
            'usr_address_city' => $request->usr_address_city,
            'usr_address_postal' => $request->usr_address_postal,
            'usr_phone_home' => $request->usr_phone_home,
            'usr_phone_mobile' => $request->usr_phone_mobile,
            'usr_npwp' => $request->usr_npwp,
            'usr_id_type' => $request->usr_id_type,
            'usr_id_no' => $request->usr_id_no,
            'usr_id_expiration' => $request->usr_id_expiration,
            'usr_dob' => $request->usr_dob,
            'usr_birth_place' => $request->usr_birth_place,
            'usr_gender' => $request->usr_gender,
            'usr_religion' => $request->usr_religion,
            'usr_merital_status' => $request->usr_merital_status,
            'usr_children' => $request->usr_children,
            'usr_bank_name' => $request->usr_bank_name,
            'usr_bank_branch' => $request->usr_bank_branch,
            'usr_bank_account' => $request->usr_bank_account,
            'usr_bank_account_name' => $request->usr_bank_account_name,
            'profile_pic' => $name_file_profile,
            'cv' => $name_file_cv,
        ]);


        // Mendapatkan tanggal dari request
        $hiredDate = $request->hired_date;

        // Mengubah tanggal menjadi objek Carbon
        $carbonDate = Carbon::parse($hiredDate);

        // Menambahkan 1 tahun ke tanggal
        $newDate = $carbonDate->addYear();

        // Mendapatkan tanggal 31 Maret setelah 1 tahun
        $newDate->month = 3;
        $newDate->day = 31;

        // Mendapatkan tahun setelah 1 tahun
        $newYear = $newDate->year;

        $emp_leave = new Emp_leave_quota();
        $emp_leave->user_id = $request->usr_id;
        $emp_leave->leave_id = 10;
        $emp_leave->quota_used = 0;
        $emp_leave->quota_left = 0;
        $emp_leave->active_periode = $request->hired_date;
        $emp_leave->expiration = $newDate->format('Y-m-d');
        $emp_leave->save();

        // $med_balance = new Emp_medical_balance();
        // $med_balance->user_id = $request->usr_id;
        // $med_balance->medical_balance = 0;
        // $med_balance->medical_deducted = 0;
        // $med_balance->save();

        $user_role = new Usr_role();
        $user_role->role_name = "employee";
        $user_role->role_id = 8;
        $user_role->user_id = $request->usr_id;
        $user_role->save();

        //Create Email Account
        $emailUser = $request->email;
        $password = $request->password;

        $employee = User::find($request->usr_id);

        dispatch(new CreateEmailAccount($emailUser, $password));
        dispatch(new NotifyUserCreation($employee));

        return redirect('/manage/users')->with('success', 'User Create successfully');
    }

    public function freelance_creation()
    {
        $dataVendor = Vendor_list::all();
        $dep_data = Department::all();
        $pos_data = Position::all();
        $latestForm = Users_detail::whereNull('deleted_at')->orderBy('id', 'desc')->pluck('employee_id')->max();
        $nextEmpID = $latestForm + 1;

        $response = Http::get('https://raw.githubusercontent.com/mul14/gudang-data/master/bank/bank.json');
        $banks = $response->json();

        // Mengubah name bank menjadi huruf kapital pada huruf pertama
        $bankNames = array_map(function ($bank) {
            $name = ucwords(strtolower($bank['name'])); // Mengubah name bank menjadi huruf kapital pada huruf pertama
            $words = explode(' ', $name); // Memisahkan kata dalam name bank

            // Mengecek panjang kata setelah kata "Bank" dan mengubahnya menjadi UPPERCASE jika kurang dari atau sama dengan 4 huruf
            foreach ($words as $key => $word) {
                if (strtolower($word) === 'bank' && isset($words[$key + 1])) {
                    $nextWord = $words[$key + 1];
                    if (strlen($nextWord) <= 4) {
                        $words[$key + 1] = strtoupper($nextWord);
                    }
                }
            }

            return implode(' ', $words); // Menggabungkan kata-kata kembali menjadi name bank
        }, $banks);

        return view('manage.freelance_creation', ['dep_data' => $dep_data, 'dataVendor' => $dataVendor, 'pos_data' => $pos_data, 'nextEmpID' => $nextEmpID, 'bankNames' => $bankNames]);
    }

    public function store_freelance(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'password' => 'required',
            'usr_id' => ['required', 'unique:users,id', 'regex:/^[a-z0-9]+$/'],
            'email' => 'required',
            'status' => 'required',
            'employee_status' => 'required',
            'position' => 'required',
            'department' => 'required',
            'hired_date' => 'required',
            'employee_id' => 'required',
            'profile_pic' => 'sometimes|file|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $lastId = Users_detail::whereNull('deleted_at')->orderBy('id', 'desc')->pluck('id')->first();
        $nextId = intval(substr($lastId, 4)) + 1;
        $hash_pwd = Hash::make($request->password);

        $employeeID = $request->employee_id;
        $emailAccount = $request->email."@perdana.co.id";

        // Memeriksa apakah file foto profil diunggah
        if ($request->hasFile('profile')) {
            $profile_file = $request->file('profile');
            $name_file_profile = $employeeID  . "_profile" . "." . $profile_file->getClientOriginalExtension();
            $upload_folder_profile = '/images_storage';
            $profile_file->move(public_path($upload_folder_profile, $name_file_profile));
        } else {
            // Tentukan nilai default untuk $name_file_profile jika file tidak diunggah
            $name_file_profile = null;
        }


        // Memeriksa apakah file CV diunggah
        if ($request->hasFile('cv')) {
            $cv_file = $request->file('cv');
            $name_file_cv = $employeeID . "_cv" . "." . $cv_file->getClientOriginalExtension();
            $upload_folder_cv = '/cv_storage';
            $cv_file->move(public_path($upload_folder_cv), $name_file_cv);
        } else {
            // Tentukan nilai default untuk $name_file_profile jika file tidak diunggah
            $name_file_cv = null;
        }

        User::create([
            'id' => $request->usr_id,
            'name' => $request->name,
            'email' => $emailAccount,
            'password' => $hash_pwd,
        ]);

        Users_detail::create([
            'id' => $nextId,
            'user_id' => $request->usr_id,
            'employee_id' => $employeeID,
            'position_id' => $request->position,
            'department_id' => $request->department,
            'status_active' => $request->status,
            'employee_status' => $request->employee_status,
            'hired_date' => $request->hired_date,
            'usr_address' => $request->usr_address,
            'usr_address_city' => $request->usr_address_city,
            'usr_address_postal' => $request->usr_address_postal,
            'usr_npwp' => $request->usr_npwp,
            'usr_id_type' => $request->usr_id_type,
            'usr_id_no' => $request->usr_id_no,
            'usr_id_expiration' => $request->usr_id_expiration,
            'usr_bank_name' => $request->usr_bank_name,
            'usr_bank_branch' => $request->usr_bank_branch,
            'usr_bank_account' => $request->usr_bank_account,
            'usr_bank_account_name' => $request->usr_bank_account_name,
            'profile_pic' => $name_file_profile,
            'cv' => $name_file_cv,
        ]);

        //dibikin dinamis
        $user_role = new Usr_role();
        $user_role->role_name = "non-internal";
        $user_role->role_id = 18;
        $user_role->user_id = $request->usr_id;
        $user_role->save();

        //Create Email Account
        $emailUser = $request->email;
        $password = $request->password;

        $employee = User::find($request->usr_id);

        dispatch(new CreateEmailAccount($emailUser, $password));
        dispatch(new NotifyUserCreation($employee));

        return redirect('/manage/users')->with('success', 'User Create successfully');
    }

    public function delete($id)
    {
        $user = Crypt::decrypt($id);
        $user_id = User::find($user);
        $name = $user_id->name;

        $data = DB::table('users')->where('id', $user)->delete();
        $data2 = DB::table('users_details')->where('user_id', $user)->delete();
        $leave = Emp_leave_quota::where('user_id', $user)->delete();
        $med = Emp_medical_balance::where('user_id', $user)->delete();
        $user_role = Usr_role::where('user_id', $user)->delete();

        return redirect('/manage/users')->with('success', "You've Deleted User $name Successfully");
    }


    public function edit($id)
    {
        $id = Crypt::decrypt($id);
        $user = User::with('users_detail')->findOrFail($id);
        $dep_data = Department::all();
        $pos_data = Position::all();

        $response = Http::get('https://raw.githubusercontent.com/mul14/gudang-data/master/bank/bank.json');
        $banks = $response->json();

        $response = Http::get('https://raw.githubusercontent.com/mul14/gudang-data/master/bank/bank.json');
        $banks = $response->json();

        // Mengubah name bank menjadi huruf kapital pada huruf pertama
        $bankNames = array_map(function ($bank) {
            $name = ucwords(strtolower($bank['name'])); // Mengubah name bank menjadi huruf kapital pada huruf pertama
            $words = explode(' ', $name); // Memisahkan kata dalam name bank

            // Mengecek panjang kata setelah kata "Bank" dan mengubahnya menjadi UPPERCASE jika kurang dari atau sama dengan 4 huruf
            foreach ($words as $key => $word) {
                if (strtolower($word) === 'bank' && isset($words[$key + 1])) {
                    $nextWord = $words[$key + 1];
                    if (strlen($nextWord) <= 4) {
                        $words[$key + 1] = strtoupper($nextWord);
                    }
                }
            }

            return implode(' ', $words); // Menggabungkan kata-kata kembali menjadi name bank
        }, $banks);
        return view('manage.users_edit', ['user' => $user, 'dep_data' => $dep_data, 'pos_data' => $pos_data, 'bankNames' => $bankNames]);
    }

    public function update(Request $request, $id)
    {

        $user = User::find($id);

        $employeeID = $user->users_detail->employee_id;

        // Memeriksa apakah file foto profil diunggah
        if ($request->hasFile('profile')) {
            $profile_file = $request->file('profile');
            $name_file_profile = $employeeID . "_profile" . "." . $profile_file->getClientOriginalExtension();
            $upload_folder_profile = '/images_storage';

            // Menghapus file profil lama jika ada
            $oldProfileImage = public_path($upload_folder_profile . '/' . $name_file_profile);
            if (file_exists($oldProfileImage)) {
                unlink($oldProfileImage);
            }

            // Memindahkan file profil yang baru diunggah
            $profile_file->move(public_path($upload_folder_profile), $name_file_profile); // Gunakan move() dengan path relatif dan name file
        } elseif ($user->users_detail->profile_pic) {
            // Menggunakan foto profil yang sudah ada dalam database jika ada
            $name_file_profile = $user->users_detail->profile_pic;
        } else {
            // Tidak ada foto profil di database dan tidak ada unggahan baru, set nilai menjadi null
            $name_file_profile = null;
        }

        // Memeriksa apakah file CV diunggah
        if ($request->hasFile('cv')) {
            $cv_file = $request->file('cv');

            $name_file_cv = $employeeID . "_cv" . "." . $cv_file->getClientOriginalExtension();
            $upload_folder_cv = '/cv_storage';

            // Menghapus file profil lama jika ada
            $oldCV = public_path($upload_folder_cv . '/' . $name_file_cv);
            if (file_exists($oldCV)) {
                unlink($oldCV);
            }

            $cv_file->move(public_path($upload_folder_cv), $name_file_cv);;
        } elseif ($user->users_detail->cv) {
            // Menggunakan foto profil yang sudah ada dalam database jika ada
            $name_file_cv = $user->users_detail->cv;
        } else {
            // Tidak ada foto profil di database dan tidak ada unggahan baru, set nilai menjadi null
            $name_file_cv = null;
        }

        $user->id = $request->usr_id;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        $user_detail = Users_detail::where('user_id', $id)->first();
        $user_detail->user_id = $request->usr_id;
        $user_detail->status_active = $request->status;
        $user_detail->department_id = $request->department;
        $user_detail->position_id = $request->position;
        $user_detail->employee_id = $request->employee_id;
        $user_detail->usr_dob = $request->usr_dob;
        $user_detail->usr_birth_place = $request->usr_birth_place;
        $user_detail->usr_gender = $request->usr_gender;
        $user_detail->usr_npwp  = $request->usr_npwp;
        $user_detail->usr_religion = $request->usr_religion;
        $user_detail->usr_merital_status = $request->usr_merital_status;
        $user_detail->usr_children = $request->usr_children;
        $user_detail->usr_id_type = $request->usr_id_type;
        $user_detail->usr_id_no = $request->usr_id_no;
        $user_detail->usr_id_expiration = $request->usr_id_expiration;
        $user_detail->employee_status = $request->employee_status;
        $user_detail->hired_date = $request->hired_date;
        $user_detail->resignation_date = $request->resignation_date;
        $user_detail->usr_address = $request->usr_address;
        $user_detail->usr_address_city = $request->usr_address_city;
        $user_detail->usr_address_postal = $request->usr_address_postal;
        $user_detail->usr_phone_home = $request->usr_phone_home;
        $user_detail->usr_phone_mobile = $request->usr_phone_mobile;
        $user_detail->usr_bank_name = $request->usr_bank_name;
        $user_detail->usr_bank_branch = $request->usr_bank_branch;
        $user_detail->usr_bank_account = $request->usr_bank_account;
        $user_detail->usr_bank_account_name = $request->usr_bank_account_name;
        $user_detail->current_address = $request->current_address;
        $user_detail->profile_pic = $name_file_profile;
        $user_detail->cv = $name_file_cv;
        $user_detail->save();


        return redirect()->back()->with('success', "You've Edited $user->name Successfully");
    }

    public function checkDuplicateEmail($checkEmail)
    {
        // Replace these with your cPanel credentials and server details
        $cpanelUsername = 'perdanac';
        $cpanelPassword = 'SYn-#,MSkTY%';
        $cpanelHostname = 'perdana.co.id'; // e.g., example.com

        $emailAccount = $checkEmail."@perdana.co.id";
        // Authenticate with cPanel API
        $client = new Client(['base_uri' => "https://$cpanelHostname:2083"]);
        try {
            // Send a request to list email accounts
            $response = $client->request('GET', '/execute/Email/list_pops', [
                'auth' => [$cpanelUsername, $cpanelPassword],
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode("$cpanelUsername:$cpanelPassword"),
                ],
            ]);

            // Get the response body
            $body = $response->getBody()->getContents();

            // Check if the request was successful
            if ($response->getStatusCode() === 200) {
                // Decode the JSON response
                $emailAccounts = json_decode($body, true);

                // Ensure the 'data' key exists in the response
                if (isset($emailAccounts['data'])) {
                    // Loop through email accounts data
                    foreach ($emailAccounts['data'] as $account) {
                        // Compare email addresses
                        if ($account['email'] === $emailAccount) {
                            // Email already exists
                            return response()->json(['success' => false, 'message' => 'Duplicate email found']);
                        }
                    }
                }

                // No duplicate found
                return response()->json(['success' => true, 'message' => 'No duplicate email found']);
            } else {
                // Failed to retrieve email accounts
                return response()->json(['success' => false, 'message' => 'Failed to retrieve email accounts']);
            }
        } catch (Exception $e) {
            // Handle exceptions
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
