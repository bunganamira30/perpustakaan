@if(session('success'))
    <div role="alert" class="mb-4 flex items-start p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-900">
        <x-heroicon-o-check-circle class="w-5 h-5 text-emerald-600 mr-3 flex-shrink-0 mt-0.5" />
        <div class="text-sm font-medium">
            {{ session('success') }}
        </div>
    </div>
@endif

@if(session('error'))
    <div role="alert" class="mb-4 flex items-start p-4 rounded-lg bg-red-50 border border-red-200 text-red-900">
        <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-red-600 mr-3 flex-shrink-0 mt-0.5" />
        <div class="text-sm font-medium">
            {{ session('error') }}
        </div>
    </div>
@endif

@if(session('warning'))
    <div role="alert" class="mb-4 flex items-start p-4 rounded-lg bg-amber-50 border border-amber-200 text-amber-900">
        <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-amber-600 mr-3 flex-shrink-0 mt-0.5" />
        <div class="text-sm font-medium">
            {{ session('warning') }}
        </div>
    </div>
@endif

@if(session('info'))
    <div role="alert" class="mb-4 flex items-start p-4 rounded-lg bg-sky-50 border border-sky-200 text-sky-900">
        <x-heroicon-o-information-circle class="w-5 h-5 text-sky-600 mr-3 flex-shrink-0 mt-0.5" />
        <div class="text-sm font-medium">
            {{ session('info') }}
        </div>
    </div>
@endif

@if(isset($errors) && $errors->any())
    <div role="alert" class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200 text-red-900">
        <div class="flex items-center mb-2">
            <x-heroicon-o-exclamation-circle class="w-5 h-5 text-red-600 mr-2 flex-shrink-0" />
            <span class="text-sm font-semibold">Terdapat kesalahan validasi pada formulir:</span>
        </div>
        <ul class="list-disc list-inside text-xs space-y-1 text-red-800 ml-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
