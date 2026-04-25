<?php

namespace App\Http\Controllers;

use App\Mail\VelondraStudioContactAutoReply;
use App\Mail\VelondraStudioContactNotification;
use App\Models\VelondraStudioSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class VelondraStudioContactController extends Controller
{
    private const NOTIFICATION_RECIPIENT = 'studio@velondra.com';

    /**
     * Admin view: list every submission, latest first.
     */
    public function index()
    {
        $submissions = VelondraStudioSubmission::orderBy('created_at', 'desc')->paginate(50);

        return view('velondra_studio_submissions', compact('submissions'));
    }

    /**
     * Public POST endpoint hit by the studio.velondra.com contact form.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'company' => 'nullable|string|max:255',
            'budget' => 'nullable|string|max:255',
            'services' => 'nullable|array',
            'services.*' => 'string|max:128',
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
            $submission = VelondraStudioSubmission::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'company' => $request->input('company'),
                'budget' => $request->input('budget'),
                'services' => $request->input('services', []),
                'message' => $request->input('message'),
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 512),
            ]);

            // Fire both emails inside a try/catch so a mail failure never costs us the row.
            try {
                Mail::to(self::NOTIFICATION_RECIPIENT)->send(new VelondraStudioContactNotification($submission));
                Mail::to($submission->email)->send(new VelondraStudioContactAutoReply($submission));
                $submission->update(['notified_at' => now()]);
            } catch (\Throwable $mailException) {
                Log::warning('Velondra Studio contact mail failed', [
                    'submission_id' => $submission->id,
                    'error' => $mailException->getMessage(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Thanks! We got your message and will be in touch within 24 hours.',
                'data' => [
                    'id' => $submission->id,
                    'created_at' => $submission->created_at,
                ],
            ], 201);
        } catch (\Exception $e) {
            Log::error('Velondra Studio contact store failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong saving your message. Please try again or email us directly.',
            ], 500);
        }
    }
}
