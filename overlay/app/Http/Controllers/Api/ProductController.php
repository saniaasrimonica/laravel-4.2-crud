<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:160'], 'per_page' => ['sometimes', 'integer', 'between:1,100']]);
        $query = Product::query()->where('user_id', $request->user()->id);
        if ($term = $filters['q'] ?? null) {
            $query->where(function ($query) use ($term) {
                $query->where('name', 'like', '%'.$term.'%')->orWhere('sku', 'like', '%'.$term.'%');
            });
        }
        return ProductResource::collection($query->latest('id')->paginate($filters['per_page'] ?? 15)->withQueryString());
    }
    public function store(ProductRequest $request): ProductResource {
        $product = new Product($request->validated());
        $product->user()->associate($request->user());
        $product->save();
        return new ProductResource($product);
    }
    public function show(Product $product): ProductResource {
        Gate::authorize('view', $product);
        return new ProductResource($product);
    }
    public function update(ProductRequest $request, Product $product): ProductResource {
        Gate::authorize('update', $product);
        $product->update($request->validated());
        return new ProductResource($product->refresh());
    }
    public function destroy(Product $product): Response {
        Gate::authorize('delete', $product);
        $product->delete();
        return response()->noContent();
    }
}
