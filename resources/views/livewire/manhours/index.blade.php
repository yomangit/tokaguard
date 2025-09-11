<section class="w-full">
    <x-toast />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>

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
        <div class="modal-box max-w-4xl">
            <form wire:submit.prevent="save">
                <fieldset class="fieldset bg-base-200 border-base-300 rounded-box border p-4 overflow-y-auto">
                    <legend class="fieldset-legend">Form Input Manhours & Manpower</legend>
                    {{-- Bulan --}}
                    <div x-data="{
                            fp: null,
                            initFlatpickr() {
                                this.fp = flatpickr(this.$refs.input, {
                                    plugins: [
                                        new monthSelectPlugin({
                                            disableMobile: true,
                                            shorthand: true,  // Jan, Feb, ...
                                            dateFormat: 'm-Y', // format yang dikirim ke Livewire
                                            altFormat: 'F Y',  // format yang ditampilkan ke user (September 2025)
                                            theme: 'light'
                                        })
                                    ],
                                    onChange: (selectedDates, dateStr) => {
                                        $wire.set('date', dateStr)
                                    }
                                })
                            }
                        }" x-init="initFlatpickr()" x-effect="if($wire.date) fp.setDate($wire.date, true)" wire:ignore>
                        <input x-ref="input" type="text" wire:model.live="date" class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" placeholder="Pilih bulan" />
                         <x-label-error :messages="$errors->get('date')" />
                    </div>

                    {{-- Kategori Perusahaan --}}
                    <div>
                        <x-form.label label="Kategori Perusahaan" required />
                        <input type="text" wire:model.live="company_category" placeholder="Masukkan kategori perusahaan..." class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                        <x-label-error :messages="$errors->get('company_category')" />
                    </div>

                    {{-- Departemen --}}
                    <div>
                        <x-form.label label="Departemen" required />
                        <input type="text" wire:model.live="department" placeholder="Masukkan nama departemen..." class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                        <x-label-error :messages="$errors->get('department')" />
                    </div>

                    {{-- Job Class --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Supervisor --}}
                        <fieldset class="fieldset border border-base-300 p-3 rounded-lg">
                            <legend class="text-sm font-semibold">Supervisor</legend>
                            <div>
                                <x-form.label label="Manhours" required />
                                <input type="number" wire:model.live="manhours_supervisor" placeholder="Masukkan manhours..." class="input input-bordered w-full input-xs focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden" />
                                <x-label-error :messages="$errors->get('manhours_supervisor')" />
                            </div>
                            <div class="mt-2">
                                <x-form.label label="Manpower" required />
                                <input type="number" wire:model.live="manpower_supervisor" placeholder="Masukkan manpower..." class="input input-bordered w-full input-xs focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden" />
                                <x-label-error :messages="$errors->get('manpower_supervisor')" />
                            </div>
                        </fieldset>

                        {{-- Operational --}}
                        <fieldset class="fieldset border border-base-300 p-3 rounded-lg">
                            <legend class="text-sm font-semibold">Operational</legend>
                            <div>
                                <x-form.label label="Manhours" required />
                                <input type="number" wire:model.live="manhours_operational" placeholder="Masukkan manhours..." class="input input-bordered w-full input-xs focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden" />
                                <x-label-error :messages="$errors->get('manhours_operational')" />
                            </div>
                            <div class="mt-2">
                                <x-form.label label="Manpower" required />
                                <input type="number" wire:model.live="manpower_operational" placeholder="Masukkan manpower..." class="input input-bordered w-full input-xs focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden" />
                                <x-label-error :messages="$errors->get('manpower_operational')" />
                            </div>
                        </fieldset>

                        {{-- Administration --}}
                        <fieldset class="fieldset border border-base-300 p-3 rounded-lg">
                            <legend class="text-sm font-semibold">Administration</legend>
                            <div>
                                <x-form.label label="Manhours" required />
                                <input type="number" wire:model.live="manhours_administration" placeholder="Masukkan manhours..." class="input input-bordered w-full input-xs focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden" />
                                <x-label-error :messages="$errors->get('manhours_administration')" />
                            </div>
                            <div class="mt-2">
                                <x-form.label label="Manpower" required />
                                <input type="number" wire:model.live="manpower_administration" placeholder="Masukkan manpower..." class="input input-bordered w-full input-xs focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden" />
                                <x-label-error :messages="$errors->get('manpower_administration')" />
                            </div>
                        </fieldset>
                    </div>
                </fieldset>

                {{-- Tombol Aksi --}}
                <div class="flex justify-end gap-2 mt-4">
                    <flux:button size="xs" variant="danger" wire:click="$set('modalOpen', false)">Batal</flux:button>
                    <flux:button size="xs" variant="primary" type="submit">Simpan</flux:button>
                </div>
            </form>
        </div>
    </div>



</section>
