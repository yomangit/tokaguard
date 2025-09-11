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
                        <fieldset class="fieldset relative">
                            <x-form.label label="Tanggal & Waktu" required />
                            <div class="relative" wire:ignore x-data="{
                            fp: null,
                            initFlatpickr() {
                                if (this.fp) this.fp.destroy();
                                this.fp = flatpickr(this.$refs.tanggalInput, {
                                    disableMobile: true,
                                    enableTime: true,
                                    
                                    defaultDate: this.$wire.entangle('tanggal').defer,
                                    clickOpens: true,
                                    
                                    appendTo: this.$refs.wrapper,
                                    onChange: (selectedDates, dateStr) => {
                                        this.$wire.set('tanggal', dateStr);
                                    }
                                });
                            }
                        }" x-ref="wrapper" x-init="
                            initFlatpickr();
                            Livewire.hook('message.processed', () => {
                                initFlatpickr();
                            });
                        ">
                                <input type="text" x-ref="tanggalInput" wire:model.live='tanggal' placeholder="Pilih Tanggal dan Waktu..." readonly class="input input-bordered cursor-pointer w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                            </div>
                            <x-label-error :messages="$errors->get('tanggal')" />
                        </fieldset>

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

</section>
