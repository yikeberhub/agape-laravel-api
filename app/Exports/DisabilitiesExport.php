<?php

namespace App\Exports;

use App\Models\Disability;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class DisabilitiesExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
{
    protected $filters;
    protected $columns;

    public function __construct(array $filters = [], array $columns = [])
    {
        $this->filters = $filters;
        $this->columns = $columns;
    }

    public function collection()
    {
        $query = Disability::with(['warrant', 'recorder', 'equipment','equipment.type','equipment.subType']);

        if (!empty($this->filters)) {
            foreach ($this->filters as $field => $value) {
                $query->where($field, $value);
            }
        }
        return $query->get();

    }

    public function map($disability): array
    {
        $row = [];
        foreach ($this->columns as $col) {
            $row[] = match ($col) {
                'id' => $disability->id,
                'first_name' => $disability->first_name,
                'middle_name' => $disability->middle_name,
                'last_name' => $disability->last_name,
                'gender' => $disability->gender,
                'date_of_birth' => $disability->date_of_birth,
                'phone_number' => $disability->phone_number,
                'region' => $disability->region,
                'zone' => $disability->zone,
                'city' => $disability->city,
                'woreda' => $disability->woreda,
                'hip_width' => $disability->hip_width,
                'backrest_height' => $disability->backrest_height,
                'thigh_length' => $disability->thigh_length,
                'profile_image' => $disability->profile_image,
                'id_image' => $disability->id_image,
                'is_provided' => $disability->is_provided ? 'Yes' : 'No',
                'is_active' => $disability->is_active ? 'Yes':'No',
                'is_deleted' => $disability->is_deleted,
                'created_at'=> $disability->created_at,
                'updated_at' => $disability->updated_at,

                // Warrant
                'warrant_first_name' => $disability->warrant->first_name ?? '',
                'warrant_middle_name' => $disability->warrant->middle_name ?? '',
                'warrant_last_name' => $disability->warrant->last_name ?? '',
                'warrant_phone_number' => $disability->warrant->phone_number ?? '',
                'warrant_gender' => $disability->warrant->gender ?? '',
                'warrant_id_image' => $disability->warrant->id_image ?? '',

                // Recorder
                'recorder_first_name' => $disability->recorder->first_name ?? '',
                'recorder_last_name' => $disability->recorder->last_name ?? '',
                'recorder_role' => $disability->recorder->role ?? '',
                'recorder_phone_number' => $disability->recorder->phone_number ?? '',

                // Equipment
                'equipment_type' => $disability->equipment->type->name ?? '',
                'equipment_subType' => $disability->equipment->subType->name ?? '',
                'equipment_size' => $disability->equipment->size ?? '',
                'equipment_cause_of_need' => $disability->equipment->cause_of_need ?? '',

                default => '',
            };
        }

        return $row;
    }

    public function headings(): array
    {
        $headings = [];
        foreach ($this->columns as $col) {
            $headings[] = match ($col) {
                'id' => 'ID',
                'first_name' => 'First Name',
                'middle_name' => 'Middle Name',
                'last_name' => 'Last Name',
                'gender' => 'Gender',
                'date_of_birth' => 'Date of Birth',
                'phone_number' => 'Phone Number',
                'region' => 'Region',
                'zone' => 'Zone',
                'city' => 'City',
                'woreda' => 'Woreda',
                'hip_width' => 'Hip Width',
                'backrest_height' => 'Backrest Height',
                'thigh_length' => 'Thigh Length',
                'profile_image' => 'Profile Image',
                'id_image' => 'ID Image',
                'is_provided' => 'Is Provided',
                'is_active' => 'Is Active',
                'is_deleted' => 'Is Deleted',
                'created_at' => 'Created At',
                'updated_at'=>'Updated At',

                // Warrant
                'warrant_first_name' => 'Warrant First Name',
                'warrant_middle_name' => 'Warrant Middle Name',
                'warrant_last_name' => 'Warrant Last Name',
                'warrant_phone_number' => 'Warrant Phone Number',
                'warrant_gender' => 'Warrant Gender',
                'warrant_id_image' => 'Warrant ID Image',

                // Recorder
                'recorder_name' => 'Recorder Name',
                'recorder_role' => 'Recorder Role',
                'recorder_phone_number' => 'Recorder Phone',

                // Equipment
                'equipment_type' => 'Equipment Type',
                'equipment_subType' => 'Equipment sub Type',
                'equipment_size' => 'Equipment Size',
                'equipment_cause_of_need' => 'Equipment Cause Of Need',

                default => ucfirst(str_replace('_', ' ', $col)),
            };
        }
        return $headings;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',',
            'enclosure' => '"',
            'line_ending' => "\r\n", 
            'use_bom' => true,
        ];
    }
    
}
