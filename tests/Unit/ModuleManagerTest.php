<?php

namespace Tests\Unit;

use App\Modules\ModuleManager;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ModuleManagerTest extends TestCase
{
    private function manager(array $enabled): ModuleManager
    {
        return new ModuleManager([
            'volunteer' => ['enabled' => $enabled['volunteer'] ?? false, 'provider' => 'V', 'requires' => ['mail-forwarding', 'reference']],
            'mail-forwarding' => ['enabled' => false, 'provider' => 'M'],
            'reference' => ['enabled' => false, 'provider' => 'R'],
            'lkd-young' => ['enabled' => $enabled['lkd-young'] ?? false, 'provider' => 'L', 'requires' => ['mail-forwarding']],
        ]);
    }

    public function test_required_modules_are_enabled_with_the_module_that_needs_them(): void
    {
        $modules = $this->manager(['volunteer' => true]);

        $this->assertSame(['volunteer', 'mail-forwarding', 'reference'], $modules->enabledModules());
    }

    public function test_disabling_volunteer_disables_the_modules_only_it_needs(): void
    {
        $modules = $this->manager(['volunteer' => false]);

        $this->assertSame([], $modules->enabledModules());
    }

    public function test_a_module_stays_enabled_while_another_enabled_module_requires_it(): void
    {
        $modules = $this->manager(['volunteer' => false, 'lkd-young' => true]);

        $this->assertTrue($modules->enabled('mail-forwarding'));
        $this->assertFalse($modules->enabled('reference'));
    }

    public function test_env_style_boolean_strings_are_understood(): void
    {
        $this->assertFalse($this->manager(['volunteer' => 'false'])->enabled('volunteer'));
        $this->assertTrue($this->manager(['volunteer' => 'true'])->enabled('volunteer'));
    }

    public function test_unknown_required_module_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ModuleManager(['a' => ['enabled' => true, 'provider' => 'A', 'requires' => ['missing']]]);
    }
}
