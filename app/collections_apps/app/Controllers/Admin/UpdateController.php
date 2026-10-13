<?php

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\View;
use App\Services\MigrationService;

class UpdateController extends BaseAdminController
{
    public function index(): void
    {
        View::render('admin.updates.index', [
            'pageTitle' => 'Updates',
            'activeNav' => 'updates',
            'hasUpdate' => (bool) MigrationService::pending(),
            'version' => APP_VERSION,
        ]);
    }

    public function run(): void
    {
        if (!csrfVerify(Request::post('csrf_token'))) {
            flashError('Your session expired. Please try the update again.');
            redirect('/admin/updates');
        }

        try {
            $ran = MigrationService::runPending();
            flashSuccess($ran > 0 ? 'Updated to version ' . APP_VERSION . ' successfully.' : 'You are already on version ' . APP_VERSION . '.');
        } catch (\Throwable $e) {
            flashError('Update failed: ' . $e->getMessage());
        }

        redirect('/admin/updates');
    }
}
