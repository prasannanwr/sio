<?php

namespace App\Services;

class UserService
{
    public function exportDhlCsv()
    {
        $fileName = 'dhl_csv_template_' . time() . '.csv';

        $headers = [
            'Order Number',
            'Date',
            'To Name',
            'Destination Building',
            'Destination Street',
            'Destination Suburb',
            'Destination City',
            'Destination Postcode',
            'Destination State',
            'Destination Country',
            'Destination Email',
            'Destination Phone',
            'Item Name',
            'Item Price',
            'Instructions',
            'Weight',
            'Shipping Method',
            'Reference',
            'SKU',
            'Qty',
            'Company',
            'Signature Required',
            'ATL',
            'Country Code',
            'Package Height',
            'Package Width',
            'Package Length',
            'Carrier',
            'Carrier Product Code',
            'Carrier Product Unit Type',
            'Declared Value Currency',
            'Code',
            'Color',
            'Size',
            'Contents',
            'Dangerous Goods',
            'Country of Manufacturer',
            'DDP',
            'ReceiverVAT',
            'ReceiverEORI',
            'ShippingFreightValue',
            'Brand',
            'Usage',
            'Material',
            'Model',
            'MID Code',
            'Receiver National ID',
            'Receiver Passport Number',
            'Receiver USCI',
            'Receiver CR',
            'Receiver Brazil CNP',
        ];

        // Example data rows
        $rows = [
            [
                'IW8KR5TNAY',
                Carbon::now()->format('d/m/Y'),
                'Mary Jennings',
                '',
                '9 Brolga Ave',
                '',
                'Southport',
                '4215',
                'QLD',
                'Australia',
                'mary@example.com',
                '0400000000',
                'Shoes',
                '120',
                '',
                '1.5',
                'DHL Express',
                '',
                'SKU-001',
                '1',
                '',
                'Yes',
                'No',
                'AU',
                '10',
                '20',
                '30',
                'DHL',
                'P',
                'KG',
                'AUD',
                '',
                'Red',
                'M',
                'Clothing',
                'No',
                'Australia',
                'Yes',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ]
        ];

        // $callback = function () use ($headers, $rows) {
        //     $file = fopen('php://output', 'w');

        //     // Add BOM for Excel compatibility
        //     fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

        //     fputcsv($file, $headers);

        //     foreach ($rows as $row) {
        //         fputcsv($file, $row);
        //     }

        //     fclose($file);
        // };

        $path = storage_path('app/dhl_export.csv');
        $file = fopen($path, 'w');
        fputcsv($file, $headers);
        foreach ($rows as $row) {
            fputcsv($file, $row);
        }
        fclose($file);


        // return response()->stream($callback, 200, [
        //     "Content-Type"        => "text/csv",
        //     "Content-Disposition" => "attachment; filename=$fileName",
        // ]);
    }
}
