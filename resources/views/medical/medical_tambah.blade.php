@extends('layouts.main')

@section('active-page-myprofile')
active
@endsection

@section('content')
<!-- Page Heading -->

@if ($message = Session::get('success'))
<div class="alert alert-success alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif

@if ($message = Session::get('failed'))
<div class="alert alert-danger alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif

@if ($message = Session::get('warning'))
<div class="alert alert-warning alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif

<div class="container">
    <div class="card mt-5 d-flex p-2">
        <div class="card-header text-center">
            <h3>Medical Form Request</h3>
        </div>
        <div class="card-body">
            <input href="#" class="btn btn-primary" type="button" id="copyButton" value="Add Entry">
            <input type="submit" class="btn btn-success" value="Submit">
            <br/>
            <br/>
            <div class="col-md-12">
                <div class="row">
                    {{-- <small style="color: red;"><u><i>This Version Only Support 1 Item!</i></u></small> --}}
                    <div class="col-md-7">
                        <small style="color: red;"><u><i>This Version Only Support 3 Items! You Can Edit It Later.</i></u></small>
                    </div>
                    <div class="col-md-5 justify-content-between flex-row">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-right">
                                    <button class="btn btn-danger btn-sm" type="button" id="undoButton" style="display:none; margin-left: 35px;"><i class="fas fa-fw fa-trash-alt"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4">
                        <div class="col" >
                            <div class="row">
                                <div class="form-group">
                                    <input type="file" class="custom-file-input" id="inputGroupFile01" aria-describedby="inputGroupFileAddon01" required>
                                    <label class="custom-file-label" for="inputGroupFile01">Input Image</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="amount">Amount (Rp) :</label>
                                    <input type="text" class="form-control" name="price[]" id="price" value="" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="description" id="items_label">Description :</label>
                                    <textarea class="form-control" name="item_to_purchase[]" rows="3" id="item_to_purchase" required></textarea>
                                </div>
                            </div>
                            {{-- <div class="col-md-3">
                                <div class="form-group">
                                    <label for="password">Total Amount :</label>
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">Rp.</div>
                                        </div>
                                        <input type="text" class="form-control" id="result" name="result[]" value="" readonly>
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
@endsection