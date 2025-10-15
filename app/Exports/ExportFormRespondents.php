<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportFormRespondents implements FromCollection, WithHeadings
{
  protected $rows;
  protected $headers;

  public function __construct($rows, $headers = [])
  {
    $this->rows = $rows;
    $this->headers = $headers;
  }

  /**
   * @return \Illuminate\Support\Collection
   */
  public function collection()
  {
    return collect($this->rows);
  }

  public function headings(): array
  {
    return $this->headers;
  }
}
