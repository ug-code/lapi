<?php

namespace App\Http\Controllers;


use App\Http\Requests\MobileApp\CreateCategoryRequest;
use App\Http\Requests\MobileApp\CreateKeywordRequest;
use App\Http\Requests\MobileApp\SetLearnKeywordRequest;
use App\Models\AppKeyword;
use App\Models\AppKeywordCategory;
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
        $checkKeyword  = AppKeyword::where('user_id', 1)->where('eng_keyword', $request->eng_keyword)
            ->first();
        if ($checkKeyword) {
            return response()->json([
                'message' => 'Kelime mevcut.',
                'data'    => $validatedData
            ]);
        }

        $appKeyword              = new AppKeyword();
        $appKeyword->user_id     = 1;
        $appKeyword->eng_keyword = $request->eng_keyword ?? null;
        $appKeyword->tr_keyword  = $request->tr_keyword ?? null;
        $appKeyword->is_learned  = false;
        $appKeyword->category_id = $request->category_id ?? 1;
        $appKeyword->detail      = $this->dictionaryService->scan($request->eng_keyword);
        $appKeyword->save();

        return response()->json([
            'message' => 'Başarılı bir şekilde kaydedildi.',
            'data'    => $validatedData
        ]);
    }

    public function getKeywordList(): JsonResponse
    {
        $data = AppKeyword::orderBy('eng_keyword')->get()->toArray();

        return response()->json([
            'message' => 'Başarılı bir şekilde kaydedildi.',
            'data'    => $data
        ]);
    }

    public function setLearnKeyword(SetLearnKeywordRequest $request): JsonResponse
    {

        $request->validated();

        $data = AppKeyword::find($request->id)->update(['is_learned' => $request->isLearned]);

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

    public function getKeyword($id): JsonResponse
    {
        $data      = AppKeyword::find($id);
        $translate = $this->dictionaryService->scan($data->eng_keyword);
        return response()->json([
            'message' => 'Başarılı bir şekilde kaydedildi.',
            'data'    => [
                'keyword'   => $data->toArray(),
                'translate' => $translate,
            ]
        ]);
    }

    public function myKeywordCount(): JsonResponse
    {
        $data = AppKeyword::where('user_id', 1)->count();
        return response()->json([
            'message' => 'Başarılı bir şekilde kaydedildi.',
            'data'    => $data
        ]);
    }


    public function createCategory(CreateCategoryRequest $request): JsonResponse
    {

        $validatedData = $request->validated();


        $category = AppKeywordCategory::firstOrCreate([
            'description' => $request->description
        ], ['user_id' => 1]);

        $message = $category->wasRecentlyCreated ? 'Başarılı bir şekilde kaydedildi.' : 'Kayıt zaten mevcut';

        return response()->json([
            'message' => $message,
            'data'    => $category
        ]);
    }

}
