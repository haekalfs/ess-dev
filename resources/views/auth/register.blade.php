@extends('layouts.main')

@section ('content')
<div style="display: flex;justify-content: center;align-items: center;">
    <div class="margin-top:20px; border border-primary;" style="text-align: start; width:50%">
        <h3 style="margin:10px">REGISTER FOR USER</h3>
        <div style=" margin:30px">
            <div>
                <div class="row g-3 align-items-top">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                    <tr>
                                        <th>ID</th>
                                        <td><input type="id" class="form-control border-dark"></td>
                                    </tr>
                                    <tr>
                                        <th>User ID</th>
                                        <td><input type="user_id" class="form-control border-dark"></td>
                                    </tr>
                                    <tr>
                                        <th>Employee ID</th>
                                        <td><input type="employee_id" class="form-control border-dark"></td>
                                    </tr>
                                    <tr>
                                        <th>Full Name</th>
                                        <td><input type="name" class="form-control border-dark"></td>
                                    </tr>
                                    <tr>
                                        <th>Position</th>
                                        <td><input type="Position" class="form-control border-dark"></td>
                                    </tr>
                            </table>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <input class="btn btn-primary" type="submit" value="Submit">
                              </div>
                        </div>
                </div>
            </div> 
        </div> 
    </div>         
</div>
@endsection
