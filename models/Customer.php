<?php
class Customer {
    private $id;
    private $fullname;
    private $phone;
    private $email;
    private $address;
    private $note;
    private $status;
    private $createdAt;
    private $updatedAt;

    public function __construct($id = null, $fullname = null, $phone = null, $email = null, $address = null, $note = null, $status = 1, $createdAt = null, $updatedAt = null) {
        $this->id = $id;
        $this->fullname = $fullname;
        $this->phone = $phone;
        $this->email = $email;
        $this->address = $address;
        $this->note = $note;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId() { return $this->id; }
    public function getFullname() { return $this->fullname; }
    public function getPhone() { return $this->phone; }
    public function getEmail() { return $this->email; }
    public function getAddress() { return $this->address; }
    public function getNote() { return $this->note; }
    public function getStatus() { return $this->status; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }

    public function setId($id) { $this->id = $id; }
    public function setFullname($fullname) { $this->fullname = $fullname; }
    public function setPhone($phone) { $this->phone = $phone; }
    public function setEmail($email) { $this->email = $email; }
    public function setAddress($address) { $this->address = $address; }
    public function setNote($note) { $this->note = $note; }
    public function setStatus($status) { $this->status = $status; }
    public function setCreatedAt($createdAt) { $this->createdAt = $createdAt; }
    public function setUpdatedAt($updatedAt) { $this->updatedAt = $updatedAt; }
}