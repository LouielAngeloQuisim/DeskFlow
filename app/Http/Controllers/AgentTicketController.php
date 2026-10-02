<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AgentTicketController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->isStaff(), 403);

        $query = Ticket::query()->with(['requester', 'category', 'assignee'])->latest('updated_at');
        $query->when($request->string('search')->trim()->isNotEmpty(), function ($query) use ($request) {
            $search = mb_strtolower($request->string('search')->trim()->toString());
            $pattern = "%{$search}%";
            $query->where(fn ($query) => $query->whereRaw('LOWER(reference) LIKE ?', [$pattern])
                ->orWhereRaw('LOWER(subject) LIKE ?', [$pattern])
                ->orWhereHas('requester', fn ($q) => $q->whereRaw('LOWER(name) LIKE ?', [$pattern])));
        });
        $query->when($request->string('status')->isNotEmpty(), fn ($query) => $query->where('status', $request->string('status')->toString()));
        $query->when($request->string('priority')->isNotEmpty(), fn ($query) => $query->where('priority', $request->string('priority')->toString()));

        return view('agent.queue', [
            'tickets' => $query->paginate(15)->withQueryString(),
            'agents' => User::query()->whereIn('role', ['agent', 'admin'])->orderBy('name')->get(),
            'statuses' => TicketStatus::cases(),
        ]);
    }

    public function update(Request $request, Ticket $ticket): RedirectResponse
    {
        Gate::authorize('update', $ticket);
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_column(TicketStatus::cases(), 'value'))],
            'priority' => ['required', 'in:low,normal,high,urgent'],
            'assigned_to' => ['nullable', 'integer', Rule::exists('users', 'id')->whereIn('role', ['agent', 'admin'])],
        ]);

        $oldStatus = $ticket->status->label();
        $newStatus = TicketStatus::from($validated['status'])->label();
        $oldAssignee = $ticket->assigned_to;
        $ticket->fill($validated);
        $ticket->resolved_at = in_array($validated['status'], ['resolved', 'closed'], true)
            ? ($ticket->resolved_at ?? now())
            : null;

        DB::transaction(function () use ($ticket, $request, $oldStatus, $newStatus, $oldAssignee): void {
            $ticket->save();

            $changes = [];
            if ($oldStatus !== $newStatus) {
                $changes[] = "status changed from {$oldStatus} to {$newStatus}";
            }
            if ($oldAssignee !== $ticket->assigned_to) {
                $name = $ticket->assignee()->value('name') ?? 'the unassigned queue';
                $changes[] = "assignment changed to {$name}";
            }

            if ($changes !== []) {
                $ticket->messages()->create([
                    'user_id' => $request->user()->id,
                    'body' => 'Ticket '.implode(' and ', $changes).'.',
                    'is_internal' => false,
                ]);
            }
        });

        return back()->with('success', 'Ticket updated.');
    }
}
