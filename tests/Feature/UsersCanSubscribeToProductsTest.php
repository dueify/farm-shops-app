<?php

namespace Tests\Feature;

use App\Http\Controllers\Product\SubscribeToProductController;
use App\Notifications\Product\SubscribeToProduct;
use App\Product;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UsersCanSubscribeToProductsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_subscribes_to_a_product_and_sends_a_notification()
    {
        /*$this->withoutExceptionHandling();*/
        Notification::fake();

        $user = factory(User::class)->create();
        $product = factory(Product::class)->create();

        $this->assertFalse($user->isSubscribedToProduct($product));

        $response = $this->actingAs($user)
            ->post(route('products.subscribe', $product));

        $response->assertRedirect(route('products.show', $product));
        $user->refresh();

        $this->assertTrue($user->isSubscribedToProduct($product));

        Notification::assertSentTo($user, SubscribeToProduct::class, function ($notification) use ($product) {
            return $notification->product->id === $product->id;
        });
    }

    /** @test */
    public function it_does_not_allow_a_subscribed_user_to_subscribe_again()
    {
        Notification::fake();

        $user = factory(User::class)->create();
        $product = factory(Product::class)->create();
        $user->subscribeToProduct($product);

        $user->refresh();

        $response = $this->actingAs($user)
            ->post(route('products.subscribe', $product));

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'user' => 'The user is already subscribed to the product.',
        ]);
    }
}
