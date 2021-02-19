<?php
namespace Unit\Services;

use App\Models\Interfaces\ModelInterface;
use App\Models\LoginToken;
use App\Models\Post;
use App\Models\Posts;
use App\Services\Interfaces\LoadFormatInterface;
use App\Services\Interfaces\RequestInterface;
use App\Services\LoaderModule;
use PHPUnit\Framework\TestCase;
use Tests\Support\ReflectionTrait;

class LoaderModuleTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var LoaderModule
     */
    private $loader_module;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->loader_module = new LoaderModule();
    }

    public function testSetToLoad()
    {
        $module_name = 'Module';
        $request_mock = \Mockery::mock(RequestInterface::class);
        $result = $this->loader_module->setToLoad($request_mock, $module_name);

        $request_property = self::setPropertyAccessiblePublic(LoaderModule::class, 'request');
        $module_name_property = self::setPropertyAccessiblePublic(
            LoaderModule::class,
            'model_name'
        );
        $this->assertEquals($request_mock, $request_property->getValue($this->loader_module));
        $this->assertEquals($module_name, $module_name_property->getValue($this->loader_module));
        $this->assertInstanceOf(LoaderModule::class, $result);
    }

    /**
     * @dataProvider getModelDataProvider
     */
    public function testGetModel(string $module_name)
    {
        $get_model_method = self::getAccessibleMethod(LoaderModule::class, 'getModel');
        $result = $get_model_method->invokeArgs($this->loader_module, [$module_name]);
        $this->assertInstanceOf(ModelInterface::class, $result);
    }

    public function testLoad()
    {
        $data_list = [
            [
                'status' => 200,
                'body' => ['test']
            ]
        ];
        $loader_module_mock = \Mockery::mock(LoaderModule::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $request_mock = \Mockery::mock(RequestInterface::class);
        $request_mock->shouldReceive('send')
            ->andReturn($data_list);
        $request_mock->shouldReceive('isSuccess')
            ->andReturn(true);
        self::setValueInPrivateProperty(
            $loader_module_mock,
            LoaderModule::class,
            'request',
            $request_mock
        );
        self::setValueInPrivateProperty(
            $loader_module_mock,
            LoaderModule::class,
            'model_name',
            'model_name'
        );
        $model_mock = \Mockery::mock(ModelInterface::class);
        $model_mock->shouldReceive('setData')
            ->andReturn($model_mock);
        $load_format_mock = \Mockery::mock(LoadFormatInterface::class);
        $load_format_mock->shouldReceive('get')
            ->andReturn([]);
        $loader_module_mock->shouldReceive('getModel')
            ->andReturn($model_mock);

        $result = $loader_module_mock->load($load_format_mock);
        $this->assertEquals([$model_mock], $result);
    }

    public function getModelDataProvider(): array
    {
        return [
            [Post::class],
            [Posts::class],
            [LoginToken::class],
        ];
    }
}