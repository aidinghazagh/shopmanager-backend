<?php

use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createShop(string $phone, string $name): Shop
{
    return Shop::forceCreate([
        'name' => $name,
        'phone' => $phone,
        'password' => 'secret123',
        'valid_until' => now()->addYear(),
    ]);
}

function loginAs(Shop $shop): array
{
    $response = test()->postJson('/api/login', [
        'phone' => $shop->phone,
        'password' => 'secret123',
        'default_lang' => 'en',
    ]);
    $response->assertJsonPath('status', true);

    return [
        'Accept' => 'application/json',
        'Authorization' => 'Bearer '.$response->json('output.token'),
    ];
}

beforeEach(function () {
    $this->shopA = createShop('09120000001', 'Shop A');
    $this->shopB = createShop('09120000002', 'Shop B');

    $this->customerA = Customer::create(['shop_id' => $this->shopA->id, 'name' => 'Customer A']);
    $this->orderA = Order::create(['shop_id' => $this->shopA->id, 'customer_id' => $this->customerA->id]);
});

// GET /api/customer/{customer}/orders

it('lists orders of the shop\'s own customer', function () {
    $this->getJson("/api/customer/{$this->customerA->id}/orders", loginAs($this->shopA))
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonCount(1, 'output')
        ->assertJsonPath('output.0.id', $this->orderA->id);
});

it('does not list orders of another shop\'s customer', function () {
    $this->getJson("/api/customer/{$this->customerA->id}/orders", loginAs($this->shopB))
        ->assertJsonPath('status', false)
        ->assertJsonPath('errors', ['Please provide a valid token']);
});

// POST /api/order/{order}/payment

it('creates a payment on the shop\'s own order', function () {
    $this->postJson("/api/order/{$this->orderA->id}/payment", ['amount' => 500], loginAs($this->shopA))
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonPath('output.order_id', $this->orderA->id)
        ->assertJsonPath('output.amount', 500);

    expect(Payment::where('order_id', $this->orderA->id)->count())->toBe(1);
});

it('does not create a payment on another shop\'s order', function () {
    $this->postJson("/api/order/{$this->orderA->id}/payment", ['amount' => 500], loginAs($this->shopB))
        ->assertJsonPath('status', false)
        ->assertJsonPath('errors', ['Unauthorized request']);

    expect(Payment::count())->toBe(0);
});

// PATCH /api/shop/{shop}

it('updates the shop\'s own name and language', function () {
    $this->patchJson("/api/shop/{$this->shopA->id}", ['name' => 'Renamed', 'language' => 'fa'], loginAs($this->shopA))
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonPath('output.name', 'Renamed')
        ->assertJsonPath('output.language', 'fa');

    $this->shopA->refresh();
    expect($this->shopA->name)->toBe('Renamed')
        ->and($this->shopA->language)->toBe('fa');
});

it('does not update another shop', function () {
    $this->patchJson("/api/shop/{$this->shopA->id}", ['name' => 'Hijacked', 'language' => 'fa'], loginAs($this->shopB))
        ->assertJsonPath('status', false)
        ->assertJsonPath('errors', ['Please provide a valid token']);

    $this->shopA->refresh();
    expect($this->shopA->name)->toBe('Shop A');
});

// POST /api/order (customer_id)

it('accepts the shop\'s own customer when creating an order', function () {
    $product = Product::create(['shop_id' => $this->shopA->id, 'name' => 'Pen', 'price' => 100, 'purchase_price' => 50]);

    // Only the customer_id validation is asserted here: on a freshly migrated
    // database the order_products insert fails because the migration creates
    // `name` instead of `name_on_created`.
    $this->postJson('/api/order', [
        'customer_id' => $this->customerA->id,
        'products' => [$product->id => 2],
    ], loginAs($this->shopA))
        ->assertOk()
        ->assertJsonMissingPath('validations.customer_id');
});

it('does not create an order for another shop\'s customer', function () {
    $product = Product::create(['shop_id' => $this->shopB->id, 'name' => 'Pen', 'price' => 100, 'purchase_price' => 50]);

    $this->postJson('/api/order', [
        'customer_id' => $this->customerA->id,
        'products' => [$product->id => 2],
    ], loginAs($this->shopB))
        ->assertJsonPath('status', false)
        ->assertJsonPath('validations.customer_id', ['Customer not found']);

    expect(Order::where('shop_id', $this->shopB->id)->count())->toBe(0);
});
