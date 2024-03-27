<?php

namespace App\Http\Controllers;

use App\Models\Employee_evaluation;
use App\Models\Metrics_question;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KPIController extends Controller
{
    public function index(Request $request)
    {
        $Month = date('m');
        $Year = date('Y');

        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);

        $employees = User::with('users_detail')
		->whereHas('users_detail', function ($query) {
			$query->whereNull('resignation_date');
		})->get();

        $questionare = Metrics_question::all();

        $validator = Validator::make($request->all(), [
            'showOpt' => 'required',
            'yearOpt' => 'required',
            'monthOpt' => 'required'
        ]);

        $userSelected = NULL;
        $savedEvaluation = null;
        $allEvaluation = Null;

        if ($validator->passes()) {
            $Year = $request->yearOpt;
            $Month = $request->monthOpt;
            $userSelected = User::find($request->showOpt);
            $savedEvaluation = Employee_evaluation::where('year', $Year)
                ->where('month', $Month)
                ->where('user_id', $request->showOpt)
                ->get();
            $allEvaluation = Employee_evaluation::where('year', $Year)
                ->where('user_id', $request->showOpt)
                ->get();
            if($savedEvaluation->isEmpty()) {
                $savedEvaluation = null; // Set it to null instead of False
            }
        }

        return view('management.kpi.index', ['allEvaluation' => $allEvaluation,'employees' => $employees, 'Year' => $Year, 'savedEvaluation' => $savedEvaluation, 'yearsBefore' => $yearsBefore, 'Month' => $Month, 'userSelected' => $userSelected, 'question' => $questionare]);
    }

    public function save_evaluation_employee(Request $request, $userId, $month, $year)
    {
        // Validate the incoming request data
        $data = $request->validate([
            'question_mark.*' => 'required|numeric', // Assuming the input names are like question_mark[id]
        ]);

        // Process and save the evaluation data
        foreach ($data['question_mark'] as $questionId => $qValue) {
            Employee_evaluation::updateOrCreate(
                [
                    'user_id' => $userId,
                    'question_id' => $questionId,
                    'month' => $month,
                    'year' => $year,
                ],
                ['q_value' => $qValue]
            );
        }
        return redirect()->back()->with('success', "Success");
    }
}
