<?php

namespace Softinline\SfwComponent\Services;

use Softinline\SfwComponent\Models\SfwToken;

/**
 * Canonical API token service (SFW).
 *
 * Centralizes issuing, validation, revocation, FCM association and maintenance of API tokens.
 * Projects must delegate to this service instead of implementing token logic locally.
 */
class ApiTokensService
{
    /**
     * Default token lifetime in days.
     */
    const DEFAULT_EXPIRATION_DAYS = 180;

    /**
     * Retention in days for purging expired/revoked records.
     */
    const PURGE_RETENTION_DAYS = 30;

    /**
     * Issue a new access token for a user/device/app, rotating the previous active token for the same device.
     *
     * @param mixed $userId
     * @param string $app
     * @param string $deviceId
     * @param array $context Supported keys: device_name, platform, fcm_token, expires_in_days, ip
     * @return array|false Returns ['token' => plainToken, 'token_id' => id, 'expires_at' => datetime|null] or false
     */
    public static function issue($userId, $app, $deviceId, array $context = [])
    {
        $userId = (string) $userId;
        $app = is_string($app) ? $app : (string) $app;
        $deviceId = is_string($deviceId) ? $deviceId : (string) $deviceId;

        if ($userId === '' || $deviceId === '') {
            return false;
        }

        $expirationDays = isset($context['expires_in_days']) ? (int) $context['expires_in_days'] : self::getExpirationDays();
        if ($expirationDays <= 0) {
            $expirationDays = self::DEFAULT_EXPIRATION_DAYS;
        }

        $now = now();
        $expiresAt = $now->copy()->addDays($expirationDays);

        // Rotate: revoke previous active tokens for the same (user, app, device)
        self::revokeDevice($userId, $app, $deviceId);

        $plainToken = self::generatePlainToken();
        $hash = self::hashToken($plainToken);

        $token = new SfwToken();
        $token->id = self::generateId();
        $token->user_id = $userId;
        $token->token_hash = $hash;
        $token->app = $app !== '' ? $app : null;
        $token->device_id = $deviceId;
        $token->device_name = isset($context['device_name']) ? (string) $context['device_name'] : null;
        $token->platform = isset($context['platform']) ? (string) $context['platform'] : null;
        $token->fcm_token = isset($context['fcm_token']) && $context['fcm_token'] !== '' ? (string) $context['fcm_token'] : null;
        $token->last_used_at = $now;
        $token->last_ip = isset($context['ip']) ? (string) $context['ip'] : null;
        $token->expires_at = $expiresAt;
        $token->revoked_at = null;

        if (!$token->save()) {
            return false;
        }

        return [
            'token' => $plainToken,
            'token_id' => $token->id,
            'expires_at' => $token->expires_at,
        ];
    }

    /**
     * Validate a bearer token and return the active SfwToken or false.
     *
     * @param string $bearer
     * @param bool $touch Whether to update last_used_at
     * @return SfwToken|false
     */
    public static function validate($bearer, $touch = true)
    {
        if (!is_string($bearer) || $bearer === '') {
            return false;
        }

        $hash = self::hashToken($bearer);
        $token = SfwToken::where('token_hash', $hash)->first();
        if (!$token) {
            return false;
        }

        if ($token->isRevoked() || $token->isExpired()) {
            return false;
        }

        $user = $token->resolveUser();
        if (!$user) {
            return false;
        }

        // Common enabled flags (projects may vary; keep permissive check)
        if (isset($user->enabled) && (int) $user->enabled !== 1) {
            return false;
        }
        if (isset($user->status) && in_array($user->status, ['banned', 'suspended', 'disabled'], true)) {
            return false;
        }

        if ($touch) {
            $token->last_used_at = now();
            $token->save();
        }

        return $token;
    }

    /**
     * Revoke a token by plain bearer value.
     *
     * @param string $bearer
     * @return bool
     */
    public static function revoke($bearer)
    {
        if (!is_string($bearer) || $bearer === '') {
            return false;
        }

        $hash = self::hashToken($bearer);
        $token = SfwToken::where('token_hash', $hash)->first();
        if (!$token) {
            return false;
        }

        if ($token->revoked_at === null) {
            $token->revoked_at = now();
            $token->save();
        }

        return true;
    }

