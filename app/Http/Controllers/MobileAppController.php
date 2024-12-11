<?php

namespace App\Http\Controllers;


use App\Http\Requests\MobileApp\CreateKeywordRequest;
use App\Models\AppKeyword;
use App\Services\DictionaryService;
use App\Services\TradingService;
use Illuminate\Http\JsonResponse;

class MobileAppController extends Controller
{
    protected DictionaryService $dictionaryService;

    public function __construct(DictionaryService $dictionaryService)
    {

        $this->dictionaryService = $dictionaryService;
    }

    public function createKeyword(CreateKeywordRequest $request): JsonResponse
    {

        $validatedData = $request->validated();

        $appKeyword              = new AppKeyword();
        $appKeyword->user_id     = 1;
        $appKeyword->eng_keyword = $request->eng_keyword ?? null;
        $appKeyword->tr_keyword  = $request->tr_keyword ?? null;
        $appKeyword->is_learned  = false;
        $appKeyword->category_id    = $request->category_id ?? 1;
        $appKeyword->save();

        return response()->json([
            'message' => 'Başarılı bir şekilde kaydedildi.',
            'data'    => $validatedData
        ]);
    }

    public function getKeywordList(): JsonResponse
    {
        $data = AppKeyword::get()->toArray();

        return response()->json([
            'message' => 'Başarılı bir şekilde kaydedildi.',
            'data'    => $data
        ]);
    }

    public function setLearnKeyword($id): JsonResponse
    {
        $data = AppKeyword::find($id)->update(['is_learned' => true]);

        return response()->json([
            'message' => 'Başarılı bir şekilde kaydedildi.',
            'data'    => $data
        ]);
    }

    public function translate($keyword)
    {
       $data = $this->dictionaryService->scan($keyword);
        return response()->json([
            'message' => 'Başarılı bir şekilde kaydedildi.',
            'data'    => $data
        ]);
    }
}
