<?php

namespace Enlightn\Enlightn\Notifications\Notifications;

use Enlightn\Enlightn\Events\EnlightnWasSuccessful;
use Enlightn\Enlightn\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Support\Facades\Storage;


class EnlightnWasSuccessfulNotification extends BaseNotification
{
    private string $jsonFilePath;
    private array $scanProperties;

    public function __construct(
        public EnlightnWasSuccessful $event,
    ) {
        $this->jsonFilePath = $this->createJsonFile($this->enlightnScanProperties());
        $this->scanProperties = $this->enlightnScanProperties()->toArray();

    }

    public function toMail($notifiable)
    {

        $meta = $this->scanProperties['meta'];
        $analyzerStats = $this->scanProperties['analyzer_stats'];
        $mailMessage = (new MailMessage)
            ->subject('Application Analysis Report')
            ->greeting('Hello!')
            ->line('Here is the analysis report for your application:')
            ->line('**Application:** ' .$this->scanProperties['Application'])
            ->line('**App Name:** ' . $meta['app_name'])
            ->line('**Environment:** ' . $meta['app_env'])
            ->line('**URL:** ' . $meta['app_url'])
            ->line('**Project Name:** ' . $meta['project_name'])
            ->line('**Repo:** ' . $meta['github_repo'])
            ->line('**Commit ID:** ' . $meta['commit_id'])
            ->line('**Trigger:** ' . $meta['trigger'])
            ->line('The detailed analysis results are attached in the JSON file.');

            $mailMessage->line('**Analyzer Stats:**');
            foreach ($analyzerStats as $category => $stats) {
                $mailMessage->line('---')
                            ->line('**' . ucfirst($category) . '**:');
                foreach ($stats as $key => $value) {
                    $mailMessage->line(ucfirst($key) . ': ' . $value);
                }
            }

            $mailMessage->attach($this->jsonFilePath, [
                'as' => 'analysis_report.json',
                'mime' => 'application/json',
            ]);

            $mailMessage->line('Thank you!');

        return $mailMessage;
    }

    /**
     * Create the JSON file with analyzer results
     *
     * @param array $data
     * @return string
     */
    private function createJsonFile($data)
    {
        $filePath = 'analysis_report_' . now()->timestamp . '.json';
        Storage::put($filePath, json_encode($data, JSON_PRETTY_PRINT));
        return Storage::path($filePath);
    }

    /**
     * Clean up the temporary JSON file.
     */
    public function __destruct()
    {
        Storage::delete($this->jsonFilePath);
    }


    public function toSlack(): SlackMessage
    {

    $fields = [];

    // Iterate over each key-value pair in the scanProperties to dynamically create headers and values
    foreach ($this->scanProperties as $key => $value) {
        // Format the key to create a header
        $header = ucfirst(str_replace('_', ' ', $key));

        if (is_array($value)) {
            // If the value is an array, format it accordingly
            $formattedValue = $this->formatArrayValue($value);
            $fields[$header] = $formattedValue;
        } else {
            // If the value is not an array, directly add it to the fields
            $fields[$header] = $value;
        }
    }

    return (new SlackMessage())
        ->success()
        ->from(config('enlightn.notifications.slack.username'), config('enlightn.notifications.slack.icon'))
        ->to(config('enlightn.notifications.slack.channel'))
        ->content('Successful new enlightn scan!')
        ->attachment(function (SlackAttachment $attachment) use ($fields) {
            $attachment->fields($fields);
        });
    }

    private function formatArrayValue(array $value, $prefix = '')
    {
        $formattedValue = [];
        foreach ($value as $subKey => $subValue) {
            $formattedSubKey = $prefix . ucfirst(str_replace('_', ' ', $subKey));
            if (is_array($subValue)) {
                // Recursive call for nested arrays
                $formattedValue[] = $this->formatArrayValue($subValue, $formattedSubKey . ' ');
            } else {
                $formattedValue[] = $formattedSubKey . ": " . $subValue;
            }
        }
        return implode("\n", $formattedValue);
    }

}
