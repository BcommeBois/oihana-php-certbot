<?php

namespace oihana\certbot\entities;

use DateInvalidTimeZoneException;
use DateMalformedStringException;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Throwable;

use oihana\reflect\traits\ConstantsTrait;

use org\schema\creativeWork\DigitalDocument;

/**
 * The certbot SSL/TLS certificates definition.
 */
class CertbotCertificate extends DigitalDocument
{
    use ConstantsTrait ;

    /**
     * The certificate key path.
     * @var string|null
     */
    public ?string $certificateKeyPath ;

    /**
     * The certbot certificates domains.
     * @var string|array|null
     */
    public null|string|array $domains ;

    /**
     * Certbot supports two certificate private key algorithms: rsa and ecdsa.
     * @var string|null
     */
    public ?string $keyType ;

    /**
     * The private key path.
     * @var string|null
     */
    public ?string $privateKeyPath ;

    /**
     * The number of valid days left.
     * @var int|null
     */
    public null|int $validDays ;

    // ------- Methods

    /**
     * Returns true if this certificate has expired at the given reference date (or now).
     *
     * @param string|DateTimeInterface|null $at The date to test expiration against (defaults to now).
     * @param string|null                   $timezone      The timezone used to interpret string dates (default: Europe/Paris).
     *
     * @return bool True if the certificate is expired, false otherwise.
     *
     * @throws DateMalformedStringException
     * @throws DateInvalidTimeZoneException
     */
    public function isExpired( null|string|DateTimeInterface $at = null, ?string $timezone = 'Europe/Paris' ): bool
    {
        if ( !is_string($this->expires) || trim($this->expires) === '' )
        {
            return true;
        }

        $tz = new DateTimeZone($timezone ?? 'Europe/Paris' );

        $ref = ($at instanceof DateTimeInterface)
             ? DateTimeImmutable::createFromInterface( $at )
             : new DateTimeImmutable($at ?: 'now', $tz ) ;

        $expiry = new DateTimeImmutable( $this->expires , $tz ) ;

        return $expiry <= $ref ;
    }

    /**
     * Returns true if this certificate is still valid at the given reference date (or now).
     *
     * @param string|DateTimeInterface|null $at       The date to test validity against (defaults to now).
     * @param string|null                   $timezone The timezone used to interpret string dates (default: Europe/Paris).
     *
     * @return bool True if the certificate is still valid at the given date, false otherwise.
     *
     * @throws DateMalformedStringException
     * @throws DateInvalidTimeZoneException
     */
    public function isValid( null|string|DateTimeInterface $at = null , string|null $timezone = 'Europe/Paris'):bool
    {
        if ( !is_string( $this->expires ) || trim( $this->expires ) === '' )
        {
            return false;
        }

        try
        {
            $tz = new DateTimeZone($timezone ?? 'Europe/Paris');
        }
        catch ( Throwable $e )
        {
            throw new DateInvalidTimeZoneException("Invalid timezone: '{$timezone}'" , 0 , $e ) ;
        }

        if ( $at instanceof DateTimeInterface )
        {
            $ref = DateTimeImmutable::createFromInterface($at);
        }
        else
        {
            try
            {
                $ref = new DateTimeImmutable($at ?? 'now' , $tz ) ;
            }
            catch ( Throwable $e )
            {
                throw new DateMalformedStringException("Malformed reference date string: '{$at}'" , 0 , $e ) ;
            }
        }

        try
        {
            $expiry = new DateTimeImmutable( $this->expires , $tz ) ;
        }
        catch ( Throwable $e )
        {
            throw new DateMalformedStringException("Malformed expiration date: '{$this->expires}'" , 0 , $e ) ;
        }

        return $expiry > $ref ;
    }

    // ------ Property names

    public const string CERTIFICATE_KEY_PATH = 'certificateKeyPath' ;
    public const string DOMAINS              = 'domains' ;
    public const string EXPIRES              = 'expires' ;
    public const string IDENTIFIER           = 'identifier' ;
    public const string KEY_TYPE             = 'keyType' ;
    public const string NAME                 = 'name' ;
    public const string PRIVATE_KEY_PATH     = 'privateKeyPath' ;
    public const string VALID_DAYS           = 'validDays' ;
}