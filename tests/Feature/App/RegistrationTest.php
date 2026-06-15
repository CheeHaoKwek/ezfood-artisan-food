<?php

namespace Tests\Feature\App;

use App\Enums\DietaryPreference;
use App\Models\Outlet;
use App\Models\QrConfig;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    private QrConfig $qrConfig;

    protected function setUp(): void
    {
        parent::setUp();

        $this->qrConfig = QrConfig::factory()->create();
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Aisyah Binti Rahman',
            'nickname' => 'Aisyah',
            'mobile_number' => '012-3456789',
            'email' => 'aisyah@example.com',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
            'company_name' => 'NXP Semiconductors',
            'dietary_preference' => DietaryPreference::Vegetarian->value,
        ], $overrides);
    }

    public function test_subscriber_registers_via_tenant_link_and_is_associated_with_outlet(): void
    {
        $response = $this->post(
            route('app.auth.register', $this->qrConfig->code),
            $this->validPayload(),
        );

        $response->assertRedirect(route('app.entry', $this->qrConfig->code));
        $this->assertAuthenticated();

        $user = User::where('email', 'aisyah@example.com')->firstOrFail();
        $this->assertSame($this->qrConfig->outlet_id, $user->outlet_id);
        $this->assertSame($this->qrConfig->id, $user->registered_qr_config_id);
        $this->assertSame('Aisyah Binti Rahman', $user->name);
        $this->assertSame('Aisyah', $user->nickname);
        $this->assertSame('012-3456789', $user->mobile_number);
        $this->assertSame('NXP Semiconductors', $user->company_name);
        $this->assertSame(DietaryPreference::Vegetarian, $user->dietary_preference);
        $this->assertFalse($user->is_admin);
        $this->assertTrue(Hash::check('secret-password', $user->password));
    }

    public function test_registration_requires_every_field(): void
    {
        $response = $this->post(route('app.auth.register', $this->qrConfig->code), []);

        $response->assertSessionHasErrorsIn('register', [
            'name', 'nickname', 'mobile_number', 'email', 'password', 'company_name', 'dietary_preference',
        ]);
        $this->assertGuest();
        $this->assertSame(0, User::count());
    }

    public function test_registration_rejects_invalid_dietary_preference(): void
    {
        $response = $this->post(
            route('app.auth.register', $this->qrConfig->code),
            $this->validPayload(['dietary_preference' => 'pescatarian']),
        );

        $response->assertSessionHasErrorsIn('register', ['dietary_preference']);
        $this->assertGuest();
    }

    public function test_registration_rejects_duplicate_email(): void
    {
        User::factory()->create(['email' => 'aisyah@example.com']);

        $response = $this->post(
            route('app.auth.register', $this->qrConfig->code),
            $this->validPayload(),
        );

        $response->assertSessionHasErrorsIn('register', ['email']);
        $this->assertGuest();
    }

    public function test_registration_on_inactive_qr_config_returns_404(): void
    {
        $inactive = QrConfig::factory()->create(['is_active' => false]);

        $this->post(route('app.auth.register', $inactive->code), $this->validPayload())
            ->assertNotFound();
    }

    public function test_registration_on_inactive_outlet_returns_404(): void
    {
        $qrConfig = QrConfig::factory()
            ->for(Outlet::factory()->state(['is_active' => false]))
            ->create();

        $this->post(route('app.auth.register', $qrConfig->code), $this->validPayload())
            ->assertNotFound();
    }

    public function test_forged_outlet_id_in_payload_is_ignored(): void
    {
        $otherOutlet = Outlet::factory()->create();

        $this->post(
            route('app.auth.register', $this->qrConfig->code),
            $this->validPayload(['outlet_id' => $otherOutlet->id]),
        );

        $user = User::where('email', 'aisyah@example.com')->firstOrFail();
        $this->assertSame($this->qrConfig->outlet_id, $user->outlet_id);
    }

    public function test_login_validation_errors_land_in_login_bag(): void
    {
        User::factory()->create(['email' => 'aisyah@example.com']);

        $response = $this->post(route('app.auth.login', $this->qrConfig->code), [
            'email' => 'aisyah@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrorsIn('login', ['email']);
        $this->assertGuest();
    }

    public function test_login_with_valid_credentials_redirects_to_entry(): void
    {
        User::factory()->create(['email' => 'aisyah@example.com']);

        $response = $this->post(route('app.auth.login', $this->qrConfig->code), [
            'email' => 'aisyah@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('app.entry', $this->qrConfig->code));
        $this->assertAuthenticated();
    }
}
