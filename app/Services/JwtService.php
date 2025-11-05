<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;
use Exception;

class JwtService
{
    /**
     * Generate access token for user (expires in 5 minutes)
     *
     * @param User $user
     * @return string
     */
    public function generateAccessToken(User $user): string
    {
        $payload = [
            'iss' => request()->url(), // Issuer
            'iat' => now()->timestamp, // Issued at
            'exp' => now()->addMinutes(5)->timestamp, // Expiration (5 minutes)
            'sub' => $user->id, // Subject (user ID)
            'email' => $user->email,
            'type' => 'access', // Token type
        ];

        return JWT::encode($payload, $this->getSecretKey(), 'HS256');
    }

    /**
     * Generate refresh token for user (expires in 7 days)
     *
     * @param User $user
     * @return string
     */
    public function generateRefreshToken(User $user): string
    {
        $payload = [
            'iss' => request()->url(), // Issuer
            'iat' => now()->timestamp, // Issued at
            'exp' => now()->addDays(7)->timestamp, // Expiration (7 days)
            'sub' => $user->id, // Subject (user ID)
            'email' => $user->email,
            'type' => 'refresh', // Token type
        ];

        return JWT::encode($payload, $this->getSecretKey(), 'HS256');
    }

    /**
     * Generate both access and refresh tokens
     *
     * @param User $user
     * @return array
     */
    public function generateTokenPair(User $user): array
    {
        return [
            'access_token' => $this->generateAccessToken($user),
            'refresh_token' => $this->generateRefreshToken($user),
        ];
    }

    /**
     * @deprecated Use generateAccessToken() instead
     * Generate JWT token for user
     *
     * @param User $user
     * @return string
     */
    public function generateToken(User $user): string
    {
        return $this->generateAccessToken($user);
    }

    /**
     * Validate and decode JWT token
     *
     * @param string $token
     * @param string|null $expectedType Expected token type ('access' or 'refresh')
     * @return object
     * @throws Exception
     */
    public function validateToken(string $token, ?string $expectedType = null): object
    {
        try {
            $decoded = JWT::decode($token, new Key($this->getSecretKey(), 'HS256'));

            // Validate token type if expected
            if ($expectedType && (!isset($decoded->type) || $decoded->type !== $expectedType)) {
                throw new Exception('Invalid token type. Expected: ' . $expectedType);
            }

            return $decoded;
        } catch (\Exception $e) {
            throw new Exception('Invalid or expired token: ' . $e->getMessage());
        }
    }

    /**
     * Get user from token
     *
     * @param string $token
     * @param string|null $expectedType Expected token type ('access' or 'refresh')
     * @return User|null
     */
    public function getUserFromToken(string $token, ?string $expectedType = null): ?User
    {
        try {
            $decoded = $this->validateToken($token, $expectedType);
            return User::find($decoded->sub);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Validate refresh token and get user
     *
     * @param string $refreshToken
     * @return User|null
     */
    public function validateRefreshToken(string $refreshToken): ?User
    {
        return $this->getUserFromToken($refreshToken, 'refresh');
    }


    /**
     * Get JWT secret key from environment
     *
     * @return string
     */
    private function getSecretKey(): string
    {
        $secret = env('JWT_SECRET');
        if (empty($secret)) {
            throw new Exception('JWT_SECRET is not configured in .env file');
        }
        return $secret;
    }
}
