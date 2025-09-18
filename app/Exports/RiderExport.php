<?php

namespace App\Exports;
use App\Helpers\General;
use App\Models\RiderActivities;
use App\Models\Riders;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

use Carbon\Carbon;

class RiderExport implements FromCollection, WithHeadings, WithMapping
{
  protected $month;

  public function __construct()
  {
    /* $this->id = $id;
    $this->month = $month; */
  }

  public function collection()
  {
    return Riders::get()/* (
[
'id',
'name',
'rider_id',
'status',
'ethnicity',
'designation',
'salary_model',
'visa_occupation',
'PID',
'emirate_id',
'personal_contact',
'company_contact',
]
) */ ; // select relevant columns
  }
  public function map($rider): array
  {
    return [
      $rider->id,
      $rider->rider_id,
      $rider->name ?? '', // prevent null error if rider missing
      General::RiderStatus($rider->status),
      $rider->ethnicity,
      $rider->designation,
      $rider->salary_model,
      $rider->visa_occupation,
      '',
      $rider->emirate_hub,
      $rider->personal_contact,
      $rider->company_contact,
      $rider->bikes?->plate,
      $rider->doj,
      $rider->dob,
      $rider->emirate_id,
      $rider->emirate_exp,
      $rider->country?->name,
      $rider->passport,
      $rider->passport_handover,
      $rider->cdm_deposit_id,
      $rider->personal_email,
      $rider->fleet_supervisor,
      $rider->wps,
      $rider->c3_card

    ];
  }

  public function headings(): array
  {
    return [
      'ID',
      'Rider ID',
      'Rider Name',
      'Status',
      'Ethnicity',
      'Designation',
      'Salary Model',
      'Occupation on Visa',
      'Project',
      'Emirate',
      'Personal Contact',
      'Company Contact',
      'Bike',
      'Joining Date',
      'DOB',
      'EID',
      'EID Expiry',
      'Nationality',
      'Passport No.',
      'Passport Handover Status',
      'CDM ID',
      'Email',
      'Fleet Supervisor',
      'WPS/NON WPS',
      'C3 Card'
    ];
  }
}
