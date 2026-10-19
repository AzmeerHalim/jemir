<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Achievement scoring
    |--------------------------------------------------------------------------
    |
    | Map of achievement.level -> score. Used by both:
    |   - AchievementController::approve (single-stage student-initiated achievements)
    |   - Coordinator\ParticipationApprovalController::approve
    |     (auto-creates an achievement row when a participation is approved)
    |
    */

    'scores' => [
        'faculty' => 5,
        'university' => 10,
        'national' => 20,
        'international' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Activity level -> achievement level
    |--------------------------------------------------------------------------
    |
    | When the coordinator approves a participation, the system auto-creates
    | an achievement row using this map. The activities table has an extra
    | 'state' level that the achievements.level enum does not support, so it
    | is folded into 'university' here.
    |
    */

    'activity_to_achievement_level' => [
        'faculty' => 'faculty',
        'university' => 'university',
        'state' => 'university',
        'national' => 'national',
        'international' => 'international',
    ],

    'default_score' => 0,
];
