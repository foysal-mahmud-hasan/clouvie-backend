<?php

namespace App\Services;

use App\Models\WaitlistEntry;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ZohoMailService
{
    public function sendWaitlistConfirmation(WaitlistEntry $entry): void
    {
        $accountId = config('services.zoho_mail.account_id');
        $from = config('services.zoho_mail.from');
        $baseUrl = rtrim(config('services.zoho_mail.base_url', 'https://mail.zoho.com/api'), '/');
        $timeout = (int) config('services.zoho_mail.timeout', 5);

        if (! $accountId || ! $from) {
            Log::warning('ZohoMailService missing configuration', [
                'account_id' => $accountId,
                'from' => $from,
            ]);

            return;
        }

        // Proactively get/refresh token before sending
        $token = $this->getAccessToken();
        if (! $token) {
            Log::error('ZohoMailService unable to get valid access token');
            return;
        }

        $endpoint = sprintf('%s/accounts/%s/messages', $baseUrl, $accountId);
        $subject = 'Welcome to the Clouvie waitlist';
        $htmlContent = view('emails.waitlist_confirmation', ['entry' => $entry])->render();

        $response = Http::timeout($timeout)
            ->withHeaders([
                'Authorization' => 'Zoho-oauthtoken ' . $token,
            ])
            ->post($endpoint, [
                'fromAddress' => $from,
                'displayName' => 'Asif from Clouvie',
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

    private function getAccessToken(): ?string
    {
        // Check if cached token exists and is still valid (not expiring soon)
        $cachedToken = Cache::get('zoho_mail_access_token');
        $tokenExpiry = Cache::get('zoho_mail_token_expiry');
        
        // If token exists and won't expire in next 5 minutes, use it
        if ($cachedToken && $tokenExpiry && now()->addMinutes(5)->lessThan($tokenExpiry)) {
            return $cachedToken;
        }

        // Token expired or expiring soon, refresh it proactively
        Log::info('ZohoMailService token expired or expiring soon, refreshing proactively...');
        return $this->refreshAccessToken();
    }

    private function refreshAccessToken(): ?string
    {
        $clientId = config('services.zoho_mail.client_id');
        $clientSecret = config('services.zoho_mail.client_secret');
        $refreshToken = config('services.zoho_mail.refresh_token');

        if (! $clientId || ! $clientSecret || ! $refreshToken) {
            Log::warning('ZohoMailService missing refresh credentials');
            return null;
        }

        try {
            $response = Http::asForm()->post('https://accounts.zoho.com/oauth/v2/token', [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'refresh_token' => $refreshToken,
                'grant_type' => 'refresh_token',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $newToken = $data['access_token'] ?? null;
                $expiresIn = $data['expires_in'] ?? 3600; // Default 1 hour
                
                if ($newToken) {
                    // Store token and its expiry time
                    $expiryTime = now()->addSeconds($expiresIn);
                    Cache::put('zoho_mail_access_token', $newToken, $expiryTime);
                    Cache::put('zoho_mail_token_expiry', $expiryTime, $expiryTime);
                    
                    Log::info('ZohoMailService token refreshed successfully', [
                        'expires_in_seconds' => $expiresIn,
                        'expires_at' => $expiryTime->toDateTimeString(),
                    ]);
                    
                    return $newToken;
                }
            }

            Log::error('ZohoMailService token refresh failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Exception $e) {
            Log::error('ZohoMailService token refresh exception', [
                'message' => $e->getMessage(),
            ]);
        }

        return null;
    }
}
