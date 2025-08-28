<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Config\AuthMiddleware;

class ProtectedController
{
    /**
     * Exemple de route protégée - accessible uniquement aux utilisateurs connectés
     */
    public function protectedRoute(): array
    {
        // Cette méthode ne sera appelée que si l'utilisateur est authentifié
        // grâce au middleware dans routes.php

        $userId = AuthMiddleware::getCurrentUserId();
        $userType = AuthMiddleware::getCurrentUserType();

        return [
            'success' => true,
            'message' => 'Route protégée accessible',
            'user' => [
                'id' => $userId,
                'type' => $userType
            ]
        ];
    }

    /**
     * Exemple de route réservée aux providers
     */
    public function providerOnlyRoute(): array
    {
        // Vérification supplémentaire dans le contrôleur
        if (!AuthMiddleware::isProvider()) {
            return [
                'success' => false,
                'message' => 'Accès réservé aux providers'
            ];
        }

        return [
            'success' => true,
            'message' => 'Route provider accessible'
        ];
    }

    /**
     * Exemple de route réservée aux clients
     */
    public function clientOnlyRoute(): array
    {
        // Vérification supplémentaire dans le contrôleur
        if (!AuthMiddleware::isClient()) {
            return [
                'success' => false,
                'message' => 'Accès réservé aux clients'
            ];
        }

        return [
            'success' => true,
            'message' => 'Route client accessible'
        ];
    }

    /**
     * Exemple de route avec vérification de propriété de ressource
     */
    public function userResourceRoute(int $resourceId): array
    {
        $currentUserId = AuthMiddleware::getCurrentUserId();
        $currentUserType = AuthMiddleware::getCurrentUserType();

        // Vérifier si l'utilisateur peut accéder à cette ressource
        if (!AuthMiddleware::canAccessResource($resourceId, $currentUserType)) {
            return [
                'success' => false,
                'message' => 'Accès non autorisé à cette ressource'
            ];
        }

        return [
            'success' => true,
            'message' => 'Accès autorisé à la ressource',
            'resourceId' => $resourceId,
            'userId' => $currentUserId
        ];
    }
}
