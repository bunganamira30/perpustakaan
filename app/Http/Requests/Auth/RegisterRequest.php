<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'min:3', 'max:150'],
            'username' => ['required', 'string', 'alpha_dash', 'min:4', 'max:30', 'unique:users,username'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:72', 'confirmed'],
            'phone' => ['required', 'string', 'regex:/^[0-9+\-\s]{10,15}$/'],
            'address' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Custom validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'full_name.min' => 'Nama lengkap minimal 3 karakter.',
            'username.required' => 'Nama pengguna (username) wajib diisi.',
            'username.alpha_dash' => 'Nama pengguna hanya boleh memuat huruf, angka, tanda hubung, dan garis bawah.',
            'username.min' => 'Nama pengguna minimal 4 karakter.',
            'username.max' => 'Nama pengguna maksimal 30 karakter.',
            'username.unique' => 'Nama pengguna sudah terdaftar, silakan pilih nama lain.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email sudah digunakan pada akun lain.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal terdiri dari :min karakter.',
            'password.max' => 'Kata sandi tidak boleh melebihi :max karakter (batas pemotongan algoritma hashing).',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'phone.required' => 'Nomor telepon wajib diisi untuk data anggota.',
            'phone.regex' => 'Format nomor telepon tidak valid (masukkan 10-15 digit angka).',
        ];
    }
}
