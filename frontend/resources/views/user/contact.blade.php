@extends('layouts.app')

@section('title', 'Contact Us - ActiveCourt')

@section('content')
<div class="bg-slate-50 min-h-screen relative overflow-hidden font-sans">
    <!-- Decorative background elements similar to Fields page -->
    <div class="absolute top-[-10%] left-[-5%] w-96 h-96 bg-primary-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-pulse pointer-events-none z-0"></div>
    <div class="absolute bottom-[-10%] right-[-5%] w-96 h-96 bg-accent-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-pulse pointer-events-none z-0" style="animation-delay: 2s;"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-40 pointer-events-none z-0"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-24 space-y-24">
        
        <!-- Hero Section -->
        <section class="flex flex-col lg:flex-row items-center gap-16" x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">
            <div class="flex-1 space-y-6 transition-all duration-1000 transform" :class="show ? 'translate-y-0 opacity-100' : 'translate-y-12 opacity-0'">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary-100/80 border border-primary-200 backdrop-blur-md">
                    <span class="w-2 h-2 rounded-full bg-primary-600 animate-pulse"></span>
                    <span class="text-sm font-bold text-primary-700 tracking-wide uppercase">Get In Touch</span>
                </div>
                <h1 class="text-5xl lg:text-6xl font-extrabold text-slate-800 leading-tight">
                    Contact <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-primary-400">Us</span>
                </h1>
                <p class="text-xl text-slate-600 leading-relaxed max-w-lg">
                    We're here to help you with bookings, partnerships, and support. Drop us a message and we'll get back to you shortly.
                </p>
            </div>
            <div class="flex-1 w-full transition-all duration-1000 delay-300 transform" :class="show ? 'translate-x-0 opacity-100' : 'translate-x-12 opacity-0'">
                <div class="relative w-full h-[400px] rounded-3xl overflow-hidden shadow-2xl border-4 border-white group">
                    <img src="https://images.unsplash.com/photo-1556740738-b6a63e27c4df?auto=format&fit=crop&q=80&w=1200" alt="Customer Support" class="w-full h-full object-cover group-hover:scale-105 transition duration-700 ease-in-out">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/40 to-transparent"></div>
                </div>
            </div>
        </section>

        <!-- Main Section (Split Columns) -->
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-12" x-data="{ show: false }" x-init="setTimeout(() => show = true, 300)">
            
            <!-- Left Column: Contact Info Card -->
            <div class="bg-primary-900 text-white rounded-3xl p-10 lg:p-12 shadow-2xl relative overflow-hidden transition-all duration-700 transform" :class="show ? 'translate-y-0 opacity-100' : 'translate-y-12 opacity-0'">
                <!-- Background decor for card -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-primary-600 rounded-full mix-blend-screen filter blur-3xl opacity-30 translate-x-1/2 -translate-y-1/2 pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-accent-500 rounded-full mix-blend-screen filter blur-3xl opacity-20 -translate-x-1/2 translate-y-1/2 pointer-events-none"></div>
                
                <div class="relative z-10">
                    <h3 class="text-3xl font-bold mb-8">Contact Information</h3>
                    <p class="text-primary-100 mb-10 leading-relaxed">Have questions about our platform or want to list your sports field? Reach out to us using the information below.</p>
                    
                    <div class="space-y-8">
                        <div class="flex items-start gap-5">
                            <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center shrink-0 border border-white/20">
                                <i class="fas fa-map-marker-alt text-xl text-accent-400"></i>
                            </div>
                            <div>
                                <h4 class="text-sm text-primary-200 font-semibold mb-1 uppercase tracking-wider">Address</h4>
                                <p class="text-lg font-medium">Jl. Siwalankerto No.121<br>Surabaya, Jawa Timur</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-5">
                            <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center shrink-0 border border-white/20">
                                <i class="fas fa-phone-alt text-xl text-accent-400"></i>
                            </div>
                            <div>
                                <h4 class="text-sm text-primary-200 font-semibold mb-1 uppercase tracking-wider">Phone</h4>
                                <p class="text-lg font-medium">+62 812-3456-7890</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-5">
                            <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center shrink-0 border border-white/20">
                                <i class="fas fa-envelope text-xl text-accent-400"></i>
                            </div>
                            <div>
                                <h4 class="text-sm text-primary-200 font-semibold mb-1 uppercase tracking-wider">Email</h4>
                                <p class="text-lg font-medium">support@activecourt.com</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-5">
                            <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center shrink-0 border border-white/20">
                                <i class="fas fa-clock text-xl text-accent-400"></i>
                            </div>
                            <div>
                                <h4 class="text-sm text-primary-200 font-semibold mb-1 uppercase tracking-wider">Opening Hours</h4>
                                <p class="text-lg font-medium">Everyday: 08.00 - 22.00 WIB</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Contact Form Card -->
            <div class="bg-white/70 backdrop-blur-xl rounded-3xl border border-white/60 p-10 shadow-xl transition-all duration-700 delay-200 transform" :class="show ? 'translate-y-0 opacity-100' : 'translate-y-12 opacity-0'">
                <h3 class="text-3xl font-bold text-slate-800 mb-2">Send us a message</h3>
                <p class="text-slate-500 mb-8">Fill out the form below and we will get back to you within 24 hours.</p>
                
                @if(session('contact_success'))
                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            Swal.mixin({
                                toast: true,
                                position: 'bottom-end',
                                showConfirmButton: false,
                                timer: 4000,
                                timerProgressBar: true
                            }).fire({
                                icon: 'success',
                                title: 'Message sent successfully! We will contact you soon.'
                            });
                        });
                    </script>
                @endif

                <form action="{{ route('contact.submit') }}" method="POST" class="space-y-5">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Full Name</label>
                            <input type="text" name="name" required placeholder="John Doe" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition shadow-inner">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Phone Number</label>
                            <input type="tel" name="phone" required placeholder="+62 8xx-xxxx-xxxx" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition shadow-inner">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Email Address</label>
                        <input type="email" name="email" required placeholder="you@example.com" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition shadow-inner">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Subject</label>
                        <select name="topic" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition shadow-inner appearance-none">
                            <option value="">Select a topic...</option>
                            <option value="Booking Inquiry">Booking Inquiry</option>
                            <option value="Payment Issue">Payment Issue</option>
                            <option value="Partnership">Partnership</option>
                            <option value="Technical Support">Technical Support</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Message</label>
                        <textarea name="message" required rows="4" placeholder="How can we help you?" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition shadow-inner resize-none"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-primary-600 hover:bg-accent-500 text-white font-bold py-4 rounded-xl transition-all duration-300 shadow-lg hover:shadow-accent-500/30 hover:-translate-y-1 flex items-center justify-center gap-2">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
        </section>

        <!-- FAQ Section -->
        <section x-data="{ show: false, active: null }" x-init="setTimeout(() => show = true, 500)">
            <div class="max-w-3xl mx-auto transition-all duration-700 transform" :class="show ? 'translate-y-0 opacity-100' : 'translate-y-12 opacity-0'">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-slate-800">Frequently Asked Questions</h2>
                    <p class="text-slate-500 mt-3">Find quick answers to common questions about ActiveCourt.</p>
                </div>
                
                <div class="space-y-4">
                    <!-- FAQ 1 -->
                    <div class="bg-white/70 backdrop-blur-xl border border-white/60 rounded-2xl shadow-md overflow-hidden">
                        <button @click="active !== 1 ? active = 1 : active = null" class="w-full flex items-center justify-between px-6 py-5 text-left focus:outline-none">
                            <span class="font-bold text-slate-800 text-lg">How do I book a field?</span>
                            <i class="fas fa-chevron-down text-primary-500 transition-transform duration-300" :class="active === 1 ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="active === 1" x-collapse x-cloak>
                            <div class="px-6 pb-5 text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                                Simply navigate to our "Fields" page, select your preferred location and sport, choose an available time slot, and proceed to checkout. You will need to create an account to manage your bookings.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="bg-white/70 backdrop-blur-xl border border-white/60 rounded-2xl shadow-md overflow-hidden">
                        <button @click="active !== 2 ? active = 2 : active = null" class="w-full flex items-center justify-between px-6 py-5 text-left focus:outline-none">
                            <span class="font-bold text-slate-800 text-lg">Can I cancel my booking?</span>
                            <i class="fas fa-chevron-down text-primary-500 transition-transform duration-300" :class="active === 2 ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="active === 2" x-collapse x-cloak>
                            <div class="px-6 pb-5 text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                                Yes, you can cancel your booking up to 3 days (H-3) before the scheduled time for a full refund. Cancellations made within 3 days may be subject to a fee. Please check your user dashboard to manage bookings.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="bg-white/70 backdrop-blur-xl border border-white/60 rounded-2xl shadow-md overflow-hidden">
                        <button @click="active !== 3 ? active = 3 : active = null" class="w-full flex items-center justify-between px-6 py-5 text-left focus:outline-none">
                            <span class="font-bold text-slate-800 text-lg">How do payments work?</span>
                            <i class="fas fa-chevron-down text-primary-500 transition-transform duration-300" :class="active === 3 ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="active === 3" x-collapse x-cloak>
                            <div class="px-6 pb-5 text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                                We accept various payment methods including credit/debit cards, bank transfers, and popular e-wallets like GoPay and OVO via our secure payment gateway (Midtrans).
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 4 -->
                    <div class="bg-white/70 backdrop-blur-xl border border-white/60 rounded-2xl shadow-md overflow-hidden">
                        <button @click="active !== 4 ? active = 4 : active = null" class="w-full flex items-center justify-between px-6 py-5 text-left focus:outline-none">
                            <span class="font-bold text-slate-800 text-lg">Can I become a venue partner?</span>
                            <i class="fas fa-chevron-down text-primary-500 transition-transform duration-300" :class="active === 4 ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="active === 4" x-collapse x-cloak>
                            <div class="px-6 pb-5 text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                                Absolutely! We are always looking to expand our network. Select "Partnership" in the contact form subject above and send us your venue details. Our partnership team will contact you within 1-2 business days.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Map Section -->
        <section x-data="{ show: false }" x-init="setTimeout(() => show = true, 700)">
            <div class="bg-white/70 backdrop-blur-xl rounded-3xl border border-white/60 p-3 shadow-xl transition-all duration-700 transform" :class="show ? 'scale-100 opacity-100' : 'scale-95 opacity-0'">
                <div class="w-full h-[400px] rounded-2xl overflow-hidden bg-slate-200">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3957.3697669463273!2d112.73516591477521!3d-7.312282294723049!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7fb7733fb01df%3A0x8eec9b775f0a719c!2sUniversitas%20Kristen%20Petra!5e0!3m2!1sen!2sid!4v1655184852955!5m2!1sen!2sid" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </section>

        <!-- CTA Banner -->
        <section x-data="{ show: false }" x-init="setTimeout(() => show = true, 900)">
            <div class="bg-gradient-to-r from-primary-700 via-primary-600 to-primary-500 rounded-3xl p-12 text-center shadow-2xl relative overflow-hidden transition-all duration-700 transform" :class="show ? 'translate-y-0 opacity-100' : 'translate-y-12 opacity-0'">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
                <div class="relative z-10">
                    <h2 class="text-3xl lg:text-4xl font-extrabold text-white mb-4">Need immediate assistance?</h2>
                    <p class="text-primary-100 mb-8 max-w-xl mx-auto text-lg">Our support team is on standby to help you resolve any issues instantly during working hours.</p>
                    <div class="flex flex-col sm:flex-row justify-center gap-4">
                        <a href="tel:+6281234567890" class="bg-white text-primary-700 hover:bg-slate-50 font-bold py-4 px-8 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-1 flex items-center justify-center gap-2">
                            <i class="fas fa-phone-alt"></i> Call Us Now
                        </a>
                        <button class="bg-accent-500 hover:bg-accent-600 text-white font-bold py-4 px-8 rounded-xl transition-all duration-300 shadow-lg hover:shadow-accent-500/30 hover:-translate-y-1 flex items-center justify-center gap-2 border border-accent-400">
                            <i class="fas fa-comments"></i> Live Chat
                        </button>
                    </div>
                </div>
            </div>
        </section>

    </div>
</div>
@endsection