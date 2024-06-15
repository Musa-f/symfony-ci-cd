<?php

namespace App\Tests;

use App\Service\SecurityService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class SecurityServiceTest extends TestCase
{
    private $securityService;

    protected function setUp(): void
    {
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->securityService = new SecurityService($entityManagerMock);
    }

    public function testValidatePasswordStrength()
    {
        $this->securityService->validatePasswordStrength('1StrongPassword#');
        $this->assertTrue(true);
    }
}
