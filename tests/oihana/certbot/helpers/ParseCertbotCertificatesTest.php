<?php

namespace tests\oihana\certbot\helpers;

use oihana\certbot\entities\CertbotCertificate;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function oihana\certbot\helpers\parseCertbotCertificates;

class ParseCertbotCertificatesTest extends TestCase
{
    private const string SINGLE_CERT = <<<CERT
    Found the following certs:
      Certificate Name: example.com
        Serial Number: 1234567890abcdef
        Key Type: ECDSA
        Domains: example.com www.example.com
        Expiry Date: 2025-10-12 10:15:56+00:00 (VALID: 66 days)
        Certificate Path: /etc/letsencrypt/live/example.com/fullchain.pem
        Private Key Path: /etc/letsencrypt/live/example.com/privkey.pem
    CERT;

    private const string MULTIPLE_CERTS = <<<CERTS
    Found the following certs:
      Certificate Name: example.com
        Serial Number: 1234567890abcdef
        Key Type: ECDSA
        Domains: example.com www.example.com
        Expiry Date: 2025-10-12 10:15:56+00:00 (VALID: 66 days)
        Certificate Path: /etc/letsencrypt/live/example.com/fullchain.pem
        Private Key Path: /etc/letsencrypt/live/example.com/privkey.pem
      Certificate Name: test.com
        Serial Number: abcdef1234567890
        Key Type: RSA
        Domains: test.com
        Expiry Date: 2025-11-04 17:38:48+00:00 (VALID: 89 days)
        Certificate Path: /etc/letsencrypt/live/test.com/fullchain.pem
        Private Key Path: /etc/letsencrypt/live/test.com/privkey.pem
    CERTS;

    // ------- Empty / null input

    #[Test]
    public function returnsEmptyArrayWhenSourceIsNull(): void
    {
        $this->assertSame( [] , parseCertbotCertificates( null ) );
    }

    #[Test]
    public function returnsEmptyArrayWhenSourceIsEmpty(): void
    {
        $this->assertSame( [] , parseCertbotCertificates( '' ) );
    }

    // ------- Single certificate

    #[Test]
    public function parsesSingleCertificate(): void
    {
        $certs = parseCertbotCertificates( self::SINGLE_CERT );

        $this->assertCount( 1 , $certs );
        $this->assertInstanceOf( CertbotCertificate::class , $certs[0] );
    }

    #[Test]
    public function parsesCertificateName(): void
    {
        $certs = parseCertbotCertificates( self::SINGLE_CERT );
        $this->assertSame( 'example.com' , $certs[0]->name );
    }

    #[Test]
    public function parsesSerialNumber(): void
    {
        $certs = parseCertbotCertificates( self::SINGLE_CERT );
        $this->assertSame( '1234567890abcdef' , $certs[0]->identifier );
    }

    #[Test]
    public function parsesKeyType(): void
    {
        $certs = parseCertbotCertificates( self::SINGLE_CERT );
        $this->assertSame( 'ECDSA' , $certs[0]->keyType );
    }

    #[Test]
    public function parsesDomains(): void
    {
        $certs = parseCertbotCertificates( self::SINGLE_CERT );
        $this->assertSame( [ 'example.com' , 'www.example.com' ] , $certs[0]->domains );
    }

    #[Test]
    public function parsesValidDays(): void
    {
        $certs = parseCertbotCertificates( self::SINGLE_CERT );
        $this->assertSame( 66 , $certs[0]->validDays );
    }

    #[Test]
    public function parsesCertificatePath(): void
    {
        $certs = parseCertbotCertificates( self::SINGLE_CERT );
        $this->assertSame( '/etc/letsencrypt/live/example.com/fullchain.pem' , $certs[0]->certificateKeyPath );
    }

    #[Test]
    public function parsesPrivateKeyPath(): void
    {
        $certs = parseCertbotCertificates( self::SINGLE_CERT );
        $this->assertSame( '/etc/letsencrypt/live/example.com/privkey.pem' , $certs[0]->privateKeyPath );
    }

    #[Test]
    public function parsesExpiryDate(): void
    {
        $certs = parseCertbotCertificates( self::SINGLE_CERT );
        $this->assertNotNull( $certs[0]->expires );
        $this->assertNotEmpty( $certs[0]->expires );
    }

    // ------- Multiple certificates

    #[Test]
    public function parsesMultipleCertificates(): void
    {
        $certs = parseCertbotCertificates( self::MULTIPLE_CERTS );

        $this->assertCount( 2 , $certs );

        $this->assertSame( 'example.com' , $certs[0]->name );
        $this->assertSame( 'ECDSA' , $certs[0]->keyType );

        $this->assertSame( 'test.com' , $certs[1]->name );
        $this->assertSame( 'RSA' , $certs[1]->keyType );
    }

    #[Test]
    public function parsesMultipleCertificatesDomains(): void
    {
        $certs = parseCertbotCertificates( self::MULTIPLE_CERTS );

        $this->assertSame( [ 'example.com' , 'www.example.com' ] , $certs[0]->domains );
        $this->assertSame( [ 'test.com' ] , $certs[1]->domains );
    }

    #[Test]
    public function parsesMultipleCertificatesValidDays(): void
    {
        $certs = parseCertbotCertificates( self::MULTIPLE_CERTS );

        $this->assertSame( 66 , $certs[0]->validDays );
        $this->assertSame( 89 , $certs[1]->validDays );
    }

    // ------- Single domain

    #[Test]
    public function parsesSingleDomain(): void
    {
        $source = <<<CERT
        Certificate Name: single.com
          Serial Number: aaa111
          Key Type: RSA
          Domains: single.com
          Expiry Date: 2025-10-12 10:15:56+00:00 (VALID: 30 days)
          Certificate Path: /etc/letsencrypt/live/single.com/fullchain.pem
          Private Key Path: /etc/letsencrypt/live/single.com/privkey.pem
        CERT;

        $certs = parseCertbotCertificates( $source );

        $this->assertCount( 1 , $certs );
        $this->assertSame( [ 'single.com' ] , $certs[0]->domains );
    }
}
