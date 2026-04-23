<?php

namespace App\Http\Controllers;

use App\Models\LiveChat;
use App\Models\Transaction;
use App\Services\SmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class LiveChatController extends Controller
{
    public function start(string $transactionId)
    {
        $transaction = Transaction::where('transaction_id', $transactionId)->firstOrFail();

        $chat = LiveChat::where('transaction_id', $transaction->id)
            ->where('status', 'open')
            ->latest('id')
            ->first();

        if (! $chat) {
            $chat = LiveChat::create([
                'transaction_id' => $transaction->id,
                'public_token' => Str::random(40),
                'admin_token' => Str::random(40),
                'status' => 'open',
                'opened_by_phone' => $transaction->sender_mobile,
            ]);

            $chat->messages()->create([
                'sender_type' => 'system',
                'message' => 'Live chat opened for transaction '.$transaction->transaction_id.'.',
            ]);
        }

        if (! $chat->admin_alerted_at) {
            $this->alertAdminBySms($chat, $transaction);
        }

        return redirect()->route('livechat.user', ['token' => $chat->public_token]);
    }

    public function showUser(string $token)
    {
        $chat = LiveChat::with(['transaction', 'messages'])->where('public_token', $token)->firstOrFail();

        return view('process.livechat', [
            'chat' => $chat,
            'transaction' => $chat->transaction,
            'isAdmin' => false,
            'chatToken' => $chat->public_token,
        ]);
    }

    public function showAdmin(string $token)
    {
        $chat = LiveChat::with(['transaction', 'messages'])->where('admin_token', $token)->firstOrFail();

        return view('process.livechat', [
            'chat' => $chat,
            'transaction' => $chat->transaction,
            'isAdmin' => true,
            'chatToken' => $chat->admin_token,
        ]);
    }

    public function messages(Request $request, string $token): JsonResponse
    {
        [$chat, $isAdmin] = $this->resolveChatByToken($token);
        $sinceId = (int) $request->query('since_id', 0);

        $messages = $chat->messages()
            ->when($sinceId > 0, fn ($q) => $q->where('id', '>', $sinceId))
            ->orderBy('id')
            ->get(['id', 'sender_type', 'message', 'created_at']);

        return response()->json([
            'success' => true,
            'is_admin' => $isAdmin,
            'messages' => $messages,
            'typing' => [
                'admin' => $this->isTyping($chat->id, 'admin'),
                'user' => $this->isTyping($chat->id, 'user'),
            ],
        ]);
    }

    public function send(Request $request, string $token): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        [$chat, $isAdmin] = $this->resolveChatByToken($token);

        $msg = $chat->messages()->create([
            'sender_type' => $isAdmin ? 'admin' : 'user',
            'message' => trim($validated['message']),
        ]);
        $this->setTypingState($chat->id, $isAdmin ? 'admin' : 'user', false);

        return response()->json([
            'success' => true,
            'message_id' => $msg->id,
            'message' => [
                'id' => $msg->id,
                'sender_type' => $msg->sender_type,
                'message' => $msg->message,
                'created_at' => optional($msg->created_at)->toISOString(),
            ],
        ]);
    }

    public function typing(Request $request, string $token): JsonResponse
    {
        $validated = $request->validate([
            'is_typing' => ['nullable', 'boolean'],
        ]);

        [$chat, $isAdmin] = $this->resolveChatByToken($token);
        $role = $isAdmin ? 'admin' : 'user';
        $isTyping = (bool) ($validated['is_typing'] ?? true);
        $this->setTypingState($chat->id, $role, $isTyping);

        return response()->json([
            'success' => true,
            'typing' => [
                'admin' => $this->isTyping($chat->id, 'admin'),
                'user' => $this->isTyping($chat->id, 'user'),
            ],
        ]);
    }

    protected function resolveChatByToken(string $token): array
    {
        $chat = LiveChat::where('public_token', $token)->first();
        if ($chat) {
            return [$chat, false];
        }

        $chat = LiveChat::where('admin_token', $token)->firstOrFail();

        return [$chat, true];
    }

    protected function alertAdminBySms(LiveChat $chat, Transaction $transaction): void
    {
        try {
            $adminPhone = '0723014032';
            $joinUrl = route('livechat.admin', ['token' => $chat->admin_token]);
            $summary = "Txn {$transaction->transaction_id}, KES ".number_format((float) $transaction->transaction_amount, 2).", ".
                ($transaction->transaction_type ?? 'escrow');
            $sms = "eConfirm LiveChat: New dispute chat started ({$summary}). Join: {$joinUrl}";

            (new SmsService())->send($adminPhone, $sms, 'livechat-'.$chat->id.'-admin-alert');
            $chat->admin_alerted_at = now();
            $chat->save();
        } catch (\Throwable $e) {
            \Log::error('Live chat admin SMS failed', [
                'chat_id' => $chat->id,
                'transaction_id' => $transaction->transaction_id,
                'error' => $e->getMessage(),
            ]);
        }
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

