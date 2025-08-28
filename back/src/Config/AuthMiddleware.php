<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Config;

class AuthMiddleware
{
    /**
     * Vérifie si l'utilisateur est connecté
     */
    public static function isAuthenticated(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['user_id']) && isset($_SESSION['user_type']);
    }

    /**
     * Vérifie si l'utilisateur est un provider
     */
    public static function isProvider(): bool
    {
        if (!self::isAuthenticated()) {
            return false;
        }

        return $_SESSION['user_type'] === 'provider';
    }

    /**
     * Vérifie si l'utilisateur est un client
     */
    public static function isClient(): bool
    {
        if (!self::isAuthenticated()) {
            return false;
        }

        return $_SESSION['user_type'] === 'client';
    }

    /**
     * Vérifie si l'utilisateur peut accéder à une ressource spécifique
     */
    public static function canAccessResource(int $resourceUserId, string $resourceUserType): bool
    {
        if (!self::isAuthenticated()) {
            return false;
        }

        // L'utilisateur peut accéder à ses propres ressources
        if ($_SESSION['user_id'] === $resourceUserId && $_SESSION['user_type'] === $resourceUserType) {
            return true;
        }

        // Les providers peuvent voir les profils des clients (pour les services)
        if (self::isProvider() && $resourceUserType === 'client') {
            return true;
        }

        // Les clients peuvent voir les profils des providers (pour chercher des services)
        if (self::isClient() && $resourceUserType === 'provider') {
            return true;
        }

        return false;
    }

    /**
     * Retourne l'ID de l'utilisateur connecté
     */
    public static function getCurrentUserId(): ?int
    {
        if (!self::isAuthenticated()) {
            return null;
        }

        return $_SESSION['user_id'];
    }

    /**
     * Retourne le type d'utilisateur connecté
     */
    public static function getCurrentUserType(): ?string
    {
        if (!self::isAuthenticated()) {
            return null;
        }

        return $_SESSION['user_type'];
    }

    /**
     * Middleware pour protéger une route
     */
    public static function requireAuth(callable $handler): callable
    {
        return function (...$args) use ($handler) {
            if (!self::isAuthenticated()) {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'Authentification requise'
                ]);
                return;
            }

            return $handler(...$args);
        };
    }

    /**
     * Middleware pour protéger une route (providers uniquement)
     */
    public static function requireProvider(callable $handler): callable
    {
        return function (...$args) use ($handler) {
            if (!self::isProvider()) {
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'message' => 'Accès réservé aux providers'
                ]);
                return;
            }

            return $handler(...$args);
        };
    }

    /**
     * Middleware pour protéger une route (clients uniquement)
     */
    public static function requireClient(callable $handler): callable
    {
        return function (...$args) use ($handler) {
            if (!self::isClient()) {
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'message' => 'Accès réservé aux clients'
                ]);
                return;
            }

            return $handler(...$args);
        };
    }
}
