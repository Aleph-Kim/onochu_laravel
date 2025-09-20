@include('admin.components.header')
<div class="admin-wrap">

    <div class="main-title-wrap">
        <h2 class="main-title">
            대시보드
        </h2>
    </div>

    <div class="dashboard-section-wrap row-group gap24">
        <div class="dashboard-section">
            <div class="row-group gap16">
                <div class="dashboard-section-title-wrap wrap-group gap16 sp-bt al-st">
                    <div class="title-group row-group gap16 fl-st">
                        <p class="title fs24 fw600">
                            누적 방문자
                        </p>
                        <div class="row-group gap4">
                            <p class="txt fs16 gray">
                                동일한 PC에서 하루에 여러번 방문해도 1회로 체크됩니다.
                            </p>
                        </div>
                    </div>
                    <div class="col-group al-ce gap16">
                        <select name="period" id="periodSelect" class="form-input" onchange="loadChart()">
                            <option value="week" {{ request('period')=='week' ? 'selected' : '' }}>주간</option>
                            <option value="month" {{ request('period')=='month' ? 'selected' : '' }}>월간</option>
                            <option value="year" {{ request('period')=='year' ? 'selected' : '' }}>연간</option>
                        </select>
                    </div>
                </div>

                <div class="chart-wrap">
                    <canvas id="chart"></canvas>
                </div>
            </div>
        </div>

        <div class="dashboard-section">
            <div class="row-group gap24">
                <div class="wrap-group gap16 sp-bt al-en">
                    <p class="title fs24 fw600">
                        방문자 상세 정보
                    </p>

                    <form class="wrap-group gap16" method="get" action="{{ route('admin.main') }}">
                        <div class="col-group al-ce gap16">
                            <input type="date" name="start_date" class="form-input"
                                   value="{{ $startDate->format('Y-m-d') }}"
                                   onchange="loadList()"
                            >
                            -
                            <input type="date" name="end_date" class="form-input"
                                   value="{{ $endDate->format('Y-m-d') }}"
                                   onchange="loadList()"
                            >
                        </div>
                    </form>
                </div>

                <div class="admin-table-wrap">
                    <table class="admin-table" id="logTable">
                        <thead>
                        <th>
                            접속일자
                        </th>
                        <th>
                            IP
                        </th>
                        <th>
                            접속 URL
                        </th>
                        <th>
                            접속 횟수
                        </th>
                        <th>
                            접속기기
                        </th>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="pagination"></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('chart');
    const periodSelect = document.getElementById('periodSelect');
    const startInput = document.querySelector('input[name="start_date"]');
    const endInput = document.querySelector('input[name="end_date"]');
    const paginationDiv = document.getElementById('pagination');

    let chart;

    // 누적 방문자 불러오기
    async function loadChart() {
        // 파라미터 설정
        const params = new URLSearchParams();
        params.set('period', periodSelect.value);
        if (startInput.value) params.set('start_date', startInput.value);
        if (endInput.value) params.set('end_date', endInput.value);

        // 비동기 요청
        const res = await fetch(`{{ route('admin.dashboard.chart') }}?` + params.toString(), {
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        });
        const chatData = await res.json();

        // 차트 데이터 설정
        const data = {
            datasets: [{
                label: '방문자',
                data: chatData,
                borderColor: '#2f83f7',
                backgroundColor: '#fff',
                borderWidth: 1,
                pointRadius: (ctx) => ctx.chart.width < 1280 ? 4 : 6,
                pointBorderWidth: (ctx) => ctx.chart.width < 1280 ? 2 : 4,
                tension: 0.2,
            }]
        };

        // 차트 옵션 설정
        const options = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {display: false},
                tooltip: {
                    callbacks: {label: (c) => ` ${c.parsed.y.toLocaleString()} 명`}
                }
            },
            interaction: {intersect: false, mode: 'index'},
            scales: {
                x: {
                    grid: {display: false},
                    type: 'category',
                    offset: true,
                    ticks: {
                        font: {
                            size: (ctx) => ctx.chart.width < 1280 ? 12 : 16,
                            weight: 600,
                            family: 'Pretendard'
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: (v) => v.toLocaleString(),
                        font: {
                            size: (ctx) => ctx.chart.width < 1280 ? 12 : 14,
                            family: 'Pretendard'
                        }
                    }
                }
            }
        };

        if (chart) { // 차트 업데이트
            chart.data = data;
            chart.options = options;
            chart.update();
        } else { // 차트 생성
            chart = new Chart(ctx, {type: 'line', data, options});
        }

        const current = new URL(window.location.href);
        current.searchParams.set('period', periodSelect.value);
        window.history.replaceState(null, '', current);
    }

    // 방문자 상세 정보 불러오기
    async function loadList(page = 1) {
        // 파라미터 설정
        const params = new URLSearchParams();
        if (startInput.value) params.set('start_date', startInput.value);
        if (endInput.value) params.set('end_date', endInput.value);
        params.set('page', page);

        // 비동기 요청
        const res = await fetch(`{{ route('admin.dashboard.list') }}?` + params.toString(), {
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        });
        const json = await res.json();
        const listData = json.data;
        const tbody = document.querySelector('#logTable tbody');

        // 기존 행 삭제
        tbody.innerHTML = '';

        // 새 데이터 삽입
        if (listData.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center">데이터가 없습니다.</td></tr>`;
            return;
        }

        listData.forEach(row => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${changeKST(row.created_at)}</td>
                <td>${row.ip}</td>
                <td>${row.path}</td>
                <td>${row.view_cnt}</td>
                <td>${row.device}</td>
            `;
            tbody.appendChild(tr);
        });

        paginationDiv.innerHTML = renderPagination(json);

        // 브라우저 주소창의 page 갱신
        const current = new URL(window.location.href);
        current.searchParams.set('page', page);
        if (startInput.value) current.searchParams.set('start_date', startInput.value); else current.searchParams.delete('start_date');
        if (endInput.value)   current.searchParams.set('end_date', endInput.value);   else current.searchParams.delete('end_date');
        window.history.replaceState(null, '', current);
    }

    function renderPagination(paginationData) {
        const {current_page, last_page, prev_page_url, next_page_url} = paginationData;

        let html = '<div class="pagination col-group">';

        // 이전 버튼
        if (prev_page_url) {
            html += `<button onclick="loadList(${current_page - 1})" class="page-btn">
                    <i class="xi-angle-left-min"></i>
                 </button>`;
        }

        // 페이지 번호
        for (let i = 1; i <= last_page; i++) {
            if (i === current_page) {
                html += `<button class="page-btn active">${i}</button>`;
            } else {
                html += `<button onclick="loadList(${i})" class="page-btn">${i}</button>`;
            }
        }

        // 다음 버튼
        if (next_page_url) {
            html += `<button onclick="loadList(${current_page + 1})" class="page-btn">
                    <i class="xi-angle-right-min"></i>
                 </button>`;
        }

        html += '</div>';
        return html;
    }

    // 한국 시간대로 변환
    function changeKST(date) {
        const utcDate = new Date(date);

        // 밀리초 단위로 9시간(9 * 60 * 60 * 1000) 더하기
        const kstDate = new Date(utcDate.getTime() + (9 * 60 * 60 * 1000));

        // Y-m-d H:i:s  형태로 반환
        return formatted = `${kstDate.getFullYear()}-${String(kstDate.getMonth() + 1).padStart(2, '0')}-${String(kstDate.getDate()).padStart(2, '0')} ` +
            `${String(kstDate.getHours()).padStart(2, '0')}:${String(kstDate.getMinutes()).padStart(2, '0')}:${String(kstDate.getSeconds()).padStart(2, '0')}`;
    }

    // 초기 로드
    loadChart();
    loadList();
</script>
@include('admin.components.footer')
