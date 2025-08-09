<?php

namespace App\Services;

use \App\Factories\PersonFactory;
use App\Models\Person;
use PDO;

class AuthService
{
    private $pdo;
    private $secretKey;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->secretKey = $_ENV['JWT_SECRET'] ?? 'your-secret-key-change-this-in-production';
    }

    public function register(array $data): array
    {
        // Check if user already exists
        if ($this->getUserByEmail($data['email'])) {
            throw new \Exception('User already exists');
        }

        // Hash the password
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare('INSERT INTO person (first_name, last_name, email, phone, role, password) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['first_name'] ?? '',
            $data['last_name'] ?? '',
            $data['email'],
            $data['phone'] ?? '',
            $data['role'] ?? 'admin',
            $hashedPassword
        ]);

        $userId = $this->pdo->lastInsertId();

        // Create role-specific person (student or teacher) using factory
        $role = $data['role'] ?? 'admin';
        $person = PersonFactory::createPerson($role, array_merge($data, ['person_id' => $userId]));

        if ($person) {
            $person = PersonFactory::createPerson($role, array_merge($data, ['person_id' => $userId])); // this line means that if the role is not set we will set it to student
            $person->save($this->pdo); // Assuming Student or Teacher has a save() method
        }

        $user = new Person([
            'id' => $userId,
            'email' => $data['email'],
            'role' => $data['role'] ?? 'admin',
            'first_name' => $data['first_name'] ?? '',
            'last_name' => $data['last_name'] ?? ''
        ]);

        return [
            'user' => $user->toArray(),
            'token' => $this->generateToken($user)
        ];
    }

    public function login(string $email, string $password): array
    {

        $user = $this->getUserByEmail($email);

        if (!$user) {
            error_log("User not found: " . $email);
            throw new \Exception('Invalid credentials');
        }

        if (!$user->verifyPassword($password)) {
            error_log("Password verification failed for: " . $email);
            throw new \Exception('Invalid credentials');
        }

        return [
            'user' => $user->toArray(),
            'token' => $this->generateToken($user)
        ];
    }

    public function getUserByEmail(string $email): ?Person
    {
        $stmt = $this->pdo->prepare('SELECT * FROM person WHERE email = ?');
        $stmt->execute([$email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? new Person(data: $data) : null;
    }

    public function generateToken(Person $user): string
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode([
            'user_id' => $user->getId(),
            'email' => $user->getEmail(),
            'iat' => time(),
            'exp' => time() + (24 * 60 * 60) // 24 hours
        ]);

        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        $signature = hash_hmac('sha256', $base64Header . "." . $base64Payload, $this->secretKey, true);
        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return $base64Header . "." . $base64Payload . "." . $base64Signature;
    }

    public function validateToken(string $token): ?array
    {
        try {
            $parts = explode('.', $token);
            if (count($parts) !== 3) {
                return null;
            }

            [$header, $payload, $signature] = $parts;

            $validSignature = hash_hmac('sha256', $header . "." . $payload, $this->secretKey, true);
            $validBase64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($validSignature));

            if (!hash_equals($validBase64Signature, $signature)) {
                return null;
            }

            $decodedPayload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $payload)), true);

            if ($decodedPayload['exp'] < time()) {
                return null; // Token expired
            }

            return $decodedPayload;
        } catch (\Exception $e) {
            return null;
        }
    }
}


