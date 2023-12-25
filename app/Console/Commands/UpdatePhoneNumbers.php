<?php

namespace App\Console\Commands;

use App\Models\Phone;
use Illuminate\Console\Command;
use App\Models\YourModel;

class UpdatePhoneNumbers extends Command
{
    protected $signature = 'update:phone-numbers';
    protected $description = 'Update phone numbers to the desired format';

    public function handle()
    {
        $this->info('Updating phone numbers...');

        // Fetch all records from the database
        $records = Phone::all();

        // Iterate through each record and update the phone number
        foreach ($records as $record) {
            $record->phone_number = $record->country_code;
            $record->country_code = "+1";

            $record->save();
        }

        $this->info('Phone numbers updated successfully.');
    }

    private function formatPhoneNumber($phoneNumber)
    {
        // Remove non-numeric characters
        $cleanedNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        // Format the phone number as xxx-xxx-xxxx
        return substr($cleanedNumber, 0, 3) . '-' . substr($cleanedNumber, 3, 3) . '-' . substr($cleanedNumber, 6);
    }
}