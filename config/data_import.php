<?php

return [
    'orders' => [
        'label' => 'Import Orders',
        'permission_required' => 'import-orders',
        'files' => [
            'file1' => [
                'label' => 'Orders - File 1',
                'table_name' => 'orders',
                'headers_to_db' => [
                    'Order Date' => [
                        'db_column' => 'order_date',
                        'type' => 'date',
                        'validation' => ['required']
                    ],
                    'Channel' => [
                        'db_column' => 'channel',
                        'type' => 'string',
                        'validation' => ['required', 'in:PT,Amazon,eBay',]
                    ],
                    'SKU' => [
                        'db_column' => 'sku',
                        'type' => 'string',
                        'validation' => ['required', 'exists:products,sku']
                    ],
                    'Item Description' => [
                        'db_column' => 'item_description',
                        'type' => 'string',
                        'validation' => ['nullable']
                    ],
                    'Origin' => [
                        'db_column' => 'origin',
                        'type' => 'string',
                        'validation' => ['required']
                    ],
                    'SO#' => [
                        'db_column' => 'so_num',
                        'type' => 'string',
                        'validation' => ['required']
                    ],
                    'Cost' => [
                        'db_column' => 'cost',
                        'type' => 'double',
                        'validation' => ['required']
                    ],
                    'Shipping Cost' => [
                        'db_column' => 'shipping_cost',
                        'type' => 'double',
                        'validation' => ['required']
                    ],
                    'Total Price' => [
                        'db_column' => 'total_price',
                        'type' => 'double',
                        'validation' => ['required']
                    ],
                ],
                'update_or_create' => ['so_num', 'sku'],
            ],
        ],
    ],
    'products_and_inventory' => [
        'label' => 'Products & Inventory',
        'permission_required' => 'import-products',
        'files' => [
            'file1' => [
                'label' => 'Products - File 1',
                'table_name' => 'products',
                'headers_to_db' => [
                    'SKU' => [
                        'db_column' => 'sku',
                        'type' => 'string',
                        'validation' => ['required']
                    ],
                    'Name' => [
                        'db_column' => 'name',
                        'type' => 'string',
                        'validation' => ['required']
                    ],
                    'Description' => [
                        'db_column' => 'description',
                        'type' => 'string',
                        'validation' => ['nullable']
                    ],
                    'Manufacturer' => [
                        'db_column' => 'manufacturer',
                        'type' => 'string',
                        'validation' => ['required']
                    ],
                ],
                'update_or_create' => ['sku'],
            ],
            'file2' => [
                'label' => 'Inventory - File 2',
                'table_name' => 'inventories',
                'headers_to_db' => [
                    'SKU' => [
                        'db_column' => 'sku',
                        'type' => 'string',
                        'validation' => ['required', 'exists:products,sku']
                    ],
                    'Stock Level' => [
                        'db_column' => 'stock_level',
                        'type' => 'integer',
                        'validation' => ['required']
                    ],
                    'Reserved Stock' => [
                        'db_column' => 'reserved_stock',
                        'type' => 'integer',
                        'validation' => ['required']
                    ],
                    'Available Stock' => [
                        'db_column' => 'available_stock',
                        'type' => 'integer',
                        'validation' => ['required']
                    ],
                    'Unit Cost' => [
                        'db_column' => 'unit_cost',
                        'type' => 'integer',
                        'validation' => ['required']
                    ],
                ],
                'update_or_create' => ['sku'],
            ],
        ],
    ],
];
