<?php

namespace App\Console\Commands;

use App\Mail\AgentMail;
use App\Models\Ev10AnnouncementOwners;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $agents = Ev10AnnouncementOwners::where('type', 'agent')->get();

        foreach ($agents as $agent) {
            try {
                Mail::to($agent->email)->send(new AgentMail());
                $this->info("Sending email to {$agent->email}");
            } catch (\Exception $e) {
                $this->error("Failed to send email to {$agent->email}: {$e->getMessage()}");
            }
        }
    }

}
