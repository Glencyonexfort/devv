<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $body;
    public $attachment;
    //public $job_id;
    public $from_name;
    public $from_email;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->subject = $data['email_subject'];
        $this->body = $data['email_body'];
        $this->attachment = isset($data['files'])?$data['files']:'';
        $this->job_id = isset($data['job_id'])?$data['job_id']:'';
        $this->from_name = $data['from_name'];
        $this->from_email = $data['from_email'];
        $this->reply_to = isset($data['reply_to'])?$data['reply_to']:'';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $email = $this->subject($this->subject)
                ->from($this->from_email, $this->from_name)
                ->replyTo($this->reply_to, $this->from_name)
                ->view('mail.customer');
        $this->withSwiftMessage(function ($message) {
            $headers = $message->getHeaders();
            $headers->addTextHeader('X-PM-TrackOpens', 'TRUE');
            $headers->addTextHeader('X-PM-Tag', $this->job_id);
        });
        if (isset($this->attachment) && $this->attachment!=null) {
            foreach ($this->attachment as $file) {
                $email->attach($file); // attach each file
                //$email->attach(public_path($file['path'])); // attach each file
            }
        }
        return $email;

    }
}
