<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveChat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LiveChatController extends Controller
{
    public function index()
    {
        $chats = LiveChat::query()
            ->withCount('messages')
            ->with(['transaction:id,transaction_id,status,transaction_amount,transaction_type'])
            ->orderByDesc('updated_at')
            ->paginate(30);

        return view('admin.live-chats.index', compact('chats'));
    }

    public function show(LiveChat $liveChat)
    {
        $liveChat->load(['transaction', 'messages']);

        return view('admin.live-chats.show', [
            'chat' => $liveChat,
            'transaction' => $liveChat->transaction,
            'messagesUrl' => route('admin.live-chats.messages', $liveChat),
            'sendUrl' => route('admin.live-chats.send', $liveChat),
            'typingUrl' => route('admin.live-chats.typing', $liveChat),
        ]);
    }

    public function messages(Request $request, LiveChat $liveChat): JsonResponse
    {
        $sinceId = (int) $request->query('since_id', 0);

        $messages = $liveChat->messages()
            ->when($sinceId > 0, fn ($q) => $q->where('id', '>', $sinceId))
            ->orderBy('id')
            ->get(['id', 'sender_type', 'message', 'created_at']);

        return response()->json([
            'success' => true,
            'is_admin' => true,
            'messages' => $messages,
            'typing' => [
                'admin' => $this->isTyping($liveChat->id, 'admin'),
                'user' => $this->isTyping($liveChat->id, 'user'),
            ],
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function send(Request $request, LiveChat $liveChat): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $msg = $liveChat->messages()->create([
            'sender_type' => 'admin',
            'message' => trim($validated['message']),
        ]);
        $this->setTypingState($liveChat->id, 'admin', false);

        return response()->json([
            'success' => true,
            'message_id' => $msg->id,
            'message' => [
                'id' => $msg->id,
                'sender_type' => $msg->sender_type,
                'message' => $msg->message,
                'created_at' => optional($msg->created_at)->toISOString(),
            ],
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function typing(Request $request, LiveChat $liveChat): JsonResponse
    {
        $validated = $request->validate([
            'is_typing' => ['nullable', 'boolean'],
        ]);

        $isTyping = (bool) ($validated['is_typing'] ?? true);
        $this->setTypingState($liveChat->id, 'admin', $isTyping);

        return response()->json([
            'success' => true,
            'typing' => [
                'admin' => $this->isTyping($liveChat->id, 'admin'),
                'user' => $this->isTyping($liveChat->id, 'user'),
            ],
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    protected function typingCacheKey(int $chatId, string $role): string
    {
        return "livechat:typing:{$chatId}:{$role}";
    }

    protected function setTypingState(int $chatId, string $role, bool $active): void
    {
        $key = $this->typingCacheKey($chatId, $role);
        if (! $active) {
            Cache::forget($key);

            return;
        }

        Cache::put($key, now()->timestamp, now()->addSeconds(7));
    }

    protected function isTyping(int $chatId, string $role): bool
    {
        return Cache::has($this->typingCacheKey($chatId, $role));
    }
}
