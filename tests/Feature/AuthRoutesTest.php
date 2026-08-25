<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthRoutesTest extends TestCase
{
    public function test_guest_authentication_forms_are_available(): void
    {
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
        $this->get('/password/reset')->assertOk();
    }

    public function test_home_page_links_to_both_seminar_flows(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('LKD Genç')
            ->assertSee(route('join-lkd-young'), false)
            ->assertSee('Seminer Talepleri')
            ->assertSee(route('create-seminar-request'), false)
            ->assertSee(route('create-seminar-offer'), false)
            ->assertSee('LKD Temsilcilikleri')
            ->assertSee('https://www.lkd.org.tr/hakkimizda/temsilcilikler/', false)
            ->assertSee(route('representations.candidate'), false);
    }
}
