<?php

namespace App\Ai\Agents;

use App\Ai\Tools\ListCategoryTool;
use App\Ai\Tools\ListOrderToolForUser;
use App\Ai\Tools\ListProductTool;
use App\Models\User;
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
        return 'You are a helpful ai assistant. You will provide ,category & order information';
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
            new ListCategoryTool

        ];
    }
}
