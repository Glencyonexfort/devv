<?php

namespace App\Notifications;

use App\EmailNotificationSetting;
use App\SlackSetting;
use App\Task;
use App\Traits\SmtpSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TaskUpdatedClient extends Notification implements ShouldQueue
{
    use Queueable, SmtpSettings;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $task;
    public function __construct(Task $task)
    {
        $this->task = $task;
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

        if($this->emailSetting[9]->send_email == 'yes'){
            array_push($via, 'mail');
        }

//        if($this->emailSetting[9]->send_slack == 'yes'){
//            array_push($via, 'slack');
//        }

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
            ->subject(__('email.taskUpdate.subject').' - '.config('app.name').'!')
            ->greeting(__('email.hello').' '.ucwords($notifiable->name).'!')
            ->line(ucfirst($this->task->heading).' '.__('email.taskUpdate.subject').'.')
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
        return [
            'id' => $this->task->id,
            'updated_at' => $this->task->updated_at->format('Y-m-d H:i:s'),
            //'created_at' => $this->task->created_at->format('Y-m-d H:i:s'),
            'heading' => $this->task->heading
        ];
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return SlackMessage
     */
    public function toSlack($notifiable)
    {
//        $slack = SlackSetting::first();
//        if(count($notifiable->employee) > 0 && (!is_null($notifiable->employee[0]->slack_username) && ($notifiable->employee[0]->slack_username != ''))){
//            return (new SlackMessage())
//                ->from(config('app.name'))
//                ->image(asset('storage/slack-logo/' . $slack->slack_logo))
//                ->to('@' . $notifiable->employee[0]->slack_username)
//                ->content(ucfirst($this->task->heading).' '.__('email.taskUpdate.subject').'.');
//        }
//        return (new SlackMessage())
//            ->from(config('app.name'))
//            ->image(asset('storage/slack-logo/' . $slack->slack_logo))
//            ->content('This is a redirected notification. Add slack username for *'.ucwords($notifiable->name).'*');
    }
}
