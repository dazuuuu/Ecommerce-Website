<?php

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\View;
use App\Models\StoreSetting;
use App\Services\UploadException;
use App\Services\UploadService;

class SettingsController extends BaseAdminController
{
    public function index(): void
    {
        View::render('admin.settings.index', [
            'pageTitle' => 'Settings',
            'activeNav' => 'settings',
            'settings' => StoreSetting::all(),
        ]);
    }

    public function update(): void
    {
        if (!csrfVerify(Request::post('csrf_token'))) {
            flashError('Your session expired. Please resubmit the form.');
            redirect('/admin/settings');
        }

        $storeName = trim((string) Request::post('store_name', ''));
        $contactPhone = trim((string) Request::post('contact_phone', ''));
        $contactEmail = trim((string) Request::post('contact_email', ''));
        $contactLocation = trim((string) Request::post('contact_location', ''));

        if ($storeName === '') {
            flashError('Store name is required.');
            redirect('/admin/settings');
        }
        if ($contactEmail !== '' && !filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
            flashError('Enter a valid contact email address.');
            redirect('/admin/settings');
        }

        StoreSetting::set('store_name', $storeName);
        StoreSetting::set('contact_phone', $contactPhone);
        StoreSetting::set('contact_email', $contactEmail);
        StoreSetting::set('contact_location', $contactLocation);

        $currentLogo = StoreSetting::get('store_logo');
        if (Request::post('remove_logo') && $currentLogo) {
            UploadService::delete($currentLogo);
            StoreSetting::set('store_logo', null);
            $currentLogo = null;
        }

        $file = Request::file('store_logo');
        if ($file && !empty($file['name'])) {
            try {
                $uploaded = UploadService::store($file, 'settings');
                if ($currentLogo) {
                    UploadService::delete($currentLogo);
                }
                StoreSetting::set('store_logo', $uploaded);
            } catch (UploadException $e) {
                flashError('Logo upload: ' . $e->getMessage());
                redirect('/admin/settings');
            }
        }

        flashSuccess('Store settings updated.');
        redirect('/admin/settings');
    }
}
