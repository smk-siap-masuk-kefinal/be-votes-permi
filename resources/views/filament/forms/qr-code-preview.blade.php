<div class="p-2">
    @php
        $path = $getState('qr_code');
        // dd($path);
    @endphp

    @if ($path)
        <img src="{{ Storage::url($path) }}"
        {{-- {{ dd($path) }} --}}
        alt="Barcode" 
        style="max-width: 200px; height: auto;"
        >
        <a href="{{ Storage::url($path) }}" target="_blank">Lihat QR Code</a>
    @else
        <div class="flex items-center justify-center h-40 bg-gray-100 rounded-lg dark:bg-gray-700">
            <span class="text-gray-500">Preview QR Code akan muncul di sini</span>
        </div>
    @endif
</div>
