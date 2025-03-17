<?php

namespace App\Http\Controllers\Api\v1\Back\Audit\CRUD;

use App\Http\Controllers\Controller;
use App\Http\Requests\Audit\QuestionListRequest;
use App\Http\Requests\Audit\QuestionRequest;
use App\Repositories\Audit\QuestionRepository;

class QuestionController extends Controller
{
    public function __construct(
        private QuestionRepository $repo,
        public $subject = 'Стандарт аудита',
        public $genus = 'male'
    )
    {
		$this->middleware('notice.message')->only(['store', 'update', 'destroy', 'restore']);
    }



    public function index(QuestionListRequest $request)
    {
        $questions = $this->repo->get($request->validated());

        return response()->json([
            'data' => $questions,
            'success' => 1
        ]);
    }



    public function store(QuestionRequest $request)
    {
        $question = $this->repo->create($request->validated());

        return response()->json([
            'data' => $question,
            'success' => 1,
        ]);
    }



    public function update(int $id, QuestionRequest $request)
    {
        $question = $this->repo->update($id, $request->validated());

        return response()->json([
            'data' => $question,
            'success' => 1,
        ]);
    }



    public function show(int $id)
    {
        $question = $this->repo->getById($id);

        return response()->json([
            'data' => $question,
            'success' => 1
        ]);
    }



    public function destroy(int $id)
    {
        $question = $this->repo->delete($id);

        return response()->json([
            'success' => 1,
        ]);
    }



    public function restore(int $id)
    {
        $question = $this->repo->restore($id);

        return response()->json([
            'success' => 1,
        ]);
    }
}
