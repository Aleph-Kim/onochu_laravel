@extends('layouts.app')

@section('content')
<div class="p-5 max-w-[500px] mx-auto">
    <form class="recommends-form flex flex-col items-center" action="{{ route('recommends.post') }}" method="post">
        @csrf
        <div class="flex w-full items-center gap-3 mb-5">
            <img src="{{ $songInfo['artists'][0]['img_url'] }}?/dims/resize/200x200/quality/90"
                 class="w-10 h-10 rounded-full object-cover cursor-pointer transition hover:opacity-80 shadow-sm"
                 onclick="window.location.href = '{{ route('artist.detail', ['id' => $songInfo['artists'][0]['flo_id']]) }}'">
            <span>
                @foreach($songInfo['artists'] as $artist)
                    <span class="text-sm text-[#333] font-medium cursor-pointer transition hover:text-primary artist-name"
                          onclick="window.location.href = '{{ route('artist.detail', ['id' => $artist['flo_id']]) }}'">{{ $artist['name'] }}</span>
                @endforeach
            </span>
        </div>
        <div class="w-full aspect-square mb-4 overflow-hidden rounded-2xl">
            <img src="{{ $songInfo['album']['img_url'] }}?/dims/resize/350x350/quality/90" class="w-full h-full object-cover">
        </div>
        <div class="w-full text-left mb-4">
            <h2 class="text-[20px] font-bold mb-2 text-[#111] tracking-tight">{{ $songInfo['song']['title'] }}</h2>
            <p class="text-[13px] text-[#8b8b9a] flex items-center gap-2">
                {{ $songInfo['album']['release_date'] ?? '발매일 미상' }}
                <span class="inline-block w-px h-2.5 mx-1 my-0 bg-[#c8c8d8]"></span>
                {{ $songInfo['song']['genre'] }}
                <span class="inline-block w-px h-2.5 mx-1 my-0 bg-[#c8c8d8]"></span>
                {{ $songInfo['song']['play_time'] }}
            </p>
        </div>
        <div class="[direction:rtl] text-[40px] my-4 mx-0">
            <input type="radio" id="star5" name="score" value="5" class="peer hidden">
            <label for="star5" class="peer text-[#e0e0e0] cursor-pointer hover:text-amber-500 peer-checked:text-amber-500 peer-hover:text-amber-500">★</label>
            <input type="radio" id="star4" name="score" value="4" class="peer hidden">
            <label for="star4" class="peer text-[#e0e0e0] cursor-pointer hover:text-amber-500 peer-checked:text-amber-500 peer-hover:text-amber-500">★</label>
            <input type="radio" id="star3" name="score" value="3" checked class="peer hidden">
            <label for="star3" class="peer text-[#e0e0e0] cursor-pointer hover:text-amber-500 peer-checked:text-amber-500 peer-hover:text-amber-500">★</label>
            <input type="radio" id="star2" name="score" value="2" class="peer hidden">
            <label for="star2" class="peer text-[#e0e0e0] cursor-pointer hover:text-amber-500 peer-checked:text-amber-500 peer-hover:text-amber-500">★</label>
            <input type="radio" id="star1" name="score" value="1" class="peer hidden">
            <label for="star1" class="peer text-[#e0e0e0] cursor-pointer hover:text-amber-500 peer-checked:text-amber-500 peer-hover:text-amber-500">★</label>
        </div>
        <textarea name="comment" placeholder="코멘트를 남겨주세요!"
                  class="w-full h-[120px] p-4 rounded-2xl resize-none text-sm mb-4 border-[1.5px] border-[#ebebf0] bg-[#f0f0f8] placeholder:text-[#b0b0c0] focus:outline-none focus:border-[#d8d8ec] focus:shadow-[0_0_0_3px_rgba(91,91,214,0.06)]"></textarea>
        <div class="w-full flex justify-end gap-2">
            <button type="button" class="py-2 px-5 rounded-full text-sm font-medium cursor-pointer bg-[#f0f0f8] text-[#555] hover:bg-[#e5e5ef] transition"
                    onclick="confirmBack()">뒤로가기</button>
            <button type="submit" class="btn-submit py-2 px-6 rounded-full text-sm font-semibold cursor-pointer bg-primary text-white hover:bg-primary-light shadow-sm hover:shadow-md transition">추천</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/recommends.js') }}"></script>
@endpush
