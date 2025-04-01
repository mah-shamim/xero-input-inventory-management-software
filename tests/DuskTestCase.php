<?php
// @todo: purchase date format fix
// @todo export purchase
namespace Tests;

use App\Models\Inventory\Customer;
use App\Models\Payroll\Department;
use App\Models\Payroll\Employee;
use App\Models\Setting;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\TestCase as BaseTestCase;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Tests\Traits\SharedTestMethods;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication, SharedTestMethods;

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     */
    public static function prepare(): void
    {
        if (!static::runningInSail()) {
            static::startChromeDriver();
        }
    }

    /**
     * Create the RemoteWebDriver instance.
     */
    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments(collect([
            $this->shouldStartMaximized() ? '--start-maximized' : '--window-size=1920,1080',
        ])->unless($this->hasHeadlessDisabled(), function (Collection $items) {
            return $items->merge([
//                '--disable-gpu',
//                '--headless=new',
            ]);
        })->all());

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? 'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }

    /**
     * Determine whether the Dusk command has disabled headless mode.
     */
    protected function hasHeadlessDisabled(): bool
    {
        return isset($_SERVER['DUSK_HEADLESS_DISABLED']) ||
            isset($_ENV['DUSK_HEADLESS_DISABLED']);
    }

    /**
     * Determine if the browser window should start maximized.
     */
    protected function shouldStartMaximized(): bool
    {
        return isset($_SERVER['DUSK_START_MAXIMIZED']) ||
            isset($_ENV['DUSK_START_MAXIMIZED']);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
    }

    public function seed_and_getUser($user = null)
    {
        $existing_user = $this->get_login_user();
        if($existing_user) return $existing_user;

        $user = User::factory()->create([
            'name' => 'test123',
            'company_id' => $company = Company::factory()->create(),
            'email' => 'test_dusk@test.com',
            'email_verified_at' => now(),
            'password' => Hash::make('123123123'), // password
            'remember_token' => Str::random(10),
        ]);
        Setting::factory()->create([
            'company_id' => $company->id,
        ]);
        return $user;
    }

    public function get_login_user()
    {
        return User::where('email', 'test_dusk@test.com')->first();
    }

    public function signInUser(): static
    {
        auth()->loginUsingId($this->get_login_user()->id);
        return $this;
    }

    public function customer_seed($user = null)
    {
        if (!$user)
            $user = $this->get_login_user();

        return Customer::factory()->create([
            'company_id' => $user->company_id
        ]);
    }

    public function getEmployee_seed($user): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|Employee
    {
        return Employee::factory()->create([
            'company_id' => $user->company_id,
            'department_id' => Department::factory()->create(['company_id' => $user->company_id])->id
        ]);
    }
}