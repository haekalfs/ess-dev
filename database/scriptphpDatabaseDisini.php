$users = User::all();
foreach($users as $user){
    Emp_leave_quota::updateOrCreate([
        'user_id' => $user->id,
        'leave_id' => 10,
        'active_periode' => "2024-04-01",
    ], [
        'quota_left' => 12
    ]);
}