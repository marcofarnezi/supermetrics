<?php
namespace Test\Unit\Models;

use App\Models\Interfaces\ModelInterface;
use App\Models\LoginToken;
use PHPUnit\Framework\TestCase;
use Tests\Support\ReflectionTrait;

class LoginTokenTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var LoginToken
     */
    private $login_token;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->login_token = new LoginToken();
    }

    public function testIfLoginTokeIsAInstanceOfModelInterface()
    {
        $this->assertInstanceOf(ModelInterface::class, $this->login_token);
    }

    /**
     * @dataProvider loginTokenDataProvider
     */
    public function testSetData(array $input, array $output)
    {
        $return = $this->login_token->setData($input);

        $client_id_property = self::setPropertyAccessiblePublic(LoginToken::class, 'client_id');
        $email_property = self::setPropertyAccessiblePublic(LoginToken::class, 'email');
        $sl_token_property = self::setPropertyAccessiblePublic(LoginToken::class, 'sl_token');

        $this->assertEquals($output['client_id'], $client_id_property->getValue($this->login_token));
        $this->assertEquals($output['email'], $email_property->getValue($this->login_token));
        $this->assertEquals($output['sl_token'], $sl_token_property->getValue($this->login_token));
        $this->assertInstanceOf(LoginToken::class, $return);
    }

    /**
     * @dataProvider loginTokenDataProvider
     */
    public function testGetData(array $input, array $output)
    {
        $this->login_token->setData($input);
        $data_return = $this->login_token->getData();
        $this->assertEquals($output, $data_return);
    }

    public function loginTokenDataProvider(): array
    {
        return [
            [
                [
                    'client_id' => 'client_id',
                    'email' => 'email',
                    'sl_token' => 'token'
                ],
                [
                    'client_id' => 'client_id',
                    'email' => 'email',
                    'sl_token' => 'token'
                ]
            ],
            [
                [
                    'client_id' => 'client_id',
                    'sl_token' => 'token'
                ],
                [
                    'client_id' => null,
                    'email' => null,
                    'sl_token' => null
                ]
            ],
        ];
    }
}