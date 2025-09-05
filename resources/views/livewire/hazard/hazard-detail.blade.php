<!-- resources/views/livewire/hazard-list.blade.php -->
<section class="w-full">
    <x-toast />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <div class="card bg-base-100 shadow-md mb-2 ">
        <div class="card-body py-2 px-4 ">
            {{-- STATUS --}}
            <div class=" flex gap-2">
                <label class="label">
                    <span class="label-text text-xs font-semibold">Status :</span>
                </label>
                <span class="text-green-600 italic text-xs capitalize">
                    {{ $hazards->status }}
                </span>
            </div>
            @php
            $isDisabled = in_array(optional($hazards)->status, ['cancelled', 'closed']);
            @endphp
            <div class="flex items-stretch gap-2">
                {{-- PROCEED TO --}}
                <div class="max-w-sm">
                    <label class="label">
                        <span class="label-text text-xs font-semibold">Lanjutkan Ke</span>
                    </label>
                    <select wire:model.live="proceedTo" class="select select-xs select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden">
                        <option value="">-- Pilih Aksi --</option>
                        @foreach ($availableTransitions as $label => $status)
                        <option value="{{ $status }}">
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- PILIH ERM --}}
                @if ($proceedTo === 'in_progress')
                <div class="max-w-sm">
                    <label class="label">
                        <span class="label-text font-semibold  text-xs">Pilih ERM Utama</span>
                    </label>
                    <select wire:model="assignTo1" class="select select-xs select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden">
                        <option value="">-- Pilih --</option>
                        @foreach ($ermList as $erm)
                        <option value="{{ $erm['id'] }}">{{ $erm['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="max-w-sm">
                    <label class="label">
                        <span class="label-text font-semibold  text-xs">Pilih ERM Tambahan (Opsional)</span>
                    </label>
                    <select wire:model="assignTo2" class="select select-xs select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden">
                        <option value="">-- Pilih --</option>
                        @foreach ($ermList as $erm)
                        <option value="{{ $erm['id'] }}">{{ $erm['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- TOMBOL SIMPAN --}}
                <div class="card-actions justify-end self-end mt-1">
                    <div x-data="{ proceedTo: @entangle('proceedTo') }" class="card-actions justify-end">
                        <div class="tooltip ">
                            <div class="tooltip-content z-40">
                                <div class="animate-bounce text-orange-400  text-sm font-black">Kirim</div>
                            </div>
                            <flux:button size="xs" wire:click="processAction" icon:trailing="send" variant="primary"></flux:button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    {{ $isDisabled }}
    <form wire:submit.prevent="submit">
        <div class="w-full bg-base-200 p-1 rounded mb-2">
            <flux:button size="xs" type="submit" icon:trailing="save" variant="primary">Simpan</flux:button>
            <flux:button size="xs" icon:trailing="trash" variant="danger">Hapus</flux:button>
        </div>
        <x-tab-hazard.layout>
            <div wire:loading.class="skeleton animate-pulse" wire:target="submit">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                    <fieldset class="fieldset">
                        <label class="block">Tipe Bahaya</label>
                        <select wire:model.live="tipe_bahaya" class="select select-xs select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden">
                            <option value="">-- Pilih --</option>
                            @foreach ($eventTypes as $et )
                            <option value="{{ $et->id }}">{{ $et->event_type_name }}</option>
                            @endforeach
                        </select>
                        <x-label-error :messages="$errors->get('tipe_bahaya')" />
                    </fieldset>
                    <fieldset class="fieldset">
                        <label class="block">Sub Tipe Bahaya</label>
                        <select wire:model.live="sub_tipe_bahaya" class="select select-xs select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden">
                            <option value="">-- Pilih --</option>
                            @if ($tipe_bahaya)
                            @foreach ($subTypes as $et )
                            <option value="{{ $et->id }}">{{ $et->event_sub_type_name }}</option>
                            @endforeach
                            @endif

                        </select>
                        <x-label-error :messages="$errors->get('sub_tipe_bahaya')" />
                    </fieldset>

                    <fieldset class="fieldset ">
                        <label class="block">Dilaporkan Oleh</label>
                        <div class="relative">
                            <!-- Input Search -->
                            <input type="text" wire:model.live.debounce.300ms="searchPelapor" placeholder="Cari Nama Pelapor..." class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                            <!-- Dropdown hasil search -->
                            @if($showPelaporDropdown && count($pelapors) > 0)
                            <ul class="absolute z-10 bg-base-100 border rounded-md w-full mt-1 max-h-60 overflow-auto shadow">
                                <!-- Spinner ketika klik -->
                                <div wire:loading wire:target="selectPelapor" class="p-2 text-center">
                                    <span class="loading loading-spinner loading-sm text-secondary"></span>
                                </div>
                                @foreach($pelapors as $pelapor)
                                <li wire:click="selectPelapor({{ $pelapor->id }}, '{{ $pelapor->name }}')" class="px-3 py-2 cursor-pointer hover:bg-base-200">
                                    {{ $pelapor->name }}
                                </li>
                                @endforeach
                            </ul>
                            @endif
                        </div>
                        <x-label-error :messages="$errors->get('pelapor_id')" />
                    </fieldset>

                    <fieldset>
                        <input id="department" value="department" wire:model="deptCont" class="peer/department radio radio-xs radio-accent" type="radio" name="deptCont" checked />
                        <label for="department" class="peer-checked/department:text-accent">Departemen</label>

                        <input id="company" value="company" wire:model="deptCont" class="peer/company radio radio-xs radio-primary" type="radio" name="deptCont" />
                        <label for="company" class="peer-checked/company:text-primary">Kontraktor</label>

                        <div class="hidden peer-checked/department:block mt-0.5">
                            {{-- Department --}}
                            <div class="relative mb-1">
                                <!-- Input Search -->

                                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari departemen..." class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs " />
                                <!-- Dropdown hasil search -->
                                @if($showDropdown && count($departments) > 0)
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
                            @if($deptCont === 'department')
                            <x-label-error :messages="$errors->get('department_id')" />
                            @endif
                        </div>
                        <div class="hidden peer-checked/company:block mt-1">
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
                            @if($deptCont === 'company')
                            <x-label-error :messages="$errors->get('contractor_id')" />
                            @endif
                        </div>
                    </fieldset>
                    <fieldset class="fieldset">
                        <label class="block">Penanggung Jawab Area</label>
                        <select wire:model.live="penanggungJawab" class="select select-xs select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden">
                            <option value="">-- Pilih --</option>
                            @foreach($penanggungJawabOptions as $pj)
                            <option value="{{ $pj['id'] }}">{{ $pj['name'] }}</option>
                            @endforeach
                        </select>
                        <x-label-error :messages="$errors->get('penanggungJawab')" />
                    </fieldset>

                    <fieldset class="fieldset ">
                        <label class="block">Lokasi</label>
                        <div class="relative">
                            <!-- Input Search -->
                            <input type="text" wire:model.live.debounce.300ms="searchLocation" placeholder="Cari Lokasi..." class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                            <!-- Dropdown hasil search -->
                            @if($showLocationDropdown && count($locations) > 0)
                            <ul class="absolute z-10 bg-base-100 border rounded-md w-full mt-1 max-h-60 overflow-auto shadow">
                                <!-- Spinner ketika klik -->
                                <div wire:loading wire:target="selectLocation" class="p-2 text-center">
                                    <span class="loading loading-spinner loading-sm text-secondary"></span>
                                </div>
                                @foreach($locations as $loc)
                                <li wire:click="selectLocation({{ $loc->id }}, '{{ $loc->name }}')" class="px-3 py-2 cursor-pointer hover:bg-base-200">
                                    {{ $loc->name }}
                                </li>
                                @endforeach
                            </ul>
                            @endif
                        </div>
                        <x-label-error :messages="$errors->get('location_id')" />
                    </fieldset>

                    {{-- Lokasi spesifik muncul hanya jika lokasi utama sudah dipilih --}}
                    @if($location_id)
                    <fieldset class="fieldset">
                        <label class="block">Lokasi Spesifik</label>
                        <input type="text" wire:model.live="location_specific" placeholder="Masukkan detail lokasi spesifik..." class=" input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                        <x-label-error :messages="$errors->get('location_specific')" />
                    </fieldset>
                    @endif

                    <fieldset class=" fieldset" x-data x-init="
                            flatpickr($refs.tanggalInput, {
                                disableMobile: true,       // aktifkan jam
                                enableTime: true,       // aktifkan jam
                                dateFormat: 'd-m-Y H:i', // format untuk Livewire
                                onChange: function(selectedDates, dateStr) {
                                    @this.set('tanggal', dateStr);
                                }
                            });
                            ">
                        <label class="block">Tanggal dan Waktu</label>
                        <input type="text" x-ref="tanggalInput" placeholder="Pilih Tanggal dan Waktu..." wire:model.live='tanggal' readonly class="input input-bordered cursor-pointer w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                        <x-label-error :messages="$errors->get('tanggal')" />
                    </fieldset>

                </div>
                <fieldset class="fieldset mb-4">
                    <label class="block">Deskripsi</label>

                    <div wire:ignore>
                        <textarea id="ckeditor-description">{{ $description }}</textarea>
                    </div>
                    <!-- Hidden input untuk binding Livewire -->
                    <input type="hidden" wire:model.live="description" id="description">
                    <x-label-error :messages="$errors->get('description')" />
                </fieldset>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4 ">
                    <fieldset class=" fieldset">
                        <label class="block">Dokumentasi Sebelum Tindakan perbaikan langsung</label>
                        <label wire:ignore for="upload-deskripsi" class="flex items-center gap-2 cursor-pointer border border-info rounded  hover:ring-1 hover:border-info hover:ring-info hover:outline-hidden">
                            <!-- Tombol custom -->
                            <span class="btn btn-info btn-xs">
                                Pilih file atau gambar
                            </span>
                            <!-- Nama file -->
                            <span id="file-name" class="text-[9px] text-gray-500 truncate max-w-sm">
                                {!! $new_doc_deskripsi? $new_doc_deskripsi->getClientOriginalName():$doc_deskripsi !!}
                            </span>
                        </label>
                        @if ($new_doc_deskripsi)
                        <div class="text-xs text-green-600">Preview file baru:</div>
                        <img src="{{ $new_doc_deskripsi->temporaryUrl() }}" class="h-24 rounded border mt-1">
                        @elseif($doc_deskripsi)
                        <div class="text-xs text-gray-600">File lama:</div>
                        <img src="{{ asset('storage/' . $doc_deskripsi) }}" class="h-24 rounded border mt-1">
                        @else
                        <span class="text-xs text-gray-400">Belum ada file</span>
                        @endif
                        <!-- Input asli (disembunyikan) -->
                        <input id="upload-deskripsi" wire:model.live='new_doc_deskripsi' type="file" class="hidden" onchange="document.getElementById('file-name').textContent = this.files[0]?.name ?? 'Belum ada file'" />
                        <x-label-error :messages="$errors->get('new_doc_deskripsi')" />
                    </fieldset>
                </div>
                <fieldset class="fieldset mb-4">
                    <label class="block">Tindakan perbaikan langsung</label>

                    <div wire:ignore>
                        <textarea id="ckeditor-immediate_corrective_action">{{ $immediate_corrective_action }}</textarea>
                    </div>
                    <!-- Hidden input untuk binding Livewire -->
                    <input type="hidden" wire:model.live="immediate_corrective_action" id="immediate_corrective_action">
                    <x-label-error :messages="$errors->get('immediate_corrective_action')" />
                </fieldset>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4 ">
                    <fieldset class=" fieldset">
                        <label class="block">Dokumentasi Sesudah Tindakan perbaikan langsung</label>
                        <label wire:ignore for="upload-corrective" class="flex items-center gap-2 cursor-pointer border border-info rounded  hover:ring-1 hover:border-info hover:ring-info hover:outline-hidden">
                            <!-- Tombol custom -->
                            <span class="btn btn-info btn-xs">
                                Pilih file atau gambar
                            </span>
                            <!-- Nama file -->
                            <span id="file-name-corrective" class="text-[9px] text-gray-500 truncate max-w-sm">
                                {!! $new_doc_corrective? $new_doc_corrective->getClientOriginalName():$doc_corrective !!}
                            </span>
                        </label>
                        @if ($new_doc_corrective)
                        <div class="text-xs text-green-600">Preview file baru:</div>
                        <img src="{{ $new_doc_corrective->temporaryUrl() }}" class="h-24 rounded border mt-1">
                        @elseif($doc_corrective)
                        <div class="text-xs text-gray-600">File lama:</div>
                        <img src="{{ asset('storage/' . $doc_corrective) }}" class="h-24 rounded border mt-1">
                        @else
                        <span class="text-xs text-gray-400">Belum ada file</span>
                        @endif
                        <!-- Input asli (disembunyikan) -->
                        <input id="upload-corrective" wire:model.live='new_doc_corrective' type="file" class="hidden" onchange="document.getElementById('file-name-corrective').textContent = this.files[0]?.name ?? 'Belum ada file'" />
                        <x-label-error :messages="$errors->get('doc_corrective')" />
                    </fieldset>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2  gap-4 mb-4 border border-gray-300 p-4 rounded">
                    {{-- KEY WORD --}}
                    <fieldset>
                        <input id="kta" value="kta" wire:model.live="keyWord" class="peer/kta radio radio-xs radio-accent" type="radio" name="keyWord" checked />
                        <label for="kta" class="peer-checked/kta:text-accent">Kondisi Tidak Aman</label>

                        <input id="tta" value="tta" wire:model.live="keyWord" class="peer/tta radio radio-xs radio-primary" type="radio" name="keyWord" />
                        <label for="tta" class="peer-checked/tta:text-primary">Tindakan Tidak Aman</label>

                        <div class="hidden peer-checked/kta:block mt-1">
                            <select wire:model.live="kondisi_tidak_aman" class="select select-xs mb-1 select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden">
                                <option value="">-- Pilih Kondisi Tidak Aman --</option>
                                @foreach ($ktas as $kta)
                                <option value="{{ $kta->id }}">{{ $kta->name }}</option>
                                @endforeach
                            </select>

                        </div>
                        <div class="hidden peer-checked/tta:block mt-1">
                            <select wire:model.live="tindakan_tidak_aman" class="select select-xs mb-1 select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden">
                                <option value="">-- Pilih Tindakan Tidak Aman --</option>
                                @foreach ($ttas as $tta)
                                <option value="{{ $tta->id }}">{{ $tta->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($keyWord === 'kta')
                        <x-label-error :messages="$errors->get('kondisi_tidak_aman')" />
                        @endif
                        @if($keyWord === 'tta')
                        <x-label-error :messages="$errors->get('tindakan_tidak_aman')" />
                        @endif

                    </fieldset>
                </div>
                <div class="flex flex-col md:flex-row gap-2">

                    {{-- Kolom Likelihood & Consequence --}}
                    <div class=" space-y-4 md:grow">
                        {{-- Consequence --}}
                        <fieldset class="fieldset ">
                            <label class="block ">Consequence</label>
                            <select wire:model.live="consequence_id" class="select select-xs md:select-xs select-bordered w-full md:max-w-md focus:ring-1 focus:border-info focus:ring-info focus:outline-none">
                                <option value="">-- Pilih --</option>
                                @foreach ($consequencess as $cons)
                                <option value="{{ $cons->id }}">{{ $cons->name }}</option>
                                @endforeach
                            </select>
                            <x-label-error :messages="$errors->get('consequence_id')" />

                            @if($consequence_id)
                            @php
                            $selectedConsequence = $consequencess->firstWhere('id', $consequence_id);
                            @endphp
                            @if($selectedConsequence)
                            <div class="mt-1 text-sm text-gray-600 h-20 overflow-y-auto border rounded p-2 bg-gray-50">
                                {{ $selectedConsequence->description ?? 'Tidak ada deskripsi' }}
                            </div>
                            @endif
                            @endif
                        </fieldset>
                        {{-- Likelihood --}}
                        <fieldset class="fieldset ">
                            <label class="block ">Likelihood</label>
                            <select wire:model.live="likelihood_id" class="select select-xs md:select-xs select-bordered w-full md:max-w-md focus:ring-1 focus:border-info focus:ring-info focus:outline-none">
                                <option value="">-- Pilih --</option>
                                @foreach ($likelihoodss as $like)
                                <option value="{{ $like->id }}">{{ $like->name }}</option>
                                @endforeach
                            </select>
                            <x-label-error :messages="$errors->get('likelihood_id')" />

                            @if($likelihood_id)
                            @php
                            $selectedLikelihood = $likelihoodss->firstWhere('id', $likelihood_id);
                            @endphp
                            @if($selectedLikelihood)
                            <div class="mt-1 text-sm text-gray-600 h-20 overflow-y-auto border rounded p-2 bg-gray-50">
                                {{ $selectedLikelihood->description ?? 'Tidak ada deskripsi' }}
                            </div>
                            @endif
                            @endif
                        </fieldset>


                    </div>

                    {{-- Kolom Risk Matrix --}}
                    <div class="overflow-x-auto  flex-none ">
                        <table class="table table-xs w-60">
                            <thead>
                                <tr class="text-center text-[9px]">
                                    <td class=" border-1">Level</td>
                                    <td class="rotate_text border-1 bg-emerald-500">Low</td>
                                    <td class="rotate_text border-1 bg-yellow-500">Moderate</td>
                                    <td class="rotate_text border-1 bg-orange-500">High</td>
                                    <td class="rotate_text border-1 bg-rose-500">Extreme</td>
                                    <td class="rotate_text border-1 bg-gray-100">Closed</td>
                                </tr>
                                <tr class="text-center text-[9px]">
                                    <th class="border-1">Likelihooc ↓ / Consequence →</th>
                                    @foreach ($consequences as $c)
                                    <th class="rotate_text border-1">{{ $c->name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($likelihoods as $l)
                                <tr class="text-center text-xs w-32">

                                    <td class=" font-bold w-1 border-1">{{ $l->name }}</td>
                                    @foreach ($consequences as $c)
                                    @php
                                    $cell = App\Models\RiskMatrixCell::where('likelihood_id', $l->id)->where('risk_consequence_id', $c->id)->first() ?? null;
                                    $score = $l->level * $c->level;
                                    $severity = $cell?->severity ?? '';
                                    $color = match($severity) {
                                    'Low' => 'bg-emerald-500',
                                    'Moderate' => 'bg-yellow-500',
                                    'High' => 'bg-orange-500',
                                    'Extreme' => 'bg-rose-500',
                                    default => 'bg-gray-100',
                                    };
                                    @endphp
                                    <td class="border cursor-pointer w-4 {{ $color }}
                                        @if($likelihood_id == $l->id && $consequence_id == $c->id) border-2 border-stone-500 @endif" wire:click="edit({{ $l->id }}, {{ $c->id }})">
                                        <div class="text-[6px]">{{ Str::upper(substr($severity, 0, 1)) }}</div>

                                    </td>

                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>

            </div>
        </x-tab-hazard.layout>
    </form>

</section>
@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script>
    const isDisabled = @json($isDisabled);
    console.log('Initial isDisabled:', isDisabled);

    document.addEventListener('livewire:navigated', () => {
        ClassicEditor
            .create(document.querySelector('#ckeditor-description'), {
                toolbar: ['bold', 'italic', 'bulletedList', 'numberedList', '|', 'undo', 'redo']
                , removePlugins: ['ImageUpload', 'EasyImage', 'MediaEmbed']
            })
            .then(editor => {
                // Set awal read-only jika isDisabled true
                if (isDisabled) {
                    editor.enableReadOnlyMode('hazard-description');
                }

                // Live update ketika status berubah
                Livewire.on('hazardStatusChanged', (payload) => {
                    if (payload.isDisabled) {
                        editor.enableReadOnlyMode('hazard-description');
                    } else {
                        editor.disableReadOnlyMode('hazard-description');
                    }
                });

                // Update hidden input dan Livewire
                editor.model.document.on('change:data', () => {
                    const data = editor.getData();
                    document.querySelector('#description').value = data;
                    @this.set('description', data);
                });
            })
            .catch(error => console.error(error));
    });

</script>

<script>
    document.addEventListener('livewire:navigated', () => {
        ClassicEditor
            .create(document.querySelector('#ckeditor-immediate_corrective_action'), {
                toolbar: [
                    // 'heading', '|'
                    , 'bold', 'italic', 'bulletedList', 'numberedList', '|'
                    , 'undo', 'redo'
                ]
                , removePlugins: ['ImageUpload', 'EasyImage', 'MediaEmbed'] // buang plugin gambar
            })
            .then(editor => {

                editor.model.document.on('change:data', () => {
                    // Update ke hidden input
                    const data = editor.getData();
                    document.querySelector('#ckeditor-immediate_corrective_action').value = data;

                    // Kirim ke Livewire
                    @this.set('immediate_corrective_action', data);
                });
            })
            .catch(error => {
                console.error(error);
            });
    });

</script>
@endpush
