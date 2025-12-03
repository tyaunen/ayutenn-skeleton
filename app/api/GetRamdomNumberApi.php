<?php
use ayutenn\core\requests\Api;

class GetRamdomNumberApi extends Api
{
    protected array $RequestParameterFormat = [];

    public function main(): array
    {
        return $this->createResponse(
            true,
            [
                'number' => mt_rand(0, 100)
            ]
        );
    }
}

return new GetRamdomNumberApi();