<?php

namespace App\Http\Controllers\Api\V1;

use App\Data\PublicUserData;
use App\Http\Controllers\Controller;
use App\Http\Requests\WebsiteTicketFormRequest;
use App\Http\Requests\WebsiteTicketReplyFormRequest;
use App\Http\Resources\Api\V1\PublicUserResource;
use App\Models\Help\WebsiteHelpCenterTicket;
use App\Models\Help\WebsiteHelpCenterTicketReply;
use App\Models\Help\WebsiteRule;
use App\Models\Help\WebsiteRuleCategory;
use App\Services\Help\TicketService;
use App\Support\AuthenticatedUser;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class SupportController extends Controller
{
    public function __construct(private readonly TicketService $tickets) {}

    public function index(): JsonResponse
    {
        return response()->json(['data' => $this->tickets->categories()->map(fn ($category): array => ['id' => $category->id, 'name' => $category->name, 'content' => $category->content, 'image_url' => $category->image_url, 'button_text' => $category->button_text, 'button_url' => $category->button_url, 'small_box' => (bool) $category->small_box, 'button_color' => $category->button_color, 'button_border_color' => $category->button_border_color])]);
    }

    public function rules(): JsonResponse
    {
        return response()->json(['data' => $this->tickets->rules()->map(fn (WebsiteRuleCategory $category): array => ['id' => $category->id, 'name' => $category->name, 'description' => $category->description, 'badge' => $category->badge, 'rules' => $category->rules->map(fn (WebsiteRule $rule): array => ['id' => $rule->id, 'paragraph' => $rule->paragraph, 'rule' => $rule->rule])])]);
    }

    public function tickets(Request $request): AnonymousResourceCollection
    {
        return JsonResource::collection($this->tickets->tickets(AuthenticatedUser::from($request), $request->boolean('all'), $request->has('open') ? $request->boolean('open') : null)->through(fn ($ticket): array => $this->ticketData($ticket)));
    }

    public function show(WebsiteHelpCenterTicket $ticket, Request $request): JsonResponse
    {
        return response()->json(['data' => $this->ticketData($this->tickets->show(AuthenticatedUser::from($request), $ticket))]);
    }

    public function store(WebsiteTicketFormRequest $request): JsonResponse
    {
        $ticket = $this->tickets->store(AuthenticatedUser::from($request), $request->ticketData());

        return response()->json(['data' => $this->ticketData($ticket)], 201);
    }

    public function update(WebsiteHelpCenterTicket $ticket, WebsiteTicketFormRequest $request): Response
    {
        $this->tickets->update(AuthenticatedUser::from($request), $ticket, $request->ticketData());

        return response()->noContent();
    }

    public function destroy(WebsiteHelpCenterTicket $ticket, Request $request): Response
    {
        $this->tickets->destroy(AuthenticatedUser::from($request), $ticket);

        return response()->noContent();
    }

    public function toggle(WebsiteHelpCenterTicket $ticket, Request $request): Response
    {
        $this->tickets->toggle(AuthenticatedUser::from($request), $ticket);

        return response()->noContent();
    }

    public function reply(WebsiteHelpCenterTicket $ticket, WebsiteTicketReplyFormRequest $request): JsonResponse
    {
        $reply = $this->tickets->reply(AuthenticatedUser::from($request), $ticket, $request->validated('content'));

        return response()->json(['data' => ['id' => $reply->id, 'content' => $reply->content]], 201);
    }

    public function destroyReply(WebsiteHelpCenterTicketReply $reply, Request $request): Response
    {
        $this->tickets->destroyReply(AuthenticatedUser::from($request), $reply);

        return response()->noContent();
    }

    /** @return array<string, mixed> */
    private function ticketData(WebsiteHelpCenterTicket $ticket): array
    {
        $data = ['id' => $ticket->id, 'category_id' => $ticket->category_id, 'title' => $ticket->title, 'content' => $ticket->content, 'open' => (bool) $ticket->open, 'can_delete' => Gate::allows('delete', $ticket), 'author' => $ticket->user ? new PublicUserResource(PublicUserData::from($ticket->user)) : null, 'created_at' => $ticket->created_at === null ? null : CarbonImmutable::parse($ticket->created_at, 'UTC')->toIso8601String()];
        if ($ticket->relationLoaded('replies')) {
            $data['replies'] = $ticket->replies->map(fn ($reply): array => ['id' => $reply->id, 'content' => $reply->content, 'can_delete' => Gate::allows('delete', $reply), 'created_at' => $reply->created_at?->toIso8601String(), 'author' => $reply->user ? new PublicUserResource(PublicUserData::from($reply->user)) : null]);
        }

        return $data;
    }
}
