<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProxyRequest extends FormRequest
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
            'ip' => 'required|ip',
            'port' => 'required|integer|min:1|max:65535',
            'type' => 'required|in:http,https,socks4,socks5',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
        ];
    }
}
