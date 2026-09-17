# AuthUser Library

`ttpryg/auth-user` is a framework-agnostic standalone PHP library for user management, authentication, token management, and password resetting.

## 🌟 Key Features

- **Framework Agnostic**: Works out of the box with any PHP application or framework (Vanilla PHP, Slim 4, Laravel, Symfony, CodeIgniter).
- **PDO-based & ORM Agnostic**: Includes standard PDO repositories (`PdoUserRepository`, `PdoTokenRepository`) without tying your project to Eloquent or Doctrine.
- **Improved Database Schema**:
  - `users` table with `updated_at`, `deleted_at` (soft deletes), composite indexing, and `metadata` JSON support.
  - Dedicated `user_tokens` table for password resets, email verification, and refresh tokens.
- **Domain Events**: Dispatches domain events (`UserRegisteredEvent`, `UserStatusChangedEvent`, `PasswordResetRequestedEvent`) for seamless integration with event listeners.
- **Security First**: Native password hashing using `password_hash()` with automatic rehash detection.

---

## 🗄️ Database Schema

Run the SQL script from `database/schema.sql` or use `DatabaseMigrator`:

```sql
CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    metadata JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    INDEX idx_user_login (email, is_active),
    INDEX idx_username_login (username, is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS user_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    type VARCHAR(50) NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_token_type (user_id, type),
    CONSTRAINT fk_user_tokens_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 🚀 Quick Usage Example (Vanilla PDO / Slim)

```php
use PDO;
use Ttpryg\AuthUser\Repositories\PdoUserRepository;
use Ttpryg\AuthUser\Repositories\PdoTokenRepository;
use Ttpryg\AuthUser\Security\NativePasswordHasher;
use Ttpryg\AuthUser\Services\RegistrationService;
use Ttpryg\AuthUser\Services\AuthenticationService;

// 1. Initialize PDO
$pdo = new PDO("mysql:host=localhost;dbname=my_db", "root", "secret");

// 2. Setup Dependencies
$userRepo = new PdoUserRepository($pdo);
$tokenRepo = new PdoTokenRepository($pdo);
$hasher = new NativePasswordHasher();

// 3. User Registration
$registrationService = new RegistrationService($userRepo, $hasher);
$user = $registrationService->register(
    email: 'johndoe@example.com',
    plainPassword: 'SuperSecretPassword123!',
    username: 'johndoe',
    metadata: ['full_name' => 'John Doe']
);

// 4. User Authentication
$authService = new AuthenticationService($userRepo, $hasher);
$authenticatedUser = $authService->authenticate('johndoe@example.com', 'SuperSecretPassword123!');

echo "Welcome back, " . $authenticatedUser->getEmail();
```

---

## 🛠 Integration with Frameworks

### Laravel
Bind contracts to PDO / Repositories in a Service Provider:
```php
$this->app->singleton(UserRepositoryInterface::class, function ($app) {
    return new PdoUserRepository(DB::connection()->getPdo());
});
```

### Symfony / DI Containers
Register services in `services.yaml` or PSR-11 container using standard interface bindings.

---

## 📄 License
MIT License.
