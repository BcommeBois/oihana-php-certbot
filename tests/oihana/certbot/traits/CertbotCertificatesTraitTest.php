<?php

declare(strict_types=1);

namespace tests\oihana\certbot\traits ;

use DateInvalidTimeZoneException;

use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\TestCase;

use oihana\certbot\entities\CertbotCertificate;
use oihana\certbot\enums\CertbotSubCommand;
use oihana\certbot\options\CertbotCertificatesOptions;
use oihana\certbot\traits\CertbotCertificatesTrait;

#[CoversTrait( CertbotCertificatesTrait::class )]
final class CertbotCertificatesTraitTest extends TestCase
{
    /**
     * The output of `certbot certificates` for two certificates; the first expires on
     * 2025-10-12 at 10:15:56 UTC.
     */
    private const string OUTPUT = <<<OUTPUT
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
    OUTPUT;

    private RecordingCertbot $certbot ;

    protected function setUp() :void
    {
        $this->certbot = new RecordingCertbot() ;
        $this->certbot->outputs = [ [ 0 , self::OUTPUT ] ] ;
    }

    // ------- initializeCertbotCertificatesOptions

    public function testTheOptionsAreReadFlat() :void
    {
        $this->certbot->initializeCertbotCertificatesOptions( [ 'certName' => 'flat.com' ] ) ;

        $this->assertInstanceOf( CertbotCertificatesOptions::class , $this->certbot->certbotCertificatesOptions ) ;
        $this->assertSame( 'flat.com' , $this->certbot->certbotCertificatesOptions->certName ) ;
    }

    public function testTheOptionsAreReadUnderCertbot() :void
    {
        $this->certbot->initializeCertbotCertificatesOptions( [ 'certbot' => [ 'certName' => 'shared.com' ] ] ) ;

        $this->assertSame( 'shared.com' , $this->certbot->certbotCertificatesOptions->certName ) ;
    }

    public function testTheOptionsOfTheCommandWinOverTheSharedOnes() :void
    {
        $this->certbot->initializeCertbotCertificatesOptions
        ([
            'certbot' => [ 'certName' => 'shared.com' , 'certificates' => [ 'certName' => 'own.com' ] ] ,
        ]) ;

        $this->assertSame( 'own.com' , $this->certbot->certbotCertificatesOptions->certName ) ;
    }

    public function testTheInitializationChains() :void
    {
        $this->assertSame( $this->certbot , $this->certbot->initializeCertbotCertificatesOptions() ) ;
    }

    // ------- certbotCertificates

    public function testTheCertificatesAreReadFromTheOutput() :void
    {
        $certificates = $this->certbot->certbotCertificates() ;

        $this->assertCount( 2 , $certificates ) ;
        $this->assertContainsOnlyInstancesOf( CertbotCertificate::class , $certificates ) ;
        $this->assertSame( [ 'example.com' , 'test.com' ] , array_map( fn( $c ) => $c->name , $certificates ) ) ;
        $this->assertSame( [ [ 'proc' , CertbotSubCommand::CERTIFICATES , '' , true , false ] ] , $this->certbot->calls ) ;
    }

    public function testTheDefaultAndPassedOptionsReachTheCommand() :void
    {
        $this->certbot->initializeCertbotCertificatesOptions( [ 'config' => '/etc/certbot.ini' ] ) ;

        $this->certbot->certbotCertificates( [ 'certName' => 'example.com' ] , dryRun : true ) ;

        $this->assertSame
        (
            [ [ 'proc' , CertbotSubCommand::CERTIFICATES , '--cert-name "example.com" -c "/etc/certbot.ini"' , true , true ] ] ,
            $this->certbot->calls
        ) ;
    }

    /**
     * `--quiet` would empty the very output the list is read from: silent never sends it.
     */
    public function testSilentSendsNoQuiet() :void
    {
        $this->assertCount( 2 , $this->certbot->certbotCertificates( silent : true ) ) ;
        $this->assertSame( '' , $this->certbot->calls[ 0 ][ 2 ] ) ;
    }

    public function testAFailedCommandGivesNoCertificate() :void
    {
        $this->certbot->outputs = [ [ 1 , self::OUTPUT ] ] ;

        $this->assertSame( [] , $this->certbot->certbotCertificates() ) ;
    }

    public function testTheDatesAreWrittenInTheFormatGiven() :void
    {
        $this->assertSame( '2025-10-12 10:15' , $this->certbot->certbotCertificates( dateFormat : 'Y-m-d H:i' )[ 0 ]->expires ) ;
    }

    /**
     * The timezone reaches the reading of the dates: an unknown one is refused there.
     */
    public function testTheTimezoneReachesTheReadingOfTheDates() :void
    {
        $this->expectException( DateInvalidTimeZoneException::class ) ;

        $this->certbot->certbotCertificates( timezone : 'Nowhere/Never' ) ;
    }

    // ------- certbotCertificate

    public function testNoNeedRunsNoCommand() :void
    {
        $this->assertNull( $this->certbot->certbotCertificate() ) ;
        $this->assertSame( [] , $this->certbot->calls ) ;
    }

