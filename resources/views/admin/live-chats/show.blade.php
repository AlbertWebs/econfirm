@extends('layouts.admin')

@section('title', 'Chat #'.$chat->id)
@section('page_title', 'Live chat (staff)')

@section('content')
    <div class="mb-6 flex flex-wrap gap-2">
        <a href="{{ route('admin.live-chats.index') }}" class="inline-flex rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
            All chats
        </a>
        <a href="{{ route('transaction.index', ['id' => $transaction->transaction_id]) }}" target="_blank" rel="noopener noreferrer" class="inline-flex rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-800 hover:bg-emerald-100">
            Public transaction
        </a>
    </div>

    <div class="mb-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <p class="font-semibold text-slate-900">Dispute chat — {{ $transaction->transaction_id }}</p>
        <p class="mt-1 text-sm text-slate-600">
            Amount: <span class="font-medium tabular-nums text-slate-800">KES {{ number_format((float) $transaction->transaction_amount, 2) }}</span>
            <span class="mx-2 text-slate-300">·</span>
            Status: <span class="font-medium text-slate-800">{{ $transaction->status }}</span>
        </p>
    </div>

    <div
        class="chat-shell"
        data-messages-url="{{ $messagesUrl }}"
        data-send-url="{{ $sendUrl }}"
        data-typing-url="{{ $typingUrl }}"
        data-role="admin"
        data-csrf="{{ csrf_token() }}"
    >
        <div
            id="chatWindow"
            class="mb-3 max-h-[min(50vh,480px)] min-h-[200px] overflow-y-auto rounded-xl border border-slate-200 bg-white p-3 shadow-inner"
            aria-live="polite"
        >
            @foreach ($chat->messages as $m)
                <div
                    @class([
                        'chat-bubble last:mb-0',
                        'admin-chat-msg-user' => $m->sender_type === 'user',
                        'admin-chat-msg-admin' => $m->sender_type === 'admin',
                        'admin-chat-msg-system' => ! in_array($m->sender_type, ['user', 'admin'], true),
                    ])
                    data-mid="{{ $m->id }}"
                >
                    {{ $m->message }}
                    <div class="mt-1 text-xs text-slate-500">{{ optional($m->created_at)->format('H:i') }}</div>
                </div>
            @endforeach
        </div>

        <form id="chatForm" class="rounded-xl border border-slate-200 bg-slate-50 p-3">
            @csrf
            <div class="flex gap-2">
                <input
                    id="chatMessage"
                    type="text"
                    maxlength="2000"
                    placeholder="Type as staff…"
                    required
                    class="block min-w-0 flex-1 rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                >
                <button id="chatSendBtn" type="submit" class="shrink-0 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                    Send
                </button>
            </div>
            <div id="chatStatus" class="mt-2 min-h-[1.1rem] text-xs text-slate-500"></div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
(() => {
    const shell = document.querySelector('.chat-shell');
    if (!shell) return;
    const messagesUrl = shell.dataset.messagesUrl;
    const sendUrl = shell.dataset.sendUrl;
    const typingUrl = shell.dataset.typingUrl;
    const win = document.getElementById('chatWindow');
    const form = document.getElementById('chatForm');
    const input = document.getElementById('chatMessage');
    const btn = document.getElementById('chatSendBtn');
    const statusEl = document.getElementById('chatStatus');
    const csrf = shell.dataset.csrf || '';
    const otherRole = 'user';
    let lastId = 0;
    const renderedIds = new Set();
    let pollTimer = null, pollInFlight = false, typingDebounceTimer = null, typingHideTimer = null, pageHidden = false;

    function scrollToBottom() { win.scrollTop = win.scrollHeight; }
    function escapeHtml(s) {
        return s.replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }
    function fmtTime(raw) {
        return raw ? new Date(raw).toTimeString().slice(0, 5) : new Date().toTimeString().slice(0, 5);
    }
    function appendMessage(m) {
        const mid = Number(m.id || 0);
        if (mid && renderedIds.has(mid)) return;
        const div = document.createElement('div');
        const st = m.sender_type || 'system';
        div.className = 'chat-bubble ' + (st === 'user' ? 'admin-chat-msg-user' : st === 'admin' ? 'admin-chat-msg-admin' : 'admin-chat-msg-system');
        if (mid) { div.setAttribute('data-mid', String(mid)); renderedIds.add(mid); }
        div.innerHTML = escapeHtml(m.message) + '<div class="mt-1 text-xs text-slate-500">' + fmtTime(m.created_at) + '</div>';
        win.appendChild(div);
        lastId = Math.max(lastId, mid);
    }
    function showStatus(t) { if (statusEl) statusEl.textContent = t || ''; }
    const ids = Array.from(win.querySelectorAll('.chat-bubble[data-mid]')).map(el => Number(el.getAttribute('data-mid') || 0));
    ids.forEach(id => renderedIds.add(id));
    if (ids.length) lastId = Math.max(...ids);
    scrollToBottom();

    async function poll() {
        if (pollInFlight) return;
        pollInFlight = true;
        try {
            const url = messagesUrl + (messagesUrl.includes('?') ? '&' : '?') + 'since_id=' + lastId + '&_=' + Date.now();
            const res = await fetch(url, { cache: 'no-store', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            if (data && data.success && Array.isArray(data.messages) && data.messages.length) {
                data.messages.forEach(appendMessage);
                scrollToBottom();
            }
            if (data && data.typing && typeof data.typing[otherRole] !== 'undefined' && data.typing[otherRole]) {
                showStatus('User is typing…');
                if (typingHideTimer) clearTimeout(typingHideTimer);
                typingHideTimer = setTimeout(() => showStatus(''), 1800);
            }
        } catch (_) { showStatus('Connection issue…'); }
        finally { pollInFlight = false; }
    }

    async function sendTyping(isTyping) {
        try {
            await fetch(typingUrl, { method: 'POST', cache: 'no-store', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ is_typing: !!isTyping }) });
        } catch (_) {}
    }
    function queueTypingPing() {
        if (typingDebounceTimer) clearTimeout(typingDebounceTimer);
        typingDebounceTimer = setTimeout(() => sendTyping(input.value.trim().length > 0), 150);
    }
    input.addEventListener('input', queueTypingPing);
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const message = input.value.trim();
        if (!message) return;
        btn.disabled = true;
        showStatus('Sending…');
        try {
            const res = await fetch(sendUrl, { method: 'POST', cache: 'no-store', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ message }) });
            const data = await res.json();
            if (data && data.success) {
                input.value = '';
                if (data.message) { appendMessage(data.message); scrollToBottom(); } else { await poll(); }
                showStatus('Sent');
                setTimeout(() => showStatus(''), 600);
            } else { showStatus('Could not send.'); }
        } catch (_) { showStatus('Failed to send.'); }
        finally { btn.disabled = false; input.focus(); }
    });
    function startPolling() { if (pollTimer) clearInterval(pollTimer); pollTimer = setInterval(poll, pageHidden ? 1800 : 450); }
    document.addEventListener('visibilitychange', () => { pageHidden = document.hidden; startPolling(); if (!pageHidden) poll(); });
    poll();
    startPolling();
})();
</script>
@endpush
