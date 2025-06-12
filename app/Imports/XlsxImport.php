<?php

namespace App\Imports;

use App\Models\Owner;
use App\Models\YourModel;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;
use Illuminate\Support\Facades\Log;

class XlsxImport implements OnEachRow
{
    protected $buffer = [];
    protected $batchSize = 50;

    public function onRow(Row $row)
    {
        $rowArray = $row->toArray();

        if (empty(array_filter($rowArray))) {
            // You can optionally log or just silently skip
            return;
        }
    

        $name_with_initial = $rowArray[2] ?? null;
        $address_line_1 = $rowArray[3] ?? null;
        $phone_number = $rowArray[4] ?? null;

        if ($name_with_initial && $address_line_1 && $phone_number) {
            $this->buffer[] = [
                'name_with_initial' => $name_with_initial,
                'address_line_1' => $address_line_1,
                'phone_number' => $phone_number,
                'password' => bcrypt($phone_number),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        } else {
            Log::warning('Skipping row with missing data.', $rowArray);
        }

        if (count($this->buffer) >= $this->batchSize) {
            $this->insertBuffer();
        }
    }

    // Insert remaining records after import finishes
    public function __destruct()
    {
        $this->insertBuffer();
    }

    protected function insertBuffer()
    {
        if (!empty($this->buffer)) {
            Owner::insert($this->buffer);
            Log::info('Inserted batch of ' . count($this->buffer) . ' records.');
            $this->buffer = [];
        }
    }
}
