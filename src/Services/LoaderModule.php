<?php
namespace App\Services;

use App\Models\Interfaces\ModelInterface;
use App\Services\Interfaces\LoaderInterface;
use App\Services\Interfaces\LoadFormatInterface;
use App\Services\Interfaces\RequestInterface;

class LoaderModule implements LoaderInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var string
     */
    protected $model_name;

    protected function getModel(string $modelName): ModelInterface
    {
        return new $modelName();
    }

    public function setToLoad(RequestInterface $request, string $model_name): LoaderInterface
    {
        $this->request = $request;
        $this->model_name = $model_name;

        return $this;
    }

    public function load(LoadFormatInterface $load_format): array
    {
        $modelList = [];
        $dataList =  $this->request->send();
        foreach ($dataList as $data) {
            if ($this->request->isSuccess($data['status'])) {
                $modelList[] = $this->getModel($this->model_name)
                    ->setData($load_format->get($data['body']));
            }
        }
        return $modelList;
    }
}