<?php

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\View;
use App\Models\Category;
use App\Models\Product;
use App\Services\UploadException;
use App\Services\UploadService;

class ProductController extends BaseAdminController
{
    public function index(): void
    {
        View::render('admin.products.index', [
            'pageTitle' => 'Products',
            'activeNav' => 'products',
            'products' => Product::search(trim((string) Request::query('q', ''))),
            'search' => trim((string) Request::query('q', '')),
        ]);
    }

    public function create(): void
    {
        $this->showForm(null, [], $this->emptyForm(), [], [['name' => '', 'hex' => '#0f2e1b', 'image' => null]]);
    }

    public function edit(string $id): void
    {
        $product = Product::findRawById((int) $id);
        if (!$product) {
            redirect('/admin/products');
        }
        $form = [
            'name' => $product['name'], 'subtitle' => $product['subtitle'], 'price' => $product['price'],
            'original_price' => $product['original_price'], 'category_key' => $product['category_key'],
            'sub_category' => $product['sub_category'], 'description' => $product['description'],
            'fabric' => $product['fabric'], 'fit' => $product['fit'],
            'sizes' => implode("\n", json_decode($product['sizes'] ?? '[]', true) ?: []),
            'details' => implode("\n", json_decode($product['details'] ?? '[]', true) ?: []),
            'is_new' => $product['is_new'], 'is_best_seller' => $product['is_best_seller'], 'is_sale' => $product['is_sale'],
            'in_stock' => $product['in_stock'], 'featured_in_lookbook' => $product['featured_in_lookbook'],
            'rating' => $product['rating'], 'review_count' => $product['review_count'],
        ];
        $images = json_decode($product['images'] ?? '[]', true) ?: [];
        $colors = json_decode($product['colors'] ?? '[]', true) ?: [['name' => '', 'hex' => '#0f2e1b', 'image' => null]];
        $this->showForm($product, [], $form, $images, $colors);
    }

    public function store(): void
    {
        $this->save(null);
    }

    public function update(string $id): void
    {
        $product = Product::findRawById((int) $id);
        if (!$product) {
            redirect('/admin/products');
        }
        $this->save($product);
    }

    public function destroy(string $id): void
    {
        if (csrfVerify(Request::post('csrf_token'))) {
            $product = Product::findRawById((int) $id);
            if ($product) {
                foreach (json_decode($product['images'] ?? '[]', true) ?: [] as $img) {
                    UploadService::delete($img);
                }
                foreach (json_decode($product['colors'] ?? '[]', true) ?: [] as $c) {
                    if (!empty($c['image'])) {
                        UploadService::delete($c['image']);
                    }
                }
                Product::delete((int) $id);
                flashSuccess('Product deleted.');
            }
        }
        redirect('/admin/products');
    }

    private function emptyForm(): array
    {
        return [
            'name' => '', 'subtitle' => '', 'price' => '', 'original_price' => '', 'category_key' => '',
            'sub_category' => '', 'description' => '', 'fabric' => '', 'fit' => '', 'sizes' => '', 'details' => '',
            'is_new' => 1, 'is_best_seller' => 0, 'is_sale' => 0, 'in_stock' => 1, 'featured_in_lookbook' => 0,
            'rating' => 5.0, 'review_count' => 0,
        ];
    }

