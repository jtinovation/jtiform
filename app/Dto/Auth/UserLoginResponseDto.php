<?php

namespace App\Dto\Auth;

class UserLoginResponseDto
{
  public function __construct(
    public string $token,
    public UserLoginInfoDto $user,
  ) {}
}

class UserLoginInfoDto
{
  public function __construct(
    public string $id,
    public string $name,
    public string $email,
    public ?array $roles = null,
    public ?array $permissions = null,
  ) {}
}
