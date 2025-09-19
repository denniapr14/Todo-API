<?php

namespace App\Exports;

use App\Models\Todo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TodosExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $todos;

    public function __construct($todos)
    {
        $this->todos = $todos;
    }

    public function collection()
    {
        $data = $this->todos->map(function ($todo, $index) {
            return [
                $index + 1,
                $todo->title,
                $todo->assignee,
                $todo->description,
                $todo->due_date,
                $todo->status,
                $todo->priority,
                $todo->created_at,
                $todo->updated_at,
            ];
        });

        // Add summary row
        $summaryRow = [
            '',
            'Total',
            $this->todos->count(),
            '',
            '',
            '',
            '',
            '',
            '',
        ];

        $data->push($summaryRow);

        return $data;
    }

    public function headings(): array
    {
        return [
            '#',
            'Title',
            'Assignee',
            'Description',
            'Due Date',
            'Status',
            'Priority',
            'Created At',
            'Updated At',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style for header row
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFDDDDDD',
                ],
            ],
        ]);

        // Style for summary row
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('A' . $highestRow . ':I' . $highestRow)->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFEEEEEE',
                ],
            ],
        ]);

        return [];
    }
}
