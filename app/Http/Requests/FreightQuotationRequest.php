<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FreightQuotationRequest extends FormRequest
{
    public const MODE_AIR = 'Air';
    public const MODE_SEA = 'Sea';
    public const MODE_ROAD = 'Road';

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'freight_mode' => ['required_without:mode', 'string', Rule::in($this->acceptedModes())],
            'mode' => ['required_without:freight_mode', 'string', Rule::in($this->acceptedModes())],
            'pickup_location_id' => ['required', 'integer'],
            'destination_location_id' => ['required', 'integer'],
            'weight' => ['required', 'numeric', 'min:0.1'],
            'length' => ['nullable', 'numeric', 'min:0'],
            'width' => ['nullable', 'numeric', 'min:0'],
            'height' => ['nullable', 'numeric', 'min:0'],
            'cargo_value' => ['nullable', 'numeric', 'min:0'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'vehicle_type' => ['nullable', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'freight_mode.in' => 'The freight mode must be Air, Sea, or Road.',
            'mode.in' => 'The mode must be Air, Sea, or Road.',
        ];
    }

    public function freightMode(): string
    {
        $mode = $this->input('freight_mode', $this->input('mode'));

        return ucfirst(strtolower($mode));
    }

    protected function acceptedModes(): array
    {
        return [
            self::MODE_AIR,
            self::MODE_SEA,
            self::MODE_ROAD,
            strtolower(self::MODE_AIR),
            strtolower(self::MODE_SEA),
            strtolower(self::MODE_ROAD),
            strtoupper(self::MODE_AIR),
            strtoupper(self::MODE_SEA),
            strtoupper(self::MODE_ROAD),
        ];
    }
}
