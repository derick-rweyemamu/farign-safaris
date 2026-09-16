<div class="space-y-4 animate-fade-in w-full">

    <!-- Header -->
    <div>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Account Settings') }}</h2>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Manage your profile and security preferences') }}</p>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200 dark:border-gray-800">
        <nav class="flex gap-1" x-data>
            @php
                $tabs = [
                    ['key' => 'profile', 'label' => 'Profile', 'icon' => 'user'],
                    ['key' => 'password', 'label' => 'Password', 'icon' => 'lock-closed'],
                    ['key' => 'security', 'label' => 'Security', 'icon' => 'shield-check']
                ];
                if (auth()->user()?->employee) {
                    $tabs[] = ['key' => 'appraisals', 'label' => 'My Appraisals', 'icon' => 'star'];
                }
            @endphp
            @foreach($tabs as $tab)
                    <button wire:click="$set('activeTab', '{{ $tab['key'] }}')"
                        class="flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 transition-colors
                                           {{ $activeTab === $tab['key']
                ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 dark:border-indigo-400'
                : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                        <x-icon name="{{ $tab['icon'] }}" class="w-4 h-4" />
                        {{ $tab['label'] }}
                    </button>
            @endforeach
        </nav>
    </div>

    <!-- Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 items-start">
        <!-- Left Side: Avatar Card (Persistent) -->
        <div class="bg-card rounded-[3px] p-5 shadow-sm border border-gray-100 dark:border-gray-800 text-center lg:sticky lg:top-5">
            <div class="relative inline-block mb-4">
                @if($avatar && method_exists($avatar, 'isPreviewable') && $avatar->isPreviewable())<img src="{{ $avatar->temporaryUrl() }}" alt="Avatar preview"
                        class="w-24 h-24 rounded-full object-cover mx-auto ring-4 ring-indigo-100 dark:ring-indigo-900/40">
                @elseif($avatar)
                    <div class="avatar-placeholder gradient-blue w-24 h-24 text-2xl mx-auto ring-4 ring-indigo-100 dark:ring-indigo-900/40 flex flex-col items-center justify-center">
                        <span class="text-[10px] text-white font-medium">{{ __('Invalid File') }}</span>
                    </div>
                @elseif(auth()->user()->profile?->avatar)<img src="{{ asset('storage/' . auth()->user()->profile->avatar) }}" alt="Avatar"
                        class="w-24 h-24 rounded-full object-cover mx-auto ring-4 ring-indigo-100 dark:ring-indigo-900/40">
                @else
                    <div
                        class="avatar-placeholder gradient-blue w-24 h-24 text-2xl mx-auto ring-4 ring-indigo-100 dark:ring-indigo-900/40">
                        {{ auth()->user()->initials }}
                    </div>
                @endif

                <label for="avatar-upload"
                    class="absolute bottom-0 right-0 w-8 h-8 gradient-blue rounded-full flex items-center justify-center cursor-pointer shadow-lg">
                    <x-icon name="camera" class="w-4 h-4 text-white" />
                </label>
                <input id="avatar-upload" type="file" wire:model="avatar" class="hidden" accept="image/*">
            </div>

            <h3 class="font-bold text-gray-900 dark:text-white">{{ auth()->user()->name }}</h3>
            
            <div class="flex flex-wrap justify-center gap-2 mt-2">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                    {{ auth()->user()->hasRole('super admin') || auth()->user()->hasRole('admin') ? 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400' : '' }}
                    {{ auth()->user()?->employee ? 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400' : '' }}
                    {{ auth()->user()->hasRole('Client') ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400' : '' }}">
                    {{ auth()->user()->getRoleNames()->first() ?? 'User' }}
                </span>
                
                @if(auth()->user()?->employee && $employee_id_code)
                    <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400 text-[10px] font-bold uppercase tracking-wider">
                        {{ $employee_id_code }}
                    </span>
                @elseif(auth()->user()->hasRole('Client') && $membership_tier)
                    <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 text-[10px] font-bold uppercase tracking-wider">
                        {{ $membership_tier }}
                    </span>
                @endif
            </div>

            <p class="text-xs text-gray-400 mt-2">{{ auth()->user()->email }}</p>

            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                <div class="flex items-center justify-center gap-2">
                    <div class="status-dot online"></div>
                    <span class="text-sm text-gray-500">{{ __('Active') }}</span>
                </div>
            </div>
        </div>

        <!-- Right Side: Content Area (Tab-based) -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Profile Tab -->
            @if($activeTab === 'profile')
                <div class="bg-card rounded-[3px] p-5 shadow-sm border border-gray-100 dark:border-gray-800">
                    <form wire:submit="saveProfile" class="space-y-4">
                        <!-- General Information -->
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-5 flex items-center gap-2">
                                <x-icon name="user" class="w-5 h-5 text-indigo-500" />{{ __('Personal Information') }}</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Full
                                        Name *</label>
                                    <input type="text" wire:model="name" class="form-input">
                                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Email Address') }}</label>
                                    <input type="email" wire:model="email" class="form-input bg-gray-50 dark:bg-gray-800/50"
                                        readonly title="{{ __('Email cannot be changed from profile settings') }}">
                                    <p class="text-[10px] text-gray-400 mt-1 italic">{{ __('Contact admin to change your login email') }}</p>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Phone') }}</label>
                                    <input type="text" wire:model="phone" class="form-input"
                                        placeholder="+1 (555) 000-0000">
                                </div>

                                @if(auth()->user()->hasRole('Client') || auth()->user()?->employee)<div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Date of
                                            Birth') }}</label>
                                        <input type="date" wire:model="date_of_birth" class="form-input">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Gender') }}</label>
                                        <select wire:model="gender" class="form-input">
                                            <option value="">{{ __('Select gender...') }}</option>
                                            <option value="male">{{ __('Male') }}</option>
                                            <option value="female">{{ __('Female') }}</option>
                                            <option value="other">{{ __('Other') }}</option>
                                        </select>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="pt-6 border-t border-gray-100 dark:border-gray-800">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-5 flex items-center gap-2">
                                <x-icon name="map-pin" class="w-5 h-5 text-indigo-500" />{{ __('Address Information') }}</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @if(auth()->user()->hasRole('Client'))<div class="sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Address Line 1') }}</label>
                                        <input type="text" wire:model="address_line1" class="form-input">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Address Line 2') }}</label>
                                        <input type="text" wire:model="address_line2" class="form-input">
                                    </div>
                                @elseif(auth()->user()?->employee)<div class="sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Residential Address') }}</label>
                                        <textarea wire:model="address" rows="2" class="form-input resize-none"></textarea>
                                    </div>
                                @else
                                    <div class="sm:col-span-2">
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Location') }}</label>
                                        <input type="text" wire:model="location" class="form-input" placeholder="{{ __('City, Country') }}">
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Emergency Contact (Clients & Employees) -->
                        @if(auth()->user()->hasRole('Client') || auth()->user()?->employee)<div class="pt-6 border-t border-gray-100 dark:border-gray-800">
                                <h3 class="font-semibold text-gray-900 dark:text-white mb-5 flex items-center gap-2">
                                    <x-icon name="phone" class="w-5 h-5 text-indigo-500" />{{ __('Emergency Contact') }}</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div class="sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Contact Name') }}</label>
                                        <input type="text" wire:model="emergency_contact_name" class="form-input">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Contact Phone') }}</label>
                                        <input type="text" wire:model="emergency_contact_phone" class="form-input">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Relationship') }}</label>
                                        <input type="text" wire:model="emergency_contact_relationship" class="form-input">
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Billing Information (Clients Only) -->
                        @if(auth()->user()->hasRole('Client'))<div class="pt-6 border-t border-gray-100 dark:border-gray-800">
                                <h3 class="font-semibold text-gray-900 dark:text-white mb-5 flex items-center gap-2">
                                    <x-icon name="credit-card" class="w-5 h-5 text-indigo-500" />{{ __('Billing Details') }}</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div class="sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Billing Name') }}</label>
                                        <input type="text" wire:model="billing_name" class="form-input">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Billing Email') }}</label>
                                        <input type="email" wire:model="billing_email" class="form-input">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Billing Phone') }}</label>
                                        <input type="text" wire:model="billing_phone" class="form-input">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Billing Address') }}</label>
                                        <textarea wire:model="billing_address" rows="2" class="form-input resize-none"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Postal Code') }}</label>
                                        <input type="text" wire:model="billing_postal_code" class="form-input">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('City') }}</label>
                                        <input type="text" wire:model="billing_city" class="form-input">
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Admin Specific Fields / Employee Read-only Info -->
                        @if(!auth()->user()->hasRole('Client'))<div class="pt-6 border-t border-gray-100 dark:border-gray-800">
                                <h3 class="font-semibold text-gray-900 dark:text-white mb-5 flex items-center gap-2">
                                    <x-icon name="clipboard-list" class="w-5 h-5 text-indigo-500" />{{ __('Professional Details') }}</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Position') }}</label>
                                        <input type="text" wire:model="position" class="form-input {{ auth()->user()?->employee ? 'bg-gray-50 dark:bg-gray-800/50' : '' }}"
                                            placeholder="{{ __('e.g. Software Engineer') }}" {{ auth()->user()?->employee ? 'readonly' : '' }}>
                                        @if(auth()->user()?->employee)<p class="text-[10px] text-gray-400 mt-1 italic">{{ __('Contact HR to update your position') }}</p>
                                        @endif
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Department') }}</label>
                                        <input type="text" wire:model="department" class="form-input {{ auth()->user()?->employee ? 'bg-gray-50 dark:bg-gray-800/50' : '' }}"
                                            placeholder="{{ __('e.g. Engineering') }}" {{ auth()->user()?->employee ? 'readonly' : '' }}>
                                        @if(auth()->user()?->employee)<p class="text-[10px] text-gray-400 mt-1 italic">{{ __('Contact HR to update your department') }}</p>
                                        @endif
                                    </div>

                                    @if(auth()->user()?->employee)<div class="sm:col-span-2" x-data="{ mode: 'upload' }">
                                            <div class="flex items-center justify-between mb-2">
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Signature') }}</label>
                                                <div class="flex bg-gray-100 dark:bg-gray-800 rounded-[3px] p-1 gap-1">
                                                    <button type="button" @click="mode = 'upload'" :class="mode === 'upload' ? 'bg-white dark:bg-gray-700 shadow-sm text-gray-900 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400'" class="px-3 py-1 text-xs font-medium rounded-[3px] transition-all">{{ __('Upload') }}</button>
                                                    <button type="button" @click="mode = 'draw'" :class="mode === 'draw' ? 'bg-white dark:bg-gray-700 shadow-sm text-gray-900 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400'" class="px-3 py-1 text-xs font-medium rounded-[3px] transition-all">{{ __('Draw') }}</button>
                                                </div>
                                            </div>

                                            <div x-show="mode === 'upload'" class="space-y-2">
                                                <div class="flex items-center gap-4">
                                                    @if(auth()->user()?->employee->signature_path)
                                                        <img src="{{ asset('storage/' . auth()->user()?->employee->signature_path) }}" alt="Signature" class="h-12 object-contain bg-white rounded border border-gray-200 px-2 py-1">
                                                    @endif
                                                    <input type="file" wire:model="signature" class="form-input text-xs w-full" accept="image/png,image/jpeg">
                                                </div>
                                                <p class="text-[10px] text-gray-400 mt-1 italic">{{ __('Upload a clear PNG image of your signature.') }}</p>
                                            </div>

                                            <div x-show="mode === 'draw'" style="display: none;" wire:ignore>
                                                <div x-data="{
                                                    init() {
                                                        const canvas = this.$refs.pad;
                                                        const ctx = canvas.getContext('2d');
                                                        let isDrawing = false;
                                                        
                                                        const resize = () => {
                                                            const rect = canvas.parentElement.getBoundingClientRect();
                                                            if (rect.width === 0) return;
                                                            const data = canvas.toDataURL();
                                                            canvas.width = rect.width;
                                                            canvas.height = 160;
                                                            ctx.strokeStyle = '#000';
                                                            ctx.lineWidth = 2.5;
                                                            ctx.lineCap = 'round';
                                                            ctx.lineJoin = 'round';
                                                            if(data && data !== 'data:,') {
                                                                const img = new Image();
                                                                img.onload = () => ctx.drawImage(img, 0, 0);
                                                                img.src = data;
                                                            }
                                                        };

                                                        // Watch for changes in mode from the parent x-data
                                                        this.$watch('mode', (val) => {
                                                            if(val === 'draw') {
                                                                setTimeout(resize, 50);
                                                            }
                                                        });

                                                        new ResizeObserver(resize).observe(canvas.parentElement);
                                                        setTimeout(resize, 100);

                                                        const getPos = (e) => {
                                                            const rect = canvas.getBoundingClientRect();
                                                            const clientX = (e.touches && e.touches.length > 0) ? e.touches[0].clientX : e.clientX;
                                                            const clientY = (e.touches && e.touches.length > 0) ? e.touches[0].clientY : e.clientY;
                                                            return { x: clientX - rect.left, y: clientY - rect.top };
                                                        };

                                                        const start = (e) => {
                                                            isDrawing = true;
                                                            const { x, y } = getPos(e);
                                                            ctx.beginPath();
                                                            ctx.moveTo(x, y);
                                                            if(e.cancelable) e.preventDefault();
                                                        };

                                                        const draw = (e) => {
                                                            if (!isDrawing) return;
                                                            const { x, y } = getPos(e);
                                                            ctx.lineTo(x, y);
                                                            ctx.stroke();
                                                            if(e.cancelable) e.preventDefault();
                                                        };

                                                        const end = () => {
                                                            if(isDrawing) {
                                                                isDrawing = false;
                                                                @this.set('signatureDataUrl', canvas.toDataURL('image/png'));
                                                            }
                                                        };

                                                        canvas.addEventListener('mousedown', start, {passive: false});
                                                        canvas.addEventListener('mousemove', draw, {passive: false});
                                                        window.addEventListener('mouseup', end);
                                                        
                                                        canvas.addEventListener('touchstart', start, {passive: false});
                                                        canvas.addEventListener('touchmove', draw, {passive: false});
                                                        window.addEventListener('touchend', end);
                                                    },
                                                    clearPad() {
                                                        const canvas = this.$refs.pad;
                                                        const ctx = canvas.getContext('2d');
                                                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                                                        @this.set('signatureDataUrl', null);
                                                    }
                                                }" class="border border-gray-300 dark:border-gray-600 rounded-[3px] bg-white relative">
                                                    <canvas x-ref="pad" class="w-full h-40 cursor-crosshair touch-none"></canvas>
                                                    <button type="button" @click="clearPad()" class="absolute top-2 right-2 text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-[3px] transition-colors border border-gray-200">{{ __('Clear') }}</button>
                                                </div>
                                                <p class="text-[10px] text-gray-400 mt-2 italic">{{ __('Draw your signature directly in the box above. It will be saved as an image.') }}</p>
                                            </div>
                                        </div>
                                    @endif

                                    @if(!auth()->user()?->employee)<div class="sm:col-span-2">
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Bio') }}</label>
                                            <textarea wire:model="bio" rows="3" class="form-input resize-none"
                                                placeholder="{{ __('A brief description about yourself...') }}"></textarea>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-end pt-2">
                            <button type="submit" class="btn-primary px-8" wire:loading.attr="disabled">
                                <x-icon name="check" class="w-4 h-4" wire:loading.remove />
                                <x-icon name="arrow-path" class="w-4 h-4 animate-spin" wire:loading />
                                <span wire:loading.remove>{{ __('Save Changes') }}</span>
                                <span wire:loading>{{ __('Saving...') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <!-- Password Tab -->
            @if($activeTab === 'password')
                <div class="bg-card rounded-[3px] p-5 shadow-sm border border-gray-100 dark:border-gray-800">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-8 h-8 gradient-blue rounded-[3px] flex items-center justify-center">
                            <x-icon name="lock-closed" class="w-4 h-4 text-white" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('Change Password') }}</h3>
                            <p class="text-xs text-gray-400">{{ __('Ensure a strong, unique password') }}</p>
                        </div>
                    </div>

                    <form wire:submit="savePassword" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Current Password
                                *</label>
                            <input type="password" wire:model="currentPassword" class="form-input" id="current-password">
                            @error('currentPassword') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">New Password
                                *</label>
                            <input type="password" wire:model="newPassword" class="form-input" id="new-password">
                            @error('newPassword') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Confirm New
                                Password *</label>
                            <input type="password" wire:model="confirmPassword" class="form-input" id="confirm-password">
                            @error('confirmPassword') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Password requirements -->
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-[3px] p-3">
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">{{ __('Password requirements:') }}</p>
                            <ul class="space-y-1">
                                @foreach(['At least 8 characters', 'Use a mix of letters and numbers', 'Avoid common words'] as $req)
                                    <li class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                        <x-icon name="check" class="w-3 h-3 text-emerald-500 flex-shrink-0" />
                                        {{ $req }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                                <x-icon name="check" class="w-4 h-4" wire:loading.remove />
                                <x-icon name="arrow-path" class="w-4 h-4 animate-spin" wire:loading />
                                <span wire:loading.remove>{{ __('Update Password') }}</span>
                                <span wire:loading>{{ __('Updating...') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <!-- Security Tab -->
            @if($activeTab === 'security')
                <div class="bg-card rounded-[3px] p-5 shadow-sm border border-gray-100 dark:border-gray-800 space-y-6">
                    <div class="flex items-center gap-3 border-b border-gray-100 dark:border-gray-800 pb-4">
                        <div class="w-8 h-8 gradient-blue rounded-[3px] flex items-center justify-center">
                            <x-icon name="shield-check" class="w-4 h-4 text-white" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('Two-Factor Authentication (2FA)') }}</h3>
                            <p class="text-xs text-gray-400">{{ __('Add an extra layer of security to your account.') }}</p>
                        </div>
                    </div>

                    @can('enable disable 2FA')
                    @if(auth()->user()->two_factor_type)
                        <div class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 rounded-[3px] p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div>
                                <h4 class="text-emerald-800 dark:text-emerald-400 font-semibold text-sm flex items-center gap-2">
                                    <x-icon name="check-circle" class="w-5 h-5" />{{ __('Two-Factor Authentication is Enabled') }}</h4>
                                <p class="text-emerald-600 dark:text-emerald-500 text-xs mt-1">{{ __('Currently using:') }}<span class="font-bold uppercase">{{ auth()->user()->two_factor_type }}</span>
                                </p>
                            </div>
                            <button wire:click="disableTwoFactor" wire:confirm="Are you sure you want to disable Two-Factor Authentication?" class="bg-white dark:bg-slate-800 text-red-600 border border-red-200 dark:border-red-900/50 hover:bg-red-50 dark:hover:bg-red-900/20 font-medium px-4 py-2 rounded-[3px] text-sm transition-colors">{{ __('Disable 2FA') }}</button>
                        </div>
                    @else
                        @if(!$showTwoFactorSetup)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="border border-gray-200 dark:border-gray-700 rounded-[3px] p-5 hover:border-indigo-500 dark:hover:border-indigo-500 transition-colors cursor-pointer group flex flex-col items-start gap-4" wire:click="initiateTwoFactorSetup('app')">
                                    <div class="w-10 h-10 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-[3px] flex items-center justify-center group-hover:scale-110 transition-transform">
                                        <x-icon name="device-phone-mobile" class="w-5 h-5" />
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 dark:text-white text-sm">{{ __('Authenticator App') }}</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Use an app like Google Authenticator or Authy to generate verification codes.') }}</p>
                                    </div>
                                    <button class="mt-auto text-indigo-600 dark:text-indigo-400 text-sm font-medium">Setup App →</button>
                                </div>

                                <div class="border border-gray-200 dark:border-gray-700 rounded-[3px] p-5 hover:border-indigo-500 dark:hover:border-indigo-500 transition-colors cursor-pointer group flex flex-col items-start gap-4" wire:click="initiateTwoFactorSetup('email')">
                                    <div class="w-10 h-10 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-[3px] flex items-center justify-center group-hover:scale-110 transition-transform">
                                        <x-icon name="envelope" class="w-5 h-5" />
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 dark:text-white text-sm">{{ __('Email OTP') }}</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Receive a one-time password via your registered email address.') }}</p>
                                    </div>
                                    <button class="mt-auto text-indigo-600 dark:text-indigo-400 text-sm font-medium">Setup Email →</button>
                                </div>
                            </div>
                        @else
                            <div class="border border-indigo-100 dark:border-indigo-900/50 bg-indigo-50/50 dark:bg-indigo-500/5 rounded-[3px] p-6">
                                <div class="flex items-center justify-between mb-6">
                                    <h4 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                        Setup {{ $setupType === 'app' ? 'Authenticator App' : 'Email OTP' }}
                                    </h4>
                                    <button wire:click="$set('showTwoFactorSetup', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                        <x-icon name="x-mark" class="w-5 h-5" />
                                    </button>
                                </div>

                                @if($setupType === 'app')
                                    <div class="flex flex-col sm:flex-row gap-8 items-center">
                                        <div class="bg-white p-3 rounded-[3px] shadow-sm border border-gray-200 inline-block">
                                            {!! $twoFactorQrCodeUrl !!}
                                        </div>
                                        <div class="flex-1 space-y-4">
                                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                                1. Scan this QR code using your authenticator app (like Google Authenticator or Authy).
                                            </p>
                                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                                2. Enter the 6-digit code generated by your app below.
                                            </p>
                                            <div class="bg-gray-100 dark:bg-gray-800 rounded p-2 text-xs font-mono text-center">
                                                Secret: {{ $twoFactorSetupSecret }}
                                            </div>
                                        </div>
                                    </div>
                                @elseif($setupType === 'email')
                                    <div class="space-y-4 mb-6">
                                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ __('We\'ve sent a 6-digit verification code to') }}<strong>{{ auth()->user()->email }}</strong>.
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ __('Please check your inbox and enter the code below to verify your email and enable 2FA.') }}</p>
                                    </div>
                                @endif

                                <form wire:submit="confirmTwoFactor" class="mt-8">
                                    <div class="max-w-xs">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Verification Code') }}</label>
                                        <div class="flex gap-3">
                                            <input type="text" wire:model="twoFactorCode" class="form-input text-center tracking-[0.25em] font-mono text-lg" placeholder="••••••" maxlength="6" autofocus>
                                            <button type="submit" class="btn-primary whitespace-nowrap" wire:loading.attr="disabled">
                                                <span wire:loading.remove>{{ __('Verify & Enable') }}</span>
                                                <span wire:loading>{{ __('Verifying...') }}</span>
                                            </button>
                                        </div>
                                        @error('twoFactorCode') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                                    </div>
                                </form>
                            </div>
                        @endif
                    @endif
                    @else
                    <div class="p-8 text-center bg-gray-50 dark:bg-gray-800/50 rounded-[3px] border border-dashed border-gray-200 dark:border-gray-700">
                        <x-icon name="lock-closed" class="w-8 h-8 text-gray-400 mx-auto mb-3" />
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Two-Factor Authentication management is restricted by your role.') }}</p>
                    </div>
                    @endcan
                </div>
            @endif

            <!-- Appraisals Tab -->
            @if($activeTab === 'appraisals' && auth()->user()?->employee)<div class="bg-card rounded-[3px] shadow-sm border border-gray-100 dark:border-gray-800">
                    <div class="p-5 flex items-center gap-3 border-b border-gray-100 dark:border-gray-800">
                        <div class="w-8 h-8 gradient-blue rounded-[3px] flex items-center justify-center">
                            <x-icon name="star" class="w-4 h-4 text-white" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('My Appraisals') }}</h3>
                            <p class="text-xs text-gray-400">{{ __('View your performance reviews and feedback') }}</p>
                        </div>
                    </div>

                    <div class="p-0">
                        @if($appraisals->isEmpty())<div class="p-8 text-center text-gray-500">
                                <x-icon name="document-text" class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-700 mb-3" />
                                <p>{{ __('You don\'t have any appraisals yet.') }}</p>
                            </div>
                        @else
                            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                                @foreach($appraisals as $appraisal)
                                    <div class="p-5 hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                                            <div>
                                                <p class="text-sm font-bold text-gray-900 dark:text-white">
                                                    Period: {{ $appraisal->period_start->format('M d, Y') }} - {{ $appraisal->period_end->format('M d, Y') }}
                                                </p>
                                                <p class="text-xs text-gray-500 mt-0.5">
                                                    Evaluated by: {{ $appraisal->evaluator->name ?? 'System' }} 
                                                    • {{ $appraisal->created_at->format('M d, Y') }}
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                @php
                                                    $scores = $appraisal->scores ?? [];
                                                    $filled = array_filter($scores, fn($v) => is_numeric($v));
                                                    $avg = count($filled) > 0 ? round(array_sum($filled) / count($filled), 1) : null;
                                                @endphp
                                                @if($avg)
                                                    <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 rounded-full font-bold text-sm">
                                                        <x-icon name="star" type="solid" class="w-3.5 h-3.5" />
                                                        {{ $avg }} / 5.0
                                                    </div>
                                                @else
                                                    <span class="text-xs text-gray-400 font-medium px-3 py-1 bg-gray-100 dark:bg-gray-800 rounded-full">{{ __('No Score') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        @if($appraisal->comments)<div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-[3px] border border-gray-100 dark:border-gray-800">
                                                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Manager\'s Feedback:') }}</p>
                                                <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $appraisal->comments }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>
