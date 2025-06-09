<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * The path to the JSON file where products are stored.
     *
     * @var string
     */
    protected $filePath;

    /**
     * Create a new controller instance and set the JSON file path.
     */
    public function __construct()
    {
        $this->filePath = storage_path('app/products.json');
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $products = [];

            if (File::exists($this->filePath)) {
                $jsonContent = File::get($this->filePath);
                $products = json_decode($jsonContent, true);
                if (!is_array($products)) {
                    $products = [];
                }
            }

            return response()->json([
                'message' => 'Products retrieved successfully.',
                'products' => ProductResource::collection($products)
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error retrieving products from JSON file: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to retrieve products due to a server error. Please try again.',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param string $id The ID of the product to retrieve.
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        try {
            if (!File::exists($this->filePath)) {
                return response()->json([
                    'message' => 'Products file not found.',
                ], 404);
            }

            $jsonContent = File::get($this->filePath);
            $products = json_decode($jsonContent, true);

            if (!is_array($products)) {
                return response()->json([
                    'message' => 'Invalid products data.',
                ], 500);
            }

            $product = collect($products)->firstWhere('id', $id);

            if (!$product) {
                return response()->json([
                    'message' => 'Product not found.',
                ], 404);
            }

            return response()->json([
                'message' => 'Product retrieved successfully.',
                'product' => new ProductResource($product)
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error retrieving product from JSON file: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to retrieve product due to a server error. Please try again.',
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreProductRequest $request The validated request data.
     * @return JsonResponse
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            $products = [];

            if (File::exists($this->filePath)) {
                $jsonContent = File::get($this->filePath);
                $products = json_decode($jsonContent, true);
                if (!is_array($products)) {
                    $products = [];
                }
            }

            $newProduct = [
                'id' => uniqid(),
                'name' => $validatedData['name'],
                'quantity' => (int) $validatedData['quantity'],
                'price' => (float) $validatedData['price'],
                'created_at' => now()->toDateTimeString(),
            ];

            $products[] = $newProduct;

            File::put($this->filePath, json_encode($products, JSON_PRETTY_PRINT));

            return response()->json([
                'message' => 'Product saved successfully!',
                'product' => new ProductResource($newProduct)
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error saving product to JSON file: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to save product due to a server error. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateProductRequest $request The validated request data.
     * @param string $id The ID of the product to update.
     * @return JsonResponse
     */
    public function update(UpdateProductRequest $request, string $id): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            if (!File::exists($this->filePath)) {
                return response()->json([
                    'message' => 'Products file not found.',
                ], 404);
            }

            $jsonContent = File::get($this->filePath);
            $products = json_decode($jsonContent, true);

            if (!is_array($products)) {
                return response()->json([
                    'message' => 'Invalid products data.',
                ], 500);
            }

            $productIndex = array_search($id, array_column($products, 'id'));

            if ($productIndex === false) {
                return response()->json([
                    'message' => 'Product not found.',
                ], 404);
            }

            $products[$productIndex]['name'] = $validatedData['name'];
            $products[$productIndex]['quantity'] = (int) $validatedData['quantity'];
            $products[$productIndex]['price'] = (float) $validatedData['price'];
            $products[$productIndex]['created_at'] = now()->toDateTimeString();

            File::put($this->filePath, json_encode($products, JSON_PRETTY_PRINT));

            return response()->json([
                'message' => 'Product updated successfully!',
                'product' => new ProductResource($products[$productIndex])
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating product in JSON file: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to update product due to a server error. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id The ID of the product to delete.
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            if (!File::exists($this->filePath)) {
                return response()->json([
                    'message' => 'Products file not found.',
                ], 404);
            }

            $jsonContent = File::get($this->filePath);
            $products = json_decode($jsonContent, true);

            if (!is_array($products)) {
                return response()->json([
                    'message' => 'Invalid products data.',
                ], 500);
            }

            $productIndex = array_search($id, array_column($products, 'id'));

            if ($productIndex === false) {
                return response()->json([
                    'message' => 'Product not found.',
                ], 404);
            }

            unset($products[$productIndex]);
            $products = array_values($products);

            File::put($this->filePath, json_encode($products, JSON_PRETTY_PRINT));

            return response()->json([
                'message' => 'Product deleted successfully!'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting product from JSON file: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to delete product due to a server error. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
