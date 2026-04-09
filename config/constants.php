<?php

return [
    /*
    |--------------------------------------------------------------------------
    | File Status Constants
    |--------------------------------------------------------------------------
    |
    | Define the workflow statuses for file lifecycle tracking
    |
    */
    'file_statuses' => [
        'CHECK_IN' => 'CHECK_IN',
        'QC' => 'QC',
        'ACCOUNTING' => 'ACCOUNTING',
        'ACCOUNTING_APPROVED' => 'ACCOUNTING_APPROVED',
        'SHIPPING' => 'SHIPPING',
        'RECORDING' => 'RECORDING',
        'RETURN' => 'RETURN',
        'CLOSED' => 'CLOSED',
    ],

    /*
    |--------------------------------------------------------------------------
    | Status Flow Rules
    |--------------------------------------------------------------------------
    |
    | Define allowed status transitions
    |
    */
    'status_transitions' => [
        'CHECK_IN' => ['QC'],
        'QC' => ['ACCOUNTING', 'CHECK_IN'], // Can go back to CHECK_IN if rejected
        'ACCOUNTING' => ['ACCOUNTING_APPROVED', 'QC'], // Can go back to QC if issues found
        'ACCOUNTING_APPROVED' => ['SHIPPING', 'ACCOUNTING'], // Can go back to ACCOUNTING if changes needed
        'SHIPPING' => ['RECORDING'],
        'RECORDING' => ['RETURN'],
        'RETURN' => ['CLOSED'],
        'CLOSED' => [], // Terminal state
    ],

    /*
    |--------------------------------------------------------------------------
    | Status Display Configuration
    |--------------------------------------------------------------------------
    |
    | Colors and labels for status badges
    |
    */
    'status_config' => [
        'CHECK_IN' => [
            'label' => 'Check In',
            'color' => 'blue',
            'bg_class' => 'bg-blue-100',
            'text_class' => 'text-blue-800',
            'border_class' => 'border-blue-300',
        ],
        'QC' => [
            'label' => 'Quality Control',
            'color' => 'yellow',
            'bg_class' => 'bg-yellow-100',
            'text_class' => 'text-yellow-800',
            'border_class' => 'border-yellow-300',
        ],
        'ACCOUNTING' => [
            'label' => 'Accounting',
            'color' => 'purple',
            'bg_class' => 'bg-purple-100',
            'text_class' => 'text-purple-800',
            'border_class' => 'border-purple-300',
        ],
        'ACCOUNTING_APPROVED' => [
            'label' => 'Fees Approved',
            'color' => 'emerald',
            'bg_class' => 'bg-emerald-100',
            'text_class' => 'text-emerald-800',
            'border_class' => 'border-emerald-300',
        ],
        'SHIPPING' => [
            'label' => 'Shipping',
            'color' => 'indigo',
            'bg_class' => 'bg-indigo-100',
            'text_class' => 'text-indigo-800',
            'border_class' => 'border-indigo-300',
        ],
        'RECORDING' => [
            'label' => 'Recording',
            'color' => 'pink',
            'bg_class' => 'bg-pink-100',
            'text_class' => 'text-pink-800',
            'border_class' => 'border-pink-300',
        ],
        'RETURN' => [
            'label' => 'Return',
            'color' => 'orange',
            'bg_class' => 'bg-orange-100',
            'text_class' => 'text-orange-800',
            'border_class' => 'border-orange-300',
        ],
        'CLOSED' => [
            'label' => 'Closed',
            'color' => 'green',
            'bg_class' => 'bg-green-100',
            'text_class' => 'text-green-800',
            'border_class' => 'border-green-300',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | QC Results
    |--------------------------------------------------------------------------
    */
    'qc_results' => [
        'PASS' => 'Pass',
        'FAIL' => 'Fail',
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit Event Types
    |--------------------------------------------------------------------------
    */
    'audit_events' => [
        'FILE_CREATED' => 'File Created',
        'FILE_UPDATED' => 'File Updated',
        'STATUS_CHANGED' => 'Status Changed',
        'QC_PERFORMED' => 'QC Performed',
        'FEE_CALCULATED' => 'Fee Calculated',
        'FEE_APPROVED' => 'Fee Approved',
        'DOCUMENT_ATTACHED' => 'Document Attached',
        'BULK_IMPORT' => 'Bulk Import',
    ],
];
