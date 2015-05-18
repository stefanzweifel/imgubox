<?php

class RoutesTest extends TestCase {

	/** @test */
	public function it_loads_landingpages()
	{
		$this->visit('/')->andSee('Store your Imgur favorites to');
	}

	/** @test */
	public function it_loads_login_page()
	{
		$this->visit('auth/login');
	}

	/** @test */
	public function it_loads_register_page()
	{
		$this->visit('auth/register');
	}

	/** @test */
	public function it_loads_about_page()
	{
		$this->visit('about');
	}

	/** @test */
	public function it_loads_dashboard_page()
	{
		$user = ImguBox\User::first();
		Auth::login($user);

		$this->visit('/')->see('Setup');
	}

	/** @test */
	public function it_loads_settings_page()
	{
		$user = ImguBox\User::first();
		Auth::login($user);

		$this->visit('/settings')->see('Settings');
	}
}
