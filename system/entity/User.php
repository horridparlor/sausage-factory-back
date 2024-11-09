<?php

namespace system;

const ACCESS_RIGHT_IS_SUPER_ADMIN = 'isSuperAdmin';
const IS_MISSING_ANY_ACCESS_RIGHT = 'None of the accepted access rights %s';
const IS_MISSING_ACCESS_RIGHT = 'Access right {%s} missing';
const ALTERING_ADMIN_RIGHT = 'You cannot %s admin access right {%s}';
const ALTERING_REMOVE = 'remove';
const ALTERING_ADD = 'add';
const NO_ERROR = 'No error, { User->getError() called. }';

const ADMIN_RIGHTS = array(
    ACCESS_RIGHT_IS_SUPER_ADMIN,
);

class User
{
    private int $id;
    private string $username;
    private string $firstname;
    private string $lastname;
    private \stdClass $accessRights;
    private string $accessRightErrorString;
    private string $adminAccessRight;
    private int $subprocessId;

    public function __construct(int $id, string $username, string $firstname, string $lastname, \stdClass $accessRights, int $subprocessId)
    {
        $this->id = $id;
        $this->username = $username;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->accessRights = $accessRights;
        $this->accessRightErrorString = NO_ERROR;
        $this->subprocessId = $subprocessId;
    }

    public static function newDummyUser(\stdClass $accessRights): User {
        return new User(0, '', '', '', $accessRights, 1);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }
    public function getFirstname(): string
    {
        return $this->firstname;
    }
    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getAccessRights(): \stdClass
    {
        return $this->accessRights;
    }

    public function getSubprocessId(): int
    {
        return $this->subprocessId;
    }

    public function isSuperAdmin(): bool
    {
        return $this->checkAccess(ACCESS_RIGHT_IS_SUPER_ADMIN);
    }
    private function hasAllRights(): bool
    {
        return $this->hasAccessRight(ACCESS_RIGHT_IS_SUPER_ADMIN);
    }

    private function checkAccess(string $key): bool
    {
        $hasAccess = $this->hasAllRights() || $this->hasAccessRight($key);
        if (!$hasAccess) {
            $this->accessRightErrorString = sprintf(IS_MISSING_ACCESS_RIGHT, $key);
        }
        return $hasAccess;
    }

    private function checkAccessAny(array $keys): bool
    {
        if ($this->hasAllRights()) {
            return true;
        }
        foreach ($keys as $key) {
            if ($this->hasAccessRight($key)) {
                return true;
            }
        }
        $keysString = '{' . implode('}/{', $keys) . '}';
        $this->accessRightErrorString = sprintf(IS_MISSING_ANY_ACCESS_RIGHT, $keysString);
        return false;
    }

    private function hasAccessRight(string $key): bool
    {
        if (!property_exists($this->accessRights, $key)) {
            return false;
        }
        return $this->accessRights->$key;
    }

    public function hasAdminRights(): bool
    {
        foreach (ADMIN_RIGHTS as $adminRight) {
            if ($this->hasAccessRight($adminRight)) {
                $this->adminAccessRight = $adminRight;
                return true;
            }
        }
        return false;
    }

    public function getError(): array
    {
        return array(
            'error' => $this->accessRightErrorString
        );
    }

    public function getAdminAccessRight(): string
    {
        return $this->adminAccessRight;
    }

    private function extractAdminRights(\stdClass $accessRights): array {
        $adminRights = array();
        foreach ($accessRights as $key => $value) {
           if (in_array($key, ADMIN_RIGHTS) && !!$value) {
               $adminRights[] = $key;
           }
        }
        sort($adminRights);
        return $adminRights;
    }

    public function wouldChangeAdminRights(\stdClass $accessRights): bool
    {
        $currentAdminRights = $this->extractAdminRights($this->accessRights);
        $newAdminRights = $this->extractAdminRights($accessRights);
        $removedRights = array_diff($currentAdminRights, $newAdminRights);
        $addedRights = array_diff($newAdminRights, $currentAdminRights);
        if ($removedRights) {
            $this->accessRightErrorString = sprintf(ALTERING_ADMIN_RIGHT, ALTERING_REMOVE, reset($removedRights));
            return true;
        }
        if ($addedRights) {
            echo json_encode($addedRights);
            $this->accessRightErrorString = sprintf(ALTERING_ADMIN_RIGHT, ALTERING_ADD, reset($addedRights));
            return true;
        }
        return false;
    }
}