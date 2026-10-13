<?php

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Panel;

function panelFor(string $id): Panel
{
    return Filament::getPanel($id);
}

it('lets an active admin into /admin only', function () {
    $admin = User::factory()->admin()->create();

    expect($admin->canAccessPanel(panelFor('admin')))->toBeTrue()
        ->and($admin->canAccessPanel(panelFor('app')))->toBeFalse();
});

it('lets an active worker into /app only', function () {
    $worker = User::factory()->create();

    expect($worker->canAccessPanel(panelFor('app')))->toBeTrue()
        ->and($worker->canAccessPanel(panelFor('admin')))->toBeFalse();
});

it('keeps inactive users out of both panels', function (string $panel, bool $admin) {
    $user = User::factory()->inactive()->when($admin, fn ($factory) => $factory->admin())->create();

    expect($user->canAccessPanel(panelFor($panel)))->toBeFalse();
})->with([
    'inactive admin → admin' => ['admin', true],
    'inactive admin → app' => ['app', true],
    'inactive worker → admin' => ['admin', false],
    'inactive worker → app' => ['app', false],
]);

it('returns 403 when a worker opens /admin', function () {
    $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
});

it('returns 403 when an admin opens /app', function () {
    $this->actingAs(User::factory()->admin()->create())->get('/app')->assertForbidden();
});

it('returns 403 for inactive users on both panels', function (string $path) {
    $this->actingAs(User::factory()->inactive()->admin()->create())->get($path)->assertForbidden();
})->with(['/admin', '/app']);

it('lets users reach their own panel', function () {
    $this->actingAs(User::factory()->admin()->create())->get('/admin')->assertSuccessful();
    $this->actingAs(User::factory()->create())->get('/app')->assertSuccessful();
});
