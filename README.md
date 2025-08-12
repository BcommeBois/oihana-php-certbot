# Oihana PHP - Certbot

[![Oihana PHP Certbot](https://raw.githubusercontent.com/BcommeBois/oihana-php-certbot/main/assets/images/oihana-php-certbot-logo-inline-512x160.png)](https://github.com/BcommeBois/oihana-php-certbot)

A PHP toolkit designed to create, modify, and manage [Let’s Encrypt](https://letsencrypt.org/) certificates through the [Certbot](https://certbot.eff.org/) command-line interface.

Built on top of the [Oihana PHP Commands](https://github.com/BcommeBois/oihana-php-commands/) Library.

[![Latest Version](https://img.shields.io/packagist/v/oihana/php-certbot.svg?style=flat-square)](https://packagist.org/packages/oihana/php-certbot)  
[![Total Downloads](https://img.shields.io/packagist/dt/oihana/php-certbot.svg?style=flat-square)](https://packagist.org/packages/oihana/php-certbot)  
[![License](https://img.shields.io/packagist/l/oihana/php-certbot.svg?style=flat-square)](LICENSE)

## 🔰 Introduction

**Let’s Encrypt** is a free, automated, and open certificate authority that provides **SSL/TLS certificates** to enable **HTTPS** on websites, improving security and privacy for users.

**Certbot** is a popular command-line tool that automates the process of obtaining, installing, and renewing **Let’s Encrypt certificates** on web servers, simplifying HTTPS deployment.

## 📦 Installation

> **Requires [PHP 8.4+](https://php.net/releases/)**

Install via [Composer](https://getcomposer.org):
### 
```shell
composer require oihana/php-certbot
```

## ✅ Running Unit Tests

To run all tests:
```shell
composer test
```

## 🧾 License

This project is licensed under the [Mozilla Public License 2.0 (MPL-2.0)](https://www.mozilla.org/en-US/MPL/2.0/).