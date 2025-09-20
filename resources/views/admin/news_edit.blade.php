@include('admin.components.header')

<div class="admin-wrap">
    <form action="{{route('admin.news.update', $board)}}" method="post" id="form" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <input type="hidden" name="return_url" value="{{ old('return_url', $returnUrl) }}">

        <div class="main-title-wrap">
            <div class="col-group sp-bt al-en">
                <h2 class="main-title">
                    {{$boardType->label()}} 게시글 수정
                </h2>
                <div class="col-group gap16">
                    <button class="btn btn-m blue">
                        저장
                    </button>
                </div>
            </div>
        </div>
        <div class="form-wrap row-group">
            <div class="form-item row-group">
                <div class="item-default">
                    공개여부
                    <span class="red">*</span>
                </div>
                <div class="item-user">
                    <div class="form-label-wrap col-group">
                        @foreach($statuses as $status)
                            <label for="status_{{$status}}">
                                <input type="radio" id="status_{{$status}}" name="status" class="form-radio"
                                       value="{{$status}}" @checked($status == $board->status)>
                                <div class="checked-item col-group">
                                    <div class="icon">
                                        <i class="xi-check"></i>
                                    </div>
                                    <p class="txt">
                                        {{$status->label()}}
                                    </p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="form-item row-group">
                <div class="item-default">
                    제목
                    <span class="red">*</span>
                </div>
                <div class="item-user">
                    <input type="text" class="form-input" placeholder="제목" name="title"
                           value="{{ $board->title }}">
                </div>
                @error('title')
                <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-item editor row-group">
                <p class="item-default">
                    내용
                    <span class="red">*</span>
                </p>
                @error('content')
                <span class="error">{{ $message }}</span>
                @enderror
                <div class="editor-container editor-container_classic-editor editor-container_include-word-count"
                     id="editor-container">
                    <div class="editor-container__editor">
                        <div id="editor">{!! $board->content !!}</div>
                        <input type="hidden" id="content" name="content">
                    </div>
                    <div class="editor_container__word-count" id="editor-word-count"></div>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- 에디터 -->
<script type="importmap">
    {
        "imports": {
            "ckeditor5": "/ckeditor5/ckeditor5.js",
            "ckeditor5/": "/ckeditor5/"
        }
    }
</script>
<script type="module" src="{{ asset('ckeditor5/main.js') }}"></script>
<script>
    let editor;
    let contentInput = document.getElementById('content');

    function formSubmitEvent() {
        document.getElementById('form').addEventListener('submit', function (event) {
            event.preventDefault();

            contentInput.value = editor.getData();

            // 폼 재전송
            event.target.submit();
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        formSubmitEvent()
    });
</script>
@include('admin.components.footer')
