<?php
namespace Test\Unit\Models;

use App\Models\Interfaces\ModelInterface;
use App\Models\Post;
use App\Models\Posts;
use Mockery\Mock;
use PHPUnit\Framework\TestCase;
use Tests\Support\ReflectionTrait;

class PostsTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var Posts
     */
    private $posts;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->posts = new Posts();
    }

    public function testIfPostsIsAInstanceOfModelInterface()
    {
        $this->assertInstanceOf(ModelInterface::class, $this->posts);
    }

    /**
     * @dataProvider postsDataProvider
     */
    public function testSetData(array $input, array $output)
    {
        $return = $this->posts->setData($input);
        $posts_property = self::setPropertyAccessiblePublic(Posts::class, 'posts');
        $this->assertEquals($output['posts'], $posts_property->getValue($this->posts));
        $this->assertInstanceOf(Posts::class, $return);
    }

    public function testGetData()
    {
        $post_mock = \Mockery::mock(Post::class)
            ->makePartial();
        $post_mock->shouldReceive('setData')
            ->andReturn($post_mock);

        $posts_mock = \Mockery::mock(Posts::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $posts_mock->shouldReceive('isValid')
            ->andReturn(true);
        $posts_mock->shouldReceive('getPostModel')
            ->andReturn($post_mock);

        $data = ['posts' => [['test']]];
        $posts_mock->setData($data);
        $data_return = $posts_mock->getData();

        $this->assertEquals($data_return, $data_return);
    }

    public function postsDataProvider(): array
    {
        return [
            [
                ['posts' => ['post']],
                ['posts' => ['post']]
            ],
            [
                [],
                ['posts' => null]
            ],
        ];
    }
}