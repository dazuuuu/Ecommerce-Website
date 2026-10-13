<?php

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\View;
use App\Models\Ad;
use App\Services\UploadException;
use App\Services\UploadService;

class AdController extends BaseAdminController
{
    public function index(): void
    {
        View::render('admin.ads.index', [
            'pageTitle' => 'Ads',
            'activeNav' => 'ads',
            'ads' => Ad::all(),
            'now' => date('Y-m-d H:i:s'),
        ]);
    }

    public function create(): void
    {
        $this->showForm(null, [], $this->emptyForm());
    }

    public function edit(string $id): void
    {
        $ad = Ad::find((int) $id);
        if (!$ad) {
            redirect('/admin/ads');
        }
        $hours = max(1, (int) round((strtotime($ad['ends_at']) - strtotime($ad['starts_at'])) / 3600));
        $useDays = $hours % 24 === 0;
        $this->showForm($ad, [], [
            'title' => $ad['title'],
            'description' => (string) $ad['description'],
            'badge' => (string) $ad['badge'],
            'link_url' => (string) $ad['link_url'],
            'cta_label' => (string) $ad['cta_label'],
            'show_countdown' => $ad['show_countdown'] ? '1' : '',
            'countdown_label' => (string) $ad['countdown_label'],
            'countdown_to' => $ad['countdown_to'] ? date('Y-m-d\TH:i', strtotime($ad['countdown_to'])) : '',
            'starts_at' => date('Y-m-d\TH:i', strtotime($ad['starts_at'])),
            'duration_value' => $useDays ? $hours / 24 : $hours,
            'duration_unit' => $useDays ? 'days' : 'hours',
            'is_active' => $ad['is_active'] ? '1' : '',
        ]);
    }

    public function store(): void
    {
        $this->save(null);
    }

    public function update(string $id): void
    {
        $ad = Ad::find((int) $id);
        if (!$ad) {
            redirect('/admin/ads');
        }
        $this->save($ad);
    }

    public function toggle(string $id): void
    {
        if (csrfVerify(Request::post('csrf_token'))) {
            $ad = Ad::find((int) $id);
            if ($ad) {
                Ad::setActive((int) $id, !$ad['is_active']);
                flashSuccess($ad['is_active'] ? 'Ad paused.' : 'Ad resumed.');
            }
        }
        redirect('/admin/ads');
    }

    public function destroy(string $id): void
    {
        if (csrfVerify(Request::post('csrf_token'))) {
            $ad = Ad::find((int) $id);
            if ($ad) {
                UploadService::delete($ad['media_path']);
                Ad::delete((int) $id);
                flashSuccess('Ad deleted.');
            }
        }
        redirect('/admin/ads');
    }

    private function emptyForm(): array
    {
        return [
            'title' => '', 'description' => '', 'badge' => '', 'link_url' => '', 'cta_label' => '',
            'show_countdown' => '', 'countdown_label' => '', 'countdown_to' => '',
            'starts_at' => '', 'duration_value' => 3, 'duration_unit' => 'days', 'is_active' => '1',
        ];
    }

    private function save(?array $ad): void
    {
        $errors = [];
        if (!csrfVerify(Request::post('csrf_token'))) {
            $errors[] = 'Your session expired. Please resubmit the form.';
        }

        $form = [];
        foreach (['title', 'description', 'badge', 'link_url', 'cta_label', 'countdown_label', 'countdown_to', 'starts_at'] as $f) {
            $form[$f] = trim((string) Request::post($f, ''));
        }
        $form['show_countdown'] = Request::post('show_countdown') ? '1' : '';
        $form['is_active'] = Request::post('is_active') ? '1' : '';
        $form['duration_value'] = max(1, (int) Request::post('duration_value', 1));
        $form['duration_unit'] = Request::post('duration_unit') === 'hours' ? 'hours' : 'days';

        if ($form['title'] === '') {
            $errors[] = 'Title is required.';
        }
        if ($form['link_url'] !== '' && !preg_match('#^(https?://|/)#i', $form['link_url'])) {
            $errors[] = 'Button link must start with https://, http:// or /.';
        }

        $startTs = $form['starts_at'] !== '' ? strtotime(str_replace('T', ' ', $form['starts_at'])) : time();
        if (!$startTs) {
            $errors[] = 'Start time is not a valid date.';
            $startTs = time();
        }
        $seconds = $form['duration_value'] * ($form['duration_unit'] === 'days' ? 86400 : 3600);
        $endTs = $startTs + $seconds;

        $countdownTs = null;
        if ($form['show_countdown'] === '1' && $form['countdown_to'] !== '') {
            $countdownTs = strtotime(str_replace('T', ' ', $form['countdown_to']));
            if (!$countdownTs) {
                $errors[] = 'Countdown date is not valid.';
            }
        }

        $mediaPath = $ad['media_path'] ?? null;
        $mediaType = $ad['media_type'] ?? null;
        $file = Request::file('media');
        if ($file && !empty($file['name'])) {
            try {
                [$newPath, $newType] = UploadService::storeMedia($file, 'ads');
                UploadService::delete($mediaPath);
                $mediaPath = $newPath;
                $mediaType = $newType;
            } catch (UploadException $e) {
                $errors[] = 'Media upload: ' . $e->getMessage();
            }
        }
        if (Request::post('remove_media') && $mediaPath && empty($file['name'])) {
            UploadService::delete($mediaPath);
            $mediaPath = null;
            $mediaType = null;
        }

        if (!$errors) {
            $data = [
                'title' => $form['title'],
                'description' => $form['description'] ?: null,
                'badge' => $form['badge'] ?: null,
                'media_path' => $mediaPath,
                'media_type' => $mediaType,
                'link_url' => $form['link_url'] ?: null,
                'cta_label' => $form['cta_label'] ?: null,
                'show_countdown' => $form['show_countdown'] === '1' ? 1 : 0,
                'countdown_label' => $form['countdown_label'] ?: null,
                'countdown_to' => $countdownTs ? date('Y-m-d H:i:s', $countdownTs) : null,
                'starts_at' => date('Y-m-d H:i:s', $startTs),
                'ends_at' => date('Y-m-d H:i:s', $endTs),
                'is_active' => $form['is_active'] === '1' ? 1 : 0,
            ];
            if ($ad) {
                Ad::update((int) $ad['id'], $data);
            } else {
                Ad::create($data);
            }
            flashSuccess($ad ? 'Ad updated.' : 'Ad created. It will show to visitors between its start and end time.');
            redirect('/admin/ads');
        }

        $this->showForm($ad, $errors, $form);
    }

    private function showForm(?array $ad, array $errors, array $form): void
    {
        View::render('admin.ads.form', [
            'pageTitle' => $ad ? 'Edit Ad' : 'New Ad',
            'activeNav' => 'ads',
            'ad' => $ad,
            'errors' => $errors,
            'form' => $form,
        ]);
    }
}
