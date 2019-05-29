<?php

/*
 * This file is part of the DigiDoc package.
 *
 * (c) Kristen Gilden <kristen.gilden@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KG\DigiDoc\Tests;

use KG\DigiDoc\Container;

/**
 * @deprecated Deprecated since version 0.1.2, to be removed in 1.0.0
 */
class ContainerTest extends \PHPUnit\Framework\TestCase
{
    public function testGetSignatureReturnsNullIfNoSuchId()
    {
        $container = new Container($this->getMockSession());

        $this->assertNull($container->getSignature('S01'));
    }

    public function testGetSignatureReturnsSignatureById()
    {
        $signature = $this->getMockSignature();
        $signature
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($signatureId = 'S01'))
        ;

        $container = new Container($this->getMockSession());
        $container->addSignature($signature);

        $this->assertSame($signature, $container->getSignature($signatureId));
    }

    /**
     * @return \KG\DigiDoc\Session|PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockSession()
    {
        return $this
            ->getMockBuilder('KG\DigiDoc\Session')
            ->disableOriginalConstructor()
            ->createMock()
        ;
    }

    /**
     * @return \KG\DigiDoc\Signature|PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockSignature()
    {
        return $this
            ->getMockBuilder('KG\DigiDoc\Signature')
            ->disableOriginalConstructor()
            ->createMock()
        ;
    }
}
