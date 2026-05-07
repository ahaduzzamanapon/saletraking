<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\FeedbackFormTemplate;
use App\Models\Visit;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function forms()
    {
        $template = FeedbackFormTemplate::where('is_default', true)->where('is_active', true)->first()
                 ?? FeedbackFormTemplate::where('is_active', true)->first();

        return response()->json(['template' => $template]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'visit_id'            => 'required|exists:visits,id',
            'template_id'         => 'nullable|exists:feedback_form_templates,id',
            'data'                => 'required|array',
            'competitor_activity' => 'nullable|string|max:255',
            'order_value'         => 'nullable|numeric',
            'satisfaction_rating' => 'nullable|integer|between:1,5',
        ]);

        $user  = $request->user();
        $visit = Visit::findOrFail($data['visit_id']);

        if ($visit->salesperson_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $feedback = Feedback::updateOrCreate(
            ['visit_id' => $visit->id],
            array_merge($data, ['salesperson_id' => $user->id])
        );

        $visit->update(['feedback_submitted' => true]);

        return response()->json(['feedback' => $feedback], 201);
    }
}
