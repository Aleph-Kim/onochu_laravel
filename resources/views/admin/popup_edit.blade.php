@include('admin.components.header')

<div class="admin-wrap main-content-wrap">
    <form action="{{ route('admin.popup.update', $popup) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="main-title-wrap col-group sp-bt al-en">
            <h2 class="main-title">
                팝업 수정
            </h2>
            <button class="btn btn-m blue">
                저장
            </button>
        </div>
        <div class="form-wrap row-group">
            <div class="form-item row-group">
                <div class="item-default">
                    상태
                    <span class="red">*</span>
                </div>
                <div class="item-user">
                    <div class="form-label-wrap col-group">
                        @foreach($statuses as $status)
                            <label for="status_{{ $status }}">
                                <input type="radio"
                                       id="status_{{ $status }}"
                                       name="status"
                                       class="form-radio"
                                       value="{{ $status->value }}"
                                    @checked($status == $popup->status)>
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
            <div class="form-list col-group per2">
                <div class="form-item row-group">
                    <p class="item-default">
                        제목<span class="red">*</span>
                    </p>
                    <div class="item-user">
                        <input
                            type="text"
                            class="form-input"
                            placeholder='팝업의 이름을 정해주세요.'
                            value="{{$popup->title}}"
                            name="title"
                        >
                    </div>
                    @error('title')
                    <span class="error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-list col-group per2">
                <div class="form-list col-group per2">
                    <div class="form-item row-group grade-period">
                        <p class="item-default">
                            노출기간
                        </p>
                        <div class="item-user">
                            <div class="col-group gap8 al-ce">
                                <input type="date" class="form-input half" name="start_date"
                                       value={{$popup->start_date}}>
                                -
                                <input type="date" class="form-input half" name="end_date"
                                       value={{$popup->end_date}}>
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
                        링크
                    </p>
                    <div class="item-user">
                        <input
                            type="text"
                            class="form-input"
                            placeholder='클릭 시 페이지 이동이 필요하시다면 링크를 여기에 적어주세요.'
                            value="{{$popup->link}}"
                            name="link"
                        >
                    </div>
                    @error('link')
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
                    <div class="file-preview" id="file">
                        <p class="file-name">{{$popup->file->origin_name}}</p>
                        <button type="button" class="file-del-btn" onclick="deleteFile(this)" name="file">
                            <i class="xi-close"></i>
                        </button>
                    </div>
                    @error('file')
                    <span class="error">{{ $message }}</span>
                    @enderror
                </div>
                @error('file')
                <span class="error">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </form>
</div>
@include('admin.components.footer')
