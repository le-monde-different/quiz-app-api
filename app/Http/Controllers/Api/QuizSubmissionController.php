<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Result;

class QuizSubmissionController extends Controller
{
    //
    public function submit(Request $request,$quizId){
        $quiz = Quiz::with('questions.answers')->findOrFaol($quizId);
        //validation données d'entrées
        $data = $request->validate([
            'user_id' =>('required|exists:users,id'),
            'answers' =>('required|array'),
            'answers.*.question_id'=>'required|exists:questions,id',
            'answers.*.answers_id' =>'required|exists:answers,id',

        ]);
        $score = 0;
        $details = [];
        foreach($data['answers'] as $submitedAnswer){
            $question = $quiz->question->where('id',$submitedAnswer['question_id'])->first();
            $correctAnswer = $question->answers->where('is_correct',true)->first();
            //vérifier si la réponse est correcte
            $is_correct = $correctAnswer && $correctAnswer->id == $submitedAnswer['answer_id'];
            if ($is_correct){
                $score++;
            }

            //stoker les détails pour chaque question
            $details[] = [
                'question_id' => $submitedAnswer['question_id'],
                'submitted_answer_id' => $submitedAnswer['answer_id'],
                'is_correct' => '$is_correct',
            ];
        }

        //Enregistrer le resultat

        $result =Result::create([
            'quiz_id' => $quizId,
            'user_id' =>$data['user_id'],
            'score' => $score,
            'details' => $details,
        ]);
        return response()->json([
            'message' => 'Quiz submitted successfully',
            'score' => $score,
            'details'=>$details,
        ],201);
    } 
}
