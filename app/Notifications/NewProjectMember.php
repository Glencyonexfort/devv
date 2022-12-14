<?php

namespace App\Notifications;

use App\EmailNotificationSetting;
use App\ProjectMember;
use App\Setting;
use App\SlackSetting;
use App\SmtpSetting;
use App\Traits\SmtpSettings;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\MailServiceProvider;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Config;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class NewProjectMember extends Notification implements ShouldQueue
{
    use Queueable, SmtpSettings;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $user;
    private $member;
    public function __construct(ProjectMember $member) {
        $user = User::findOrFail($member->user_id);
        $this->user = $user;
        $this->member = $member;
        $this->emailSetting = email_notification_setting();
        $this->setMailConfigs();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable) {

        $via = ['database'];

        if($this->emailSetting[5]->send_email == 'yes'){
            array_push($via, 'mail');
        }

        if($this->emailSetting[5]->send_slack == 'yes'){
            array_push($via, 'slack');
        }

        if($this->emailSetting[5]->send_push == 'yes'){
            array_push($via, OneSignalChannel::class);
        }

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable) {

        return (new MailMessage)
            ->subject(__('email.newProjectMember.subject').' - '.config('app.name').'!')
            ->greeting(__('email.hello').' ' . ucwords($this->user->name) . '!')
            ->line(__('email.newProjectMember.text').' - ' . ucwords($this->member->project->project_name))
            ->action(__('email.loginDashboard'), url('/'))
            ->line(__('email.thankyouNote'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable) {

        return $this->member->toArray();
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
                ->image(asset('storage/slack-logo/' . $slack->slack_logo))
                ->to('@' . $notifiable->employee[0]->slack_username)
                ->content('You have been added as a member to the project - *' . ucwords($this->member->project->project_name) . '*');
        }
        return (new SlackMessage())
            ->from(config('app.name'))
            ->image(asset('storage/slack-logo/' . $slack->slack_logo))
            ->content('This is a redirected notification. Add slack username for *'.ucwords($notifiable->name).'*');
    }

    public function toOneSignal($notifiable)
    {
        return OneSignalMessage::create()
            ->subject(__('email.newProjectMember.subject'))
            ->body(ucwords($this->member->project->project_name));
    }
}
