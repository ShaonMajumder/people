<?php

namespace Database\Seeders;

use App\Models\InteractionStatus;
use Illuminate\Database\Seeder;

class InteractionStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // action-done
        // action-to-be-done
        // waiting-for-event-trigger
        // event-triggered
        $statuses = [
            [
                "name" => "inserted-in-system",
                "type" => "action-done",
            ],
            [
                "name" => "discovered-social-id",
                "type" => "event-triggered",
                
            ],
            [
                "name" => "waiting-for-saying-hi",
                "type" => "action-to-be-done",
                
            ],
            [
                "name" => "said-hi",
                "type" => "action-done",
                
            ],
            [
                "name" => "waiting-for-receiving-hi",
                "type" => "waiting-for-event-trigger",
                
            ],
            [
                "name" => "received-hi",
                "type" => "event-triggered",
            ],
            [
                "name" => "waiting-for-receiving-meet-proposal",
                "type" => "waiting-for-event-trigger",
            ],
            [
                "name" => "waiting-for-giving-meet-proposal",
                "type" => "action-to-be-done",
            ],
            [
                "name" => "met-on-person",
                "type" => "action-done"
            ],
            
        ];

        InteractionStatus::insert($statuses);
        return InteractionStatus::all();

    }
}
