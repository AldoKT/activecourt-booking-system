@extends('layouts.admin')

@section('title', 'Edit Field - ActiveCourt')
@section('page-title', 'Edit Field')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800">Edit Field</h3>
        </div>

        <form action="{{ route('admin.fields.update', $field->id_fields) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Field Name</label>
                    <input type="text" name="name_fields" value="{{ old('name_fields', $field->name_fields) }}" required
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Sport Type</label>
                    @php
                        $currentType = old('type_fields', $field->type_fields);
                        $isStandard = in_array($currentType, ['Futsal', 'Badminton', 'Basketball', 'Volleyball', 'Tennis', 'Basket', 'Voli', 'Tenis']);
                        // Map older/Indonesian standard types to translated standards
                        if (in_array($currentType, ['Basket', 'Basketball'])) {
                            $mappedType = 'Basketball';
                        } elseif (in_array($currentType, ['Voli', 'Volleyball'])) {
                            $mappedType = 'Volleyball';
                        } elseif (in_array($currentType, ['Tenis', 'Tennis'])) {
                            $mappedType = 'Tennis';
                        } else {
                            $mappedType = $currentType;
                        }
                        $isStandardMapped = in_array($mappedType, ['Futsal', 'Badminton', 'Basketball', 'Volleyball', 'Tennis']);
                        $selectedType = $isStandardMapped ? $mappedType : 'Other';
                        $customType = $isStandardMapped ? '' : $mappedType;
                    @endphp
                    <div x-data="{ selectedType: '{{ $selectedType }}', customType: '{{ $customType }}' }">
                        <input type="hidden" name="type_fields" :value="selectedType === 'Other' ? customType : selectedType">
                        <select x-model="selectedType" required class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                            <option value="Futsal">Futsal</option>
                            <option value="Badminton">Badminton</option>
                            <option value="Basketball">Basketball</option>
                            <option value="Volleyball">Volleyball</option>
                            <option value="Tennis">Tennis</option>
                            <option value="Other">Other (Specify)</option>
                        </select>
                        <div x-show="selectedType === 'Other'" style="{{ $selectedType === 'Other' ? '' : 'display: none;' }}" class="mt-2">
                            <input type="text" x-model="customType" :required="selectedType === 'Other'" placeholder="Type sport type..." class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Address</label>
                    <input type="text" name="address" value="{{ old('address', $field->address) }}" required
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Price per Hour (Rp)</label>
                    <input type="number" name="price_per_hour" value="{{ old('price_per_hour', $field->price_per_hour) }}" required min="0"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Capacity</label>
                    <input type="number" name="capacity" value="{{ old('capacity', $field->capacity) }}" required min="1"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Sub-Courts (Comma Separated)</label>
                    <input type="text" name="sub_courts" value="{{ old('sub_courts', is_array($field->sub_courts) ? collect($field->sub_courts)->map(fn($c) => is_array($c) ? ($c['name'] ?? '') : $c)->implode(', ') : 'Court 1') }}" required
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none"
                           placeholder="Example: Court A, Court B">
                    <p class="text-xs text-slate-400 mt-1">Separate names with a comma (,)</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Field Image (Optional)</label>
                    <input type="file" name="image" accept="image/*"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-accent-50 file:text-accent-700 hover:file:bg-accent-100">
                    @error('image')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    @if($field->image)
                        <div class="mt-2">
                            <p class="text-xs text-slate-500 mb-1">Current photo:</p>
                            <img src="{{ asset('storage/' . $field->image) }}" alt="Field Photo" class="h-20 w-auto rounded-md object-cover border border-slate-200">
                        </div>
                    @endif
                </div>
            </div>

            @php
                $attachedFacilities = $field->facilities->pluck('name_facilities')->toArray();
                $standardNames = ['Free WiFi', 'Shower Room', 'Secure Parking', 'Full AC Area', 'Locker Room', 'Canteen', 'Lightings', 'Toilet', 'Prayer Room'];
                $customAttached = array_filter($attachedFacilities, function($name) use ($standardNames) {
                    return !in_array($name, $standardNames);
                });
                $customAttachedString = implode(', ', $customAttached);
            @endphp

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Field Facilities</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-3 bg-slate-50 p-4 rounded-lg border border-slate-200">
                        <label class="flex items-center gap-2 text-sm text-slate-650 cursor-pointer">
                            <input type="checkbox" name="facilities[]" value="Free WiFi" class="rounded text-accent-500 focus:ring-accent-500"
                                {{ in_array('Free WiFi', $attachedFacilities) ? 'checked' : '' }}>
                            <span><i class="fas fa-wifi text-blue-500 mr-1.5"></i> Free WiFi</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-650 cursor-pointer">
                            <input type="checkbox" name="facilities[]" value="Shower Room" class="rounded text-accent-500 focus:ring-accent-500"
                                {{ in_array('Shower Room', $attachedFacilities) ? 'checked' : '' }}>
                            <span><i class="fas fa-shower text-blue-500 mr-1.5"></i> Shower Room</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-650 cursor-pointer">
                            <input type="checkbox" name="facilities[]" value="Secure Parking" class="rounded text-accent-500 focus:ring-accent-500"
                                {{ in_array('Secure Parking', $attachedFacilities) ? 'checked' : '' }}>
                            <span><i class="fas fa-parking text-blue-500 mr-1.5"></i> Secure Parking</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-650 cursor-pointer">
                            <input type="checkbox" name="facilities[]" value="Full AC Area" class="rounded text-accent-500 focus:ring-accent-500"
                                {{ in_array('Full AC Area', $attachedFacilities) ? 'checked' : '' }}>
                            <span><i class="fas fa-snowflake text-blue-500 mr-1.5"></i> Full AC Area</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-650 cursor-pointer">
                            <input type="checkbox" name="facilities[]" value="Locker Room" class="rounded text-accent-500 focus:ring-accent-500"
                                {{ in_array('Locker Room', $attachedFacilities) ? 'checked' : '' }}>
                            <span><i class="fas fa-key text-blue-500 mr-1.5"></i> Locker Room</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-650 cursor-pointer">
                            <input type="checkbox" name="facilities[]" value="Canteen" class="rounded text-accent-500 focus:ring-accent-500"
                                {{ in_array('Canteen', $attachedFacilities) ? 'checked' : '' }}>
                            <span><i class="fas fa-coffee text-blue-500 mr-1.5"></i> Cafe / Canteen</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-650 cursor-pointer">
                            <input type="checkbox" name="facilities[]" value="Lightings" class="rounded text-accent-500 focus:ring-accent-500"
                                {{ in_array('Lightings', $attachedFacilities) ? 'checked' : '' }}>
                            <span><i class="fas fa-lightbulb text-blue-500 mr-1.5"></i> Lightings</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-650 cursor-pointer">
                            <input type="checkbox" name="facilities[]" value="Toilet" class="rounded text-accent-500 focus:ring-accent-500"
                                {{ in_array('Toilet', $attachedFacilities) ? 'checked' : '' }}>
                            <span><i class="fas fa-toilet text-blue-500 mr-1.5"></i> Toilet</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-650 cursor-pointer">
                            <input type="checkbox" name="facilities[]" value="Prayer Room" class="rounded text-accent-500 focus:ring-accent-500"
                                {{ in_array('Prayer Room', $attachedFacilities) ? 'checked' : '' }}>
                            <span><i class="fas fa-mosque text-blue-500 mr-1.5"></i> Prayer Room</span>
                        </label>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Other Custom Facilities (Comma Separated)</label>
                        <input type="text" name="custom_facilities" value="{{ $customAttachedString }}"
                               class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none"
                               placeholder="Example: Digital Scoreboard, Licensed Referee">
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Description</label>
                <textarea name="description" rows="3"
                          class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">{{ old('description', $field->description) }}</textarea>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-accent-500 hover:bg-accent-600 text-white text-sm font-bold px-6 py-2.5 rounded-lg transition shadow-sm">
                    <i class="fas fa-save mr-1"></i> Save Changes
                </button>
                <a href="{{ route('admin.fields') }}" class="bg-white border border-slate-200 text-slate-600 text-sm font-bold px-6 py-2.5 rounded-lg hover:bg-slate-50 transition shadow-sm">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
