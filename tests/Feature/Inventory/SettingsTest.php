<?php

beforeEach(function () {
    $this->user = $this->seed_and_getUser();
    login();
});

it('settings page created', function () {
    $response = test()->getJson(route('settings.index'));
    $response->assertStatus(200);
});

it('update settings', function () {
    $settings = test()->getJson(route('settings.index'))
        ->getOriginalContent()['settings'];

    test()->postJson(route('settings.index'), $settings->settings)
        ->assertStatus(200)
        ->assertJsonFragment(['type' => 'success']);
});

it('update company detail', function () {
    $company_detail = test()->getJson(route('settings.index'))
        ->getOriginalContent()['company_detail'];
    $company_detail = $company_detail->toArray();

    test()->postJson(route('settings.index') . '?' . http_build_query(['company_detail' => true]), $company_detail)
        ->assertStatus(200)
        ->assertJsonFragment(['type' => 'success']);
});

it('validate settings form', function (mixed $key) {
    $settings = test()->getJson(route('settings.index'))->getOriginalContent()['settings'];
    $settings = $settings->settings;

    $arr = explode('.', $key);

    for($i=0; $i<count($arr);$i++){
        unset($settings[$arr[$i]]);
    }

    test()->postJson(route('settings.index'), $settings)->assertStatus(422);
})->with('settings_key');


dataset('settings_key', [
    'site_name',
    'currency',
    'default_email',

    'design.topbar_color',
    'design.sidebar_color',

    'inventory.account_method',
    'inventory.profit_percent',
    'inventory.quantity_label',
    'inventory.shipping_cost_label',

    'inventory.sale.stock_out_sale',
    'inventory.sale.default_payment_mood',

    'inventory.purchase.default_payment_mood',
]);
