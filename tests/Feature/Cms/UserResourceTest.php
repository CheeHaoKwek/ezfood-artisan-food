<?php

namespace Tests\Feature\Cms;

use App\Enums\DietaryPreference;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    public function test_admin_sees_subscriber_with_outlet_association(): void
    {
        $outlet = Outlet::factory()->create(['name' => 'NXP Factory Penang']);
        $subscriber = User::factory()->create([
            'outlet_id' => $outlet->id,
            'dietary_preference' => DietaryPreference::Vegetarian->value,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListUsers::class)
            ->assertCanSeeTableRecords([$subscriber])
            ->assertCanRenderTableColumn('outlet.name');
    }

    public function test_admin_can_filter_users_by_outlet(): void
    {
        $outlet = Outlet::factory()->create();
        $otherOutlet = Outlet::factory()->create();
        $subscriber = User::factory()->create(['outlet_id' => $outlet->id]);
        $otherSubscriber = User::factory()->create(['outlet_id' => $otherOutlet->id]);

        Livewire::actingAs($this->admin)
            ->test(ListUsers::class)
            ->filterTable('outlet_id', $outlet->id)
            ->assertCanSeeTableRecords([$subscriber])
            ->assertCanNotSeeTableRecords([$otherSubscriber]);
    }

    public function test_non_admin_cannot_access_cms_users(): void
    {
        $subscriber = User::factory()->create();

        $this->actingAs($subscriber)
            ->get('/cms/users')
            ->assertForbidden();
    }
}
