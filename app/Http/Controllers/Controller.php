<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * notFound: Manage Not Found response for the given lang name attribute    
     *
     * @param  mixed $labelName
     * @return void
     */
    protected function notFound($labelName)
    {
        $label = 'message.' . $labelName;
        return response404(__('message.not_found', ['name' => __($label)]));
    }
}
