<?php

namespace App\Enums;

enum UserRole: string
{
    case Student = 'student';
    case Coordinator = 'coordinator';
    case Lecturer = 'lecturer';
    case ItcAdmin = 'itc_admin';
    case Committee = 'committee';
    case StudentAffairs = 'student_affairs';

    public function label(): string
    {
        return match ($this) {
            self::Student => 'Student',
            self::Coordinator => 'Talent Unit Coordinator',
            self::Lecturer => 'Accompanying Lecturer',
            self::ItcAdmin => 'ITC Admin',
            self::Committee => 'Committee Member FSKTM',
            self::StudentAffairs => 'Student Affairs UTHM',
        };
    }

    public function portal(): string
    {
        return match ($this) {
            self::Student => 'Student Portal',
            self::Coordinator => 'Coordinator Portal',
            self::Lecturer => 'Lecturer Portal',
            self::ItcAdmin => 'ITC Admin Portal',
            self::Committee => 'Committee Portal',
            self::StudentAffairs => 'Student Affairs Portal',
        };
    }

    public function dashboardRoute(): string
    {
        return match ($this) {
            self::Student => 'student.dashboard',
            self::Coordinator => 'coordinator.dashboard',
            self::Lecturer => 'lecturer.dashboard',
            self::ItcAdmin => 'itc.dashboard',
            self::Committee => 'committee.dashboard',
            self::StudentAffairs => 'affairs.dashboard',
        };
    }

    public function homeRoute(): string
    {
        return match ($this) {
            self::Student => 'dashboard',
            self::Coordinator => 'coordinator.dashboard',
            self::Lecturer => 'lecturer.dashboard',
            self::ItcAdmin => 'itc.dashboard',
            self::Committee => 'committee.dashboard',
            self::StudentAffairs => 'affairs.dashboard',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
