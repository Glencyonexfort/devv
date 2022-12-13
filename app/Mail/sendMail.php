<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\File;

class sendMail extends Mailable
{
    use Queueable, SerializesModels;

    public $body;

    public function __construct($data)
    {
        $this->data = $data;
        $this->body = $data['email_body'];
        $this->attachment = isset($data['files'])?$data['files']:'';
        $this->auto_email = isset($data['auto'])?$data['auto']:0;
        $this->invoice_pdf = isset($data['invoice_pdf'])?$data['invoice_pdf']:'';
    }

    public function build()
    {
        $email = $this->subject($this->data['email_subject'])
                ->from($this->data['from_email'], $this->data['from_name'])                
                ->view('mail.customer');
        if(isset($this->data['cc']) && !empty($this->data['cc'])){
            $cc_email = str_replace(' ', '', $this->data['cc']);
            $cc = explode(',',$cc_email);
            $email->cc($cc);
        }
        if(isset($this->data['bcc']) && !empty($this->data['bcc'])){
            $bcc_email = str_replace(' ', '', $this->data['bcc']);
            $bcc = explode(',',$bcc_email);
            $email->bcc($bcc);
        }
        if(isset($this->data['reply_to']) && !empty($this->data['reply_to'])){
            $email->replyTo($this->data['reply_to'], $this->data['from_name']);
        }
        $this->withSwiftMessage(function ($message) {
            $headers = $message->getHeaders();
            $headers->addTextHeader('X-PM-TrackOpens', 'TRUE');
            if(isset($this->data['jobs_moving_id'])) {
                $headers->addTextHeader('X-PM-Tag', $this->data['jobs_moving_id']);
            }
        });
        if (isset($this->invoice_pdf) && $this->invoice_pdf!=null) {
                $email->attach(public_path($this->invoice_pdf)); // attach invoice_pdf
        }
        if (isset($this->attachment) && $this->attachment!=null) {
            if($this->auto_email==1){
                foreach ($this->attachment as $file) {
                    if(File::exists($file['path'])){
                        $email->attach($file['path']); // attach each file
                    }
                }
            }else{
                foreach ($this->attachment as $file) {
                    if(File::exists(public_path($file['path']))){
                        $email->attach(public_path($file['path'])); // attach each file
                    }
                }
            }
            
        }
        return $email;
    }
}

