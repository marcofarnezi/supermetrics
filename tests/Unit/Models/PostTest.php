<?php
namespace Test\Unit\Models;

use App\Models\Interfaces\ModelInterface;
use App\Models\Post;
use DateTime;
use PHPUnit\Framework\TestCase;
use Tests\Support\ReflectionTrait;

class PostTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var Post
     */
    private $post;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->post = new Post();
    }

    public function testIfPostIsAInstanceOfModelInterface()
    {
        $this->assertInstanceOf(ModelInterface::class, $this->post);
    }

    /**
     * @dataProvider postDataProvider
     */
    public function testSetData(array $input, array $output)
    {
        $return = $this->post->setData($input);

        $id_property = self::setPropertyAccessiblePublic(Post::class, 'id');
        $from_name_property = self::setPropertyAccessiblePublic(Post::class, 'from_name');
        $from_id_property = self::setPropertyAccessiblePublic(Post::class, 'from_id');
        $message_property = self::setPropertyAccessiblePublic(Post::class, 'message');
        $type_property = self::setPropertyAccessiblePublic(Post::class, 'type');
        $create_time_property = self::setPropertyAccessiblePublic(Post::class, 'created_time');

        $this->assertEquals($output['id'], $id_property->getValue($this->post));
        $this->assertEquals($output['from_name'], $from_name_property->getValue($this->post));
        $this->assertEquals($output['from_id'], $from_id_property->getValue($this->post));
        $this->assertEquals($output['message'], $message_property->getValue($this->post));
        $this->assertEquals($output['type'], $type_property->getValue($this->post));
        $this->assertEquals($output['created_time'], $create_time_property->getValue($this->post));
        $this->assertInstanceOf(Post::class, $return);
    }

    /**
     * @dataProvider postDataProvider
     */
    public function testGetData(array $input, array $output)
    {
        $this->post->setData($input);
        $data_return = $this->post->getData();
        $this->assertEquals($output, $data_return);
    }

    public function postDataProvider(): array
    {
        return [
            [
                [
                    'id' => 1,
                    'from_name' => 'from_name',
                    'from_id' => 'from_id',
                    'message' => 'message',
                    'type' => 'type',
                    'created_time' => '2000-01-01'
                ],
                [
                    'id' => 1,
                    'from_name' => 'from_name',
                    'from_id' => 'from_id',
                    'message' => 'message',
                    'type' => 'type',
                    'created_time' => new DateTime('2000-01-01')
                ]
            ],
            [
                [
                    'from_name' => 'from_name',
                    'from_id' => 'from_id',
                    'message' => 'message',
                    'type' => 'type',
                    'created_time' => '2000-01-01'
                ],
                [
                    'id' => null,
                    'from_name' => null,
                    'from_id' => null,
                    'message' => null,
                    'type' => null,
                    'created_time' => null
                ]
            ],
        ];
    }
}