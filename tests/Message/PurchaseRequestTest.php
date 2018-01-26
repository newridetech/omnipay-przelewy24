<?php

namespace Omnipay\Przelewy24\Message;

use Omnipay\Common\CreditCard;
use Omnipay\Przelewy24\Gateway;
use Omnipay\Przelewy24\Message\AbstractRequest\PurchaseRequest;
use Omnipay\Przelewy24\Message\AbstractResponse\PurchaseResponse;
use Omnipay\Tests\TestCase;

class PurchaseRequestTest extends TestCase
{
    /**
     * @var PurchaseRequest
     */
    private $request;

    public function setUp()
    {
        $card = new CreditCard(array(
            'email' => 'test@example.com',
            'country' => 'NL',
        ));

        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(array(
            'merchantId'  => '144354',
            'posId'       => '144354',
            'crc'         => '1287875353948',
            'sessionId'   => '42',
            'amount'      => '12.00',
            'currency'    => 'PLN',
            'description' => 'Description',
            'returnUrl'   => 'https://www.example.com/return',
            'notifyUrl'   => 'https://www.example.com/notify',
            'card'        => $card,
        ));
    }

    public function channelProvider() {
        return array(
            array(Gateway::P24_CHANNEL_ALL),
            array(null)
        );
    }
    /**
     * @dataProvider channelProvider
     */
    public function testGetData($channel)
    {
        $card = new CreditCard(array(
            'email' => 'test@example.com',
            'country' => 'NL',
        ));

        $this->request->initialize(array(
            'merchantId'  => '144354',
            'posId'       => '144354',
            'crc'         => '1287875353948',
            'sessionId'   => '42',
            'amount'      => '12.00',
            'currency'    => 'PLN',
            'description' => 'Description',
            'returnUrl'   => 'https://www.example.com/return',
            'notifyUrl'   => 'https://www.example.com/notify',
            'card'        => $card,
            'channel'     => $channel,
        ));

        $data = $this->request->getData();

        $this->assertEquals("42", $data['p24_session_id']);
        $this->assertEquals(1200, $data['p24_amount']);
        $this->assertEquals("PLN", $data['p24_currency']);
        $this->assertEquals('Description', $data['p24_description']);
        $this->assertEquals("test@example.com", $data['p24_email']);
        $this->assertEquals("", $data['p24_client']);
        $this->assertEquals("NL", $data['p24_country']);
        $this->assertEquals('https://www.example.com/return', $data['p24_url_return']);
        $this->assertEquals('https://www.example.com/notify', $data['p24_url_status']);
        $this->assertEquals('d565d579d28f4374a7c2852a8e3f8fd7', $data['p24_sign']);
        $this->assertEquals('3.2', $data['p24_api_version']);

        if (null === $channel) {
            $this->assertCount(15, $data);
        } else {
            $this->assertEquals($channel, $data['p24_channel']);
            $this->assertCount(16, $data);
        }

    }

    public function testSendSuccess()
    {
        $this->setMockHttpResponse('PurchaseSuccess.txt');
        $response = $this->request->send();

        $this->assertInstanceOf(PurchaseResponse::class, $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertEquals('GET', $response->getRedirectMethod());
        $this->assertEquals(
            'https://secure.przelewy24.pl/trnRequest/3F17389551-5285CA-F0B10D-A700D9B023',
            $response->getRedirectUrl()
        );
        $this->assertNull($response->getRedirectData());
        $this->assertNull($response->getTransactionReference());
        $this->assertEquals('0', $response->getCode());
        $this->assertNull($response->getMessage());
    }

    public function testSendSignatureFailure()
    {
        $this->setMockHttpResponse('PurchaseSignatureFailure.txt');
        $response = $this->request->send();

        $this->assertInstanceOf(PurchaseResponse::class, $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertEquals('err00', $response->getCode());
        $this->assertEquals('Invalid CRC', $response->getMessage());
    }
}
