@include('admin.components.header')

<div class="admin-wrap main-content-wrap">
    <form id="form" action="{{ route('admin.popup.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="continue" name="continue" value="">
        <div class="main-title-wrap col-group sp-bt al-en">
            <h2 class="main-title">
                팝업 등록
            </h2>
            <div class="col-group gap16">
                <button class="btn btn-m gray" type="button"
                        onclick="window.location.href='{{route('admin.popup.index')}}'">
                    취소
                </button>
                <button id="formContinue" class="btn btn-m blue" type="submit">
                    등록 후 계속
                </button>
                <button class="btn btn-m blue" type="submit">
                    등록
                </button>
            </div>
        </div>
        <div class="form-wrap row-group">
            <div class="form-item row-group">
                <div class="item-default">
                    공개 여부
                    <span class="red">*</span>
                </div>
                <div class="item-user">
                    <div class="form-label-wrap col-group">
                        @foreach($statuses as $status)
                            <label for="status_{{ $status->name }}">
                                <input type="radio"
                                       id="status_{{ $status->name }}"
                                       name="status"
                                       class="form-radio"
                                       value="{{ $status->value }}"
                                    @checked($status->value == old('status'))>
                                <div class="checked-item col-group">
                                    <div class="icon">
                                        <i class="xi-check"></i>
                                    </div>
                                    <p class="txt">
                                        {{ $status->label() }}
                                    </p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
                @error('status')
                <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-item row-group">
                <div class="item-default">
                    제목
                    <span class="red">*</span>
                </div>
                <div class="item-user">
                    <input type="text" class="form-input" placeholder="팝업의 이름을 입력해주세요." name="title"
                           value="{{ old('title') }}">
                </div>
                @error('title')
                <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-item row-group">
                <p class="item-default">
                    URL
                </p>
                <div class="item-user">
                    <input
                        type="text"
                        class="form-input"
                        placeholder='URL을 입력하세요(https:// 포함)'
                        value="{{old('link')}}"
                        name="link"
                    >
                </div>
                @error('link')
                <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-list col-group per2">
                <div class="form-item row-group grade-period">
                    <p class="item-default">
                        노출기간
                    </p>
                    <div class="item-user">
                        <div class="col-group gap8 al-ce">
                            <input type="date" class="form-input half" name="start_date"
                                   value={{old('start_date')}}>
                            -
                            <input type="date" class="form-input half" name="end_date"
                                   value={{old('end_date')}}>
                        </div>
                    </div>
                    @error('start_date')
                    <span class="error">{{ $message }}</span>
                    @enderror
                    @error('end_date')
                    <span class="error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-item row-group">
                <p class="item-default">
                    이미지
                    <span class="red">*</span>
                </p>
                <div class="file-upload-wrap">
                    <input type='file' id='file-upload' name="file"
                           accept="image/gif, image/jpg, image/jpeg, image/png, image/*"
                           onchange="changePreviewFileWithDelete(this)">
                    <label for="file-upload" class="file-upload-btn">
                        파일 업로드
                    </label>
                    <span class="guide-txt">
                        900*900px 비율 고해상도 사진 등록
                    </span>
                    <div class="file-preview" id="file" style="display: none">
                        <p class="file-name"></p>
                        <button type="button" class="file-del-btn" onclick="deleteFile(this)" name="file">
                            <i class="xi-close"></i>
                        </button>
                    </div>
                </div>
                @error('file')
                <span class="error">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </form>
</div>
<script>
    function formSubmitEvent() {
        document.getElementById('form').addEventListener('submit', function (event) {
            event.preventDefault();

            if (event.submitter.id === "formContinue") { // 등록 후 계속
                document.getElementById('continue').value = 1
            }

            // 폼 재전송
            this.submit();
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        formSubmitEvent()
    });
</script>
@include('admin.components.footer')
