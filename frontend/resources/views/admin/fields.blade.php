@extends('layouts.admin')

@section('title', 'Field Management - ActiveCourt')
@section('page-title', 'Field Management')

@section('content')
<div x-data="{ addModalOpen: false }">

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800">Manage Fields</h3>
            <button @click="addModalOpen = true" class="bg-accent-500 hover:bg-accent-600 text-white text-sm font-bold px-4 py-2 rounded-lg transition shadow-sm flex items-center gap-1.5">
                <i class="fas fa-plus"></i>
                Add New Field
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Field Name</th>
                        <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Sport Type</th>
                        <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Location</th>
                        <th class="text-center py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Sub-courts</th>
                        <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Price / Hour</th>
                        <th class="text-center py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($fields ?? [] as $field)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-slate-200 rounded-lg overflow-hidden flex-shrink-0">
                                    <img src="{{ $field->image ? asset('storage/' . $field->image) : 'https://images.unsplash.com/photo-1544919982-b61976f0ba43?auto=format&fit=crop&q=80&w=100' }}" class="w-full h-full object-cover">
                                </div>
                                <span class="font-semibold text-slate-800">{{ $field->name_fields }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <span class="px-3 py-1 bg-primary-100 text-primary-700 text-xs font-bold rounded-full">{{ $field->type_fields }}</span>
                        </td>
                        <td class="py-4 px-6 text-sm text-slate-600">{{ $field->address ? Str::limit($field->address, 30) : '-' }}</td>
                        <td class="py-4 px-6 text-center">
                            <span class="text-sm font-semibold text-slate-700">{{ count($field->sub_courts ?? []) }}</span>
                        </td>
                        <td class="py-4 px-6">
                            <span class="font-semibold text-slate-700">Rp {{ number_format($field->price_per_hour, 0, ',', '.') }}</span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.fields.edit', $field->id_fields) }}" class="w-8 h-8 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg transition flex items-center justify-center" title="Edit">
                                    <i class="fas fa-pen text-xs"></i>
                                </a>
                                <form action="{{ route('admin.fields.destroy', $field->id_fields) }}" method="POST" class="inline" data-confirm="Are you sure you want to delete court {{ $field->name_fields }}?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition flex items-center justify-center" title="Delete">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center">
                            <div class="text-5xl text-blue-500 mb-3"><i class="fas fa-volleyball-ball"></i></div>
                            <p class="text-slate-500 font-medium">No fields found</p>
                            <p class="text-slate-400 text-sm mt-1">Add a new field to start managing.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Field Modal -->
    <div x-show="addModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
         x-cloak>
        
        <!-- Modal container -->
        <div @click.away="addModalOpen = false" 
             x-show="addModalOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col mx-4">
             
             <!-- Modal Header -->
             <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex justify-between items-center">
                 <div class="flex items-center gap-3">
                     <div class="w-10 h-10 bg-primary-100 text-primary-600 rounded-lg flex items-center justify-center">
                         <i class="fas fa-plus"></i>
                     </div>
                     <div>
                         <h3 class="font-bold text-slate-800">Add New Field</h3>
                         <p class="text-xs text-slate-500 font-medium">Create a new sports facility venue</p>
                     </div>
                 </div>
                 <button @click="addModalOpen = false" class="text-slate-400 hover:text-slate-600 focus:outline-none">
                     <i class="fas fa-times text-lg"></i>
                 </button>
             </div>
             
             <!-- Modal Form (Scrollable) -->
             <form action="{{ route('admin.fields.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4 overflow-y-auto flex-1 max-h-[calc(90vh-140px)]">
                 @csrf
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                     <div>
                         <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Field Name</label>
                         <input type="text" name="name_fields" required
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                     </div>
                     <div>
                         <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Sport Type</label>
                         <div x-data="{ selectedType: 'Futsal', customType: '' }">
                             <input type="hidden" name="type_fields" :value="selectedType === 'Other' ? customType : selectedType">
                             <select x-model="selectedType" class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                                 <option value="Futsal">Futsal</option>
                                 <option value="Badminton">Badminton</option>
                                 <option value="Basketball">Basketball</option>
                                 <option value="Volleyball">Volleyball</option>
                                 <option value="Tennis">Tennis</option>
                                 <option value="Other">Other (Specify)</option>
                             </select>
                             <div x-show="selectedType === 'Other'" style="display: none;" class="mt-2">
                                 <input type="text" x-model="customType" :required="selectedType === 'Other'" placeholder="Type sport type..." class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                             </div>
                         </div>
                     </div>
                     <div>
                         <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Address</label>
                         <input type="text" name="address" required
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                     </div>
                     <div>
                         <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Price per Hour (Rp)</label>
                         <input type="number" name="price_per_hour" required min="0"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                     </div>
                     <div>
                         <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Capacity</label>
                         <input type="number" name="capacity" required min="1"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                     </div>
                     <div>
                         <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Sub-Courts (Comma Separated)</label>
                         <input type="text" name="sub_courts" value="Court 1" required
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none"
                                placeholder="Example: Court A, Court B">
                         <p class="text-xs text-slate-400 mt-1">Separate names with a comma (,)</p>
                     </div>
                 </div>
                 <div>
                     <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Field Image (Optional)</label>
                     <input type="file" name="image" accept="image/*"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-accent-50 file:text-accent-700 hover:file:bg-accent-100">
                     @error('image')
                         <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                     @enderror
                 </div>
                 <div>
                     <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Field Facilities</label>
                     <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mb-3 bg-slate-50 p-4 rounded-lg border border-slate-200">
                         <label class="flex items-center gap-2 text-sm text-slate-650 cursor-pointer">
                             <input type="checkbox" name="facilities[]" value="Free WiFi" class="rounded text-accent-500 focus:ring-accent-500">
                             <span><i class="fas fa-wifi text-blue-500 mr-1.5"></i> Free WiFi</span>
                         </label>
                         <label class="flex items-center gap-2 text-sm text-slate-650 cursor-pointer">
                             <input type="checkbox" name="facilities[]" value="Shower Room" class="rounded text-accent-500 focus:ring-accent-500">
                             <span><i class="fas fa-shower text-blue-500 mr-1.5"></i> Shower Room</span>
                         </label>
                         <label class="flex items-center gap-2 text-sm text-slate-650 cursor-pointer">
                             <input type="checkbox" name="facilities[]" value="Secure Parking" class="rounded text-accent-500 focus:ring-accent-500">
                             <span><i class="fas fa-parking text-blue-500 mr-1.5"></i> Secure Parking</span>
                         </label>
                         <label class="flex items-center gap-2 text-sm text-slate-650 cursor-pointer">
                             <input type="checkbox" name="facilities[]" value="Full AC Area" class="rounded text-accent-500 focus:ring-accent-500">
                             <span><i class="fas fa-snowflake text-blue-500 mr-1.5"></i> Full AC Area</span>
                         </label>
                         <label class="flex items-center gap-2 text-sm text-slate-650 cursor-pointer">
                             <input type="checkbox" name="facilities[]" value="Locker Room" class="rounded text-accent-500 focus:ring-accent-500">
                             <span><i class="fas fa-key text-blue-500 mr-1.5"></i> Locker Room</span>
                         </label>
                         <label class="flex items-center gap-2 text-sm text-slate-650 cursor-pointer">
                             <input type="checkbox" name="facilities[]" value="Canteen" class="rounded text-accent-500 focus:ring-accent-500">
                             <span><i class="fas fa-coffee text-blue-500 mr-1.5"></i> Cafe / Canteen</span>
                         </label>
                         <label class="flex items-center gap-2 text-sm text-slate-650 cursor-pointer">
                             <input type="checkbox" name="facilities[]" value="Lightings" class="rounded text-accent-500 focus:ring-accent-500">
                             <span><i class="fas fa-lightbulb text-blue-500 mr-1.5"></i> Lightings</span>
                         </label>
                         <label class="flex items-center gap-2 text-sm text-slate-650 cursor-pointer">
                             <input type="checkbox" name="facilities[]" value="Toilet" class="rounded text-accent-500 focus:ring-accent-500">
                             <span><i class="fas fa-toilet text-blue-500 mr-1.5"></i> Toilet</span>
                         </label>
                         <label class="flex items-center gap-2 text-sm text-slate-650 cursor-pointer">
                             <input type="checkbox" name="facilities[]" value="Prayer Room" class="rounded text-accent-500 focus:ring-accent-500">
                             <span><i class="fas fa-mosque text-blue-500 mr-1.5"></i> Prayer Room</span>
                         </label>
                     </div>
                     <div class="mb-2">
                         <label class="block text-xs font-semibold text-slate-500 mb-1">Other Custom Facilities (Comma Separated)</label>
                         <input type="text" name="custom_facilities" 
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none"
                                placeholder="Example: Digital Scoreboard, Licensed Referee">
                     </div>
                 </div>
                 <div>
                     <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Description</label>
                     <textarea name="description" rows="3"
                               class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none"></textarea>
                 </div>
                 
                 <!-- Modal Footer Actions -->
                 <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 no-print">
                     <button type="button" @click="addModalOpen = false" class="bg-white border border-slate-200 text-slate-700 text-sm font-bold px-5 py-2.5 rounded-lg hover:bg-slate-50 transition">
                         Cancel
                     </button>
                     <button type="submit" class="bg-accent-500 hover:bg-accent-600 text-white text-sm font-bold px-6 py-2.5 rounded-lg transition shadow-sm flex items-center gap-2">
                         <i class="fas fa-plus"></i> Add Field
                     </button>
                 </div>
             </form>
        </div>
    </div>

</div>
@endsection
