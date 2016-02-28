<?php

namespace Reset\Jobs;

use Reset\Classes\Crest;
use Reset\Jobs\Job;
use Reset\User;

class GetCrestContacts extends Job
{

    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Crest $crest)
    {
        $contactsJson = $crest->readCharacterContacts();
    }
}
