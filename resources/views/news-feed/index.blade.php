@extends('layouts.main')

@section('title', 'News Feed - ESS')

@section('active-page-HR')
active
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between mb-4">
    <h1 class="h4 mb-0 text-gray-800 font-weight-bold"><i class="fas fa-list"></i> News Feed Management</h1>
    {{-- <a data-toggle="modal" data-target="#addMem" class="d-none d-sm-inline-block btn btn-sm btn-danger shadow-sm"><i class="fas fa-list"></i> Execute</a> --}}
</div>
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
@if ($message = Session::get('success'))
<div class="alert alert-success alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif
<div class="row zoom90">
    <div class="col-xl-12 col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary" id="judul">News Feed History</h6>
                <div class="text-right">
                    <a href="/news-feed/manage/create" class="d-none d-sm-inline-block btn btn-sm btn-danger shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Add News</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered zoom90" id="listAssignments" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th>No.</th>
                                <th>File Name</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($headline as $pic)
                            <tr>
                                <td>{{ $pic->id }}</td>
                                <td>{{ $pic->filename }}</td>
                                <td>{{ $pic->title }}</td>
                                <td>{!! $pic->subtitle !!}</td>
                                <td class="text-center">
                                    <a data-toggle="modal" data-target="#setHeadline" data-item-id="{{ $pic->id }}" class="btn btn-primary btn-sm btn-edit">Action</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Headline</h6>
                <div class="text-right">
                    <button class="d-none d-sm-inline-block btn btn-sm btn-danger shadow-sm" type="button" id="headlineButton"><i class="fas fa-plus fa-sm text-white-50"></i> Set Headline</button>
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="lc-block position-relative">
                            <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
                                <div class="carousel-inner">
                                    @foreach($headline as $index => $hl)
                                        <div class="carousel-item{{ $index === 0 ? ' active' : '' }}">
                                            <img class="d-block w-100 img-fluid rounded shadow" src="{{ asset($hl->filepath) }}" alt="Slide {{ $index + 1 }}">
                                        </div>
                                    @endforeach
                                </div>
                                <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" id="news-list">
                        <div style="height:350px;" class="transparent-scroll2">
                            <div class="card-body transparent-scroll">
                                @foreach($newsFeed as $feed)
                                    <h5 class="card-title font-weight-bold">{{ $feed->title }}</h5>
                                    {!! $feed->content !!}
                                    <div class="mb-4">
                                        <a class="btn btn-sm btn-primary mt-0" data-id="{{ $feed->id }}" class="card-link">Read more</a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 zoom90" id="edit-headline" style="display: none;">

                    </div>
                </div>
            </div>
        </div> --}}
    </div>
</div>
<div class="modal fade setHeadline" tabindex="-1" id="setHeadline" role="dialog" aria-labelledby="setHeadline" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="exampleModalLabel">Set Headline Image</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
            <form method="post" id="editItemForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="item_id" id="item_id" value="">
                <div class="modal-body zoom90">
                    <div class="form-group">
                        <label for="receipt">Thumbnail :</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="receiptInput" name="receipt" aria-describedby="inputreceipt" onchange="displayFileName()">
                            <label class="custom-file-label" for="receiptInput" id="receipt-label">Choose file</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="comment">Title :</label>
                        <input type="text" id="title" name="title" style="border: none; background: none; font-size: 24px; width: 100%; outline: none; border-bottom: 0.25px solid rgb(215, 215, 215);">
                    </div>
                    <div class="form-group">
                        <label for="password">Description :</label>
                        <textarea id="editor" name="content"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="updateDataSubmit">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
tinymce.init({
    selector: 'textarea',
    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | align lineheight | tinycomments | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
    // tinycomments_mode: 'embedded',
    tinycomments_author: 'Author name',
    mergetags_list: [
      { value: 'First.Name', title: 'First Name' },
      { value: 'Email', title: 'Email' },
    ],
    ai_request: (request, respondWith) => respondWith.string(() => Promise.reject("See docs to implement AI Assistant")),
});
$(document).ready(function() {
    var newsList = $('#news-list');
    var editHeadline = $('#edit-headline');
    var toggleButton = $('#headlineButton');
    var isVisible = false; // Initial state: edit-headline is hidden

    toggleButton.click(function() {
        isVisible = !isVisible; // Toggle the visibility state
        if (isVisible) {
            newsList.hide();
            editHeadline.show();
        } else {
            newsList.show();
            editHeadline.hide();
        }
    });
});
const label = document.getElementById("receipt-label");
$(document).on('click', '.btn-edit', function() {
    var itemId = $(this).data('item-id');
    $('#item_id').val(itemId);

    $.ajax({
        url: '/news-feed/get-id-pic/' + itemId,
        method: 'GET',
        success: function(response) {
            label.innerText = response.filename;
            $('#editor').val(response.subtitle);
            $('#title').val(response.title);
        },
        error: function(xhr) {
            // Handle error
            console.log(xhr.responseText);
        }
    });
});

$(document).on('click', '#updateDataSubmit', function() {
    // Get the content of the TinyMCE editor
    var content = tinymce.get('editor').getContent();

    // Include the content in your FormData
    var formData = new FormData($('#editItemForm')[0]);
    formData.append('content', content);

    var itemId = $('#item_id').val();

    // Make an AJAX request to update the project data
    $.ajax({
        url: '/news-feed/update-headline/' + itemId,
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            // Handle success
            console.log(response);

            // Close the modal
            $('#setHeadline').modal('hide');
            $('#editItemForm')[0].reset();
            window.location.reload();
        },
        error: function(xhr) {
            // Handle error
            console.log(xhr.responseText);
        }
    });
});

function displayFileName() {
    const fileInput = document.getElementById("receiptInput");
    const fileName = fileInput.files[0].name;
    const label = document.getElementById("receipt-label");
    label.innerText = fileName;
}
</script>
@endsection
