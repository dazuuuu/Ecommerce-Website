<?php

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\View;
use App\Models\GalleryItem;
use App\Services\UploadException;
use App\Services\UploadService;

class GalleryController extends BaseAdminController
{
    public function index(): void
    {
        View::render('admin.gallery.index', [
            'pageTitle' => 'Gallery',
            'activeNav' => 'gallery',
            'items' => GalleryItem::all(),
            'errors' => [],
            'form' => $this->emptyForm(),
        ]);
    }

    public function store(): void
    {
        $errors = [];
        if (!csrfVerify(Request::post('csrf_token'))) {
            $errors[] = 'Your session expired. Please resubmit the form.';
        }

        $form = [
            'title' => trim((string) Request::post('title', '')),
            'description' => trim((string) Request::post('description', '')),
            'is_new' => Request::post('is_new') ? '1' : '',
            'new_arrival_until' => trim((string) Request::post('new_arrival_until', '')),
        ];

        $newArrivalUntil = null;
        if ($form['is_new'] === '1' && $form['new_arrival_until'] !== '') {
            $newArrivalUntil = $this->mysqlDatetimeValue($form['new_arrival_until']);
            if ($newArrivalUntil === null) {
                $errors[] = 'New arrival duration must be a valid date and time.';
            }
        }

        $files = $_FILES['gallery_images'] ?? null;
        if (!$files || empty($files['name'][0])) {
            $errors[] = 'Choose at least one gallery image.';
        }

        if (!$errors) {
            foreach ($files['name'] as $idx => $name) {
                if ($name === '') {
                    continue;
                }
                try {
                    $image = UploadService::store([
                        'name' => $files['name'][$idx],
                        'type' => $files['type'][$idx],
                        'tmp_name' => $files['tmp_name'][$idx],
                        'error' => $files['error'][$idx],
                        'size' => $files['size'][$idx],
                    ], 'gallery');

                    GalleryItem::create([
                        'image' => $image,
                        'title' => $form['title'] ?: null,
                        'description' => $form['description'] ?: null,
                        'is_new' => $form['is_new'] === '1' ? 1 : 0,
                        'new_arrival_until' => $form['is_new'] === '1' ? $newArrivalUntil : null,
                    ]);
                } catch (UploadException $e) {
                    $errors[] = $name . ': ' . $e->getMessage();
                }
            }
        }

        if (!$errors) {
            flashSuccess('Gallery images uploaded.');
            redirect('/admin/gallery');
        }

        View::render('admin.gallery.index', [
            'pageTitle' => 'Gallery',
            'activeNav' => 'gallery',
            'items' => GalleryItem::all(),
            'errors' => $errors,
            'form' => $form,
        ]);
    }

    public function update(string $id): void
    {
        $item = GalleryItem::find((int) $id);
        if (!$item) {
            redirect('/admin/gallery');
        }

        if (!csrfVerify(Request::post('csrf_token'))) {
            flashError('Your session expired. Please resubmit the form.');
            redirect('/admin/gallery');
        }

        $isNew = Request::post('is_new') ? '1' : '';
        $newArrivalUntil = null;
        $newArrivalInput = trim((string) Request::post('new_arrival_until', ''));
        if ($isNew === '1' && $newArrivalInput !== '') {
            $newArrivalUntil = $this->mysqlDatetimeValue($newArrivalInput);
            if ($newArrivalUntil === null) {
                flashError('New arrival duration must be a valid date and time.');
                redirect('/admin/gallery');
            }
        }

        GalleryItem::update((int) $id, [
            'title' => trim((string) Request::post('title', '')) ?: null,
            'description' => trim((string) Request::post('description', '')) ?: null,
            'is_new' => $isNew === '1' ? 1 : 0,
            'new_arrival_until' => $isNew === '1' ? $newArrivalUntil : null,
            'sort_order' => (int) Request::post('sort_order', 0),
        ]);

        flashSuccess('Gallery item updated.');
        redirect('/admin/gallery');
    }

    public function destroy(string $id): void
    {
        if (csrfVerify(Request::post('csrf_token'))) {
            $item = GalleryItem::find((int) $id);
            if ($item) {
                UploadService::delete($item['image']);
                GalleryItem::delete((int) $id);
                flashSuccess('Gallery image deleted.');
            }
        }
        redirect('/admin/gallery');
    }

    private function emptyForm(): array
    {
        return [
            'title' => '',
            'description' => '',
            'is_new' => '',
            'new_arrival_until' => '',
        ];
    }

    private function mysqlDatetimeValue(string $value): ?string
    {
        $timestamp = strtotime(str_replace('T', ' ', $value));
        return $timestamp ? date('Y-m-d H:i:s', $timestamp) : null;
    }
}
