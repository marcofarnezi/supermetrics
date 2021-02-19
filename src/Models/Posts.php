<?php
namespace App\Models;

use App\Models\Interfaces\ModelInterface;

class Posts extends Model
{
    const POST_MODEL_NAME = Post::class;

    protected $posts;

    protected function getPostModel(): Post
    {
        $post_name_class = self::POST_MODEL_NAME;
        return new $post_name_class();
    }

    public function setData(array $data): ModelInterface
    {
        if ($this->isValid($data)) {
            $this->posts = $data['posts'];
        }
        return $this;
    }

    public function getData(): array
    {
        $post_list = [];

        foreach ($this->posts as $post_data) {
            $post_list[] = $this->getPostModel()->setData($post_data);
        }

        return $post_list;
    }
}