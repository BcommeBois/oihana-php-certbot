<?php

namespace tests\oihana\certbot\entities;

use DateInvalidTimeZoneException;
use DateMalformedStringException;
use DateTimeImmutable;
use DateTimeZone;

use oihana\certbot\entities\CertbotCertificate;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CertbotCertificateTest extends TestCase
{
    // ------- isExpired

    #[Test]
    public function isExpiredReturnsTrueWhenExpiresIsWhitespace(): void
    {
        $cert = new CertbotCertificate([ 'expires' => '   ' ]);
        $this->assertTrue( $cert->isExpired() );
    }

    #[Test]
    public function isExpiredReturnsTrueWhenExpiresIsEmpty(): void
    {
        $cert = new CertbotCertificate([ 'expires' => '' ]);
        $this->assertTrue( $cert->isExpired() );
    }

    #[Test]
    public function isExpiredReturnsTrueWhenExpiresIsInThePast(): void
    {
        $cert = new CertbotCertificate([ 'expires' => '2020-01-01T00:00:00' ]);
        $this->assertTrue( $cert->isExpired() );
    }

    #[Test]
    public function isExpiredReturnsFalseWhenExpiresIsInTheFuture(): void
    {
        $cert = new CertbotCertificate([ 'expires' => '2099-12-31T23:59:59' ]);
        $this->assertFalse( $cert->isExpired() );
    }

    #[Test]
    public function isExpiredWithCustomReferenceDateAsString(): void
    {
        $cert = new CertbotCertificate([ 'expires' => '2025-06-15T00:00:00' ]);
        $this->assertFalse( $cert->isExpired( '2025-01-01T00:00:00' ) );
        $this->assertTrue( $cert->isExpired( '2025-12-31T00:00:00' ) );
    }

    #[Test]
    public function isExpiredWithCustomReferenceDateAsDateTimeInterface(): void
    {
        $cert = new CertbotCertificate([ 'expires' => '2025-06-15T00:00:00' ]);
        $before = new DateTimeImmutable( '2025-01-01T00:00:00' , new DateTimeZone('Europe/Paris') );
        $after  = new DateTimeImmutable( '2025-12-31T00:00:00' , new DateTimeZone('Europe/Paris') );
        $this->assertFalse( $cert->isExpired( $before ) );
        $this->assertTrue( $cert->isExpired( $after ) );
    }

    #[Test]
    public function isExpiredWithDifferentTimezone(): void
    {
        $cert = new CertbotCertificate([ 'expires' => '2099-12-31T23:59:59' ]);
        $this->assertFalse( $cert->isExpired( null, 'UTC' ) );
        $this->assertFalse( $cert->isExpired( null, 'America/New_York' ) );
    }

    // ------- isValid

    #[Test]
    public function isValidReturnsFalseWhenExpiresIsWhitespace(): void
    {
        $cert = new CertbotCertificate([ 'expires' => '   ' ]);
        $this->assertFalse( $cert->isValid() );
    }

    #[Test]
    public function isValidReturnsFalseWhenExpiresIsEmpty(): void
    {
        $cert = new CertbotCertificate([ 'expires' => '' ]);
        $this->assertFalse( $cert->isValid() );
    }

    #[Test]
    public function isValidReturnsFalseWhenExpiresIsInThePast(): void
    {
        $cert = new CertbotCertificate([ 'expires' => '2020-01-01T00:00:00' ]);
        $this->assertFalse( $cert->isValid() );
    }

    #[Test]
    public function isValidReturnsTrueWhenExpiresIsInTheFuture(): void
    {
        $cert = new CertbotCertificate([ 'expires' => '2099-12-31T23:59:59' ]);
        $this->assertTrue( $cert->isValid() );
    }

    #[Test]
    public function isValidWithCustomReferenceDateAsString(): void
    {
        $cert = new CertbotCertificate([ 'expires' => '2025-06-15T00:00:00' ]);
        $this->assertTrue( $cert->isValid( '2025-01-01T00:00:00' ) );
        $this->assertFalse( $cert->isValid( '2025-12-31T00:00:00' ) );
    }

    #[Test]
    public function isValidWithCustomReferenceDateAsDateTimeInterface(): void
    {
        $cert = new CertbotCertificate([ 'expires' => '2025-06-15T00:00:00' ]);
        $before = new DateTimeImmutable( '2025-01-01T00:00:00' , new DateTimeZone('Europe/Paris') );
        $after  = new DateTimeImmutable( '2025-12-31T00:00:00' , new DateTimeZone('Europe/Paris') );
        $this->assertTrue( $cert->isValid( $before ) );
        $this->assertFalse( $cert->isValid( $after ) );
    }

    #[Test]
    public function isValidThrowsOnInvalidTimezone(): void
    {
        $cert = new CertbotCertificate([ 'expires' => '2099-12-31T23:59:59' ]);
        $this->expectException( DateInvalidTimeZoneException::class );
        $cert->isValid( null , 'Invalid/Timezone' );
    }

    #[Test]
    public function isValidThrowsOnMalformedReferenceDate(): void
    {
        $cert = new CertbotCertificate([ 'expires' => '2099-12-31T23:59:59' ]);
        $this->expectException( DateMalformedStringException::class );
        $cert->isValid( 'not-a-date' );
    }

    #[Test]
    public function isValidThrowsOnMalformedExpiresDate(): void
    {
        $cert = new CertbotCertificate([ 'expires' => 'not-a-date' ]);
        $this->expectException( DateMalformedStringException::class );
        $cert->isValid( '2025-01-01T00:00:00' );
    }

    // ------- Properties & Constants

    #[Test]
    public function propertiesAreSetViaConstructor(): void
    {
        $cert = new CertbotCertificate([
            'name'               => 'example.com' ,
            'identifier'         => 'abc123' ,
            'keyType'            => 'ECDSA' ,
            'domains'            => [ 'example.com' , 'www.example.com' ] ,
            'expires'            => '2025-12-31T23:59:59' ,
            'certificateKeyPath' => '/etc/letsencrypt/live/example.com/fullchain.pem' ,
            'privateKeyPath'     => '/etc/letsencrypt/live/example.com/privkey.pem' ,
            'validDays'          => 90 ,
        ]);

        $this->assertSame( 'example.com' , $cert->name );
        $this->assertSame( 'abc123' , $cert->identifier );
        $this->assertSame( 'ECDSA' , $cert->keyType );
        $this->assertSame( [ 'example.com' , 'www.example.com' ] , $cert->domains );
        $this->assertSame( '2025-12-31T23:59:59' , $cert->expires );
        $this->assertSame( '/etc/letsencrypt/live/example.com/fullchain.pem' , $cert->certificateKeyPath );
        $this->assertSame( '/etc/letsencrypt/live/example.com/privkey.pem' , $cert->privateKeyPath );
        $this->assertSame( 90 , $cert->validDays );
    }

    #[Test]
    public function constantsMatchPropertyNames(): void
    {
        $this->assertSame( 'certificateKeyPath' , CertbotCertificate::CERTIFICATE_KEY_PATH );
        $this->assertSame( 'domains' , CertbotCertificate::DOMAINS );
        $this->assertSame( 'expires' , CertbotCertificate::EXPIRES );
        $this->assertSame( 'identifier' , CertbotCertificate::IDENTIFIER );
        $this->assertSame( 'keyType' , CertbotCertificate::KEY_TYPE );
        $this->assertSame( 'name' , CertbotCertificate::NAME );
        $this->assertSame( 'privateKeyPath' , CertbotCertificate::PRIVATE_KEY_PATH );
        $this->assertSame( 'validDays' , CertbotCertificate::VALID_DAYS );
    }
}
