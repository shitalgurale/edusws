<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GenericNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subjectLine;
    public $messageBody;
    public $attachmentFile;

    public function __construct($subjectLine, $messageBody, $attachmentFile = null)
    {
        $this->subjectLine = $subjectLine;
        $this->messageBody = $messageBody;
        $this->attachmentFile = $attachmentFile;
    }

    public function build()
    {
        $email = $this->subject($this->subjectLine)
                      ->view('admin.compose.generic_notification')
                      ->with([
                          'messageBody' => $this->messageBody,
                      ]);

        if ($this->attachmentFile) {
            $email->attach($this->attachmentFile->getRealPath(), [
                'as' => $this->attachmentFile->getClientOriginalName(),
                'mime' => $this->attachmentFile->getMimeType(),
            ]);
        }

        return $email;
    }
}