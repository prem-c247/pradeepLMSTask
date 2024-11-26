<?php

namespace App\Http\Controllers;

abstract class Controller
{
    // Manage Not Found response for the given data lang name attribute
    protected function notFound($labelName)
    {
        $label = 'message.' . $labelName;
        return response404(__('message.not_found', ['name' => __($label)]));
    }
}
