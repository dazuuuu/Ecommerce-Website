<?php

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\View;
use App\Models\StoreSetting;
use App\Models\Admin;
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
            'admins' => Admin::all(),
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
        $whatsappNumber = trim((string) Request::post('whatsapp_number', ''));
        // Search consoles hand out a whole <meta> tag; accept that or just its content value.
        $verification = function (string $field): string {
            $value = trim((string) Request::post($field, ''));
            if (preg_match('/content=["\']([^"\']+)["\']/i', $value, $m)) {
                $value = $m[1];
            }
            return preg_replace('/[^A-Za-z0-9_\-]/', '', $value);
        };

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
        StoreSetting::set('whatsapp_number', $whatsappNumber);
        StoreSetting::set('google_site_verification', $verification('google_site_verification'));
        StoreSetting::set('bing_site_verification', $verification('bing_site_verification'));

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

    public function addAdmin(): void
    {
        if (!csrfVerify(Request::post('csrf_token'))) {
            flashError('Your session expired. Please resubmit the form.');
            redirect('/admin/settings');
        }

        $email = trim((string) Request::post('email', ''));
        $password = (string) Request::post('password', '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flashError('Please provide a valid email address.');
            redirect('/admin/settings');
        }
        if (strlen($password) < 6) {
            flashError('Password must be at least 6 characters.');
            redirect('/admin/settings');
        }

        if (Admin::findByEmail($email)) {
            flashError('An administrator with this email already exists.');
            redirect('/admin/settings');
        }

        Admin::create($email, $password);
        flashSuccess('New administrator added successfully.');
        redirect('/admin/settings');
    }

    public function deleteAdmin(string $id): void
    {
        if (!csrfVerify(Request::post('csrf_token'))) {
            flashError('Your session expired. Please resubmit the form.');
            redirect('/admin/settings');
        }

        $adminId = (int) $id;
        $currentAdminId = \App\Core\AdminSession::id();

        if ($adminId === $currentAdminId) {
            flashError('You cannot delete your own administrator account.');
            redirect('/admin/settings');
        }

        $admin = Admin::findById($adminId);
        if (!$admin) {
            flashError('Administrator not found.');
            redirect('/admin/settings');
        }

        Admin::delete($adminId);
        flashSuccess('Administrator removed successfully.');
        redirect('/admin/settings');
    }
}
