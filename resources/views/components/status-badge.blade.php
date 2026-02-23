@props(['status'])

@php
    $config = config("constants.status_config.{$status}", [
        'label' => $status,
        'bg_class' => 'bg-gray-100',
        'text_class' => 'text-gray-800',
        'border_class' => 'border-gray-300',
    ]);
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {$config['bg_class']} {$config['text_class']} {$config['border_class']}"]) }}>
    {{ $config['label'] }}
</span>