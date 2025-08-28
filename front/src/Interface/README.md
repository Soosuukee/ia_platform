# 📁 Interface - Types TypeScript centralisés

Ce dossier contient **toutes les interfaces TypeScript** correspondant aux entités PHP du backend pour garantir la cohérence des types dans l'application.

## 🎯 **Objectif**

- ✅ **Centraliser** tous les types en un seul endroit
- ✅ **Synchroniser** les types frontend avec les entités backend
- ✅ **Éviter** la duplication de définitions d'interfaces
- ✅ **Faciliter** la maintenance et les mises à jour

## 📋 **Structure**

```
Interface/
├── index.ts          # Toutes les interfaces et types
└── README.md         # Cette documentation
```

## 🔧 **Utilisation**

### **Import simple d'une interface :**

```typescript
import { Provider, Client, ProvidedService } from "@/Interface";

const provider: Provider = {
  id: 1,
  firstName: "Jean",
  lastName: "Dupont",
  // ...
};
```

### **Import de types de formulaires :**

```typescript
import { CreateServiceData, UpdateProviderData } from "@/Interface";

const serviceData: CreateServiceData = {
  title: "Développement Web",
  description: "Création de sites web modernes",
  duration: "2 semaines",
};
```

### **Import de constantes et validations :**

```typescript
import { USER_ROLES, isValidEmail, isProvider } from "@/Interface";

if (user.role === USER_ROLES.PROVIDER) {
  // Logique provider
}

if (isValidEmail(email)) {
  // Email valide
}
```

### **Import par défaut pour les utilitaires :**

```typescript
import InterfaceUtils from "@/Interface";

if (InterfaceUtils.isProvider(user)) {
  // User est un provider
}
```

## 📊 **Entités disponibles**

### **👥 Utilisateurs**

- `Client` - Client de la plateforme
- `Provider` - Prestataire de services
- `User` - Union type (Client | Provider)

### **🛠️ Services**

- `ProvidedService` - Service proposé par un provider
- `Skill` - Compétence technique
- `ProviderSkill` - Association provider-compétence

### **📅 Réservations**

- `AvailabilitySlot` - Créneau de disponibilité
- `Booking` - Réservation d'un créneau

### **💬 Communication**

- `Request` - Demande client vers provider
- `Review` - Avis client sur provider
- `Notification` - Notification système

### **🎓 Éducation & Portfolio**

- `ProviderDiploma` - Diplôme du provider
- `CompletedWork` - Travail réalisé
- `CompletedWorkMedia` - Média d'un travail

## 🔧 **Types de formulaires**

### **Création (sans ID)**

```typescript
CreateClientData;
CreateProviderData;
CreateServiceData;
CreateSlotData;
CreateDiplomaData;
CreateCompletedWorkData;
CreateRequestData;
CreateReviewData;
CreateBookingData;
```

### **Mise à jour (champs optionnels)**

```typescript
UpdateProviderData;
UpdateServiceData;
UpdateSlotData;
UpdateDiplomaData;
UpdateCompletedWorkData;
UpdateRequestData;
```

## 📡 **Types de réponses API**

```typescript
ApiResponse<T>; // Réponse générique
LoginResponse; // Réponse de connexion
DashboardData; // Données du dashboard
PaginatedResponse<T>; // Réponse paginée
```

## 🏷️ **Types utilitaires**

```typescript
UserRole; // 'client' | 'provider'
BookingStatus; // 'pending' | 'accepted' | 'declined'
RequestStatus; // 'pending' | 'accepted' | 'declined' | 'completed'
MediaType; // 'image' | 'video' | 'document' | 'other'
EntityId; // number
DateString; // string (format ISO)
UrlString; // string (URL)
```

## 🛡️ **Validations et Guards**

```typescript
// Type guards
isClient(user: User): user is Client
isProvider(user: User): user is Provider

// Validations
isValidEmail(email: string): boolean
isValidRating(rating: number): boolean
isValidBookingStatus(status: string): status is BookingStatus
isValidRequestStatus(status: string): status is RequestStatus
```

## 🔄 **Synchronisation avec le backend**

Les interfaces sont **synchronisées** avec les entités PHP suivantes :

| Interface TypeScript | Entité PHP Backend                       |
| -------------------- | ---------------------------------------- |
| `Client`             | `back/src/Entity/Client.php`             |
| `Provider`           | `back/src/Entity/Provider.php`           |
| `ProvidedService`    | `back/src/Entity/ProvidedService.php`    |
| `AvailabilitySlot`   | `back/src/Entity/AvailabilitySlot.php`   |
| `Booking`            | `back/src/Entity/Booking.php`            |
| `Request`            | `back/src/Entity/Request.php`            |
| `Review`             | `back/src/Entity/Review.php`             |
| `Skill`              | `back/src/Entity/Skill.php`              |
| `ProviderSkill`      | `back/src/Entity/ProviderSkill.php`      |
| `ProviderDiploma`    | `back/src/Entity/ProviderDiploma.php`    |
| `CompletedWork`      | `back/src/Entity/CompletedWork.php`      |
| `CompletedWorkMedia` | `back/src/Entity/CompletedWorkMedia.php` |
| `Notification`       | `back/src/Entity/Notification.php`       |

## ⚠️ **Règles importantes**

1. **Ne modifiez jamais** les interfaces sans vérifier le backend
2. **Utilisez toujours** ces interfaces dans vos services et composants
3. **Mettez à jour** ce fichier quand vous modifiez une entité backend
4. **Testez** la cohérence des types après chaque modification

## 🚀 **Exemples pratiques**

### **Dans un service :**

```typescript
import { Provider, CreateServiceData } from "@/Interface";

export async function createService(
  providerId: number,
  data: CreateServiceData
): Promise<Provider> {
  // Logique du service
}
```

### **Dans un composant React :**

```typescript
import { Provider, BOOKING_STATUSES } from "@/Interface";

interface Props {
  provider: Provider;
}

export default function ProviderCard({ provider }: Props) {
  // Composant avec types sûrs
}
```

### **Dans un hook :**

```typescript
import { useState } from "react";
import { DashboardData, UpdateProviderData } from "@/Interface";

export function useProviderDashboard() {
  const [data, setData] = useState<DashboardData | null>(null);

  const updateProfile = (updates: UpdateProviderData) => {
    // Logique de mise à jour
  };

  return { data, updateProfile };
}
```

---

## 🔧 **Maintenance**

Quand vous ajoutez/modifiez une entité backend :

1. ✅ Mettez à jour l'interface correspondante dans `index.ts`
2. ✅ Ajoutez les types de formulaires nécessaires
3. ✅ Testez la compilation TypeScript
4. ✅ Mettez à jour cette documentation si nécessaire

---

_Dernière mise à jour : [Date actuelle]_
