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
<form action="/news-feed/manage/update-post/{{$newsFeed->id}}" method="POST">
    @csrf
    <div class="zoom90 d-sm-flex align-items-center zoom90 justify-content-between mb-4">
        <h1 class="h3 mb-2 font-weight-bold text-gray-800"><i class="fas fa-newspaper"></i> News Information </h1>
        <div>
            <a class="btn btn-danger mr-2" id="deletePost" href="/news-feed/manage/delete-post/{{ $newsFeed->id }}"><i class="fas fa-trash-alt"></i> Delete Post</a>
            <button class="btn btn-md btn-primary shadow-sm" type="submit">+ Update News</button>
        </div>
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
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="comment">Title :</label>
                                <input type="text" id="title" value="{{ $newsFeed->title }}" name="title" style="border: none; background: none; font-size: 24px; width: 100%; outline: none; border-bottom: 0.25px solid rgb(215, 215, 215);">
                            </div>                            
                        </div>    
                        <div class="col-md-12">
                            <textarea id="editor" name="content">{{ $newsFeed->content }}</textarea>
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
    plugins: 'ai tinycomments mentions anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed permanentpen footnotes advtemplate advtable advcode editimage tableofcontents mergetags powerpaste tinymcespellchecker autocorrect a11ychecker typography inlinecss',
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | align lineheight | tinycomments | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
    tinycomments_mode: 'embedded',
    tinycomments_author: 'Author name',
    mergetags_list: [
      { value: 'First.Name', title: 'First Name' },
      { value: 'Email', title: 'Email' },
    ],
    ai_request: (request, respondWith) => respondWith.string(() => Promise.reject("See docs to implement AI Assistant")),
  });
</script>
@endsection

