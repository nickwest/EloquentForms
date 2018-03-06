<?php namespace Nickwest\EloquentForms;

use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Support\Facades\Event;

class FormObserver {

    /**
     * Fire the namespaced form event
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
     * @return boolean
     */
    public function saving(Model $model)
    {
        // Fire the namespaced version event if hooked in client code
        if ($this->fireFormEvent('saving', $model) !== null) {
            return;
        }

        if($model->isValid()) {
            // Fire the versioning.passed event.
            $this->fireFormEvent('passed', $model);
        } else {
            $this->fireFormEvent('skipped', $model);

            return false;
        }

    }

}
