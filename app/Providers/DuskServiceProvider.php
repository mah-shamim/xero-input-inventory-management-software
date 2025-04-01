<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\Browser;

class DuskServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            return;
        }
        if (!\class_exists('\Laravel\Dusk\Browser', false)) {
            return;
        }
        Browser::macro('vAutocompleteWithSelector', function (string $element, string $inputSelector, string $text = null) {
            $this->click($element)
                ->type('input[name=' . $inputSelector . ']', $text)
                ->pause(1000)
                ->whenAvailable('.menuable__content__active', function ($dropDown) {
                    $dropDown
                        ->elements('.v-list-item')[0]
                        ->click();
                });
            return $this;
        });



        Browser::macro(/**
         * @param string $id must pass id from HTML
         * @return \App\Providers\DuskServiceProvider
         */ 'randomVSelect', function (string $id) {
            $this->click("{$id} .v-select")
                ->pause(100)
                ->waitFor('.v-menu__content.menuable__content__active')
                ->within('.menuable__content__active', function (Browser $dropDown) {
                    $count = 0;
                    $count = $dropDown
                        ->elements('.v-list-item');

                    $max= count($count)-1;
                    $value = rand(0, $max);


                    if(count($count)){
                        $dropDown->elements('.v-list-item .v-list-item__content')[intval($value)]
                            ->click();
                    }


                });
            return $this;
        });


        Browser::macro('pickADate', function (string $selector) {
            $this->click($selector)
                ->waitFor('.v-picker')
                ->pause(200)
                ->within('.v-date-picker-table', function (Browser $datepicker) {
                    $datepicker->click('.v-btn--active>.v-btn__content');
                });
            return $this;
        });

        Browser::macro('seeSweetAlertSuccessMessage', function (string $message) {
            $this->waitFor('.swal2-popup.swal2-modal.swal2-show')
                ->whenAvailable('.swal2-content', function ($success_dialog) use ($message) {
                    $success_dialog
                        ->waitFor('#swal2-content')
                        ->assertSee($message);
                });
            return $this;
        });

        Browser::macro('seeSweetAlertDeleteMessage', function (string $message) {
            $this->waitFor('.swal2-popup.swal2-modal.swal2-show')
                ->whenAvailable('.swal2-content', function ($delete_dialog) use ($message) {
                    $delete_dialog->assertSee($message);
                });
            return $this;
        });

        Browser::macro('actionDialogOpen', function (string $assertSee, string $button = '@create') {
            $this->click($button)
                ->waitFor('.v-dialog.v-dialog--active')
                ->whenAvailable('.v-dialog.v-dialog--active', function ($dialog) use ($assertSee) {
                    $dialog->assertSee($assertSee);
                });
            return $this;
        });

        Browser::macro('clickSwalDeleteButton', function (string $button = '@delete-0') {
            $this->click($button)
                ->waitFor('.swal2-popup.swal2-modal.swal2-show')
                ->whenAvailable('.swal2-actions', function ($dialog) {
                    $dialog->assertSee('Yes, delete it!')
                        ->press('.swal2-confirm.swal2-styled');
                });
            return $this;
        });

        Browser::macro('visitIndexPage', function (string $page, string $assertSee) {
            $this->visit($page)
                ->assertSee($assertSee)
                ->waitFor('tbody tr');

            return $this;
        });

        Browser::macro('clearVue', function ($selector) {
            $element = $this->resolver->format($selector);
            $this->script("document.querySelector('$element').value = ''");
            $this->script("document.querySelector('$element').dispatchEvent(new Event('input'))");

            return $this;
        });

        Browser::macro('clearVueAndNew', function ($selector, $value) {
            $element = $this->resolver->format($selector);
            $this->script("document.querySelector('$element').value = ''");
            $this->script("document.querySelector('$element').dispatchEvent(new Event('input'))");
            $this->pause(100);
            $this->type($selector, $value);

            return $this;
        });

    }
}
