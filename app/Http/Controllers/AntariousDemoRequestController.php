<?php

namespace App\Http\Controllers;

use App\Models\AntariousDemoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AntariousDemoRequestController extends Controller
{
    private const NOTIFICATION_RECIPIENT = 'sales@antarious.com';

    private const FROM_ADDRESS = 'Antarious <sales@antarious.com>';

    private const TEAM_SIZES = ['1-10', '11-50', '51-200', '200+'];

    public function index()
    {
        $requests = AntariousDemoRequest::orderBy('created_at', 'desc')->paginate(50);

        return view('antarious_demo_requests', ['requests' => $requests]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'company' => 'required|string|max:255',
            'role' => 'nullable|string|max:120',
            'team_size' => 'nullable|string|in:'.implode(',', self::TEAM_SIZES),
            'use_case' => 'nullable|string|max:4000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $submission = AntariousDemoRequest::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'company' => $request->input('company'),
                'role' => $request->input('role'),
                'team_size' => $request->input('team_size'),
                'use_case' => $request->input('use_case'),
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 512),
            ]);

            try {
                $apiKey = config('services.resend_antarious.api_key');
                if (empty($apiKey)) {
                    throw new \RuntimeException('RESEND_ANTARIOUS_API_KEY is not configured');
                }

                $client = \Resend::client($apiKey);

                $client->emails->send([
                    'from' => self::FROM_ADDRESS,
                    'to' => [self::NOTIFICATION_RECIPIENT],
                    'reply_to' => [$submission->email],
                    'subject' => "New Antarious demo request — {$submission->name}".
                        ($submission->company ? " ({$submission->company})" : ''),
                    'html' => view('emails.antarious.notification', ['submission' => $submission])->render(),
                ]);

                $client->emails->send([
                    'from' => self::FROM_ADDRESS,
                    'to' => [$submission->email],
                    'reply_to' => [self::NOTIFICATION_RECIPIENT],
                    'subject' => "We got your request, {$submission->name} — Antarious",
                    'html' => view('emails.antarious.auto_reply', ['submission' => $submission])->render(),
                ]);

                $submission->update(['notified_at' => now()]);
            } catch (\Throwable $mailException) {
                Log::warning('Antarious demo mail failed', [
                    'submission_id' => $submission->id,
                    'error' => $mailException->getMessage(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => "Thanks! The Antarious team will be in touch within 24 hours.",
                'data' => [
                    'id' => $submission->id,
                    'created_at' => $submission->created_at,
                ],
            ], 201);
        } catch (\Exception $e) {
            Log::error('Antarious demo store failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong saving your request. Please try again or email sales@antarious.com directly.',
            ], 500);
        }
    }
}
