<?php

namespace ImguBox\Http\Requests;

class PurgeFavoriteRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return (auth()->check()) && ($this->logs->user->id == $this->user()->id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }

    public function forbiddenResponse()
    {
        return redirect()->back()->withError("Nope. You don't have access to do that.");
    }
}
