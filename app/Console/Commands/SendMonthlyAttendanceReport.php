<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MonthlyAttendanceDispatcher;

class SendMonthlyAttendanceReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    //protected $signature = 'command:name';
    protected $signature = 'attendance:send-monthly-report';
    protected $description = 'Send monthly attendance reports to all students and parents';

    /**
     * The console command description.
     *
     * @var string
     */
    //protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info("✅ Running SendMonthlyAttendanceReport command");
    
        // ✅ Pick a default admin
        $admin = \App\Models\User::where('role_id', 2)->first();
    
        // ✅ Pass admin to the dispatcher
        \App\Services\MonthlyAttendanceDispatcher::sendReports($admin);
    
        $this->info("✅ Monthly attendance reports dispatched successfully.");
    }
}
