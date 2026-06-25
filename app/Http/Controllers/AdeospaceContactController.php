<?php

namespace App\Http\Controllers;

use App\Mail\AdeospaceContactAutoReply;
use App\Mail\AdeospaceContactNotification;
use App\Models\AdeospaceSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AdeospaceContactController extends Controller
{
    private const NOTIFICATION_RECIPIENT = 'hello@adeospace.co.uk';

    /**
     * Admin view: list every submission, latest first.
     */
    public function index()
    {
        $submissions = AdeospaceSubmission::orderBy('created_at', 'desc')->paginate(50);

        return view('adeospace_submissions', compact('submissions'));
    }

    /**
     * Public POST endpoint hit by the adeospace.co.uk "Start a Conversation" form.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'organisation' => 'nullable|string|max:255',
            'sector' => 'nullable|string|max:255',
            'message' => 'required|string|max:10000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $submission = AdeospaceSubmission::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'organisation' => $request->input('organisation'),
                'sector' => $request->input('sector'),
                'message' => $request->input('message'),
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 512),
            ]);

            // Fire both emails inside a try/catch so a mail failure never costs us the row.
            try {
                Mail::to(self::NOTIFICATION_RECIPIENT)->send(new AdeospaceContactNotification($submission));
                Mail::to($submission->email)->send(new AdeospaceContactAutoReply($submission));
                $submission->update(['notified_at' => now()]);
            } catch (\Throwable $mailException) {
                Log::warning('AdeoSpace contact mail failed', [
                    'submission_id' => $submission->id,
                    'error' => $mailException->getMessage(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Thanks! We got your message and will be in touch within 48 hours.',
                'data' => [
                    'id' => $submission->id,
                    'created_at' => $submission->created_at,
                ],
            ], 201);
        } catch (\Exception $e) {
            Log::error('AdeoSpace contact store failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong saving your message. Please try again or email us directly.',
            ], 500);
        }
    }
}
