<?php

namespace Database\Seeders;

use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(DatabaseSeeder::class);
        $customer = User::query()->updateOrCreate(['email' => 'customer@deskflow.test'], [
            'name' => 'Jamie Customer', 'password' => Hash::make('DeskFlowDemo!2026'), 'role' => 'customer',
        ]);
        $agent = User::query()->updateOrCreate(['email' => 'agent@deskflow.test'], [
            'name' => 'Alex Support', 'password' => Hash::make('DeskFlowDemo!2026'), 'role' => 'agent',
        ]);

        if ($customer->tickets()->exists()) {
            return;
        }

        $ticket = $customer->tickets()->create([
            'category_id' => TicketCategory::query()->where('slug', 'account-access')->value('id'),
            'subject' => 'I cannot access my team workspace',
            'description' => 'I am trying to sign in to the workspace but the page keeps returning me to the login screen. I have already reset my password and the issue is still happening.',
            'status' => 'in_progress',
            'priority' => 'high',
            'assigned_to' => $agent->id,
        ]);
        $ticket->messages()->create(['user_id' => $agent->id, 'body' => 'Thanks for the details. I am checking the workspace sign-in configuration now and will update you shortly.']);
        $ticket->messages()->create(['user_id' => $agent->id, 'body' => 'Confirmed this is related to a session setting. Reviewing the fix before applying it.', 'is_internal' => true]);

        $customer->tickets()->create([
            'category_id' => TicketCategory::query()->where('slug', 'billing')->value('id'),
            'subject' => 'Question about the latest invoice',
            'description' => 'Could you help me understand one of the line items on my latest invoice? I would like to confirm what it covers before I submit payment.',
            'status' => 'open',
            'priority' => 'normal',
        ]);
    }
}
