<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('listing') === null || $this->user()->can('update', $this->route('listing'));
    }

    public function rules(): array
    {
        $isUpdate = $this->route('listing') !== null;

        return [
            'title' => ['required', 'string', 'max:200'],
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')],
            'price' => ['required', 'integer', 'min:0', 'max:9223372036854775807'],
            'item_condition' => ['required', Rule::in(['new', 'like_new', 'good', 'fair'])],
            'description' => ['required', 'string', 'max:10000'],
            'location' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'images' => [$isUpdate ? 'nullable' : 'required_without:image', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'delete_image_ids' => ['nullable', 'array'],
            'delete_image_ids.*' => ['integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'images.required_without' => 'Wajib mengunggah minimal satu foto barang.',
            'images.max' => 'Maksimal 5 foto barang yang dapat diunggah.',
            'images.*.image' => 'File harus berupa foto yang valid.',
            'images.*.mimes' => 'Format foto harus berupa JPG, PNG, atau WebP.',
            'images.*.max' => 'Ukuran foto maksimal 2 MB per file.',
            'image.image' => 'File harus berupa foto yang valid.',
            'image.mimes' => 'Format foto harus berupa JPG, PNG, atau WebP.',
            'image.max' => 'Ukuran foto maksimal 2 MB.',
        ];
    }
}
