<?php

namespace App\Notifications;

use App\EmailNotificationSetting;
use App\Notice;
use App\SlackSetting;
use App\Traits\SmtpSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class NewNotice extends Notification implements ShouldQueue
{
    use Queueable, SmtpSettings;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $notice;
    public function __construct(Notice $notice)
    {
        $this->notice = $notice;
        $this->emailSetting = email_notification_setting();
        $this->setMailConfigs();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = ['database'];

        if($this->emailSetting[6]->send_email == 'yes'){
            array_push($via, 'mail');
        }

        if($this->emailSetting[6]->send_slack == 'yes'){
            array_push($via, 'slack');
        }

        if($this->emailSetting[6]->send_push == 'yes'){
            array_push($via, OneSignalChannel::class);
        }

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                ->subject(__('email.newNotice.subject').' - '.config('app.name'))
                ->greeting(__('email.hello').' '.ucwords($notifiable->name).'!')
                ->line(__('email.newNotice.text'))
                ->action(__('email.loginDashboard'), url('/'))
                ->line(__('email.thankyouNote'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return $this->notice->toArray();
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return SlackMessage
     */
    public function toSlack($notifiable)
    {
        $slack = SlackSetting::first();
        if(count($notifiable->employee) > 0 && (!is_null($notifiable->employee[0]->slack_username) && ($notifiable->employee[0]->slack_username != ''))){
            return (new SlackMessage())
                ->from(config('app.name'))
                ->image(asset('storage/slack-logo/'.$slack->slack_logo))
//                ->to('@abhinav')
                ->to('@'.$notifiable->employee[0]->slack_username)
                ->content('*New Notice* - '.ucfirst($this->notice->heading).". \n<".url('/')."|Login to Dashboard> for more details.");
        }
        return (new SlackMessage())
            ->from(config('app.name'))
            ->image(asset('storage/slack-logo/' . $slack->slack_logo))
            ->content('This is a redirected notification. Add slack username for *'.ucwords($notifiable->name).'*');
    }

    public function toOneSignal($notifiable)
    {
        return OneSignalMessage::create()
            ->subject(__('email.newNotice.subject'))
            ->body($this->notice->heading);
    }
}
