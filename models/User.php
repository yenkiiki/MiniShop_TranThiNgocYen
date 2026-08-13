<?php

class User {
    public int $id;
    public string $fullName;
    public string $userName;
    public string $password;
    public string $email;
    public ?string $phone;
    public ?string $address;
    public int $role;
    public int $status;
    public string $createdAt;
    public string $updatedAt;
public ?string $rememberToken;
    public function __construct() {
        $this->role = 0;
        $this->status = 1;
    }
}