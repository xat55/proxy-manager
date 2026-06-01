<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProxyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'ip' => 'sometimes|required|ip',
            'port' => 'sometimes|required|integer|min:1|max:65535',
            'type' => 'sometimes|required|in:http,https,socks4,socks5',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
        ];
    }
}
