<?php

use App\Livewire\Manhours\Index;
use App\Livewire\Settings\Profile;
use App\Livewire\Hazard\HazardList;
use App\Livewire\Settings\Password;
use App\Livewire\Hazard\HazardDetail;
use App\Livewire\Settings\Appearance;
use Illuminate\Support\Facades\Route;
use App\Livewire\Administration\Menu\ListMenu;
use App\Livewire\Administration\DeptGroup\Group;
use App\Livewire\Administration\RiskMatrix\Grid;
use App\Livewire\Administration\Menu\ListSubMenu;
use App\Livewire\Administration\Menu\ExtraSubMenu;
use App\Livewire\Administration\Custodian\Custodian;
use App\Livewire\Administration\RiskMatrix\RiskMatrix;
use App\Livewire\Administration\Companies\CompanyIndex;
use App\Livewire\Administration\Contractor\Contractors;
use App\Livewire\Administration\Departement\Department;
use App\Livewire\Administration\EventGeneral\EventType;
use App\Livewire\Administration\DeptGroup\DepartmentGroup;
use App\Livewire\Administration\EventGeneral\EventSubType;
use App\Livewire\Administration\BusinessUnit\BusinessUnits;
use App\Livewire\Administration\CauseAnalysis\Kta;
use App\Livewire\Administration\CauseAnalysis\Tta;
use App\Livewire\Administration\EventGeneral\EventCategory;
use App\Livewire\Administration\RiskLikelihood\RiskLikelihood;
use App\Livewire\Administration\RiskConsequence\RiskConsequence;
use App\Livewire\Administration\EventGeneral\ErmAssignmentManager;
use App\Livewire\Administration\EventGeneral\ModeratorAssignmentManager;
use App\Livewire\Administration\Locations\Location;
use App\Livewire\Administration\People\User;
use App\Livewire\Administration\RelasiContUser\ContractorUserManager;
use App\Livewire\Administration\RelasiDeptUser\DepartmentUserManager;
use App\Livewire\Administration\Roles\Role;
use App\Livewire\Administrator\UserRoleManager\UserRole;
use App\Livewire\Hazard\HazardForm;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('storage',function(){
Artisan::call('storage:link');
    return 'successfully';
});

Route::redirect('/', 'dashboard');
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
    Route::get('manhours', Index::class)->name('manhours');
    Route::get('hazard', HazardList::class)->name('hazard');
    Route::get('hazard/form', HazardForm::class)->name('hazard-form');
    Route::get('hazard/{hazard}', HazardDetail::class)->name('hazard-detail');
});
Route::middleware(['auth'])->group(function () {

    Route::get('administration/companies', CompanyIndex::class)->name('administration-companies');
    Route::get('administration/department', Department::class)->name('administration-department');
    Route::get('administration/menu', ListMenu::class)->name('administration-menu');
    Route::get('administration/menu/submenu', ListSubMenu::class)->name('administration-menu-submenu');
    Route::get('administration/menu/extrasubmenu', ExtraSubMenu::class)->name('administration-menu-extrasubmenu');
    Route::get('administration/custodian', Custodian::class)->name('administration-custodian');
    Route::get('administration/department-group', DepartmentGroup::class)->name('administration-department-group');
    Route::get('administration/department-group/group', Group::class)->name('administration-department-group-group');
    Route::get('administration/contractor', Contractors::class)->name('administration-contractor');
    Route::get('administration/business_unit', BusinessUnits::class)->name('administration-Business-Units');
    Route::get('administration/event_general/event_category', EventCategory::class)->name('administration-event_general-eventCategory');
    Route::get('administration/event_general/event_type', EventType::class)->name('administration-event_general-eventType');
    Route::get('administration/event_general/event_sub_type', EventSubType::class)->name('administration-event_general-eventSubType');
    Route::get('administration/event_general/ErmAssignmentManager', ErmAssignmentManager::class)->name('administration-event_general-ErmAssignmentManager');
    Route::get('administration/event_general/ModeratorAssignmentManager', ModeratorAssignmentManager::class)->name('administration-event_general-ModeratorAssignmentManager');
    Route::get('administration/risk/Consequence', RiskConsequence::class)->name('administration-risk-Consequence');
    Route::get('administration/risk/Likelihood', RiskLikelihood::class)->name('administration-risk-Likelihood');
    Route::get('administration/risk/Matrix', Grid::class)->name('administration-risk-Matrix');
    Route::get('administration/location', Location::class)->name('location');
    Route::get('administration/cause_analysis/kta', Kta::class)->name('kta');
    Route::get('administration/cause_analysis/tta', Tta::class)->name('tta');
    Route::get('administration/userManager/departmentUserManager', DepartmentUserManager::class)->name('departmentUserManager');
    Route::get('administration/userManager/contractorUserManager', ContractorUserManager::class)->name('contractorUserManager');
    Route::get('administration/userManager/roles', Role::class)->name('roles');
    Route::get('administration/userManager/user_roles', UserRole::class)->name('user_roles');
    Route::get('administration/userManager/people', User::class)->name('people');
});

require __DIR__ . '/auth.php';
