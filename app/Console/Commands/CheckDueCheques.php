<?php

namespace App\Console\Commands;

use App\Models\Cheque;
use App\Models\SystemNotification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckDueCheques extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cheques:check-due';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for due, upcoming (up to 3 days advance), and overdue cheques and generate system notifications.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        $todayStr = $today->toDateString();
        $threeDaysLaterStr = Carbon::today()->addDays(3)->toDateString();
        
        $this->info("Checking cheques up to {$threeDaysLaterStr}...");

        // Cheques due up to 3 days in advance OR overdue
        $dueCheques = Cheque::with('customer')
            ->whereIn('status', ['in_hand', 'deposited'])
            ->whereDate('cheque_date', '<=', $threeDaysLaterStr)
            ->get();

        if ($dueCheques->isEmpty()) {
            $this->info('No due, upcoming (3 days), or overdue cheques found.');
            return 0;
        }

        // Notify Admins
        $adminUsers = User::all();

        $count = 0;
        foreach ($dueCheques as $cheque) {
            $chequeDate = Carbon::parse($cheque->cheque_date);
            $partyName = $cheque->customer ? $cheque->customer->customer_name : 'Customer';
            $amtFormatted = number_format($cheque->amount, 2);

            if ($chequeDate->isPast() && !$chequeDate->isToday()) {
                $title = "⚠️ Cheque Overdue: #{$cheque->cheque_no}";
                $message = "Cheque #{$cheque->cheque_no} (Rs. {$amtFormatted} - {$partyName}) is OVERDUE! Due date was {$chequeDate->format('d M, Y')}.";
            } elseif ($chequeDate->isToday()) {
                $title = "🔔 Cheque Due Today: #{$cheque->cheque_no}";
                $message = "Cheque #{$cheque->cheque_no} (Rs. {$amtFormatted} - {$partyName}) is DUE TODAY! Please deposit in bank.";
            } else {
                $days = (int) $today->diffInDays($chequeDate);
                $title = "⏳ Cheque Due in {$days} Days: #{$cheque->cheque_no}";
                $message = "Upcoming: Cheque #{$cheque->cheque_no} (Rs. {$amtFormatted} - {$partyName}) is due in {$days} days on {$chequeDate->format('d M, Y')}.";
            }

            foreach ($adminUsers as $user) {
                // Prevent duplicate notification on the same day for the same cheque
                $alreadyNotified = SystemNotification::where('user_id', $user->id)
                    ->where('source_type', Cheque::class)
                    ->where('source_id', $cheque->id)
                    ->whereDate('created_at', $todayStr)
                    ->exists();

                if (!$alreadyNotified) {
                    SystemNotification::create([
                        'user_id' => $user->id,
                        'title' => $title,
                        'message' => $message,
                        'type' => 'cheque_alert',
                        'source_type' => Cheque::class,
                        'source_id' => $cheque->id,
                        'action_url' => url('/cheques?search=' . urlencode($cheque->cheque_no)),
                        'is_read' => false,
                    ]);
                    $count++;
                }
            }
        }

        $this->info("Generated {$count} cheque notification(s).");
        return 0;
    }
}
