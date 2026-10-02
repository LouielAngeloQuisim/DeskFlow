<?php

namespace Database\Seeders;

use App\Models\TicketCategory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Account access', 'slug' => 'account-access', 'description' => 'Sign-in, profile, and account questions.', 'sort_order' => 1],
            ['name' => 'Billing', 'slug' => 'billing', 'description' => 'Invoices, payments, and subscriptions.', 'sort_order' => 2],
            ['name' => 'Technical issue', 'slug' => 'technical-issue', 'description' => 'Something is not working as expected.', 'sort_order' => 3],
            ['name' => 'How-to question', 'slug' => 'how-to-question', 'description' => 'Advice on getting the most from the product.', 'sort_order' => 4],
            ['name' => 'Other', 'slug' => 'other', 'description' => 'Anything else we can help with.', 'sort_order' => 5],
        ];

        foreach ($categories as $category) {
            TicketCategory::query()->updateOrCreate(['slug' => $category['slug']], $category + ['is_active' => true]);
        }
    }
}