    public function testACertificateIsFoundByItsName() :void
    {
        $this->assertSame( 'test.com' , $this->certbot->certbotCertificate( 'test.com' )?->name ) ;
    }

    /**
     * A certificate named apart from its domains is still found by its name.
     */
    public function testACertificateIsFoundByANameThatIsNoneOfItsDomains() :void
    {
        $this->certbot->outputs = [ [ 0 , "Found the following certs:\n  Certificate Name: shop\n    Domains: example.com\n" ] ] ;

        $this->assertSame( 'shop' , $this->certbot->certbotCertificate( 'shop' )?->name ) ;
    }

    public function testACertificateIsFoundByOneOfItsDomains() :void
    {
        $this->assertSame( 'example.com' , $this->certbot->certbotCertificate( 'www.example.com' )?->name ) ;
    }

    public function testTheFirstNeedThatMatchesFindsTheCertificate() :void
    {
        $this->assertSame( 'test.com' , $this->certbot->certbotCertificate( [ 'unknown.org' , 'test.com' ] )?->name ) ;
    }

    public function testAnUnknownNeedFindsNothing() :void
    {
        $this->assertNull( $this->certbot->certbotCertificate( [ 'unknown.org' , 'example' ] ) ) ;
    }

    public function testNoCertificateFindsNothing() :void
    {
        $this->certbot->outputs = [ [ 1 , '' ] ] ;

        $this->assertNull( $this->certbot->certbotCertificate( 'example.com' ) ) ;
    }

    public function testTheLookupPassesItsArguments() :void
    {
        $this->certbot->certbotCertificate( 'example.com' , [ 'certName' => 'example.com' ] , dryRun : true ) ;

        $this->assertSame
        (
            [ [ 'proc' , CertbotSubCommand::CERTIFICATES , '--cert-name "example.com"' , true , true ] ] ,
            $this->certbot->calls
        ) ;
    }

    public function testTheLookupReadsTheDatesAsAsked() :void
    {
        $this->assertSame( '10:15' , $this->certbot->certbotCertificate( 'example.com' , dateFormat : 'H:i' )?->expires ) ;

        $this->certbot->outputs = [ [ 0 , self::OUTPUT ] ] ;

        $this->expectException( DateInvalidTimeZoneException::class ) ;

        $this->certbot->certbotCertificate( 'example.com' , timezone : 'Nowhere/Never' ) ;
    }

    // ------- certbotCertificateExists

    public function testAKnownCertificateExists() :void
    {
        $this->assertTrue( $this->certbot->certbotCertificateExists( 'www.example.com' ) ) ;
    }

    public function testAnUnknownCertificateDoesNotExist() :void
    {
        $this->assertFalse( $this->certbot->certbotCertificateExists( 'unknown.org' ) ) ;
        $this->assertFalse( $this->certbot->certbotCertificateExists() ) ;
    }

    public function testExistsPassesItsArguments() :void
    {
        $this->certbot->certbotCertificateExists( 'example.com' , [ 'certName' => 'example.com' ] , dryRun : true ) ;

        $this->assertSame( '--cert-name "example.com"' , $this->certbot->calls[ 0 ][ 2 ] ) ;
        $this->assertTrue( $this->certbot->calls[ 0 ][ 4 ] ) ;
    }

    // ------- certbotCertificateIsValid

    public function testACertificateIsValidBeforeItsExpiry() :void
    {
        $this->assertTrue( $this->certbot->certbotCertificateIsValid( 'example.com' , '2025-10-01' ) ) ;
    }

    public function testACertificateIsNotValidAfterItsExpiry() :void
    {
        $this->assertFalse( $this->certbot->certbotCertificateIsValid( 'example.com' , '2025-10-13' ) ) ;
    }

    public function testAnUnknownCertificateIsNotValid() :void
    {
        $this->assertFalse( $this->certbot->certbotCertificateIsValid( 'unknown.org' , '2025-10-01' ) ) ;
    }

    public function testACertificateGivenIsReadWithoutCommand() :void
    {
        $certificate = new CertbotCertificate( [ 'name' => 'given.com' , 'expires' => '2025-10-12T10:15:56' ] ) ;

        $this->assertTrue( $this->certbot->certbotCertificateIsValid( $certificate , '2025-10-01' ) ) ;
        $this->assertSame( [] , $this->certbot->calls ) ;
    }

    public function testACertificateWithoutExpiryIsNotValid() :void
    {
        $certificate = new CertbotCertificate( [ 'name' => 'given.com' , 'expires' => null ] ) ;

        $this->assertFalse( $this->certbot->certbotCertificateIsValid( $certificate , '2025-10-01' ) ) ;
    }

    public function testIsValidPassesItsArguments() :void
    {
        $this->certbot->certbotCertificateIsValid( 'example.com' , '2025-10-01' , [ 'certName' => 'example.com' ] , dryRun : true ) ;

        $this->assertSame( '--cert-name "example.com"' , $this->certbot->calls[ 0 ][ 2 ] ) ;
        $this->assertTrue( $this->certbot->calls[ 0 ][ 4 ] ) ;
    }
}
