@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 dark:border-none focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-black']) }}>
