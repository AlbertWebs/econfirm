@extends('process.master')

@section('title', 'Live Chat | eConfirm')

@section('header-actions')
    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Live Chat</span>
@endsection

@section('content')
<style>
    .chat-shell {
        max-width: 920px;
        margin: 0 auto;
    }
    .chat-summary {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #fff;
        padding: 14px;
    }
    .chat-window {
        height: min(58vh, 520px);
        overflow-y: auto;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #fff;
        padding: 12px;
    }
    .chat-bubble {
        max-width: 82%;
        border-radius: 12px;
        padding: 8px 10px;
        margin-bottom: 8px;
        font-size: 0.93rem;
        line-height: 1.35;
        word-break: break-word;
    }
    .chat-bubble.user { background: #dcfce7; margin-left: auto; }
    .chat-bubble.admin { background: #e0e7ff; margin-right: auto; }
    .chat-bubble.system { background: #f3f4f6; margin: 6px auto; text-align: center; max-width: 95%; color: #4b5563; }
    .chat-input-card {
        position: sticky;
        bottom: 0;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 10px;
    }
    .chat-status {
        font-size: 0.8rem;
        color: #6b7280;
        min-height: 1.1rem;
        margin-top: 6px;
    }
    .chat-bubble.pending {
        opacity: 0.75;
        border: 1px dashed #94a3b8;
    }
    @media (max-width: 767.98px) {
        .chat-window { height: 52vh; padding: 10px; }
        .chat-bubble { max-width: 92%; font-size: 0.88rem; }
        .chat-summary { padding: 10px; }
        .chat-input-card { padding: 8px; }
    }
</style>

<div class="chat-shell" data-chat-token="{{ $chatToken }}" data-chat-role="{{ $isAdmin ? 'admin' : 'user' }}">
    <div class="chat-summary mb-3">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <h6 class="mb-1 fw-bold">Dispute Chat — {{ $transaction->transaction_id }}</h6>
                <div class="small text-muted">
                    Type: <strong>{{ $transaction->transaction_type ?? '-' }}</strong> |
                    Amount: <strong>KES {{ number_format((float) $transaction->transaction_amount, 2) }}</strong> |
                    Status: <strong>{{ $transaction->status ?? '-' }}</strong>
                </div>
            </div>
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('transaction.index', ['id' => $transaction->transaction_id]) }}">
                Back to Transaction
            </a>
        </div>
    </div>

    <div id="chatWindow" class="chat-window mb-3" aria-live="polite">
        @foreach($chat->messages as $m)
            <div class="chat-bubble {{ $m->sender_type }}" data-mid="{{ $m->id }}">
                {{ $m->message }}
                <div class="small text-muted mt-1">{{ optional($m->created_at)->format('H:i') }}</div>
            </div>
        @endforeach
    </div>

    <form id="chatForm" class="chat-input-card">
        <div class="d-flex gap-2">
            <input id="chatMessage" type="text" class="form-control" maxlength="2000" placeholder="Type your message..." required>
            <button id="chatSendBtn" type="submit" class="btn btn-danger">Send</button>
        </div>
        <div id="chatStatus" class="chat-status"></div>
    </form>
</div>

<script>
(() => {
    const shell = document.querySelector('.chat-shell');
    if (!shell) return;
    const token = shell.dataset.chatToken;
    const role = shell.dataset.chatRole || 'user';
    const otherRole = role === 'admin' ? 'user' : 'admin';
    const win = document.getElementById('chatWindow');
    const form = document.getElementById('chatForm');
    const input = document.getElementById('chatMessage');
    const btn = document.getElementById('chatSendBtn');
    const statusEl = document.getElementById('chatStatus');
    let lastId = 0;
    const renderedIds = new Set();
    let pendingEl = null;
    let typingHideTimer = null;
    let pollTimer = null;
    let pollInFlight = false;
    let typingDebounceTimer = null;
    let pageHidden = false;

    function scrollToBottom() { win.scrollTop = win.scrollHeight; }
    function escapeHtml(s) {
        return s.replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }

    function fmtTime(raw) {
        const d = raw ? new Date(raw) : new Date();
        return d.toTimeString().slice(0, 5);
    }

    function appendMessage(m) {
        const mid = Number(m.id || 0);
        if (mid && renderedIds.has(mid)) return;
        const div = document.createElement('div');
        div.className = `chat-bubble ${m.sender_type}`;
        if (mid) {
            div.setAttribute('data-mid', String(mid));
            renderedIds.add(mid);
        }
        const time = fmtTime(m.created_at);
        div.innerHTML = `${escapeHtml(m.message)}<div class="small text-muted mt-1">${time}</div>`;
        win.appendChild(div);
        lastId = Math.max(lastId, mid);
    }

    function showStatus(text = '') {
        if (!statusEl) return;
        statusEl.textContent = text;
    }

    function showRemoteTyping(active) {
        if (active) {
            showStatus(`${otherRole === 'admin' ? 'Admin' : 'User'} is typing...`);
            if (typingHideTimer) clearTimeout(typingHideTimer);
            typingHideTimer = setTimeout(() => showStatus(''), 1800);
            return;
        }
        if (!pendingEl) showStatus('');
    }

    function addPendingBubble(text) {
        const div = document.createElement('div');
        div.className = `chat-bubble ${role} pending`;
        div.setAttribute('data-pending', '1');
        div.innerHTML = `${escapeHtml(text)}<div class="small text-muted mt-1">Sending...</div>`;
        win.appendChild(div);
        pendingEl = div;
        scrollToBottom();
    }

    function clearPendingBubble() {
        if (pendingEl && pendingEl.parentNode) {
            pendingEl.parentNode.removeChild(pendingEl);
        }
        pendingEl = null;
    }

    // initialize lastId from rendered messages
    const ids = Array.from(win.querySelectorAll('.chat-bubble[data-mid]')).map(el => Number(el.getAttribute('data-mid') || 0));
    ids.forEach(id => renderedIds.add(id));
    if (ids.length) lastId = Math.max(...ids);
    scrollToBottom();

    async function poll() {
        if (pollInFlight) return;
        pollInFlight = true;
        try {
            const url = `/livechat/${encodeURIComponent(token)}/messages?since_id=${lastId}&_=${Date.now()}`;
            const res = await fetch(url, {
                cache: 'no-store',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Cache-Control': 'no-cache' }
            });
            const data = await res.json();
            if (data && data.success && Array.isArray(data.messages) && data.messages.length) {
                data.messages.forEach(appendMessage);
                scrollToBottom();
            }
            if (data && data.typing && typeof data.typing[otherRole] !== 'undefined') {
                showRemoteTyping(Boolean(data.typing[otherRole]));
            }
        } catch (_) {
            showStatus('Connection issue. Retrying…');
        } finally {
            pollInFlight = false;
        }
    }

    async function sendTyping(isTyping) {
        try {
            await fetch(`/livechat/${encodeURIComponent(token)}/typing`, {
                method: 'POST',
                cache: 'no-store',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache'
                },
                body: JSON.stringify({ is_typing: !!isTyping })
            });
        } catch (_) {
            // silent; polling keeps chat alive
        }
    }

    function queueTypingPing() {
        if (typingDebounceTimer) clearTimeout(typingDebounceTimer);
        typingDebounceTimer = setTimeout(() => sendTyping(input.value.trim().length > 0), 150);
    }

    input.addEventListener('input', queueTypingPing);
    input.addEventListener('focus', () => {
        if (input.value.trim().length > 0) sendTyping(true);
    });
    input.addEventListener('blur', () => sendTyping(false));

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const message = input.value.trim();
        if (!message) return;
        btn.disabled = true;
        showStatus('Sending…');
        sendTyping(false);
        addPendingBubble(message);
        try {
            const res = await fetch(`/livechat/${encodeURIComponent(token)}/send`, {
                method: 'POST',
                cache: 'no-store',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache'
                },
                body: JSON.stringify({ message })
            });
            const data = await res.json();
            if (data && data.success) {
                input.value = '';
                clearPendingBubble();
                if (data.message) {
                    appendMessage(data.message);
                    scrollToBottom();
                } else {
                    await poll();
                }
                showStatus('Sent');
                setTimeout(() => showStatus(''), 700);
                poll();
            } else {
                clearPendingBubble();
                showStatus((data && data.message) ? data.message : 'Could not send message.');
            }
        } catch (_) {
            clearPendingBubble();
            showStatus('Failed to send. Check your connection and retry.');
        } finally {
            btn.disabled = false;
            input.focus();
        }
    });

    function startPolling() {
        if (pollTimer) clearInterval(pollTimer);
        pollTimer = setInterval(poll, pageHidden ? 1800 : 450);
    }

    document.addEventListener('visibilitychange', () => {
        pageHidden = document.hidden;
        startPolling();
        if (!pageHidden) poll();
    });

    window.addEventListener('beforeunload', () => {
        sendTyping(false);
    });

    poll();
    startPolling();
})();
</script>
@endsection

