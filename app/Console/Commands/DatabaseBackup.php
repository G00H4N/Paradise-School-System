<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class DatabaseBackup extends Command
{
    // Terminal command name
    protected $signature = 'db:backup';
    protected $description = 'Create a daily backup of the database';

    public function handle()
    {
        $filename = "backup-" . Carbon::now()->format('Y-m-d-H-i') . ".sql";

        // Database credentials
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD'); // Note: In production, handle securely

        // Mysqldump command (Server par install hona zaroori hai)
        // Windows ke liye logic alag hoti hai, yeh Linux/Server standard hai
        $command = "mysqldump --user={$username} --password={$password} {$database} > " . storage_path("app/backups/{$filename}");

        $returnVar = NULL;
        $output = NULL;

        // Command chalao
        exec($command, $output, $returnVar);

        if ($returnVar === 0) {
            $this->info("Backup Successful: $filename");
            // Optional: Upload to S3 here
            // Storage::disk('s3')->put($filename, file_get_contents(storage_path("app/backups/{$filename}")));
        } else {
            $this->error("Backup Failed. Ensure mysqldump is installed.");
        }
    }
}