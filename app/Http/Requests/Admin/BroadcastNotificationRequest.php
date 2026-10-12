<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BroadcastNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'user_ids' => ['required'],
            // user_ids can be "all" string or array of integers
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $userIds = $this->input('user_ids');

            if ($userIds !== 'all' && ! is_array($userIds)) {
                $validator->errors()->add('user_ids', 'user_ids must be "all" or an array of user IDs.');
            }
        });
    }
}
