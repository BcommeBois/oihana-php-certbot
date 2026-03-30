<?php

namespace tests\oihana\certbot\enums;

use oihana\certbot\enums\CertbotParam;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CertbotParamTest extends TestCase
{
    // --- Constants ---

    #[Test]
    public function certbotConstant(): void
    {
        $this->assertSame( 'certbot' , CertbotParam::CERTBOT );
    }

    #[Test]
    public function certificatesConstant(): void
    {
        $this->assertSame( 'certificates' , CertbotParam::CERTIFICATES );
    }

    #[Test]
    public function certOnlyConstant(): void
    {
        $this->assertSame( 'certOnly' , CertbotParam::CERT_ONLY );
    }

    #[Test]
    public function deleteConstant(): void
    {
        $this->assertSame( 'delete' , CertbotParam::DELETE );
    }

    #[Test]
    public function renewConstant(): void
    {
        $this->assertSame( 'renew' , CertbotParam::RENEW );
    }

    #[Test]
    public function revokeConstant(): void
    {
        $this->assertSame( 'revoke' , CertbotParam::REVOKE );
    }

    // --- ConstantsTrait methods ---

    #[Test]
    public function getAll(): void
    {
        $all = CertbotParam::getAll();
        $this->assertIsArray( $all );
        $this->assertArrayHasKey( 'CERTBOT' , $all );
        $this->assertArrayHasKey( 'CERTIFICATES' , $all );
        $this->assertArrayHasKey( 'CERT_ONLY' , $all );
        $this->assertArrayHasKey( 'DELETE' , $all );
        $this->assertArrayHasKey( 'RENEW' , $all );
        $this->assertArrayHasKey( 'REVOKE' , $all );
        $this->assertCount( 6 , $all );
    }

    #[Test]
    public function enums(): void
    {
        $enums = CertbotParam::enums();
        $this->assertIsArray( $enums );
        $this->assertContains( 'certbot' , $enums );
        $this->assertContains( 'certificates' , $enums );
        $this->assertContains( 'certOnly' , $enums );
        $this->assertContains( 'delete' , $enums );
        $this->assertContains( 'renew' , $enums );
        $this->assertContains( 'revoke' , $enums );
    }

    #[Test]
    public function getConstantKeys(): void
    {
        $keys = CertbotParam::getConstantKeys();
        $this->assertContains( 'CERTBOT' , $keys );
        $this->assertContains( 'CERTIFICATES' , $keys );
        $this->assertContains( 'CERT_ONLY' , $keys );
        $this->assertContains( 'DELETE' , $keys );
        $this->assertContains( 'RENEW' , $keys );
        $this->assertContains( 'REVOKE' , $keys );
    }

    #[Test]
    public function getConstantValues(): void
    {
        $values = CertbotParam::getConstantValues();
        $this->assertContains( 'certbot' , $values );
        $this->assertContains( 'certificates' , $values );
        $this->assertContains( 'certOnly' , $values );
        $this->assertContains( 'delete' , $values );
        $this->assertContains( 'renew' , $values );
        $this->assertContains( 'revoke' , $values );
    }

    #[Test]
    public function includesWithValidValue(): void
    {
        $this->assertTrue( CertbotParam::includes( 'certbot' ) );
        $this->assertTrue( CertbotParam::includes( 'certificates' ) );
        $this->assertTrue( CertbotParam::includes( 'delete' ) );
    }

    #[Test]
    public function includesWithInvalidValue(): void
    {
        $this->assertFalse( CertbotParam::includes( 'unknown' ) );
        $this->assertFalse( CertbotParam::includes( '' ) );
    }

    #[Test]
    public function getConstantReturnsKeyFromValue(): void
    {
        $this->assertSame( 'CERTBOT' , CertbotParam::getConstant( 'certbot' ) );
        $this->assertSame( 'DELETE' , CertbotParam::getConstant( 'delete' ) );
        $this->assertSame( 'RENEW' , CertbotParam::getConstant( 'renew' ) );
    }

    #[Test]
    public function getConstantReturnsNullForUnknownValue(): void
    {
        $this->assertNull( CertbotParam::getConstant( 'unknown' ) );
    }

    #[Test]
    public function getReturnsValueWhenValid(): void
    {
        $this->assertSame( 'certbot' , CertbotParam::get( 'certbot' ) );
        $this->assertSame( 'renew' , CertbotParam::get( 'renew' ) );
    }

    #[Test]
    public function getReturnsDefaultWhenInvalid(): void
    {
        $this->assertNull( CertbotParam::get( 'unknown' ) );
        $this->assertSame( 'fallback' , CertbotParam::get( 'unknown' , 'fallback' ) );
    }

    #[Test]
    public function validateWithValidValue(): void
    {
        $this->expectNotToPerformAssertions();
        CertbotParam::validate( 'certbot' );
        CertbotParam::validate( 'delete' );
    }

    #[Test]
    public function validateWithInvalidValueThrows(): void
    {
        $this->expectException( \Exception::class );
        CertbotParam::validate( 'unknown' );
    }
}
