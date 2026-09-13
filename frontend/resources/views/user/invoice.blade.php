<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Invoice {{ $booking->booking_code }} - ActiveCourt</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 300: '#93c5fd', 400: '#60a5fa',
                            500: '#3b82f6', 600: '#1d4ed8', 700: '#1e3a5f', 800: '#1e2d4f', 900: '#0f1b33',
                        },
                        accent: {
                            50: '#fff7ed', 100: '#ffedd5', 200: '#fed7aa', 300: '#fdba74', 400: '#fb923c',
                            500: '#f97316', 600: '#ea580c', 700: '#c2410c',
                        }
                    }
                }
            }
        }
    </script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        @media print {
            body { background-color: #ffffff; }
            .no-print { display: none !important; }
            #invoice-content { 
                border: none !important; 
                box-shadow: none !important; 
                padding: 0 !important; 
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased py-8 sm:py-12">

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Top bar with Close & Print Actions (Hidden in Print) -->
        <div class="flex justify-between items-center mb-6 no-print bg-white border border-slate-200 shadow-sm rounded-2xl px-6 py-4">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                <span class="text-sm font-semibold text-slate-600">Official Invoice</span>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="window.print()" class="bg-primary-600 hover:bg-primary-700 text-white font-bold px-4 py-2 rounded-xl transition shadow-md flex items-center gap-2 text-sm">
                    <i class="fas fa-print"></i> Print
                </button>
                <button onclick="closeOrRedirect()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 font-bold px-4 py-2 rounded-xl transition text-sm flex items-center gap-2">
                    <i class="fas fa-times-circle"></i> Close
                </button>
            </div>
        </div>

        <!-- Invoice Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden" id="invoice-content">
            <!-- Header -->
            <div class="bg-primary-700 px-8 py-8 text-white">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold flex items-center gap-2.5">
                            <i class="fas fa-receipt"></i> INVOICE
                        </h1>
                        <p class="text-primary-200 text-sm mt-1.5 font-medium">ActiveCourt - Sports Venue Booking Platform</p>
                    </div>
                    <div class="sm:text-right">
                        <p class="text-3xl font-black tracking-wide">{{ $booking->booking_code }}</p>
                        <p class="text-primary-200 text-xs mt-1 uppercase font-semibold tracking-wider">Booking Code</p>
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="p-6 sm:p-8">
                <!-- Status Badge -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">Payment Status</h2>
                        <p class="text-sm text-slate-500 mt-0.5">This invoice is an official proof of payment from ActiveCourt</p>
                    </div>
                    <span class="px-4 py-2 bg-green-100 text-green-700 text-sm font-extrabold rounded-full border border-green-200 flex items-center gap-2 self-start sm:self-auto">
                        <i class="fas fa-check-circle"></i> PAID
                    </span>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-slate-50 rounded-xl p-5 border border-slate-100">
                        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Customer Details</h3>
                        <div class="space-y-2.5 text-sm">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Name</span>
                                <span class="font-semibold text-slate-800">{{ $booking->user->name_users ?? $booking->user->username }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Email</span>
                                <span class="font-semibold text-slate-800">{{ $booking->user->email }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-5 border border-slate-100">
                        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Booking Details</h3>
                        <div class="space-y-2.5 text-sm">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Booking Code</span>
                                <span class="font-bold text-primary-700">{{ $booking->booking_code }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Booking Date</span>
                                <span class="font-semibold text-slate-800">{{ \Carbon\Carbon::parse($booking->created_at)->format('d M Y, H:i') }}</span>
                            </div>
                            @if($booking->payment && $booking->payment->paid_at)
                            <div class="flex justify-between">
                                <span class="text-slate-500">Paid At</span>
                                <span class="font-semibold text-slate-800">{{ \Carbon\Carbon::parse($booking->payment->paid_at)->format('d M Y, H:i') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Field Detail -->
                <div class="mb-8">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Field Details</h3>
                    <div class="bg-slate-50 rounded-xl p-5 border border-slate-100">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-primary-50 text-primary-600 rounded-xl flex items-center justify-center flex-shrink-0 border border-primary-100">
                                <i class="fas fa-volleyball-ball text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-slate-800 text-lg">{{ $booking->schedule->field->name_fields ?? '-' }}</h4>
                                <p class="text-sm text-slate-500 mt-0.5">{{ $booking->schedule->field->type_fields ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Jadwal -->
                @php
                    $schedulesList = $booking->getSchedulesList();
                    $field = $booking->schedule ? $booking->schedule->field : null;
                @endphp
                <div class="mb-8">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Rental Schedule</h3>
                    <div class="bg-slate-50 rounded-xl p-5 border border-slate-100">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-slate-500 block text-xs uppercase tracking-wide font-medium">Date</span>
                                <span class="font-bold text-slate-800 text-base mt-0.5 block">{{ \Carbon\Carbon::parse($booking->play_date)->format('d M Y') }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500 block text-xs uppercase tracking-wide font-medium">Time</span>
                                <span class="font-bold text-slate-800 text-base mt-0.5 block">
                                    {{ $schedulesList->map(fn($s) => substr($s->start_time, 0, 5) . ' - ' . substr($s->end_time, 0, 5))->implode(', ') }}
                                </span>
                            </div>
                            <div>
                                <span class="text-slate-500 block text-xs uppercase tracking-wide font-medium">Duration</span>
                                <span class="font-bold text-slate-800 text-base mt-0.5 block">{{ $schedulesList->count() }} hrs</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Detail -->
                <div class="mb-8">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Payment Details</h3>
                    <div class="bg-slate-50 rounded-xl p-5 border border-slate-100">
                        <div class="space-y-3.5 text-sm">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Price per Hour</span>
                                <span class="font-semibold text-slate-800">Rp {{ number_format($field->price_per_hour ?? 0, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Total Hours</span>
                                <span class="font-semibold text-slate-800">{{ $schedulesList->count() }} hrs</span>
                            </div>
                            @if($booking->payment && $booking->payment->payment_method)
                            <div class="flex justify-between">
                                <span class="text-slate-500">Payment Method</span>
                                <span class="font-semibold text-slate-800 capitalize">{{ str_replace('_', ' ', $booking->payment->payment_method) }}</span>
                            </div>
                            @endif
                            @if($booking->payment && $booking->payment->midtrans_transaction_id)
                            <div class="flex justify-between">
                                <span class="text-slate-500">Transaction ID</span>
                                <span class="font-semibold text-slate-800 text-xs">{{ $booking->payment->midtrans_transaction_id }}</span>
                            </div>
                            @endif
                            <div class="border-t border-slate-200 pt-4 flex justify-between items-center">
                                <span class="font-bold text-slate-800 text-base">Total Payment</span>
                                <span class="font-extrabold text-accent-600 text-2xl">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="border-t border-slate-200 pt-6 text-center">
                    <p class="text-xs text-slate-400 leading-relaxed">
                        This invoice is a valid proof of payment. <br>
                        ActiveCourt &copy; {{ date('Y') }} - Sports Court Booking Platform
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function closeOrRedirect() {
            if (window.opener || window.history.length === 1) {
                window.close();
            } else {
                window.location.href = "{{ route('user.history') }}";
            }
        }
    </script>
</body>
</html>
