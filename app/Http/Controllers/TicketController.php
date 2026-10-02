<?php

namespace App\Http\Controllers;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\TicketCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function dashboard(Request $request): View
    {
        $user = $request->user();
        $query = Ticket::query()->with(['requester', 'category', 'assignee'])->latest('updated_at');

        if (! $user->isStaff()) {
            $query->where('user_id', $user->id);
        }

        $tickets = $query->limit(6)->get();
        $base = Ticket::query();

        if (! $user->isStaff()) {
            $base->where('user_id', $user->id);
        }

        return view('dashboard', [
            'tickets' => $tickets,
            'counts' => [
                'open' => (clone $base)->whereIn('status', ['open', 'in_progress', 'waiting_on_customer'])->count(),
                'waiting' => (clone $base)->where('status', 'waiting_on_customer')->count(),
                'resolved' => (clone $base)->whereIn('status', ['resolved', 'closed'])->count(),
            ],
        ]);
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $query = Ticket::query()->with(['requester', 'category', 'assignee'])->latest('updated_at');

        if (! $user->isStaff()) {
            $query->where('user_id', $user->id);
        }

        $query->when($request->string('search')->trim()->isNotEmpty(), function ($query) use ($request) {
            $search = mb_strtolower($request->string('search')->trim()->toString());
            $pattern = "%{$search}%";
            $query->where(fn ($query) => $query->whereRaw('LOWER(reference) LIKE ?', [$pattern])
                ->orWhereRaw('LOWER(subject) LIKE ?', [$pattern]));
        });

        $query->when($request->string('status')->isNotEmpty(), fn ($query) => $query->where('status', $request->string('status')->toString()));
        $query->when($request->string('priority')->isNotEmpty(), fn ($query) => $query->where('priority', $request->string('priority')->toString()));

        return view('tickets.index', ['tickets' => $query->paginate(12)->withQueryString()]);
    }

    public function create(): View
    {
        Gate::authorize('create', Ticket::class);

        return view('tickets.create', [
            'categories' => TicketCategory::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'priorities' => TicketPriority::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Ticket::class);

        $validated = $request->validate([
            'subject' => ['required', 'string', 'min:8', 'max:160'],
            'category_id' => ['required', 'integer', 'exists:ticket_categories,id'],
            'priority' => ['required', 'in:low,normal,high,urgent'],
            'description' => ['required', 'string', 'min:20', 'max:10000'],
        ]);

        $ticket = DB::transaction(fn () => $request->user()->tickets()->create($validated + ['status' => TicketStatus::Open]));

        return redirect()->route('tickets.show', $ticket)->with('success', 'Your support request has been submitted.');
    }

    public function show(Request $request, Ticket $ticket): View
    {
        Gate::authorize('view', $ticket);
        $ticket->load(['requester', 'assignee', 'category']);
        $messages = $ticket->messages()
            ->when(! $request->user()->isStaff(), fn ($query) => $query->where('is_internal', false))
            ->with('author')
            ->get();

        return view('tickets.show', [
            'ticket' => $ticket,
            'messages' => $messages,
            'canReply' => $request->user()->can('reply', $ticket),
        ]);
    }

    public function reply(Request $request, Ticket $ticket): RedirectResponse
    {
        Gate::authorize('reply', $ticket);

        $validated = $request->validate([
            'body' => ['required', 'string', 'min:2', 'max:10000'],
            'is_internal' => ['sometimes', 'boolean'],
        ]);
        $isInternal = $request->user()->isStaff() && (bool) ($validated['is_internal'] ?? false);

        DB::transaction(function () use ($request, $ticket, $validated, $isInternal): void {
            $ticket->messages()->create([
                'user_id' => $request->user()->id,
                'body' => $validated['body'],
                'is_internal' => $isInternal,
            ]);

            if (! $request->user()->isStaff() && $ticket->status === TicketStatus::WaitingOnCustomer) {
                $ticket->status = TicketStatus::Open;
            } elseif ($request->user()->isStaff() && $ticket->status === TicketStatus::Open) {
                $ticket->status = TicketStatus::InProgress;
            }

            $ticket->save();
        });

        return back()->with('success', $isInternal ? 'Internal note added.' : 'Reply sent.');
    }
}