    /**
     * Revoke all tokens for a user (optionally scoped by app).
     *
     * @param mixed $userId
     * @param string|null $app
     * @return int Number of affected tokens
     */
    public static function revokeAllForUser($userId, $app = null)
    {
        $query = SfwToken::where('user_id', (string) $userId)->whereNull('revoked_at');
        if ($app !== null && $app !== '') {
            $query->where('app', $app);
        }

        return $query->update(['revoked_at' => now()]);
    }

    /**
     * Revoke active tokens for a specific user/app/device.
     *
     * @param mixed $userId
     * @param string $app
     * @param string $deviceId
     * @return int
     */
    public static function revokeDevice($userId, $app, $deviceId)
    {
        $query = SfwToken::where('user_id', (string) $userId)
            ->where('device_id', (string) $deviceId)
            ->whereNull('revoked_at');

        if ($app !== '') {
            $query->where(function ($q) use ($app) {
                $q->where('app', $app)->orWhereNull('app');
            });
        }

        return $query->update(['revoked_at' => now()]);
    }

    /**
     * Register or update the FCM token for the authenticated token/device.
     *
     * @param SfwToken|string $tokenOrBearer
     * @param string $fcmToken
     * @return bool
     */
    public static function registerFcmToken($tokenOrBearer, $fcmToken)
    {
        $token = $tokenOrBearer instanceof SfwToken ? $tokenOrBearer : self::validate((string) $tokenOrBearer, false);
        if (!$token) {
            return false;
        }

        $token->fcm_token = is_string($fcmToken) ? $fcmToken : (string) $fcmToken;
        $token->save();

        return true;
    }

    /**
     * Get valid FCM tokens for a user.
     *
     * @param mixed $userId
     * @param string|null $app
     * @return array
     */
    public static function getFcmTokensForUser($userId, $app = null)
    {
        $query = SfwToken::where('user_id', (string) $userId)
            ->whereNull('revoked_at')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->whereNotNull('fcm_token')
            ->where('fcm_token', '<>', '');

        if ($app !== null && $app !== '') {
            $query->where('app', $app);
        }

        return $query->pluck('fcm_token')->unique()->values()->all();
    }

    /**
     * Purge expired/revoked tokens (centralized maintenance).
     *
     * Marks expired tokens as revoked and deletes expired/revoked records older than retention.
     * Intended to be called by each project's scheduler/cron.
     *
     * @return array
     */
    public static function purgeExpired()
    {
        $now = now();

        $expiredMarked = SfwToken::whereNull('revoked_at')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $now)
            ->update(['revoked_at' => $now]);

        $threshold = $now->copy()->subDays(self::PURGE_RETENTION_DAYS);

        $deleted = SfwToken::whereNotNull('revoked_at')
            ->where(function ($q) use ($threshold) {
                $q->where('revoked_at', '<=', $threshold)
                  ->orWhere(function ($q2) use ($threshold) {
                      $q2->whereNotNull('expires_at')->where('expires_at', '<=', $threshold);
                  });
            })
            ->delete();

        return [
            'expired_marked' => $expiredMarked,
            'deleted' => $deleted,
        ];
    }

    /**
     * Extract bearer token from current request (Authorization header only).
     *
     * @return string|null
     */
    public static function bearerFromRequest()
    {
        $request = request();
        $auth = $request->header('Authorization');
        if (!is_string($auth) || $auth === '') {
            return null;
        }
        if (stripos($auth, 'Bearer ') === 0) {
            $auth = trim(substr($auth, 7));
        } else {
            $auth = trim($auth);
        }

        return $auth !== '' ? $auth : null;
    }

    /**
     * Get configured expiration days.
     *
     * @return int
     */
    public static function getExpirationDays()
    {
        $days = (int) config('sfw.tokens_expiration_days', self::DEFAULT_EXPIRATION_DAYS);
        return $days > 0 ? $days : self::DEFAULT_EXPIRATION_DAYS;
    }

    /**
     * Generate a cryptographically secure plain token.
     *
     * @return string
     */
    private static function generatePlainToken()
    {
        return bin2hex(random_bytes(40));
    }

    /**
     * Hash a plain token for storage/lookup.
     *
     * @param string $plain
     * @return string
     */
    public static function hashToken($plain)
    {
        return hash('sha256', $plain);
    }

    /**
     * Generate a primary key for sfw_tokens.
     *
     * @return string
     */
    private static function generateId()
    {
        return 'ST' . str_replace('.', '', uniqid('', true));
    }
}
