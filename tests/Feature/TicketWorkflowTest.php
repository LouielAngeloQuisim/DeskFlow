<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private TicketCategory $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->category = TicketCategory::query()->where('slug', 'technical-issue')->firstOrFail();
    }

    public function test_new_registration_is_a_customer_and_can_reach_the_portal_without_email_delivery(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Taylor Customer',
            'email' => 'taylor@example.test',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'admin',
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'taylor@example.test', 'role' => 'customer']);
        $this->get(route('dashboard'))->assertOk()->assertSee('How can we help');
    }

    public function test_customer_can_create_and_view_a_ticket(): void
    {
        $customer = User::factory()->create();
        $this->actingAs($customer);

        $response = $this->post(route('tickets.store'), [
            'subject' => 'Cannot load my workspace',
            'category_id' => $this->category->id,
            'priority' => 'high',
            'description' => 'The workspace stays blank after I sign in, even after trying another browser.',
        ]);

        $ticket = Ticket::query()->firstOrFail();
        $response->assertRedirect(route('tickets.show', $ticket->reference));
        $this->assertSame($customer->id, $ticket->user_id);
        $this->assertMatchesRegularExpression('/^DF-[A-Z0-9]{8}$/', $ticket->reference);
        $this->get(route('tickets.show', $ticket->reference))->assertOk()->assertSee($ticket->reference)->assertSee($ticket->description);
    }

    public function test_customer_cannot_view_another_customers_ticket_or_the_staff_queue(): void
    {
        $owner = User::factory()->create();
        $otherCustomer = User::factory()->create();
        $ticket = $owner->tickets()->create([
            'category_id' => $this->category->id,
            'subject' => 'Private account access issue',
            'description' => 'This ticket belongs to a different customer and must stay private.',
        ]);
        $this->actingAs($otherCustomer);

        $this->get(route('tickets.show', $ticket->reference))->assertForbidden();
        $this->get(route('agent.queue'))->assertForbidden();
        $this->patch(route('agent.tickets.update', $ticket->reference), [
            'status' => 'resolved', 'priority' => 'normal', 'assigned_to' => null,
        ])->assertForbidden();
    }

    public function test_customer_ticket_search_stays_scoped_to_the_signed_in_customer(): void
    {
        $customer = User::factory()->create();
        $otherCustomer = User::factory()->create();
        $customer->tickets()->create([
            'category_id' => $this->category->id,
            'subject' => 'Unique projector setup issue',
            'description' => 'The projector connection drops after a few minutes of use.',
        ]);
        $otherCustomer->tickets()->create([
            'category_id' => $this->category->id,
            'subject' => 'Unique projector billing request',
            'description' => 'Please help me understand this specific projector rental invoice.',
        ]);

        $this->actingAs($customer)->get(route('tickets.index', ['search' => 'PROJECTOR']))
            ->assertOk()
            ->assertSee('Unique projector setup issue')
            ->assertDontSee('Unique projector billing request');
    }

    public function test_customers_cannot_reply_to_a_resolved_ticket(): void
    {
        $customer = User::factory()->create();
        $ticket = $customer->tickets()->create([
            'category_id' => $this->category->id,
            'subject' => 'Resolved issue should stay closed',
            'description' => 'The support team completed the request and it is now resolved.',
            'status' => 'resolved',
        ]);

        $this->actingAs($customer)->post(route('tickets.replies.store', $ticket->reference), [
            'body' => 'I am trying to reply to a resolved request.',
        ])->assertForbidden();
    }

    public function test_customer_reply_reopens_a_ticket_waiting_on_them(): void
    {
        $customer = User::factory()->create();
        $ticket = $customer->tickets()->create([
            'category_id' => $this->category->id,
            'subject' => 'Follow-up needed on my request',
            'description' => 'I am adding a little more information to help the team investigate.',
            'status' => 'waiting_on_customer',
        ]);
        $this->actingAs($customer)->post(route('tickets.replies.store', $ticket->reference), ['body' => 'I have attached the steps I took before the issue appeared.'])
            ->assertRedirect();

        $this->assertSame('open', $ticket->fresh()->status->value);
        $this->assertDatabaseHas('ticket_messages', ['ticket_id' => $ticket->id, 'user_id' => $customer->id, 'is_internal' => false]);
    }

    public function test_internal_notes_are_visible_to_staff_and_never_to_customers(): void
    {
        $customer = User::factory()->create();
        $agent = User::factory()->create(['role' => 'agent']);
        $ticket = $customer->tickets()->create([
            'category_id' => $this->category->id,
            'subject' => 'Question about an access setting',
            'description' => 'I have a question about how my access setting is configured.',
        ]);
        $ticket->messages()->create(['user_id' => $agent->id, 'body' => 'Private troubleshooting context for our team.', 'is_internal' => true]);

        $this->actingAs($customer)->get(route('tickets.show', $ticket->reference))->assertOk()->assertDontSee('Private troubleshooting context');
        $this->actingAs($agent)->get(route('tickets.show', $ticket->reference))->assertOk()->assertSee('Private troubleshooting context')->assertSee('Internal note');
    }

    public function test_agent_can_assign_and_resolve_a_ticket_and_the_change_is_recorded(): void
    {
        $customer = User::factory()->create();
        $agent = User::factory()->create(['role' => 'agent']);
        $ticket = $customer->tickets()->create([
            'category_id' => $this->category->id,
            'subject' => 'Please review this support request',
            'description' => 'Please review this issue and help me understand what needs to be done next.',
        ]);

        $this->actingAs($agent)->patch(route('agent.tickets.update', $ticket->reference), [
            'status' => 'resolved', 'priority' => 'urgent', 'assigned_to' => $agent->id,
        ])->assertRedirect();

        $ticket->refresh();
        $this->assertSame('resolved', $ticket->status->value);
        $this->assertSame($agent->id, $ticket->assigned_to);
        $this->assertNotNull($ticket->resolved_at);
        $this->assertDatabaseHas('ticket_messages', ['ticket_id' => $ticket->id, 'user_id' => $agent->id, 'is_internal' => false]);
        $this->assertTrue(TicketMessage::query()->where('ticket_id', $ticket->id)->exists());
    }

    public function test_staff_can_add_an_internal_note_without_exposing_it_to_the_requester(): void
    {
        $customer = User::factory()->create();
        $agent = User::factory()->create(['role' => 'agent']);
        $ticket = $customer->tickets()->create([
            'category_id' => $this->category->id,
            'subject' => 'A support request with a private note',
            'description' => 'We need to discuss this issue while keeping internal context private.',
        ]);

        $this->actingAs($agent)->post(route('tickets.replies.store', $ticket->reference), [
            'body' => 'Check the service logs before sending the customer an update.',
            'is_internal' => '1',
        ])->assertRedirect();

        $this->actingAs($customer)->get(route('tickets.show', $ticket->reference))->assertDontSee('Check the service logs');
    }
}
