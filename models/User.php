<?php
class User {
    private $id;
    private $fullname;
    private $username;
    private $password;
    private $email;
    private $phone;
    private $address;
    private $role;
    private $status;
    private $createdAt;
    private $updatedAt;

    public function __construct($id = null, $fullname = null, $username = null, $password = null, $email = null, $phone = null, $address = null, $role = 0, $status = 1, $createdAt = null, $updatedAt = null) {
        $this->id = $id;
        $this->fullname = $fullname;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->phone = $phone;
        $this->address = $address;
        $this->role = $role;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId() { return $this->id; }
    public function getFullname() { return $this->fullname; }
    public function getUsername() { return $this->username; }
    public function getPassword() { return $this->password; }
    public function getEmail() { return $this->email; }
    public function getPhone() { return $this->phone; }
    public function getAddress() { return $this->address; }
    public function getRole() { return $this->role; }
    public function getStatus() { return $this->status; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }

    public function setId($id) { $this->id = $id; }
    public function setFullname($fullname) { $this->fullname = $fullname; }
    public function setUsername($username) { $this->username = $username; }
    public function setPassword($password) { $this->password = $password; }
    public function setEmail($email) { $this->email = $email; }
    public function setPhone($phone) { $this->phone = $phone; }
    public function setAddress($address) { $this->address = $address; }
    public function setRole($role) { $this->role = $role; }
    public function setStatus($status) { $this->status = $status; }
    public function setCreatedAt($createdAt) { $this->createdAt = $createdAt; }
    public function setUpdatedAt($updatedAt) { $this->updatedAt = $updatedAt; }
}