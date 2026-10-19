# Student Talent Management System

A multi-role web application for managing, tracking, and recognising student talents and achievements — built for **FSKTM, Universiti Tun Hussein Onn Malaysia (UTHM)**.

## Features

- **6 User Roles** — Student, Coordinator, Lecturer, ITC Admin, Committee, Student Affairs
- **Two-Stage Approval Workflow** — Lecturer verifies → Coordinator approves (for both achievements and activity participations)
- **Smart Talent Matching** — ITC Admin can define required talents on activities and auto-invite matching students
- **Auto-Achievement Creation** — Verified participations are automatically converted into scored achievement records
- **Merit & Leaderboard System** — Real-time rankings computed from approved achievement scores
- **In-App Notifications** — Students receive notifications for every approval, rejection, announcement, and invitation
- **Report Export** — Coordinator and management can export achievement/student reports
- **Student Awards** — Special recognition for top-performing students

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 12 (PHP 8.x) |
| Database | MySQL / MariaDB |
| Frontend | Blade Templates + TailwindCSS |
| Build Tool | Vite |
| Auth | Laravel Breeze |

## Installation

### Prerequisites

- PHP 8.2+
- Composer
- Node.js & npm
- MySQL / MariaDB

### Setup

```bash
# Clone the repository
git clone https://github.com/YOUR_USERNAME/student-talent-management-system.git
cd student-talent-management-system

# Install PHP dependencies
composer install

# Install JS dependencies
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Configure database in .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=student_talent_db
# DB_USERNAME=root
# DB_PASSWORD=

# Run migrations
php artisan migrate

# Create storage symlink
php artisan storage:link

# Start development servers
php artisan serve
npm run dev
```

## User Roles

| Role | Description |
|---|---|
| **Student** | Submit achievements, join activities, upload proof, view leaderboard |
| **Coordinator** | Final approver, manages users/categories/awards, broadcasts announcements |
| **Lecturer** | Stage-1 verifier for participations and achievements assigned to them |
| **ITC Admin** | Creates activities, manages clusters, talent-matching and invitations |
| **Committee** | Read-only access to search and browse student talent profiles |
| **Student Affairs** | Read-only dashboard and reports access |

## Approval Workflows

### Self-Submitted Achievement
```
Student submits → Lecturer verifies (Stage 1) → Coordinator approves (Stage 2) → Score assigned
```

### Activity Participation
```
Student joins activity → Uploads proof → Lecturer verifies → Coordinator approves → Achievement auto-created
```

## Project Structure

```
app/
├── Enums/           # UserRole enum
├── Http/
│   ├── Controllers/
│   │   ├── Admin/         # Dashboard, reports, settings, student management
│   │   ├── Coordinator/   # Approvals, announcements, awards, user management
│   │   ├── Itc/           # Activity & cluster management
│   │   ├── Lecturer/      # Verification controllers
│   │   ├── Management/    # Talent search
│   │   ├── Student/       # Activity browsing, participations, notifications
│   │   └── Reports/       # Report exports
│   └── Middleware/        # Role-based access middleware
├── Models/                # Eloquent models (14 models)
├── Services/              # MeritService, NotificationService
└── Observers/             # Model observers
```

## License

This project is developed for academic purposes at UTHM.
