<?php
namespace Tests\Feature;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;
    private function payload(array $overrides = []): array {
        return array_replace(['name' => 'RF module', 'sku' => 'RF-001', 'price_cents' => 12345, 'stock' => 4], $overrides);
    }
    public function test_guests_cannot_access_inventory(): void {
        $this->getJson('/api/v1/products')->assertUnauthorized();
    }
    public function test_owner_can_complete_product_lifecycle(): void {
        Sanctum::actingAs(User::factory()->create());
        $id = $this->postJson('/api/v1/products', $this->payload())->assertCreated()->assertJsonPath('data.price_cents', 12345)->json('data.id');
        $this->getJson("/api/v1/products/$id")->assertOk()->assertJsonPath('data.sku', 'RF-001');
        $this->putJson("/api/v1/products/$id", $this->payload(['stock' => 9]))->assertOk()->assertJsonPath('data.stock', 9);
        $this->deleteJson("/api/v1/products/$id")->assertNoContent();
        $this->assertDatabaseMissing('products', ['id' => $id]);
    }
    public function test_other_users_cannot_read_modify_or_delete_product(): void {
        $product = Product::factory()->create();
        Sanctum::actingAs(User::factory()->create());
        $this->getJson("/api/v1/products/$product->id")->assertForbidden();
        $this->putJson("/api/v1/products/$product->id", $this->payload())->assertForbidden();
        $this->deleteJson("/api/v1/products/$product->id")->assertForbidden();
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 5]);
    }
    public function test_list_is_scoped_searchable_and_paginated(): void {
        $user = User::factory()->create();
        Product::factory()->create(['user_id' => $user->id, 'name' => 'Antenna']);
        Product::factory()->create(['name' => 'Antenna']);
        Product::factory()->create(['user_id' => $user->id, 'name' => 'Cable']);
        Sanctum::actingAs($user);
        $this->getJson('/api/v1/products?q=Antenna&per_page=1')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('meta.total', 1);
    }
    public function test_invalid_values_and_duplicate_sku_are_rejected(): void {
        Sanctum::actingAs(User::factory()->create());
        $this->postJson('/api/v1/products', $this->payload(['stock' => -1, 'price_cents' => 1.5]))->assertUnprocessable()->assertJsonValidationErrors(['stock', 'price_cents']);
        $this->postJson('/api/v1/products', $this->payload())->assertCreated();
        $this->postJson('/api/v1/products', $this->payload())->assertUnprocessable()->assertJsonValidationErrors('sku');
    }
    public function test_same_sku_is_allowed_for_different_owners_and_ownership_cannot_be_injected(): void {
        $other = User::factory()->create();
        Product::factory()->create(['user_id' => $other->id, 'sku' => 'RF-001']);
        $owner = User::factory()->create();
        Sanctum::actingAs($owner);
        $id = $this->postJson('/api/v1/products', $this->payload(['user_id' => $other->id]))->assertCreated()->json('data.id');
        $this->assertDatabaseHas('products', ['id' => $id, 'user_id' => $owner->id]);
    }
    public function test_unknown_id_returns_not_found(): void {
        Sanctum::actingAs(User::factory()->create());
        $this->getJson('/api/v1/products/999999')->assertNotFound();
    }
}
