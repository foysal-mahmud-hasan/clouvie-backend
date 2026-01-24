<?php

namespace App\Http\Controllers;

use App\Models\WaitlistEntry;
use App\Services\ZohoMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WaitlistController extends Controller
{
    public function __construct(private ZohoMailService $zohoMail)
    {
    }

    /**
     * Show all waitlist entries in an admin view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $entries = WaitlistEntry::select('id', 'name', 'email', 'monthly_revenue_range', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('waitlist', compact('entries'));
    }

    /**
     * Store a new waitlist signup.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:waitlist_entries,email',
            'monthly_revenue_range' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $entry = WaitlistEntry::create([
                'name' => $request->name,
                'email' => $request->email,
                'monthly_revenue_range' => $request->monthly_revenue_range,
            ]);

            // Send confirmation email via Zoho Mail HTTP API, but never
            // let mail issues break or significantly delay the response.
            try {
                $this->zohoMail->sendWaitlistConfirmation($entry);
            } catch (\Throwable $mailException) {
                // Silently ignore mail failures here; they are logged inside the service.
            }

            return response()->json([
                'success' => true,
                'message' => 'Added to waitlist successfully!',
                'data' => [
                    'id' => $entry->id,
                    'name' => $entry->name,
                    'email' => $entry->email,
                    'monthly_revenue_range' => $entry->monthly_revenue_range,
                    'created_at' => $entry->created_at,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add to waitlist',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
