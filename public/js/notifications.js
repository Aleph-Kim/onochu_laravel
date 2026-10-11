// 헤더 알림 탭 (신곡 알림 목록 조회 및 개별/전체 읽음 처리)

const notificationBtn = document.getElementById('notificationBtn');
const notificationDropdown = document.getElementById('notificationDropdown');
const notificationBadge = document.getElementById('notificationBadge');
const notificationList = document.getElementById('notificationList');
const notificationEmpty = document.getElementById('notificationEmpty');

function escapeHtml(value) {
    const div = document.createElement('div');
    div.textContent = value ?? '';
    return div.innerHTML;
}

function renderNotifications(items) {
    notificationList.innerHTML = items.map(item => `
        <a href="${escapeHtml(item.url)}" data-notification-id="${escapeHtml(item.id)}"
           data-unread="${item.unread ? '1' : '0'}"
           class="notification-item group flex items-start gap-3 px-4 py-3 hover:bg-fill transition-colors">
            <img src="${escapeHtml(item.img_url)}?/dims/resize/200x200/quality/90" alt="" loading="lazy"
                 class="w-11 h-11 rounded-md object-cover flex-none group-data-[unread=0]:opacity-50">
            <span class="min-w-0 flex-1 flex flex-col gap-1.5">
                ${item.label ? `<span class="self-start text-caption-2 font-semibold text-primary group-data-[unread=0]:opacity-50">${escapeHtml(item.label)}</span>` : ''}
                <span class="block text-subheadline font-semibold text-label line-clamp-2 group-data-[unread=0]:font-normal group-data-[unread=0]:text-label-secondary">${escapeHtml(item.title)}</span>
            </span>
            <span class="w-2 h-2 mt-1.5 rounded-full bg-primary flex-none group-data-[unread=0]:hidden"></span>
        </a>
    `).join('');

    notificationEmpty.classList.toggle('hidden', items.length > 0);
}

function setNotificationBadge(count) {
    if (count > 0) {
        notificationBadge.textContent = count > 99 ? '99+' : count;
        notificationBadge.classList.remove('hidden');
    } else {
        notificationBadge.classList.add('hidden');
    }
}

// 알림 탭을 열면 최신 목록을 불러옴 (읽음 처리는 개별 클릭 또는 '모두 읽음' 버튼으로만 발생)
function loadNotifications() {
    fetchApi('/api/notifications', null, 'GET')
        .then(response => {
            if (!response.success) {
                return;
            }
            renderNotifications(response.data.notifications);
            setNotificationBadge(response.data.unread_count);
        })
        .catch(() => {
        });
}

function toggleNotificationDropdown() {
    const willOpen = notificationDropdown.classList.contains('hidden');
    notificationDropdown.classList.toggle('hidden');

    if (willOpen) {
        loadNotifications();
    }
}

// 개별 알림 클릭 시 해당 항목만 읽음 처리 (이동은 기본 링크 동작에 맡김)
notificationList.addEventListener('click', (event) => {
    const item = event.target.closest('.notification-item');
    if (!item || item.dataset.unread !== '1') {
        return;
    }

    item.dataset.unread = '0';
    // 배지가 '99+'여도 parseInt는 99로 읽어 정확한 실제 개수는 아니지만 표시상 문제 없음
    setNotificationBadge(Math.max(0, (parseInt(notificationBadge.textContent, 10) || 0) - 1));

    fetchApi(`/api/notifications/${item.dataset.notificationId}/read`, null, 'POST').catch(() => {
    });
});

function markAllNotificationsRead() {
    fetchApi('/api/notifications/read-all', null, 'POST')
        .then(response => {
            if (!response.success) {
                return;
            }
            notificationList.querySelectorAll('.notification-item[data-unread="1"]').forEach(item => {
                item.dataset.unread = '0';
            });
            setNotificationBadge(0);
        })
        .catch(() => {
        });
}

document.addEventListener('click', (event) => {
    const notificationBox = document.getElementById('notificationBox');
    if (notificationBox && !notificationBox.contains(event.target)) {
        notificationDropdown.classList.add('hidden');
    }
});
