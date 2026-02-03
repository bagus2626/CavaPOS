<label class="relative flex items-start justify-between cursor-pointer rounded-lg border border-gray-200 bg-white p-4 transition-all hover:border-[#ae1504]/50 hover:bg-gray-50 has-[:checked]:border-[#ae1504] has-[:checked]:bg-[#ae1504]/5">
    <div class="flex items-start gap-3 min-w-0">
        <div class="mt-0.5 h-5 w-5 shrink-0 aspect-square flex items-center justify-center rounded-full border border-gray-300 self-start">
            <div class="h-2.5 w-2.5 rounded-full bg-[#ae1504] opacity-0 transition-opacity peer-checked:opacity-100"></div>
        </div>

        <div class="min-w-0">
            <div class="text-gray-900 font-medium text-sm">
                {{ $opt['label'] }}
            </div>

            @if(!empty($opt['desc']))
                {{-- penting: jangan pakai truncate --}}
                <div class="text-xs text-gray-500 mt-0.5 whitespace-normal break-words leading-snug">
                    {{ $opt['desc'] }}
                </div>
            @endif
        </div>
    </div>

    <input
        class="hidden peer"
        name="payment"
        type="radio"
        value="{{ $opt['key'] }}"
        @if(!empty($opt['manual_id'])) data-manual-id="{{ $opt['manual_id'] }}" @endif
    />
</label>
