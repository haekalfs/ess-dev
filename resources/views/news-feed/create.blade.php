@extends('layouts.main')

@section('title', 'News Feed - ESS')

@section('active-page-HR')
active
@endsection

@section('content')
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
<form action="{{ route('news-feed.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <!-- Your form content -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4 zoom90">
        <h1 class="h3 mb-2 font-weight-bold text-gray-800"><i class="fas fa-newspaper"></i> News Information </h1>
        <button class="btn btn-md btn-primary shadow-sm" type="submit">+ Post News</button>
    </div>

    <div class="row zoom90">
        <div class="col-xl-12 col-md-12">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole">News Information</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label for="comment">Title :</label>
                                <input type="text" id="title" name="title" style="border: none; background: none; font-size: 24px; width: 100%; outline: none; border-bottom: 0.25px solid rgb(215, 215, 215);">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="receipt">Thumbnail :</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="thumbnailInput" name="thumbnail" aria-describedby="inputthumbnail" onchange="displayFileName()">
                                    <label class="custom-file-label" for="receiptInput" id="thumbnail-label">Choose file</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <textarea id="editor" name="content"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
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
  function displayFileName() {
        const fileInput = document.getElementById("thumbnailInput");
        const fileName = fileInput.files[0].name;
        const label = document.getElementById("thumbnail-label");
        label.innerText = fileName;
    }
</script>
@endsection

