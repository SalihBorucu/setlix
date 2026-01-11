<?php

namespace App\Console\Commands;

use App\Mail\PricingChangeAnnouncement;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendPricingChangeEmail extends Command
{
    protected $signature = 'email:pricing-change
                            {--dry-run : Preview without sending}
                            {--to= : Send test email to a specific email address}';
    protected $description = 'Send pricing change announcement email to all users (one-time)';

    /**
     * Email addresses to exclude from the mailing
     */
    private array $exceptions = [
    ];

    public function handle(): int
    {
        // Send test email to a specific address
        if ($testEmail = $this->option('to')) {
            return $this->sendTestEmail($testEmail);
        }

        $query = User::query()
            ->whereNotNull('email_verified_at')
            ->where('is_trial', false)
            ->where('unsubscribed_from_marketing', false)
            ->whereNotIn('email', $this->exceptions);

        $totalUsers = $query->count();

        if ($totalUsers === 0) {
            $this->warn('No users found to send emails to.');
            return self::SUCCESS;
        }

        $this->info("Found {$totalUsers} users to email.");
        $this->info("Excluding " . count($this->exceptions) . " email(s) from exceptions list.");

        if ($this->option('dry-run')) {
            $this->warn('DRY RUN MODE - No emails will be sent.');
            $this->newLine();
            $this->info('Users who would receive the email:');

            $query->each(function ($user) {
                $this->line("  - {$user->email} ({$user->name})");
            });

            return self::SUCCESS;
        }

        if (!$this->confirm("Are you sure you want to send {$totalUsers} emails?")) {
            $this->info('Cancelled.');
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($totalUsers);
        $bar->start();

        $sent = 0;
        $failed = 0;

        $query->each(function ($user) use (&$sent, &$failed, $bar) {
            try {
                Mail::to($user->email)->send(new PricingChangeAnnouncement($user));
                $sent++;
            } catch (\Exception $e) {
                $failed++;
                $this->newLine();
                $this->error("Failed to send to {$user->email}: {$e->getMessage()}");
            }

            $bar->advance();
        });

        $bar->finish();
        $this->newLine(2);

        $this->info("Completed! Sent: {$sent}, Failed: {$failed}");

        return self::SUCCESS;
    }

    /**
     * Send a test email to a specific address
     */
    private function sendTestEmail(string $email): int
    {
        $this->info("Sending test email to: {$email}");

        // Try to find the user, or create a fake one for the test
        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = new User([
                'name' => 'Test User',
                'email' => $email,
            ]);
            $this->warn("User not found in database, using placeholder name 'Test User'");
        }

        try {
            Mail::to($email)->send(new PricingChangeAnnouncement($user));
            $this->info("Test email sent successfully to {$email}!");
            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to send test email: {$e->getMessage()}");
            return self::FAILURE;
        }
    }
}
