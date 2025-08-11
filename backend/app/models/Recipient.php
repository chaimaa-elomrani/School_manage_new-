<?php

// namespace App\Models;

// // A simplified Recipient model to hold communication details
// class Recipient
// {
//     private int $id;
//     private string $firstName;
//     private string $lastName;
//     private ?string $email;
//     private ?string $phoneNumber; // For SMS

//     public function __construct(array $data)
//     {
//         $this->id = $data['id'];
//         $this->firstName = $data['first_name'];
//         $this->lastName = $data['last_name'];
//         $this->email = $data['email'] ?? null;
//         $this->phoneNumber = $data['phone_number'] ?? null;
//     }

//     public function getId(): int { return $this->id; }
//     public function getFirstName(): string { return $this->firstName; }
//     public function getLastName(): string { return $this->lastName; }
//     public function getEmail(): ?string { return $this->email; }
//     public function getPhoneNumber(): ?string { return $this->phoneNumber; }

//     public function toArray(): array
//     {
//         return [
//             'id' => $this->id,
//             'first_name' => $this->firstName,
//             'last_name' => $this->lastName,
//             'email' => $this->email,
//             'phone_number' => $this->phoneNumber,
//         ];
//     }
// }
