<?php

namespace tests\oihana\certbot\helpers;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function oihana\certbot\helpers\certificateKeyType;

#[CoversFunction('oihana\certbot\helpers\certificateKeyType')]
final class CertificateKeyTypeTest extends TestCase
{
    private const array ECDSA = [ 'private_key_type' => OPENSSL_KEYTYPE_EC  , 'curve_name' => 'prime256v1' ] ;
    private const array RSA   = [ 'private_key_type' => OPENSSL_KEYTYPE_RSA , 'private_key_bits' => 2048 ] ;

    /**
     * A self-signed certificate for a key made with these options of openssl_pkey_new().
     *
     * @param array<string, mixed> $key
     * @param array<string, mixed> $options The options of the signature.
     */
    private static function certificate( array $key , array $options = [] ) :string
    {
        $private = openssl_pkey_new( $key ) ;
        $request = openssl_csr_new( [ 'commonName' => 'example.com' ] , $private , $options ) ;
        $signed  = openssl_csr_sign( $request , null , $private , 1 , $options ) ;

        openssl_x509_export( $signed , $pem ) ;

        return $pem ;
    }

    public function testAnEcdsaCertificate() :void
    {
        $this->assertSame( 'ECDSA' , certificateKeyType( self::certificate( self::ECDSA ) ) ) ;
    }

    public function testAnRsaCertificate() :void
    {
        $this->assertSame( 'RSA' , certificateKeyType( self::certificate( self::RSA ) ) ) ;
    }

    /**
     * A full chain, as certbot writes `fullchain.pem`: the site's certificate comes first.
     */
    public function testTheFirstCertificateOfAChainIsTheOneRead() :void
    {
        $this->assertSame( 'ECDSA' , certificateKeyType( self::certificate( self::ECDSA ) . self::certificate( self::RSA ) ) ) ;
        $this->assertSame( 'RSA'   , certificateKeyType( self::certificate( self::RSA ) . self::certificate( self::ECDSA ) ) ) ;
    }

    public function testAChainReadFromAFile() :void
    {
        $file = tempnam( sys_get_temp_dir() , 'oihana-fullchain-' ) ;

        try
        {
            file_put_contents( $file , self::certificate( self::RSA ) . self::certificate( self::ECDSA ) ) ;

            $this->assertSame( 'RSA' , certificateKeyType( (string) file_get_contents( $file ) ) ) ;
        }
        finally
        {
            unlink( $file ) ;
        }
    }

    /**
     * A certificate issued for another type of key is neither of the two certbot writes.
     */
    public function testACertificateOfAnotherTypeOfKey() :void
    {
        $pem = self::certificate( [ 'private_key_type' => OPENSSL_KEYTYPE_ED25519 ] , [ 'digest_alg' => 'null' ] ) ;

        $this->assertStringContainsString( '-----BEGIN CERTIFICATE-----' , $pem , 'The fixture is a certificate.' ) ;
        $this->assertNull( certificateKeyType( $pem ) ) ;
    }

    public function testATextThatIsNoCertificate() :void
    {
        $this->assertNull( certificateKeyType( '' ) ) ;
        $this->assertNull( certificateKeyType( 'not a certificate' ) ) ;
        $this->assertNull( certificateKeyType( "-----BEGIN CERTIFICATE-----\nnope\n-----END CERTIFICATE-----\n" ) ) ;
    }

    /**
     * Read from the public half only: a private key is not a certificate.
     */
    public function testAPrivateKeyIsNoCertificate() :void
    {
        openssl_pkey_export( openssl_pkey_new( self::RSA ) , $private ) ;

        $this->assertNull( certificateKeyType( $private ) ) ;
    }
}
