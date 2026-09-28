<?php

namespace App\Services;

use App\Models\Chapter;
use App\Models\Progress;
use App\Models\Reward;
use App\Models\Subject;
use App\Models\User;

class ProgressService
{
    /**
     * Mark a chapter as completed for a user.
     * Creates or updates the progress record and sets completed_at.
     */
    public function markChapterComplete(User $user, Chapter $chapter): Progress
    {
        $progress = Progress::firstOrCreate(
            ['user_id' => $user->id, 'chapter_id' => $chapter->id],
            ['is_completed' => false]
        );

        if (! $progress->is_completed) {
            $progress->update([
                'is_completed' => true,
                'completed_at' => now(),
            ]);
        }

        return $progress;
    }

    /**
     * Get the user's progress for all chapters in a subject.
     *
     * @return array Array of ['chapter_id' => bool] completion status.
     */
    public function getUserProgress(User $user, Subject $subject): array
    {
        $chapterIds = $subject->chapters()->pluck('id');

        $completed = Progress::where('user_id', $user->id)
            ->whereIn('chapter_id', $chapterIds)
            ->where('is_completed', true)
            ->pluck('chapter_id')
            ->toArray();

        return $chapterIds->mapWithKeys(fn ($id) => [$id => in_array($id, $completed)])->toArray();
    }

    /**
     * Award a reward to a user for a chapter or class achievement.
     */
    public function awardReward(User $user, ?Chapter $chapter, string $type, array $data = []): Reward
    {
        return Reward::create([
            'user_id' => $user->id,
            'chapter_id' => $chapter?->id,
            'reward_type' => $type,
            'reward_data' => $data,
            'earned_at' => now(),
        ]);
    }
}
