@include('admin.components.header')

<div class="admin-wrap main-content-wrap">
    <form action="{{route('admin.user.update', $user)}}" method="post">
        @csrf
        @method('PATCH')
        <div class="main-title-wrap">
            <div class="col-group sp-bt al-en">
                <h2 class="main-title">
                    관리자 수정
                </h2>

                <div class="col-group gap16">
                    <button class="btn btn-m gray">
                        삭제
                    </button>
                    <button class="btn btn-m blue">
                        저장
                    </button>
                </div>
            </div>
        </div>

        <div class="row-group gap40">
            <div class="form-list col-group per3">
                <div class="form-item row-group">
                    <div class="item-default">
                        이름
                    </div>
                    <div class="item-user">
                        <input type="text" name="name" class="form-input" placeholder="홍길동" value="{{$user->name}}">
                    </div>
                    @error('name')
                    <span class="error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-list col-group per3">
                <div class="form-item row-group">
                    <div class="item-default">
                        아이디
                    </div>
                    <div class="item-user">
                        <input type="text" name="login_id" class="form-input disable" placeholder="로그인 아이디"
                               value="{{$user->login_id}}"
                               readonly
                        >
                    </div>
                    @error('login_id')
                    <span class="error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-list col-group per3">
                <div class="form-item row-group">
                    <div class="item-default">
                        비밀번호
                    </div>
                    <div class="item-user">
                        <input type="password" name="password" class="form-input" placeholder="비밀번호">
                    </div>
                    @error('password')
                    <span class="error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-list col-group per3">
                <div class="form-item row-group">
                    <div class="item-default">
                        비밀번호 확인
                    </div>
                    <div class="item-user">
                        <input type="password" name="password_confirmation" class="form-input" placeholder="비밀번호 확인">
                    </div>
                    @error('password_confirmation')
                    <span class="error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
    </form>
</div>

@include('admin.components.footer')
