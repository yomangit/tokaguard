<section class="w-full">
    <x-toast />
    <script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>
    @include('partials.manhours')
    <div class="flex justify-between">
        <!-- You can open the modal using ID.showModal() method -->
        <flux:tooltip content="tambah data" position="top">
            <flux:button size="xs" wire:click='open_modal' icon="add-icon" variant="primary"></flux:button>
        </flux:tooltip>
    </div>
    <x-manhours.layout>
        <div class="overflow-x-auto ">
            <table class="table table-xs">
                <thead>
                    <tr>
                        <th></th>
                        <th>Name</th>
                        <th>Job</th>
                        <th>company</th>
                        <th>location</th>
                        <th>Last Login</th>
                        <th>Favorite Color</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>1</th>
                        <td>Cy Ganderton</td>
                        <td>Quality Control Specialist</td>
                        <td>Littel, Schaden and Vandervort</td>
                        <td>Canada</td>
                        <td>12/16/2020</td>
                        <td>Blue</td>
                    </tr>
                    <tr>
                        <th>2</th>
                        <td>Hart Hagerty</td>
                        <td>Desktop Support Technician</td>
                        <td>Zemlak, Daniel and Leannon</td>
                        <td>United States</td>
                        <td>12/5/2020</td>
                        <td>Purple</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-manhours.layout>
    <div class="modal {{ $modalOpen }}">
        <div class="modal-box ">
            <form class='grid justify-items-stretch'>
                @csrf
                <fieldset class="fieldset bg-base-200 border-base-300 rounded-box border p-4 max-w-full sm:max-w-md md:max-w-2xl lg:max-w-4xl mx-auto">
                    <legend class="fieldset-legend">Input Manhours</legend>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        {{-- Tanggal --}}
                        <div>
                            <x-label-req>{{ __('Tanggal') }} </x-label-req>
                            <x-text-input wire:model.live='date' :error="$errors->get('date')" type="text" placeholder="Date" id="myDatepicker" class="w-full" />
                            <x-label-error :messages="$errors->get('date')" />
                        </div>

                        {{-- Nama Perusahaan --}}
                        <div>
                            <x-label-req>{{ __('Nama perusahaan') }} </x-label-req>
                            <flux:dropdown class="btn btn-xs btn-outline btn-info w-full" position="bottom" align="start">
                                <flux:navlist.search icon:trailing="chevrons-up-down" wire:navigate>{{ $company_name }}</flux:navlist.search>
                                <flux:menu class="w-full md:w-96">
                                    <flux:input size="xs" icon="magnifying-glass" wire:model.live='search_company' placeholder="Cari Perusahaan" class="w-full" />
                                    <flux:menu.separator />
                                    <flux:menu.radio.group>
                                        @foreach ($Companies as $company)
                                        <flux:menu.radio wire:click='id_company({{ $company->id }})' wire:navigate>
                                            {{ $company->company_name }}
                                        </flux:menu.radio>
                                        @endforeach
                                    </flux:menu.radio.group>
                                </flux:menu>
                            </flux:dropdown>
                            <x-label-error :messages="$errors->get('company_id')" />
                        </div>

                        {{-- Nama Department --}}
                        <div>
                            <x-label-req>{{ __('Nama Department') }} </x-label-req>
                            <flux:dropdown class="btn btn-xs btn-outline btn-info w-full" position="bottom" align="start">
                                <flux:navlist.search icon:trailing="chevrons-up-down" wire:navigate>{{ $department_name ?? 'Pilih Department' }}</flux:navlist.search>
                                <flux:menu class="w-full md:w-96">
                                    <flux:input size="xs" icon="magnifying-glass" wire:model.live='search_department' placeholder="Cari Department" class="w-full" />
                                    <flux:menu.separator />
                                    <flux:menu.radio.group>
                                        @foreach ($Departments as $department)
                                        <flux:menu.radio wire:click='id_department({{ $department->id }})' wire:navigate>
                                            {{ $department->department_name }}
                                        </flux:menu.radio>
                                        @endforeach
                                    </flux:menu.radio.group>
                                </flux:menu>
                            </flux:dropdown>
                            <x-label-error :messages="$errors->get('department_id')" />
                        </div>

                        {{-- Job Class --}}
                        <div>
                            <x-label-req>{{ __('Job Class') }} </x-label-req>
                            <flux:dropdown class="btn btn-xs btn-outline btn-info w-full" position="bottom" align="start">
                                <flux:navlist.search icon:trailing="chevrons-up-down" wire:navigate>{{ $job_class ?? 'Pilih Job Class' }}</flux:navlist.search>
                                <flux:menu class="w-full md:w-96">
                                    <flux:menu.radio.group>
                                        <flux:menu.radio wire:click="$set('job_class','Supervisor')" wire:navigate>Supervisor</flux:menu.radio>
                                        <flux:menu.radio wire:click="$set('job_class','Operational')" wire:navigate>Operational</flux:menu.radio>
                                        <flux:menu.radio wire:click="$set('job_class','Administrator')" wire:navigate>Administrator</flux:menu.radio>
                                    </flux:menu.radio.group>
                                </flux:menu>
                            </flux:dropdown>
                            <x-label-error :messages="$errors->get('job_class')" />
                        </div>
                    </div>
                </fieldset>

                <div class="modal-action">
                    <flux:button size="xs" wire:click='store' icon="save-icon" variant="primary">Save</flux:button>
                    <flux:button size="xs" wire:click='close_modal' icon="close-icon" variant="danger">Close</flux:button>
                </div>
            </form>
        </div>
    </div>
    <script>
        var picker = new Pikaday({
            field: document.getElementById('myDatepicker')
            , format: 'MM-YYYY'
            , toString(date, format) {
                const month = ("0" + (date.getMonth() + 1)).slice(-2); // 01, 02, ...
                const year = date.getFullYear();
                var tgl = month + '-' + year;
                @this.set('date', tgl);
                return `${month}-${year}`;
            }
            , parse(dateString, format) {
                const parts = dateString.split('-');
                const month = parseInt(parts[0], 10) - 1;
                const year = parseInt(parts[1], 10);
                return new Date(year, month, 1);
            },
            // Optional: biar tidak pilih hari
            onSelect: function(date) {
                const month = ("0" + (date.getMonth() + 1)).slice(-2);
                const year = date.getFullYear();
                picker.hide(); // tutup langsung setelah pilih bulan
                @this.set('date', month + '-' + year);
            }
        });

    </script>

    <style>
        /* Sembunyikan tampilan hari */
        .pika-single .pika-lendar table {
            display: none;
        }

    </style>
</section>
