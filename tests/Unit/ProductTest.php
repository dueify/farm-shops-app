<?php

namespace Tests\Unit;

use App\Product;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_checks_if_a_user_is_subscribed_to_a_product()
    {
        $user = factory(User::class)->create();
        $product = factory(Product::class)->create();

        $this->assertFalse($user->isSubscribedToProduct($product));

        $user->subscribeToProduct($product);

        $user->refresh();
        $this->assertTrue($user->isSubscribedToProduct($product));
    }
}
