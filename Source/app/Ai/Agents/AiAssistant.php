<?php

namespace App\Ai\Agents;

use App\Ai\Tools\ListCategoryTool;
use App\Ai\Tools\ListOrderToolForUser;
use App\Ai\Tools\ListProductTool;
use App\Ai\Tools\CancelOrderTool;
use App\Ai\Tools\CreateOrderTool;
use App\Models\User;
use GuzzleHttp\Promise\Create;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Stringable;

class AiAssistant implements Agent, Conversational, HasTools
{
    use Promptable;

    public function __construct(public User $user) {
        $this->user = $user;
    }

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return 'You are a helpful ai assistant. You will provide category & order information.';
    // IMPORTANT FORMATTING RULES:
    // - Never use tables or markdown tables
    // - Never use | characters
    // - Show each order on separate lines like this:
    //   Order(Invoice Number): Product Name - Qty: 3 - Price: $79.99
    // - Use simple bullet points or numbered lists only';
    }

    /**
     * Get the list of messages comprising the conversation so far.
     *
     * @return Message[]
     */
    public function messages(): iterable
    {
        return [];
    }

    /**
     * Get the tools available to the agent.
     *
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [
            new ListProductTool,
            new ListCategoryTool,
            new ListOrderToolForUser($this->user),
            new CancelOrderTool($this->user),
            new CreateOrderTool($this->user),


        ];
    }
}
