@include('admin.components.header')

<div class="admin-wrap main-content-wrap">
    <div class="main-title-wrap sp-bt al-en">
        <h2 class="main-title">
            관리자 목록
        </h2>
        <form action="{{route('admin.user.index')}}" class="col-group fl-en" method="get">
            <div class="col-group gap16 ml-auto">
                <div class="col-group">
                    <input type="search" class="search-input" placeholder="이름" name="name"
                           value="{{request()->name}}">
                    <button type="submit" class="search-btn">
                        <i class="xi-search"></i>
                    </button>
                </div>
                <a href="{{route('admin.user.create')}}" class="create-btn btn blue">
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
                        이름
                    </th>
                    <th>
                        ID
                    </th>
                    <th>
                        등록일
                    </th>
                    <th>
                        수정 / 삭제
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach ($users as $key => $user)
                    <tr>
                        <td>
                            {{ ($users->currentPage() - 1) * $users->perPage() + $key + 1 }}
                        </td>
                        <td>
                            {{ $user->name  }}
                        </td>
                        <td>
                            {{ $user->login_id }}
                        </td>
                        <td>
                            {{$user->created_at->format('Y-m-d H:i:s')}}
                        </td>
                        <td>
                            <div class="sub-btn-wrap col-group gap16">
                                <button class="sub-btn blue"
                                        onclick="window.location.href = '{{route('admin.user.edit', $user)}}'">
                                    수정
                                </button>
                                @if($user->id != 1)
                                <form action="{{route('admin.user.destroy', $user)}}" method="post" onsubmit="return formConfirm()">
                                    @csrf
                                    @method('DELETE')
                                    <button class="sub-btn red">
                                        삭제
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div id="pagination">
            @include('admin.components.pagination', ['paginator' => $users])
        </div>
    </div>
</div>

@include('admin.components.footer')
