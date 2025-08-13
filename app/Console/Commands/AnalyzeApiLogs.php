<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class AnalyzeApiLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:analyze-api 
                            {--last-hours=24 : Analyze logs from the last n hours}
                            {--endpoint=all : Specific API endpoint to analyze}
                            {--errors-only : Show only errors}
                            {--clear : Clear logs before analysis}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze API logs for troubleshooting';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('clear')) {
            $this->clearLogs();
        }
        
        $this->info('🔍 API Log Analysis Tool 🔍');
        $this->info('================================');
        
        // Get the logs path
        $logPath = storage_path('logs');
        $this->info("Analyzing logs in: $logPath");
        
        // Get all log files
        $logFiles = collect(File::files($logPath))
            ->filter(function ($file) {
                return preg_match('/laravel-(\d{4}-\d{2}-\d{2})\.log/', $file->getFilename());
            })
            ->sortByDesc(function ($file) {
                // Extract date from filename
                preg_match('/laravel-(\d{4}-\d{2}-\d{2})\.log/', $file->getFilename(), $matches);
                return $matches[1] ?? '0000-00-00';
            });
            
        if ($logFiles->isEmpty()) {
            $this->error('No log files found.');
            return 1;
        }
        
        $this->info("Found " . count($logFiles) . " log files.");
        
        // Get the timestamp for X hours ago
        $hoursAgo = now()->subHours($this->option('last-hours'))->timestamp;
        
        $endpoint = $this->option('endpoint');
        $errorsOnly = $this->option('errors-only');
        
        $this->info("Analyzing logs from the last " . $this->option('last-hours') . " hours" . 
                    ($endpoint !== 'all' ? " for endpoint '$endpoint'" : '') . 
                    ($errorsOnly ? " (errors only)" : ''));
        
        $apiRequests = [];
        $errorCount = 0;
        $registrationCount = 0;
        $registrationErrorCount = 0;
        
        foreach ($logFiles as $logFile) {
            $this->info("Analyzing " . $logFile->getFilename() . "...");
            
            // Use grep to extract relevant lines (much faster than reading the whole file)
            $process = new Process(['grep', '-i', '"api"', $logFile->getPathname()]);
            $process->run();
            
            if (!$process->isSuccessful()) {
                $this->warn("No API entries found in " . $logFile->getFilename());
                continue;
            }
            
            $apiLines = explode("\n", $process->getOutput());
            
            foreach ($apiLines as $line) {
                if (empty($line)) continue;
                
                // Try to parse the log entry
                if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $dateMatches)) {
                    $logTimestamp = strtotime($dateMatches[1]);
                    
                    // Check if the log is within our time window
                    if ($logTimestamp >= $hoursAgo) {
                        
                        // Check if this is an error
                        $isError = (stripos($line, 'error') !== false) || 
                                   (stripos($line, 'exception') !== false) || 
                                   (stripos($line, 'failed') !== false);
                        
                        // Check if it's related to the registration endpoint
                        $isRegistration = (stripos($line, '/register') !== false) || 
                                         (stripos($line, 'registration') !== false);
                        
                        if ($isRegistration) {
                            $registrationCount++;
                            if ($isError) $registrationErrorCount++;
                        }
                        
                        if ($isError) $errorCount++;
                        
                        // If we're filtering by errors and this isn't one, skip
                        if ($errorsOnly && !$isError) continue;
                        
                        // If we're filtering by endpoint and this doesn't match, skip
                        if ($endpoint !== 'all' && stripos($line, $endpoint) === false) continue;
                        
                        $apiRequests[] = [
                            'timestamp' => $dateMatches[1],
                            'is_error' => $isError,
                            'is_registration' => $isRegistration,
                            'log_entry' => $line,
                        ];
                    }
                }
            }
        }
        
        // Output summary
        $this->info("\n📊 Analysis Summary 📊");
        $this->info("================================");
        $this->info("Total API logs analyzed: " . count($apiRequests));
        $this->info("Total errors found: " . $errorCount);
        $this->info("Registration requests: " . $registrationCount);
        $this->info("Registration errors: " . $registrationErrorCount);
        
        if ($registrationCount > 0) {
            $this->info("Registration error rate: " . round(($registrationErrorCount / $registrationCount) * 100, 1) . "%");
        }
        
        // Output details
        if (count($apiRequests) > 0) {
            $this->info("\n🔎 Detailed Analysis 🔎");
            $this->info("================================");
            
            $this->table(
                ['Timestamp', 'Type', 'Log Entry'],
                collect($apiRequests)->map(function ($entry) {
                    return [
                        $entry['timestamp'],
                        $entry['is_error'] ? 'ERROR' : 'INFO',
                        $this->truncateLogEntry($entry['log_entry']),
                    ];
                })->toArray()
            );
            
            // If there are registration errors, highlight them
            if ($registrationErrorCount > 0) {
                $this->error("\n⚠️  Registration Errors Found ⚠️");
                $this->info("================================");
                
                $registrationErrors = collect($apiRequests)
                    ->filter(function ($entry) {
                        return $entry['is_registration'] && $entry['is_error'];
                    })
                    ->map(function ($entry) {
                        return [
                            $entry['timestamp'],
                            $this->truncateLogEntry($entry['log_entry'], 1000),
                        ];
                    })
                    ->toArray();
                
                $this->table(['Timestamp', 'Error Details'], $registrationErrors);
                
                $this->info("\n🛠️ Troubleshooting Recommendations 🛠️");
                $this->info("================================");
                $this->info("1. Check that your server is correctly processing POST requests to /api/register");
                $this->info("2. Verify that the request headers include the correct Content-Type (application/json or multipart/form-data if uploading files)");
                $this->info("3. Test if the route is accessible using a simple GET request to /api/register-test");
                $this->info("4. Check server configuration for any rewrite rules that might be affecting API routes");
                $this->info("5. Check that CORS is properly configured for your production domain");
                $this->info("6. Run the diagnostics endpoint to verify API connectivity: /api/diagnostics");
            }
        } else {
            $this->warn("No relevant log entries found for the selected criteria.");
        }
        
        return 0;
    }
    
    /**
     * Truncate a log entry to a reasonable size for display
     */
    protected function truncateLogEntry($entry, $maxLength = 100)
    {
        if (strlen($entry) <= $maxLength) {
            return $entry;
        }
        
        return substr($entry, 0, $maxLength) . '...';
    }
    
    /**
     * Clear log files before analysis
     */
    protected function clearLogs()
    {
        if (!$this->confirm('This will clear all log files. Are you sure?')) {
            return;
        }
        
        $logPath = storage_path('logs');
        $files = File::files($logPath);
        
        foreach ($files as $file) {
            if (preg_match('/laravel-.*\.log/', $file->getFilename())) {
                File::put($file->getPathname(), '');
                $this->info("Cleared: " . $file->getFilename());
            }
        }
        
        $this->info('All log files cleared.');
    }
}
