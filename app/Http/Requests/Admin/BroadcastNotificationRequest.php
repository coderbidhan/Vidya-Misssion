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
            'body' => ['nullable', 'string', 'required_without:message'],
            'message' => ['nullable', 'string', 'required_without:body'],
            'user_ids' => ['nullable'],
            'target_type' => ['nullable', 'string', 'in:all,class,user'],
            'target_class' => ['nullable', 'integer'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $userIds = $this->input('user_ids');

            if ($userIds !== null && $userIds !== 'all' && ! is_array($userIds)) {
                $validator->errors()->add('user_ids', 'user_ids must be "all" or an array of user IDs.');
            }
        });
    }
}
