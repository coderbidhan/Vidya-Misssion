<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BroadcastNotificationRequest;
use App\Models\Notification;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    use ApiResponse;

    /**
     * Broadcast a notification to all users, specific class, or a specific set of users.
     *
     * POST /api/admin/notifications/broadcast
     */
    public function broadcast(BroadcastNotificationRequest $request): JsonResponse
    {
        $userIds = $request->input('user_ids', 'all');
        $targetType = $request->input('target_type');
        $targetClass = $request->input('target_class');

        $query = User::where('is_active', true)->where('role', 'student');

        if ($targetType === 'trial') {
            $query->whereNotNull('trial_starts_at')->where('trial_ends_at', '>=', now());
        } elseif ($targetClass || $targetType === 'class') {
            $classLevel = $targetClass ?: $request->input('class');
            if ($classLevel) {
                $query->whereHas('studentProfile', fn ($q) => $q->where('class', $classLevel));
            }
        } elseif ($userIds !== 'all' && is_array($userIds)) {
            $query->whereIn('id', $userIds);
        }

        $title = strip_tags($request->title);
        $body = strip_tags($request->input('body') ?: $request->input('message'));

        $recipients = $query->pluck('id');

        $notifications = $recipients->map(fn ($userId) => [
            'user_id' => $userId,
            'title' => $title,
            'body' => $body,
            'type' => 'broadcast',
            'data' => json_encode(['target_type' => $targetType ?: 'all', 'target_class' => $targetClass]),
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();

        // Batch insert in chunks to avoid query size limits
        if (! empty($notifications)) {
            collect($notifications)->chunk(500)->each(fn ($chunk) => Notification::insert($chunk->toArray()));
        }

        return $this->successResponse([
            'recipients_count' => count($notifications),
        ], 'Notification broadcast to '.count($notifications).' users.');
    }

    /**
     * Get recent broadcasts grouped by title and created timestamp.
     *
     * GET /api/admin/notifications/recent
     */
    public function recent(): JsonResponse
    {
        $recent = Notification::where('type', 'broadcast')
            ->selectRaw('title, body, count(*) as recipients_count, max(created_at) as created_at')
            ->groupBy('title', 'body')
            ->orderByDesc('created_at')
            ->limit(15)
            ->get();

        return $this->successResponse($recent);
    }
}
