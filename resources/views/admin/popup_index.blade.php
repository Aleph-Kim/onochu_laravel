@include('admin.components.header')
<div class="admin-wrap">
    <div class="main-title-wrap col-group sp-bt al-en">
        <h2 class="main-title">
            팝업 관리
        </h2>
        <a href="{{route('admin.popup.create')}}" class="btn btn-m blue">
            등록
        </a>
    </div>

    <div class="board-wrap col-group">

        @if($popups->isEmpty())
            <div class="null-txt">
                등록한 팝업이 없습니다.
            </div>
        @else
            @foreach($popups as $key => $popup)
                <div class="board-item">
                    <div class="img-box">
                        <div class="status-txt {{$popup->status->cssClass()}}">{{$popup->status->label()}}</div>
                        <img src="{{$popup->file->url ?? ''}}" alt="">
                    </div>
                    <div class="txt-box row-group">
                        <div class="col-group sp-bt al-ce">
                            {{$popup->title}}
                        </div>
                        <div class="col-group sp-bt al-ce">
                            {{$popup->start_date ? $popup->start_date->format('Y-m-d') . " ~ " . $popup->end_date->format('Y-m-d') : ''}}
                        </div>
                        <div class="sub-btn-wrap col-group gap16">
                            <button class="sub-btn blue"
                                    onclick="window.location.href = '{{ route('admin.popup.edit', $popup) }}'">
                                수정
                            </button>
                            <form action="{{route('admin.popup.destroy', $popup)}}" method="post"
                                  onsubmit="return formConfirm()">
                                @csrf
                                @method('DELETE')
                                <button class="sub-btn blue" type="submit">
                                    삭제
                                </button>
                            </form>
                        </div>
                        <div class="col-group align-btn-wrap">
                            @if($key != 0)
                                <form action="{{route('admin.popup.order', $popup)}}" method="post">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="order" value="prev">
                                    <button class="align-btn">
                                        <i class="xi-arrow-left"></i>
                                    </button>
                                </form>
                            @endif
                            @if(count($popups) != $key + 1)
                                <form action="{{route('admin.popup.order', $popup)}}" method="post">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="order" value="next">
                                    <button class="align-btn">
                                        <i class="xi-arrow-right"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
@include('admin.components.footer')
