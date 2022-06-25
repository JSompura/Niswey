<?php

namespace App\Jobs;

use App\Contacts;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Validator;

class importContact implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $contacts;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($contacts)
    {
        $this->contacts = $contacts;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $contactsData = [];
        foreach ($this->contacts as $contact) {
            $validator = Validator::make((array)$contact, [
                'name' => 'bail|required',
                'lastName' => 'bail|nullable',
                'phone' => 'bail|nullable'
            ]);
            if ($validator->fails()) {
                continue;
            } else {
                array_push($contactsData, [
                    'name' => $contact['name'],
                    'last_name' => $contact['lastName'] ?? '',
                    'phone' => $contact['phone'] ?? ''
                ]);
            }
        }
        Contacts::insert($contactsData);
    }
}
