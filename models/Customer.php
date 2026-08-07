<?php

class Customer {
    public int $id;
    public string $fullName;
    public string $phone;
    public ?string $email;
    public ?string $address;
    public ?string $note;
    public int $status;
    public string $createdAt;
    public string $updatedAt;

    public function __construct() {
        $this->status = 1;
    }
}