    private function save(?array $product): void
    {
        $errors = [];
        if (!csrfVerify(Request::post('csrf_token'))) {
            $errors[] = 'Your session expired. Please resubmit the form.';
        }

        $form = [];
        foreach (['name', 'subtitle', 'sub_category', 'description', 'fabric', 'fit'] as $f) {
            $form[$f] = trim((string) Request::post($f, ''));
        }
        $form['price'] = (float) Request::post('price', 0);
        $form['original_price'] = Request::post('original_price', '') !== '' ? (float) Request::post('original_price') : null;
        $form['category_key'] = (string) Request::post('category_key', '');
        $form['sizes'] = trim((string) Request::post('sizes', ''));
        $form['details'] = trim((string) Request::post('details', ''));
        $form['is_new'] = Request::post('is_new') ? 1 : 0;
        $form['is_best_seller'] = Request::post('is_best_seller') ? 1 : 0;
        $form['is_sale'] = Request::post('is_sale') ? 1 : 0;
        $form['in_stock'] = Request::post('in_stock') ? 1 : 0;
        $form['featured_in_lookbook'] = Request::post('featured_in_lookbook') ? 1 : 0;
        $form['rating'] = (float) Request::post('rating', 5.0);
        $form['review_count'] = (int) Request::post('review_count', 0);

        if ($form['name'] === '') $errors[] = 'Product name is required.';
        if ($form['price'] <= 0) $errors[] = 'Price must be greater than 0.';
        if ($form['category_key'] === '') $errors[] = 'Please choose a category.';

        $existingImages = $product ? (json_decode($product['images'] ?? '[]', true) ?: []) : [];
        $removeImages = Request::post('images_remove', []);
        $images = array_values(array_filter($existingImages, fn($img) => !in_array($img, $removeImages, true)));
        foreach ($removeImages as $rm) {
            if (in_array($rm, $existingImages, true)) {
                UploadService::delete($rm);
            }
        }
        $newImageFiles = $_FILES['images'] ?? null;
        if ($newImageFiles && !empty($newImageFiles['name'][0])) {
            foreach ($newImageFiles['name'] as $i => $name) {
                if ($name === '') continue;
                try {
                    $images[] = UploadService::store([
                        'name' => $newImageFiles['name'][$i], 'type' => $newImageFiles['type'][$i],
                        'tmp_name' => $newImageFiles['tmp_name'][$i], 'error' => $newImageFiles['error'][$i], 'size' => $newImageFiles['size'][$i],
                    ], 'products');
                } catch (UploadException $e) {
                    $errors[] = 'Photo upload: ' . $e->getMessage();
                }
            }
        }
        if (!$images) {
            $errors[] = 'Add at least one product photo.';
        }

        $colors = [];
        $colorsInput = Request::post('colors', []);
        foreach ($colorsInput as $i => $c) {
            $name = trim($c['name'] ?? '');
            if ($name === '') continue;
            $hex = preg_match('/^#[0-9a-fA-F]{6}$/', $c['hex'] ?? '') ? $c['hex'] : '#0f2e1b';
            $colorImage = $c['existing_image'] ?: null;
            if (!empty($c['remove_image']) && $colorImage) {
                UploadService::delete($colorImage);
                $colorImage = null;
            }
            $colorFile = $_FILES['colors']['name'][$i]['image_file'] ?? null;
            if (!empty($colorFile)) {
                try {
                    $uploaded = UploadService::store([
                        'name' => $_FILES['colors']['name'][$i]['image_file'], 'type' => $_FILES['colors']['type'][$i]['image_file'],
                        'tmp_name' => $_FILES['colors']['tmp_name'][$i]['image_file'], 'error' => $_FILES['colors']['error'][$i]['image_file'],
                        'size' => $_FILES['colors']['size'][$i]['image_file'],
                    ], 'products');
                    if ($colorImage) UploadService::delete($colorImage);
                    $colorImage = $uploaded;
                } catch (UploadException $e) {
                    $errors[] = "Color \"{$name}\": " . $e->getMessage();
                }
            }
            $colors[] = ['name' => $name, 'hex' => $hex, 'image' => $colorImage];
        }
        if (!$colors) {
            $errors[] = 'Add at least one color option.';
        }

        $sizes = array_values(array_filter(array_map('trim', explode("\n", $form['sizes']))));
        $details = array_values(array_filter(array_map('trim', explode("\n", $form['details']))));
        if (!$sizes) $errors[] = 'Add at least one size.';

        if (!$errors) {
            $data = [
                'name' => $form['name'], 'subtitle' => $form['subtitle'] ?: null, 'price' => $form['price'],
                'original_price' => $form['original_price'], 'category_key' => $form['category_key'],
                'sub_category' => $form['sub_category'] ?: null, 'description' => $form['description'] ?: null,
                'details' => json_encode($details, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'fabric' => $form['fabric'] ?: null, 'fit' => $form['fit'] ?: null,
                'sizes' => json_encode($sizes, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'colors' => json_encode($colors, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'images' => json_encode($images, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'is_new' => $form['is_new'], 'is_best_seller' => $form['is_best_seller'], 'is_sale' => $form['is_sale'],
                'in_stock' => $form['in_stock'], 'featured_in_lookbook' => $form['featured_in_lookbook'],
                'rating' => $form['rating'], 'review_count' => $form['review_count'],
            ];

            if ($product) {
                Product::update((int) $product['id'], $data);
            } else {
                Product::create($data);
            }

            flashSuccess($product ? 'Product updated.' : 'Product created.');
            redirect('/admin/products');
        }

        $this->showForm($product, $errors, $form, $images, $colors ?: [['name' => '', 'hex' => '#0f2e1b', 'image' => null]]);
    }

    private function showForm(?array $product, array $errors, array $form, array $images, array $colors): void
    {
        View::render('admin.products.form', [
            'pageTitle' => $product ? 'Edit Product' : 'Add Product',
            'activeNav' => 'product-form',
            'product' => $product,
            'errors' => $errors,
            'form' => $form,
            'existingImages' => $images,
            'existingColors' => $colors,
            'categories' => Category::allWithCounts(),
        ]);
    }
}
