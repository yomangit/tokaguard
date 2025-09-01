<section class="w-full">
    <x-toast />
    <script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>
    @include('partials.event-general-head')
    @push('styles')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    @endpush
    <!-- name of each tab group should be unique -->
    <x-tabs-event.layout>

        @if (session()->has('message'))
        <div class="text-green-600">{{ session('message') }}</div>
        @elseif (session()->has('error'))
        <div class="text-red-600">{{ session('error') }}</div>
        @endif

        <div class="grid grid-cols-4 gap-2">
            <fieldset class="fieldset ">
                <label class="block">Pilih Moderator</label>
                <div class="relative">
                    <!-- Input Search -->
                    <input type="text" wire:model.live.debounce.300ms="searchModerator" placeholder="Pilih Moderator..." class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                    <!-- Dropdown hasil search -->
                    @if($showMpderatorDropdown && count($users) > 0)
                    <ul class="absolute z-10 bg-base-100 border rounded-md w-full mt-1 max-h-60 overflow-auto shadow">
                        <!-- Spinner ketika klik -->
                        <div wire:loading wire:target="selectModerator" class="p-2 text-center">
                            <span class="loading loading-spinner loading-sm text-secondary"></span>
                        </div>
                        @foreach($users as $user)
                        <li wire:click="selectModerator({{ $user->id }}, '{{ $user->name }}')" class="px-3 py-2 cursor-pointer hover:bg-base-200">
                            {{ $user->name }}
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
                <x-label-error :messages="$errors->get('user_id')" />
            </fieldset>

            <fieldset>
                <input id="department" value="department" wire:model="status" class="peer/department radio radio-xs radio-accent" type="radio" name="status" checked />
                <label for="department" class="peer-checked/department:text-accent">Departemen</label>

                <input id="company" value="company" wire:model="status" class="peer/company radio radio-xs radio-primary" type="radio" name="status" />
                <label for="company" class="peer-checked/company:text-primary">Kontraktor</label>

                <div class="hidden peer-checked/department:block mt-0.5">
                    {{-- Department --}}
                    <div class="relative mb-1">
                        <!-- Input Search -->
                        <input type="text" wire:model.live.debounce.300ms="searchDepartemen" placeholder="Cari departemen..." class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs " />
                        <!-- Dropdown hasil search -->
                        @if($showDepartemenDropdown && count($departments) > 0)
                        <ul class="absolute z-10 bg-base-100 border rounded-md w-full mt-1 max-h-60 overflow-auto shadow">
                            <!-- Spinner ketika klik salah satu -->
                            <div wire:loading wire:target="selectDepartment" class="p-2 text-center">
                                <span class="loading loading-spinner loading-sm text-secondary"></span>
                            </div>
                            @foreach($departments as $dept)
                            <li wire:click="selectDepartment({{ $dept->id }}, '{{ $dept->department_name }}')" class="px-3 py-2 cursor-pointer hover:bg-base-200">
                                {{ $dept->department_name }}
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                    @if($status === 'department')
                    <x-label-error :messages="$errors->get('department_id')" />
                    @endif
                </div>
                <div class="hidden peer-checked/company:block mt-0.5">
                    {{-- Contractor --}}
                    <div class="relative mb-1">
                        <!-- Input Search -->
                        <input type="text" wire:model.live.debounce.300ms="searchContractor" placeholder="Cari kontraktor..." class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                        <!-- Dropdown hasil search -->
                        @if($showContractorDropdown && count($contractors) > 0)
                        <ul class="absolute z-10 bg-base-100 border rounded-md w-full mt-1 max-h-60 overflow-auto shadow">
                            <!-- Spinner ketika klik -->
                            <div wire:loading wire:target="selectContractor" class="p-2 text-center">
                                <span class="loading loading-spinner loading-sm text-secondary"></span>
                            </div>
                            @foreach($contractors as $contractor)
                            <li wire:click="selectContractor({{ $contractor->id }}, '{{ $contractor->contractor_name }}')" class="px-3 py-2 cursor-pointer hover:bg-base-200">
                                {{ $contractor->contractor_name }}
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                    @if($status === 'company')
                    <x-label-error :messages="$errors->get('contractor_id')" />
                    @endif
                </div>
            </fieldset>
          
            <x-flux.select-searchable id="companyId" :wireModel="'companyId'" label="Perusahaan">
                <option value="">-- Perusahaan --</option>
                @foreach($companies as $co)
                <option value="{{ $co->id }}">{{ $co->company_name }}</option>
                @endforeach
            </x-flux.select-searchable>
        </div>

        <div class="mt-2">
            <flux:button size="xs" wire:click="assign" icon:trailing="add-icon" variant="primary">
                Tambah Moderator
            </flux:button>
        </div>
        <hr class="my-4">
        <input type="text" wire:model.live="search" placeholder="Cari nama moderator..." class="px-3 py-1 border rounded text-sm w-1/2 mb-2">
        <table class="table-auto w-full text-sm border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-2 py-1">User</th>
                    <th class="border px-2 py-1">Dept</th>
                    <th class="border px-2 py-1">Contractor</th>
                    <th class="border px-2 py-1">Company</th>
                    <th class="border px-2 py-1">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assignments as $mod)
                <tr>
                    <td class="border px-2">{{ $mod->user->name }}</td>
                    <td class="border px-2">{{ $mod->department->department_name ?? '-' }}</td>
                    <td class="border px-2">{{ $mod->contractor->contractor_name ?? '-' }}</td>
                    <td class="border px-2">{{ $mod->company->company_name ?? '-' }}</td>
                    <td class="border px-2">
                        <button wire:click="delete({{ $mod->id }})" class="text-red-500 hover:underline text-xs">
                            Hapus
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </x-tabs-event.layout>


</section>
