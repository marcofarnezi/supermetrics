<?php
namespace Unit\Services\Posts;

use App\Models\Post;
use App\Services\Interfaces\RuleInterface;
use App\Services\Post\PostsCalculator;
use PHPUnit\Framework\TestCase;
use Tests\Support\ReflectionTrait;

class PostsCalculatorTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var PostsCalculator
     */
    private $posts_calculator;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->posts_calculator = new PostsCalculator();
    }

    public function testSetRule()
    {
        $rule_mock = \Mockery::mock(RuleInterface::class);
        $this->posts_calculator->setRule($rule_mock);
        $rules_property = self::setPropertyAccessiblePublic(PostsCalculator::class, 'rules');
        $this->assertEquals([$rule_mock], $rules_property->getValue($this->posts_calculator));
    }

    public function testSetAPostModel()
    {
        $post_mock = \Mockery::mock(Post::class);
        $rule_mock = \Mockery::mock(RuleInterface::class);
        $rule_mock->shouldReceive('run')
            ->andReturn();

        self::setValueInPrivateProperty(
            $this->posts_calculator,
            PostsCalculator::class,
            'rules',
            [$rule_mock]
        );

        $response = $this->posts_calculator->setModel($post_mock);

        $this->assertInstanceOf(PostsCalculator::class, $response);
    }

    public function testCalculate()
    {
        $mock_return = ['test'];
        $rule_mock = \Mockery::mock(RuleInterface::class);
        $rule_mock->shouldReceive('showResult')
            ->andReturn($mock_return);

        self::setValueInPrivateProperty(
            $this->posts_calculator,
            PostsCalculator::class,
            'rules',
            [$rule_mock]
        );

        $this->assertEquals([$mock_return], $this->posts_calculator->calculate());
    }
}