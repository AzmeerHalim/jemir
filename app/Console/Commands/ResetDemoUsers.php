<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use Database\Seeders\AccompanyingLecturerSeeder;
use Database\Seeders\BalancedDemoUsersSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetDemoUsers extends Command
{
    protected $signature = 'users:reset
                            {--force : Required to actually run; otherwise prints a preview and exits}';

    protected $description = 'Truncate the users table (and FK dependents) and reseed a balanced set of demo users. DEV ONLY.';

    /**
     * Tables truncated before the FK back to users is broken.
     * Order doesn't matter because we toggle FOREIGN_KEY_CHECKS off.
     */
    private array $dependentTables = [
        'notifications',
        'announcements',
        'student_awards',
        'leaderboards',
        'merits',
        'participations',
        'student_profiles',
        'achievements',
        'accompanying_lecturers',
        'activities',
    ];

    public function handle(): int
    {
        if (app()->environment('production')) {
            $this->error('Refusing to run in production environment.');
            return self::FAILURE;
        }

        $this->warn('!!! DESTRUCTIVE OPERATION !!!');
        $this->warn('This will TRUNCATE the following tables and reseed users + student profiles.');
        $this->newLine();

        $counts = $this->currentRowCounts();
        $this->table(['Table', 'Current rows'], $counts);

        $this->newLine();
        $this->info('After reset:');
        $this->table(
            ['Role', 'Count'],
            collect(UserRole::cases())->map(fn ($r) => [
                $r->value,
                $r === UserRole::Student ? 12 : 1,
            ])->all()
        );
        $this->newLine();

        if (! $this->option('force')) {
            $this->info('Dry run. Re-run with --force to apply.');
            return self::SUCCESS;
        }

        $confirmation = $this->ask('Type RESET to confirm');
        if ($confirmation !== 'RESET') {
            $this->error('Aborted.');
            return self::FAILURE;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach ($this->dependentTables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }
        if (Schema::hasTable('users')) {
            DB::table('users')->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->info('Truncated. Reseeding...');
        $this->callSilent('db:seed', ['--class' => BalancedDemoUsersSeeder::class]);
        $this->callSilent('db:seed', ['--class' => AccompanyingLecturerSeeder::class]);

        $this->newLine();
        $this->info('Done. Final counts:');
        $final = DB::table('users')
            ->selectRaw('role, COUNT(*) as c')
            ->groupBy('role')
            ->orderBy('role')
            ->get();
        $this->table(['Role', 'Count'], $final->map(fn ($r) => [(string) $r->role, (int) $r->c])->all());

        return self::SUCCESS;
    }

    /**
     * @return array<int, array{0:string,1:int}>
     */
    private function currentRowCounts(): array
    {
        $rows = [];
        $tables = array_merge($this->dependentTables, ['users']);
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                $rows[] = [$table, (int) DB::table($table)->count()];
            }
        }
        return $rows;
    }
}
