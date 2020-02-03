<?php

namespace Tests;

use App\Http\Resources\Product;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function create($model, $attributes = []): Product
    {
        $product = factory("App\\$model")->create($attributes);
        return (new Product($product));
    }
}
