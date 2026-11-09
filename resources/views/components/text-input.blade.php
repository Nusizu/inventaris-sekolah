@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-md border-gray-300 bg-white text-sm shadow-sm placeholder:text-gray-400 focus:border-brand-500 focus:ring-brand-500 disabled:bg-gray-100 disabled:text-gray-500']) }}>