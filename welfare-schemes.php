<?php
// welfare-schemes.php
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Welfare Schemes - Giveoraemart Ecosystem';
require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">

    <!-- Hero / Header Section -->
    <div class="relative overflow-hidden rounded-3xl bg-slate-900 border border-slate-800 p-8 md:p-12 text-center space-y-4">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <span class="inline-flex items-center gap-2 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-4 py-1.5 rounded-full text-xs font-bold tracking-wider uppercase">
            <i class="fa-solid fa-hand-holding-heart text-amber-400"></i> Social Upliftment & Community Care
        </span>

        <h1 class="text-3xl md:text-5xl font-black text-white tracking-tight">
            Giveoraemart Welfare Schemes
        </h1>

        <p class="text-slate-400 max-w-2xl mx-auto text-sm md:text-base leading-relaxed">
            At Giveoraemart, commerce meets compassion. We allocate a dedicated pool of ecosystem earnings to support our active members and their families during critical life events and educational journeys.
        </p>

        <div class="flex flex-wrap justify-center gap-4 pt-4 text-xs font-bold">
            <a href="#charity-scheme" class="bg-emerald-500 hover:bg-emerald-600 text-slate-950 px-6 py-3 rounded-xl transition flex items-center gap-2 shadow-lg shadow-emerald-500/10">
                <i class="fa-solid fa-heart-pulse"></i> Charity Support
            </a>
            <a href="#vidya-vikas" class="bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 px-6 py-3 rounded-xl transition flex items-center gap-2">
                <i class="fa-solid fa-graduation-cap text-amber-400"></i> Vidya Vikas Scheme
            </a>
        </div>
    </div>

    <!-- Scheme 1: Charity Support Scheme -->
    <div id="charity-scheme" class="bg-slate-900 border border-slate-800 rounded-3xl p-6 md:p-10 space-y-8 scroll-mt-24">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-800 pb-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-2xl flex items-center justify-center text-2xl font-bold">
                    <i class="fa-solid fa-hand-holding-medical"></i>
                </div>
                <div>
                    <span class="text-xs uppercase font-bold text-rose-400 tracking-wider">Healthcare & Emergency Aid</span>
                    <h2 class="text-2xl font-extrabold text-white">Charity Support Scheme</h2>
                </div>
            </div>
            <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-4 py-2 rounded-xl text-xs font-bold self-start md:self-auto">
                <i class="fa-solid fa-shield-check mr-1"></i> 100% Ecosystem Funded
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Feature 1 -->
            <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800 space-y-3">
                <i class="fa-solid fa-hospital text-emerald-400 text-2xl"></i>
                <h3 class="text-sm font-bold text-white">Emergency Medical Assistance</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Financial assistance granted up to $2,000 for unexpected hospitalizations, critical surgeries, or emergency healthcare expenses.
                </p>
            </div>
            <!-- Feature 2 -->
            <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800 space-y-3">
                <i class="fa-solid fa-people-roof text-amber-400 text-2xl"></i>
                <h3 class="text-sm font-bold text-white">Family Security Net</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    In the unfortunate event of permanent disability or bereavement of an active member, direct family support grants are provided.
                </p>
            </div>
            <!-- Feature 3 -->
            <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800 space-y-3">
                <i class="fa-solid fa-kit-medical text-rose-400 text-2xl"></i>
                <h3 class="text-sm font-bold text-white">Disaster Relief Fund</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Immediate relief hampers, basic necessity packs, and structural rebuilding assistance during natural disasters or accidents.
                </p>
            </div>
        </div>

        <!-- Eligibility & How it Works -->
        <div class="bg-slate-950/60 rounded-2xl p-6 border border-slate-800/80 grid grid-cols-1 md:grid-cols-2 gap-8 text-xs">
            <div class="space-y-3">
                <h4 class="font-bold text-slate-200 uppercase tracking-wider text-xs flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-400"></i> Eligibility Criteria
                </h4>
                <ul class="space-y-2 text-slate-400 list-disc list-inside leading-relaxed">
                    <li>Must be a registered Giveoraemart member with an active account.</li>
                    <li>Completion of basic account profile verification (KYC & Phone verification).</li>
                    <li>Referral activity or minimum matrix package subscription enhances approval priority.</li>
                </ul>
            </div>
            <div class="space-y-3">
                <h4 class="font-bold text-slate-200 uppercase tracking-wider text-xs flex items-center gap-2">
                    <i class="fa-solid fa-file-lines text-amber-400"></i> How to Submit a Claim
                </h4>
                <p class="text-slate-400 leading-relaxed">
                    Submit authentic medical discharge summaries, hospital bills, or relevant government emergency certificates through the user dashboard or directly via customer support.
                </p>
            </div>
        </div>
    </div>

    <!-- Scheme 2: Vidya Vikas Scheme -->
    <div id="vidya-vikas" class="bg-slate-900 border border-slate-800 rounded-3xl p-6 md:p-10 space-y-8 scroll-mt-24">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-800 pb-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-amber-500/10 border border-amber-500/20 text-amber-400 rounded-2xl flex items-center justify-center text-2xl font-bold">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div>
                    <span class="text-xs uppercase font-bold text-amber-400 tracking-wider">Education & Youth Empowerment</span>
                    <h2 class="text-2xl font-extrabold text-white">Vidya Vikas Educational Scheme</h2>
                </div>
            </div>
            <span class="bg-amber-500/10 text-amber-400 border border-amber-500/20 px-4 py-2 rounded-xl text-xs font-bold self-start md:self-auto">
                <i class="fa-solid fa-award mr-1"></i> Youth Empowerment
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Feature 1 -->
            <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800 space-y-3">
                <i class="fa-solid fa-book-open-reader text-amber-400 text-2xl"></i>
                <h3 class="text-sm font-bold text-white">School & College Fee Grants</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Annual educational subsidies to assist with school tuition fees, college admission costs, and examination fees for children of members.
                </p>
            </div>
            <!-- Feature 2 -->
            <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800 space-y-3">
                <i class="fa-solid fa-laptop-code text-emerald-400 text-2xl"></i>
                <h3 class="text-sm font-bold text-white">Digital Learning Kits</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Sponsorship for tablets, laptops, and digital learning tools to empower students in higher secondary and vocational courses.
                </p>
            </div>
            <!-- Feature 3 -->
            <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800 space-y-3">
                <i class="fa-solid fa-star-exclamation text-rose-400 text-2xl"></i>
                <h3 class="text-sm font-bold text-white">Merit Excellence Awards</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Special financial awards and certificates of distinction for students scoring above 85% in board or degree examinations.
                </p>
            </div>
        </div>

        <!-- Application Process -->
        <div class="bg-slate-950/60 rounded-2xl p-6 border border-slate-800/80 grid grid-cols-1 md:grid-cols-2 gap-8 text-xs">
            <div class="space-y-3">
                <h4 class="font-bold text-slate-200 uppercase tracking-wider text-xs flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-400"></i> Who Can Qualify?
                </h4>
                <ul class="space-y-2 text-slate-400 list-disc list-inside leading-relaxed">
                    <li>Dependent children of verified Giveoraemart members.</li>
                    <li>Students enrolled in recognized primary, secondary, or undergraduate educational institutions.</li>
                </ul>
            </div>
            <div class="space-y-3">
                <h4 class="font-bold text-slate-200 uppercase tracking-wider text-xs flex items-center gap-2">
                    <i class="fa-solid fa-graduation-cap text-amber-400"></i> Required Documentation
                </h4>
                <p class="text-slate-400 leading-relaxed">
                    Upload latest marksheet/grade card, student ID card, and current academic year fee receipt via the account support desk.
                </p>
            </div>
        </div>
    </div>

    <!-- Call To Action -->
    <div class="bg-gradient-to-r from-emerald-950 via-slate-900 to-amber-950 border border-slate-800 rounded-3xl p-8 text-center space-y-4">
        <h3 class="text-xl font-bold text-white">Ready to join a community that gives back?</h3>
        <p class="text-xs text-slate-300 max-w-xl mx-auto leading-relaxed">
            Every product purchased and package activated directly funds our welfare pools. Register today to be part of the Giveoraemart family.
        </p>
        <div class="pt-2">
            <a href="/register.php" class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-extrabold px-8 py-3.5 rounded-xl transition text-xs shadow-xl shadow-emerald-500/20">
                Join Giveoraemart Now <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
