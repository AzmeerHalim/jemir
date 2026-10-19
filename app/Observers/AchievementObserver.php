<?php

namespace App\Observers;

use App\Models\Achievement;
use App\Services\MeritService;

class AchievementObserver
{
    public function __construct(private MeritService $merit) {}

    public function created(Achievement $achievement): void
    {
        $this->merit->recomputeAll();
    }

    public function updated(Achievement $achievement): void
    {
        if ($achievement->isDirty('status') || $achievement->isDirty('score')) {
            $this->merit->recomputeAll();
        }
    }

    public function deleted(Achievement $achievement): void
    {
        $this->merit->recomputeAll();
    }
}
