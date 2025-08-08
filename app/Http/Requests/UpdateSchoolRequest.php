<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSchoolRequest extends FormRequest
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
        $schoolId = $this->route('school')->id;

        return [
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Z0-9]+$/',
                Rule::unique('schools', 'code')->ignore($schoolId)
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('schools', 'email')->ignore($schoolId)
            ],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive,suspended',
            'password' => 'nullable|string|min:8',
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
            'code.required' => 'School code is required.',
            'code.regex' => 'School code must contain only uppercase letters and numbers.',
            'code.unique' => 'This school code is already taken.',
            'email.unique' => 'This email address is already registered.',
            'status.required' => 'School status is required.',
            'status.in' => 'School status must be active, inactive, or suspended.',
            'password.min' => 'Password must be at least 8 characters long.',
            'levels.required' => 'At least one educational level must be selected.',
            'levels.min' => 'At least one educational level must be selected.',
            'levels.*.in' => 'Invalid educational level selected.',
        ];
    }

    /**
     * Get the validated data with only changed fields
     */
    public function getChangedData(): array
    {
        $school = $this->route('school');
        $validated = $this->validated();
        $changed = [];

        foreach ($validated as $key => $value) {
            if ($key === 'password') {
                // Only include password if it's not empty
                if (!empty($value)) {
                    $changed[$key] = $value;
                }
            } elseif ($key === 'levels') {
                // Always include levels for comparison
                $changed[$key] = $value;
            } elseif ($key !== 'password' && isset($school->$key) && $school->$key !== $value) {
                $changed[$key] = $value;
            }
        }

        return $changed;
    }

    /**
     * Check if password is being updated
     */
    public function isPasswordUpdate(): bool
    {
        return !empty($this->password);
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure code is uppercase
        if ($this->code) {
            $this->merge([
                'code' => strtoupper($this->code)
            ]);
        }

        // Remove password field if it's empty to prevent accidental updates
        if (empty($this->password)) {
            $this->request->remove('password');
        }

        // Set default levels if none selected (shouldn't happen with frontend validation)
        if (!$this->levels) {
            $this->merge(['levels' => ['school', 'college']]);
        }
    }
}
