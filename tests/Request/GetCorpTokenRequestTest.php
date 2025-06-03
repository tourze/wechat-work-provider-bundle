<?php

namespace WechatWorkProviderBundle\Tests\Request;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\Request\GetCorpTokenRequest;

class GetCorpTokenRequestTest extends TestCase
{
    private GetCorpTokenRequest $request;

    protected function setUp(): void
    {
        $this->request = new GetCorpTokenRequest();
    }

    public function testExtendsApiRequest(): void
    {
        $this->assertInstanceOf(ApiRequest::class, $this->request);
    }

    public function testGetRequestPath(): void
    {
        $expectedPath = '/cgi-bin/gettoken';
        $this->assertSame($expectedPath, $this->request->getRequestPath());
    }

    public function testGetRequestMethod(): void
    {
        $expectedMethod = 'GET';
        $this->assertSame($expectedMethod, $this->request->getRequestMethod());
    }

    public function testAuthCorpIdGetterAndSetter(): void
    {
        $authCorpId = 'test_corp_id_123';
        $this->request->setAuthCorpId($authCorpId);
        $this->assertSame($authCorpId, $this->request->getAuthCorpId());
    }

    public function testPermanentCodeGetterAndSetter(): void
    {
        $permanentCode = 'permanent_code_abc123def456';
        $this->request->setPermanentCode($permanentCode);
        $this->assertSame($permanentCode, $this->request->getPermanentCode());
    }

    public function testGetRequestOptionsWithValidData(): void
    {
        $authCorpId = 'test_corp_id';
        $permanentCode = 'test_permanent_code';
        
        $this->request->setAuthCorpId($authCorpId);
        $this->request->setPermanentCode($permanentCode);
        
        $options = $this->request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertArrayHasKey('corpid', $options['query']);
        $this->assertArrayHasKey('corpsecret', $options['query']);
        $this->assertSame($authCorpId, $options['query']['corpid']);
        $this->assertSame($permanentCode, $options['query']['corpsecret']);
    }

    public function testGetRequestOptionsStructure(): void
    {
        $this->request->setAuthCorpId('test_corp');
        $this->request->setPermanentCode('test_code');
        
        $options = $this->request->getRequestOptions();
        
        $expectedStructure = [
            'query' => [
                'corpid' => 'test_corp',
                'corpsecret' => 'test_code',
            ]
        ];
        
        $this->assertSame($expectedStructure, $options);
    }

    public function testEmptyParametersInOptions(): void
    {
        // 不设置任何参数，测试空值情况
        $this->expectException(\Error::class);
        $this->request->getRequestOptions();
    }

    public function testSetterMethods(): void
    {
        // 这些方法返回 void，不支持链式调用
        $this->request->setAuthCorpId('corp_123');
        $this->request->setPermanentCode('code_456');
        
        $this->assertSame('corp_123', $this->request->getAuthCorpId());
        $this->assertSame('code_456', $this->request->getPermanentCode());
        
        // 重新设置新的值
        $this->request->setAuthCorpId('new_corp');
        $this->request->setPermanentCode('new_code');
        
        $this->assertSame('new_corp', $this->request->getAuthCorpId());
        $this->assertSame('new_code', $this->request->getPermanentCode());
    }

    public function testSpecialCharactersInParameters(): void
    {
        $authCorpId = 'corp!@#$%^&*()_+-=';
        $permanentCode = 'code测试中文123';
        
        $this->request->setAuthCorpId($authCorpId);
        $this->request->setPermanentCode($permanentCode);
        
        $options = $this->request->getRequestOptions();
        
        $this->assertSame($authCorpId, $options['query']['corpid']);
        $this->assertSame($permanentCode, $options['query']['corpsecret']);
    }

    public function testLongParameterValues(): void
    {
        $authCorpId = str_repeat('a', 1000);
        $permanentCode = str_repeat('b', 1000);
        
        $this->request->setAuthCorpId($authCorpId);
        $this->request->setPermanentCode($permanentCode);
        
        $options = $this->request->getRequestOptions();
        
        $this->assertSame($authCorpId, $options['query']['corpid']);
        $this->assertSame($permanentCode, $options['query']['corpsecret']);
    }
} 