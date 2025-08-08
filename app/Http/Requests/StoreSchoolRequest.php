<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('super-admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:schools|regex:/^[A-Z0-9]+$/',
            'password' => 'required|string|min:8|confirmed',
            'email' => 'nullable|email|max:255|unique:schools,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:1000',
            'admin_name' => 'nullable|string|max:255',
            'admin_email' => 'nullable|email|max:255|unique:users,email',
            'admin_password' => 'nullable|string|min:8',
            'levels' => 'required|array|min:1',
            'levels.*' => 'required|string|in:school,college,bachelor',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'School name is required.',
            'code.regex' => 'School code must contain only uppercase letters and numbers.',
            'code.unique' => 'This school code is already taken.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Password confirmation does not match.',
            'email.unique' => 'This email address is already registered.',
            'admin_email.unique' => 'This admin email address is already registered.',
            'levels.required' => 'At least one educational level must be selected.',
            'levels.min' => 'At least one educational level must be selected.',
            'levels.*.in' => 'Invalid educational level selected.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'admin_name' => 'administrator name',
            'admin_email' => 'administrator email',
            'admin_password' => 'administrator password',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Auto-generate school code if not provided
        if (!$this->code) {
            $this->merge([
                'code' => $this->generateSchoolCode($this->name)
            ]);
        }

        // Set default admin credentials if not provided
        if (!$this->admin_name) {
            $this->merge(['admin_name' => 'School Administrator']);
        }

        if (!$this->admin_email) {
            $this->merge(['admin_email' => 'admin@' . strtolower($this->code) . '.school']);
        }

        // Set default levels if none selected
        if (!$this->levels) {
            $this->merge(['levels' => ['school', 'college']]);
        }
    }

    /**
     * Generate a unique school code from name
     */
    private function generateSchoolCode(string $name): string
    {
        $baseCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $name), 0, 3));
        $counter = 1;

        do {
            $code = $baseCode . str_pad($counter, 3, '0', STR_PAD_LEFT);
            $counter++;
        } while (\App\Models\School::where('code', $code)->exists());

        return $code;
    }
}
