<?php

/*
 * This file is part of the DigiDoc package.
 *
 * (c) Kristen Gilden <kristen.gilden@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KG\DigiDoc\X509;

/**
 * Represents a PKI signature. Quite a few extra pieces of data are needed to
 * verify a signature in practice. It brings together the initial bytes signed,
 * the signature itself and the algorithm used to sign the bytes. The object
 * will then be able to tell, whether a particular certificate (or, rather,
 * the public key embedded in the certificate) was used to sign a piece of data.
 */
class Signature
{
    /**
     * @var string
     */
    private $algorithm;

    /**
     * @var string
     */
    private $bytesSigned;

    /**
     * @var string
     */
    private $signature;

    /**
     * @var array
     */
    private static $supportedAlgorithms;

    /**
     * @param string         $bytesSigned  The string of data used to generate the signature previously
     * @param string         $signature    A raw binary string signature, generated by openssl_sign() or similar means
     * @param integer|string $algorithm    Algorithm used to create the signature
     *
     * @throws \InvalidArgumentException If the algorithm is unsupported
     */
    public function __construct($bytesSigned, $signature, $algorithm)
    {
        $this->bytesSigned = $bytesSigned;
        $this->signature = $signature;

        if (!$this->isAlgorithmSupported($algorithm)) {
            throw new \InvalidArgumentException(sprintf('Algorithm "%s" is not supported.', $algorithm));
        }

        $this->algorithm = $algorithm;
    }

    /**
     * @see http://php.net/manual/en/function.openssl-get-publickey.php
     *
     * @param resource $key Public key corresponding to the private key used for signing
     *
     * @return boolean Whether the signature was signed by the given key
     *
     * @throws \RuntimeException If an error occurred during verification
     */
    public function isSignedByKey($key)
    {
        $result = openssl_verify($this->bytesSigned, $this->signature, $key, $this->algorithm);

        if (1 === $result) {
            return true;
        }

        if (0 === $result) {
            return false;
        }

        throw new \RuntimeException(openssl_error_string());
    }

    /**
     * @return boolean
     */
    private static function isAlgorithmSupported($algorithm)
    {
        if (!self::$supportedAlgorithms) {
            $supportedAlgorithms = openssl_get_md_methods(true);
            $constants = array(
                'OPENSSL_ALGO_DSS1',
                'OPENSSL_ALGO_SHA1',
                'OPENSSL_ALGO_SHA224',
                'OPENSSL_ALGO_SHA256',
                'OPENSSL_ALGO_SHA384',
                'OPENSSL_ALGO_SHA512',
                'OPENSSL_ALGO_RMD160',
                'OPENSSL_ALGO_MD5',
                'OPENSSL_ALGO_MD4',
                'OPENSSL_ALGO_MD2',
            );

            foreach ($constants as $constant) {
                if (defined($constant)) {
                    $supportedAlgorithms[] = constant($constant);
                }
            }

            self::$supportedAlgorithms = $supportedAlgorithms;
        }

        return in_array($algorithm, self::$supportedAlgorithms);
    }
}
