<?php
class UserToken {
    private int $id;
    private int $user_id;
    private string $token;
    private string $expires_at;
    private string $created_at;
    public function __construct(int $user_id, string $token, string $expires_at) {
        $this->user_id = $user_id;
        $this->token = $token;
        $this->expires_at = $expires_at;
    }

    public function getId(): int {
        return $this->id;
    }
    public function getUserId(): int {
        return $this->user_id;
    }
    public function getToken(): string {
        return $this->token;
    }
    public function getExpiresAt(): string {
        return $this->expires_at;
    }
    public function getCreatedAt(): string {
        return $this->created_at;
    }
    public function setUserId(int $user_id): void {
        $this->user_id = $user_id;
    }
    public function setToken(string $token): void {
        $this->token = $token;
    }
    public function setExpiresAt(string $expires_at): void {
        $this->expires_at = $expires_at;
    }
    public function setCreatedAt(string $created_at): void {
        $this->created_at = $created_at;
    }
}