<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Mail\MessageNotification;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNotificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $messageBody;
    protected $senderName;

    /**
     * Create a new job instance.
     */
    public function __construct($email, $messageBody, $senderName)
    {
        $this->email = $email;
        $this->messageBody = $messageBody;
        $this->senderName = $senderName;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Mail::to($this->email)->send(new MessageNotification($this->messageBody, $this->senderName));
    }
}
