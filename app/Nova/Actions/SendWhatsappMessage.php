<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

class SendWhatsappMessage extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $model) {
            // WhatsApp URL yaratmaq
            $phone = $model->phone;

            $phone = $model->phone;
            if (substr($phone, 0, 1) === '0') {
                $phone = '994' . substr($phone, 1);
            }

            $name = urlencode($model->name);
            $message = urlencode("Salam $name, bizim saytımız yenidir, burada elan yerləşdirin.");

            $url = "https://api.whatsapp.com/send/?phone=$phone&type=phone_number&app_absent=0&text=$message";

            $model->sent = true;
            $model->save();

            return Action::openInNewTab($url);
        }

        return Action::message('Mesaj göndərildi və status yeniləndi.');
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [];
    }
}