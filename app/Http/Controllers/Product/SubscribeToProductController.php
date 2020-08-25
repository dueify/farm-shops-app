<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Notifications\Product\SubscribeToProduct;
use App\Product;
use Illuminate\Http\Request;

class SubscribeToProductController extends Controller
{
    public function __invoke(Product $product) {
        auth()->user()->subscribeToProduct($product);

        auth()->user()->notify(new SubscribeToProduct($product));

        return redirect()->route('products.show', $product);
    }
}
