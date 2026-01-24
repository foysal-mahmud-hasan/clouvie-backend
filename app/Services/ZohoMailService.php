<?php

namespace App\Services;

use App\Models\WaitlistEntry;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZohoMailService
{
    public function sendWaitlistConfirmation(WaitlistEntry $entry): void
    {
        $accountId = config('services.zoho_mail.account_id');
        $from = config('services.zoho_mail.from');
        $token = config('services.zoho_mail.token');
        $baseUrl = rtrim(config('services.zoho_mail.base_url', 'https://mail.zoho.com/api'), '/');
        $timeout = (int) config('services.zoho_mail.timeout', 5);

        if (! $accountId || ! $from || ! $token) {
            Log::warning('ZohoMailService missing configuration', [
                'account_id' => $accountId,
                'from' => $from,
            ]);

            return;
        }

        $endpoint = sprintf('%s/accounts/%s/messages', $baseUrl, $accountId);

        $subject = 'Welcome to the Clouvie waitlist';

        // Render HTML template with dynamic entry data
        $htmlContent = view('emails.waitlist_confirmation', ['entry' => $entry])->render();

        $response = Http::timeout($timeout)
            ->withHeaders([
                'Authorization' => 'Zoho-oauthtoken ' . $token,
            ])
            ->post($endpoint, [
                'fromAddress' => $from,
                'toAddress' => $entry->email,
                'subject' => $subject,
                'content' => $htmlContent,
                'askReceipt' => 'no',
            ]);

        if (! $response->successful()) {
            Log::error('ZohoMailService failed to send waitlist confirmation', [
                'waitlist_entry_id' => $entry->id,
                'to_email' => $entry->email,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }
    }
}
