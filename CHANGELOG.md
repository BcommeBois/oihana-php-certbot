# Oihana PHP Certbot library - Change Log

All notable changes to this project will be documented in this file.

This project adheres to [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)  
and follows [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

---

## [1.0.0] - 2025-08-13

### Added

- Initial stable release.
- Strongly-typed options for Certbot subcommands:
  - `CertbotCertOnlyOptions`, `CertbotRenewOptions`, `CertbotRevokeOptions`,
    `CertbotDeleteOptions`, `CertbotCertificatesOptions`, and base `CertbotOptions`.
- Plugin and webroot support:
  - `CertbotWebrootOptions` plus plugin traits for manual, nginx, and webroot flows.
- Trait-based API you can mix into your services:
  - `CertbotTrait` (bundling `CertbotCertificatesTrait`, `CertbotCertOnlyTrait`,
    `CertbotRenewTrait`, `CertbotRevokeTrait`, `CertbotDeleteTrait`).
- Helpers:
  - `getCertbotDomains()` to safely build domain lists.
  - `parseCertbotCertificates()` to parse `certbot certificates` output.
- Entity:
  - `CertbotCertificate` with typed properties and `isExpired()` / `isValid()` helpers.
- Enums:
  - `CertbotSubCommand` and `CertbotParam`.
- Execution features:
  - Optional verbose and silent modes, dry-run support, and automatic sudo execution.
- Tooling:
  - PHPUnit configuration and Composer scripts.

---


