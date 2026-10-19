<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    public function send(int $userId, string $message, string $type = 'general', ?string $link = null): Notification
    {
        return Notification::create([
            'user_id' => $userId,
            'notification_message' => $message,
            'type' => $type,
            'link' => $link,
            'notification_date' => now(),
        ]);
    }

    public function achievementApproved(int $userId, string $title): void
    {
        $this->send(
            $userId,
            "Your achievement \"{$title}\" has been approved.",
            'achievement_approved',
            route('achievements.index')
        );
    }

    public function achievementRejected(int $userId, string $title, ?string $reason = null): void
    {
        $message = "Your achievement \"{$title}\" was rejected." . ($reason ? " Reason: {$reason}" : '');
        $this->send($userId, $message, 'achievement_rejected', route('achievements.index'));
    }

    public function participationVerified(int $userId, string $activityName): void
    {
        $this->send(
            $userId,
            "Your participation in \"{$activityName}\" has been verified.",
            'participation_verified',
            route('student.participations.index')
        );
    }

    public function participationRejected(int $userId, string $activityName, ?string $reason = null): void
    {
        $message = "Your participation in \"{$activityName}\" was rejected." . ($reason ? " Reason: {$reason}" : '');
        $this->send($userId, $message, 'participation_rejected', route('student.participations.index'));
    }

    public function newAnnouncement(int $userId, string $title): void
    {
        $this->send(
            $userId,
            "New announcement: {$title}",
            'announcement',
            route('student.announcements.index')
        );
    }

    public function activityInvitation(int $userId, string $activityName, string $activityUrl): void
    {
        $this->send(
            $userId,
            "You may be interested in \"{$activityName}\" — your talent matches this activity!",
            'activity_invitation',
            $activityUrl
        );
    }
}
