<?php

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\View;
use App\Services\MigrationService;

class MigrationController extends BaseAdminController
{
    public function index(): void
    {
        View::render('admin.migrations.index', [
            'pageTitle' => 'Migrations',
            'activeNav' => 'migrations',
            'pendingMigrations' => MigrationService::pending(),
            'appliedMigrations' => MigrationService::appliedRecords(),
        ]);
    }

    public function run(): void
    {
        if (!csrfVerify(Request::post('csrf_token'))) {
            flashError('Your session expired. Please try the migration again.');
            redirect('/admin/migrations');
        }

        try {
            $ran = MigrationService::runPending();
            flashSuccess($ran > 0
                ? "Migrated successfully. Ran {$ran} migration(s)."
                : 'Everything is already up to date.');
        } catch (\Throwable $e) {
            flashError('Migration failed: ' . $e->getMessage());
        }

        redirect('/admin/migrations');
    }
}
