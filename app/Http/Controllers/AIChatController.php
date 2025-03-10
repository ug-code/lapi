<?php

namespace App\Http\Controllers;


use App\Http\Requests\AIChat\ChatWithAIRequest;
use App\Services\AIService;

use Illuminate\Http\Request;

class AIChatController extends Controller
{
    protected AIService $aiService;

    public function __construct(AIService $aiService)
    {

        $this->aiService = $aiService;
    }

    public function chatWithAICustom(ChatWithAIRequest $request)
    {
        $body = $request->message ?? "";

        return $this->aiService->chatWithAI($body);
    }

    public function chatWithAI(ChatWithAIRequest $request)
    {
        $userData = $request->message ?? "";
        $body     = [
            "model"       => "gpt-4o-mini",
            "messages"    => [
                [
                    "role"    => "system",
                    "content" => "Sen bir diyet uzmanısın. Kullanıcının sağlık verilerini analiz ederek öneriler sunuyorsun."
                ],
                [
                    "role"    => "user",
                    "content" => $userData
                ]
            ],
            "temperature" => 0.7
        ];
        return $this->aiService->chatWithAI($body);
    }

}
