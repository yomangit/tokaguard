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
                        <th>Tanggal</th>
                        <th>Jenis Entitas</th>
                        <th>Perusahaan</th>
                        <th>Departemen</th>
                        <th>Departemen Group</th>
                        <th>Job Class</th>
                        <th>Manhour</th>
                        <th>Manpower</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($manhours as $no => $manhour)
                    <tr>
                        <th>{{ $manhours->firstItem() + $no }}</th>
                        <td>{{ $manhour->date }}</td>
                        <td>{{ $manhour->company_category }}</td>
                        <td>{{ $manhour->company }}</td>
                        <td>{{ $manhour->department }}</td>
                        <td>{{ $manhour->dept_group }}</td>
                        <td>{{ $manhour->job_class }}</td>
                        <td>{{ $manhour->manhours }}</td>
                        <td>{{ $manhour->manpower }}</td>
                        <th class='flex justify-center flex-row gap-2'>
                            <flux:tooltip content="edit" position="top">
                                <flux:button wire:click="open_modal({{ $manhour->id }})" size="xs" icon="pencil-square" variant="subtle"></flux:button>
                            </flux:tooltip>
                            <flux:modal.trigger name="delete-bu">
                                <flux:tooltip content="hapus" position="top">
                                    <flux:button wire:click="showDelete({{ $manhour->id }})" size="xs" icon="trash" variant="danger"></flux:button>
                                </flux:tooltip>
                            </flux:modal.trigger>
                        </th>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $manhours->links() }}</div>
        <div class="modal {{ $modalOpen }}">
            <div class="modal-box max-w-4xl w-11/12 max-h-[90vh] md:max-h-[85vh] lg:max-h-[80vh] overflow-y-auto">
                <form wire:submit.prevent="store">
                    <fieldset class="fieldset bg-base-200 border-base-300 rounded-box border p-4 overflow-y-auto">
                        <legend class="fieldset-legend">Form Input Manhours & Manpower</legend>
                        {{-- Bulan --}}
                        <fieldset class="fieldset">
                            <x-form.label label="Bulan" required />
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
                                <input x-ref="input" type="text" wire:model.live="date" class="input input-bordered w-full md:max-w-md focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" placeholder="Pilih bulan" />
                            </div>
                            <x-label-error :messages="$errors->get('date')" />
                        </fieldset>

                        {{-- Kategori Perusahaan --}}
                        <fieldset class="fieldset">
                            <x-form.label label="Jenis Entitas" required />
                            <select wire:model.live="entity_type" class="select select-xs md:select-xs select-bordered w-full md:max-w-md focus:ring-1 focus:border-info focus:ring-info focus:outline-none">
                                <option value="">-- Pilih --</option>
                                <option value="owner">Perusahaan (Owner)</option>
                                <option value="contractor">Kontraktor</option>

                            </select>

                            <x-label-error :messages="$errors->get('entity_type')" />
                        </fieldset>

                        {{-- Perusahaan --}}
                        <fieldset class="fieldset">
                            <x-form.label label="perusahaan" required />
                            <select wire:model.live="company" class="select select-xs md:select-xs select-bordered w-full md:max-w-md focus:ring-1 focus:border-info focus:ring-info focus:outline-none">

                                @if ($entity_type==="owner")
                                <option value="">-- Pilih --</option>
                                @foreach ($bu as $company)
                                <option value="{{ $company->company_name }}">{{ $company->company_name }}</option>
                                @endforeach
                                @elseif($entity_type==="contractor")
                                <option value="">-- Pilih --</option>
                                @foreach ($cont as $co)
                                <option value="{{ $co->contractor_name }}">{{ $co->contractor_name }}</option>
                                @endforeach
                                @else
                                <option value="">-- Pilih --</option>
                                @endif

                            </select>
                            <x-label-error :messages="$errors->get('company')" />
                        </fieldset>
                        {{-- Departemen --}}
                        <fieldset class="fieldset">
                            <x-form.label label="department" required />
                            @if($entity_type ==="contractor")
                            <select wire:model.live="department" class="select select-xs md:select-xs select-bordered w-full md:max-w-md focus:ring-1 focus:border-info focus:ring-info focus:outline-none">
                                <option value="">-- Pilih --</option>
                                @foreach ($custodian as $cust)
                                <option value="{{ $cust->Departemen->department_name }}">{{ $cust->Departemen->department_name }}</option>
                                @endforeach
                            </select>
                            @else
                            <select wire:model.live="department" class="select select-xs md:select-xs select-bordered w-full md:max-w-md focus:ring-1 focus:border-info focus:ring-info focus:outline-none">
                                <option value="">-- Pilih --</option>
                                @foreach ($deptGroup as $dg)
                                <option value="{{ $dg->Departemen->department_name }}">{{ $dg->Departemen->department_name }}</option>
                                @endforeach
                            </select>
                            @endif
                            <x-label-error :messages="$errors->get('department')" />
                        </fieldset>
                        {{-- Job Class --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {{-- Supervisor --}}
                            <fieldset class="fieldset border border-base-300 p-3 rounded-lg">
                                <legend class="text-xs font-semibold flex gap-2">
                                   <span> Supervisor</span>
                                    <label class="flex items-center space-x-1">
                                        <input type="checkbox" wire:model.live="showOnlySelected" class="checkbox checkbox-xs">
                                        <span class="text-[8px] text-rose-500 capitalize">tidak ada Supervisor</span>
                                    </label>
                                </legend>
                                <fieldset class="fieldset">
                                    <x-form.label label="Manhours" required />
                                    <input type="number" wire:model.live="manhours_supervisor" placeholder="Masukkan manhours..." class="input input-bordered w-full input-xs focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden" />
                                    <x-label-error :messages="$errors->get('manhours_supervisor')" />
                                </fieldset>
                                <fieldset class="fieldset mt-2">
                                    <x-form.label label="Manpower" required />
                                    <input type="number" wire:model.live="manpower_supervisor" placeholder="Masukkan manpower..." class="input input-bordered w-full input-xs focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden" />
                                    <x-label-error :messages="$errors->get('manpower_supervisor')" />
                                </fieldset>
                            </fieldset>

                            {{-- Operational --}}
                            <fieldset class="fieldset border border-base-300 p-3 rounded-lg">
                                <legend class="text-sm font-semibold">Operational</legend>
                                <fieldset class="fieldset">
                                    <x-form.label label="Manhours" required />
                                    <input type="number" wire:model.live="manhours_operational" placeholder="Masukkan manhours..." class="input input-bordered w-full input-xs focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden" />
                                    <x-label-error :messages="$errors->get('manhours_operational')" />
                                </fieldset>
                                <fieldset class="fieldset mt-2">
                                    <x-form.label label="Manpower" required />
                                    <input type="number" wire:model.live="manpower_operational" placeholder="Masukkan manpower..." class="input input-bordered w-full input-xs focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden" />
                                    <x-label-error :messages="$errors->get('manpower_operational')" />
                                </fieldset>
                            </fieldset>

                            {{-- Administration --}}
                            <fieldset class="fieldset border border-base-300 p-3 rounded-lg">
                                <legend class="text-sm font-semibold">Administration</legend>
                                <fieldset class="fieldset">
                                    <x-form.label label="Manhours" required />
                                    <input type="number" wire:model.live="manhours_administration" placeholder="Masukkan manhours..." class="input input-bordered w-full input-xs focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden" />
                                    <x-label-error :messages="$errors->get('manhours_administration')" />
                                </fieldset>
                                <fieldset class="fieldset mt-2">
                                    <x-form.label label="Manpower" required />
                                    <input type="number" wire:model.live="manpower_administration" placeholder="Masukkan manpower..." class="input input-bordered w-full input-xs focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden" />
                                    <x-label-error :messages="$errors->get('manpower_administration')" />
                                </fieldset>
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
    </x-manhours.layout>



</section>
