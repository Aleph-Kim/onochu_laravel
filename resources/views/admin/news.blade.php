@include('admin.components.header')
<div class="admin-wrap">

    <div class="main-title-wrap col-group sp-bt al-en">
        <h2 class="main-title">
            {{$boardType->label()}} 게시글 목록
        </h2>
        <form action="{{route('admin.news.index')}}" method="get">
            <div class="col-group gap16">
                <select name="status" class="form-input quarter">
                    <option value="" selected>전체</option>
                    @foreach($statuses as $status)
                        <option value="{{$status}}" @selected(request()->status === (string)$status->value)>{{$status->label()}}</option>
                    @endforeach
                </select>
                <div class="col-group">
                    <input type="search" class="search-input" placeholder="제목" name="q"
                           value="{{ request()->q }}">
                    <button type="submit" class="search-btn">
                        <i class="xi-search"></i>
                    </button>
                </div>
                <a href="{{route('admin.news.create')}}" class="create-btn btn blue">
                    등록
                </a>
            </div>
        </form>
    </div>

    <div class="row-group gap24">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                <tr>
                    <th>
                        No
                    </th>
                    <th>
                        공개 여부
                    </th>
                    <th>
                        제목
                    </th>
                    <th>
                        등록일
                    </th>
                    <th>
                        관리
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach ($boards as $key => $board)
                    <tr>
                        <td>
                            {{ $boards->total() - (($boards->currentPage() - 1) * $boards->perPage() + $key) }}
                        </td>
                        <td>
                            <div class="order-state {{$board->status->cssClass()}}">
                                {{$board->status->label()}}
                            </div>
                        </td>
                        <td>
                            <a href="{{route('admin.news.edit', $board)}}">
                                <strong class="underline">
                                    {{$board->title}}
                                </strong>
                            </a>
                        </td>
                        <td>
                            {{$board->created_at->format('Y-m-d H:i:s')}}
                        </td>
                        <td>
                            <div class="sub-btn-wrap col-group gap16">
                                <button class="sub-btn blue"
                                        onclick="window.location.href = '{{route('admin.news.edit', $board)}}'">
                                    수정
                                </button>
                                <form action="{{route('admin.news.destroy', $board)}}" method="post" onsubmit="return formConfirm()">
                                    @csrf
                                    @method('DELETE')
                                    <button class="sub-btn red">
                                        삭제
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div id="pagination">
            @include('admin.components.pagination', ['paginator' => $boards])
        </div>
    </div>

</div>
@include('admin.components.footer')
