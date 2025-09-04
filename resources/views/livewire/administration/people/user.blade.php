<section class="w-full">
    <x-toast />
    <x-tabs-relation.layout>
        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">User Management</h2>
                <flux:tooltip content="tambah data" position="top">
                    <flux:button size="xs" wire:click="create" icon="add-icon" variant="primary"></flux:button>
                </flux:tooltip>
            </div>

            <div class="overflow-x-auto">
                <table class="table table-xs">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Username</th>
                            <th>Department</th>
                            <th>Employee ID</th>
                            <th>Email</th>
                            <th>Date Commenced</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->gender }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->department_name }}</td>
                            <td>{{ $user->employee_id }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->date_commenced }}</td>
                            <td class="flex gap-2">
                                <!-- Edit -->

                                <div class="tooltip tooltip-right ">
                                    <div class="tooltip-content ">
                                        <div class="animate-bounce text-warning text-xs font-black">Edit</div>
                                    </div>
                                    <flux:button wire:click="edit({{ $user->id }})" size="xs" icon="pencil-square" variant="subtle"></flux:button>
                                </div>
                                <!-- Delete -->
                                <div class="tooltip tooltip-right ">
                                    <div class="tooltip-content ">
                                        <div class="animate-bounce text-error text-xs font-black">Delete</div>
                                    </div>
                                    <flux:button wire:click="confirmDelete({{ $user->id }})" size="xs" icon="trash" variant="danger"></flux:button>
                                </div>

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $users->links() }}

            {{-- Create/Edit Modal --}}
            <dialog class="modal" @if($showModal) open @endif>
                <div class="modal-box w-11/12 max-w-2xl">
                    <h3 class="font-bold text-lg">{{ $userId ? 'Edit User' : 'Add User' }}</h3>

                    <div class="grid grid-cols-2 gap-4 mt-4">

                        <fieldset class="fieldset">
                            <label class="block">Nama</label>
                            <input type="text" wire:model.live="name" class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                            <x-label-error :messages="$errors->get('name')" />
                        </fieldset>

                        <fieldset class="fieldset">
                            <label class="block">Jenis Kelamin</label>
                            <select wire:model.live="gender" class="select select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs">
                                <option value="">-- Pilih --</option>
                                <option value="L">Laki - laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                            <x-label-error :messages="$errors->get('gender')" />
                        </fieldset>

                        <fieldset class="fieldset">
                            <label class="block">Tanggal Lahir</label>
                            <input type="date" wire:model.live="date_birth" class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                            <x-label-error :messages="$errors->get('date_birth')" />
                        </fieldset>

                        <fieldset class="fieldset">
                            <label class="block">Username</label>
                            <input type="text" wire:model.live="username" class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                            <x-label-error :messages="$errors->get('username')" />
                        </fieldset>

                        <fieldset class="fieldset">
                            <label class="block">Department</label>
                            <input type="text" wire:model.live="department_name" class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                            <x-label-error :messages="$errors->get('department_name')" />
                        </fieldset>

                        <fieldset class="fieldset">
                            <label class="block">Employee ID</label>
                            <input type="text" wire:model.live="employee_id" class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                            <x-label-error :messages="$errors->get('employee_id')" />
                        </fieldset>

                        <fieldset class="fieldset">
                            <label class="block">Tanggal Masuk</label>
                            <input type="date" wire:model.live="date_commenced" class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                            <x-label-error :messages="$errors->get('date_commenced')" />
                        </fieldset>

                        <fieldset class="fieldset">
                            <label class="block">Email</label>
                            <input type="email" wire:model.live="email" class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                            <x-label-error :messages="$errors->get('email')" />
                        </fieldset>
                    </div>

                    <div class="modal-action">
                        <button class="btn" wire:click="$set('showModal', false)">Cancel</button>
                        <button class="btn btn-primary" wire:click="save">Save</button>
                    </div>
                </div>
            </dialog>


            {{-- Delete Confirmation Modal --}}
            <dialog class="modal" @if($showDeleteModal) open @endif>
                <div class="modal-box">
                    <h3 class="font-bold text-lg">Confirm Delete</h3>
                    <p>Are you sure you want to delete this user?</p>
                    <div class="modal-action">
                        <button class="btn" wire:click="$set('showDeleteModal', false)">Cancel</button>
                        <button class="btn btn-error" wire:click="delete">Delete</button>
                    </div>
                </div>
            </dialog>
        </div>

    </x-tabs-relation.layout>
</section>
