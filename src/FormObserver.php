<?php

namespace Nickwest\EloquentForms;

use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Model;

class FormObserver
{
    /**
     * Fire the namespaced form event.
     *
     * @param  string $event
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return mixed
     */
    protected function fireFormEvent($event, Model $model)
    {
        return Event::until('form.'.$event, [$model]);
    }

    /**
     * Register the validation event for saving the model.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return bool
     */
    public function saving(Model $model)
    {
        // Fire the namespaced version event if hooked in client code
        if (! $model->validateOnSave() || $this->fireFormEvent('saving', $model) !== null) {
            return;
        }

        if ($model->isFormValid()) {
            // Fire the versioning.passed event.
            $this->fireFormEvent('passed', $model);
        } else {
            $this->fireFormEvent('skipped', $model);

            return false;
        }
    }
}
