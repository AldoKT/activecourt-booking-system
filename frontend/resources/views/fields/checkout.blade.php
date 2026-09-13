@extends('layouts.app')

@section('title', 'Payment - ActiveCourt')

@section('content')
<div class="bg-slate-50 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-sm text-slate-500 mb-6 flex items-center gap-2">
            <a href="{{ route('home') }}" class="hover:text-primary-600 font-medium transition">Home</a>
            <span>&rsaquo;</span>
            <a href="{{ route('fields.index') }}" class="hover:text-primary-600 font-medium transition">Fields</a>
            <span>&rsaquo;</span>
            <a href="{{ route('user.history') }}" class="hover:text-primary-600 font-medium transition">History</a>
            <span>&rsaquo;</span>
            <span class="text-primary-700 font-bold">Payment</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 items-start">

            {{-- Order Summary --}}
            <div class="lg:col-span-3">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 md:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center">
                            <i class="fas fa-receipt text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-slate-800">Order Summary</h2>
                            <p class="text-xs text-slate-500 font-medium">{{ $booking->booking_code }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-lg">
                            <div class="w-12 h-12 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-volleyball-ball"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-slate-800">{{ $booking->schedule->field->name_fields ?? '-' }}</h4>
                                @php
                                    $schedulesList = $booking->getSchedulesList();
                                @endphp
                                <p class="text-sm text-slate-500 font-medium mt-0.5">
                                    {{ $booking->subcourt_name }} &middot;
                                    {{ \Carbon\Carbon::parse($booking->play_date)->format('d M Y') }} &middot;
                                    {{ $schedulesList->map(fn($s) => substr($s->start_time, 0, 5) . ' - ' . substr($s->end_time, 0, 5))->implode(', ') }}
                                    ({{ $schedulesList->count() }} hrs)
                                </p>
                            </div>
                        </div>

                        <div class="divide-y divide-slate-100">
                            <div class="flex justify-between py-3">
                                <span class="text-slate-600 font-medium">Price per hour</span>
                                <span class="font-bold text-slate-800">Rp {{ number_format($booking->schedule->field->price_per_hour ?? 0, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between py-3">
                                <span class="text-slate-600 font-medium">Duration</span>
                                <span class="font-bold text-slate-800">{{ $schedulesList->count() }} hrs</span>
                            </div>
                            <div class="flex justify-between py-3">
                                <span class="text-slate-600 font-medium">Sub-Court</span>
                                <span class="font-bold text-slate-800">{{ $booking->subcourt_name }}</span>
                            </div>
                        </div>

                        <div class="pt-4 border-t-2 border-slate-200 flex justify-between items-center">
                            <span class="text-lg font-bold text-slate-800">Total Payment</span>
                            <span class="text-2xl font-extrabold text-accent-600">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payment Panel --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 md:p-8 sticky top-24">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-full bg-green-50 text-green-600 flex items-center justify-center">
                            <i class="fas fa-credit-card text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-slate-800">Payment</h2>
                            <p class="text-xs text-slate-500 font-medium">Select payment method</p>
                        </div>
                    </div>

                    <p class="text-sm text-slate-600 mb-6 leading-relaxed">
                        Click the button below to open the Midtrans payment popup.
                        You can pay via <strong>GoPay, ShopeePay, Bank Transfer,</strong> or other methods.
                    </p>

                    <button id="pay-button"
                            class="w-full bg-accent-500 hover:bg-accent-600 text-white font-bold py-3.5 rounded-lg transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                        <i class="fas fa-lock"></i>
                        Pay Now
                    </button>

                    <a href="{{ route('user.history') }}"
                       class="w-full mt-3 block text-center bg-white border border-slate-200 text-slate-600 font-bold py-3 rounded-lg hover:bg-slate-50 transition-all text-sm">
                        Back to History
                    </a>

                    <div class="mt-6 p-4 bg-slate-50 rounded-lg">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-shield-alt text-primary-500 mt-0.5"></i>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                Your payment is processed securely by <strong class="text-slate-700">Midtrans</strong>.
                                Your card and transaction details are encrypted with the highest security standards.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script
    src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="{{ config('midtrans.client_key') }}">
</script>

<script>
document.getElementById('pay-button').addEventListener('click', function(e) {
    e.preventDefault();

    let button = this;
    let originalText = button.innerHTML;
    let snapToken = "{{ $snapToken }}";

    if (!snapToken) {
        Swal.fire('Error', 'Snap Token not found', 'error');
        return;
    }

    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

    snap.pay(snapToken, {
        onSuccess: function(result) {
            fetch("{{ route('payment.verify', $booking->id_bookings) }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json"
                }
            })
            .then(response => response.json())
            .then(data => {
                window.location.href =
                    "{{ route('user.history') }}?payment=success";
            })
            .catch(error => {
                console.error(error);
                Swal.fire('Error', 'Failed to update booking status', 'error');
            });
        },

        onPending: function(result) {
            console.log('PENDING', result);
            window.location.href =
                "{{ route('user.history') }}?payment=pending";
        },

        onError: function(result) {
            console.log('ERROR', result);
            button.disabled = false;
            button.innerHTML = originalText;
            Swal.fire('Failed', 'Payment failed.', 'error');
        },

        onClose: function() {
            button.disabled = false;
            button.innerHTML = originalText;
            Swal.fire('Closed', 'You closed the payment window.', 'info');
        }
    });
});
</script>
@endpush
