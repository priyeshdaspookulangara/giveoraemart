<?php
// welfare-schemes.php
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Giveoraemart – Welfare Schemes & Community Protection';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero" style="background:var(--navy)">
  <div class="wrap hero-grid">
    <div>
      <h1>Social Upliftment & Community Welfare Protection</h1>
      <p>Commerce meets compassion at Giveoraemart. A dedicated portion of ecosystem earnings powers financial aid, medical coverage, and student scholarship grants for our active community members.</p>
      <div class="cta">
        <a class="btn" href="#charity">Charity Support Scheme</a>
        <a class="btn ghost" href="#vidya-vikas">Vidya Vikas Scheme</a>
      </div>
    </div>
    <aside class="panel" aria-label="Welfare Pool Highlights">
      <h3>100% Ecosystem Funded</h3>
      <p>Active members receive automatic health and educational coverage.</p>
      <div class="item">
        <div><strong>Charity Support Payout</strong><span>Up to 60 monthly cycles</span></div>
        <b style="color:var(--gold)">₹3,000 / mo</b>
      </div>
      <div class="item">
        <div><strong>Max Health Insurance Cover</strong><span>Hospitalisation & Critical Care</span></div>
        <b>₹10 Lakh</b>
      </div>
    </aside>
  </div>
</section>

<!-- Scheme 1: Charity Support Scheme -->
<section id="charity" class="s" style="padding-bottom:0">
  <div class="wrap">
    <div class="head">
      <div>
        <h2>Charity Support Scheme</h2>
        <p>Comprehensive health protection, ongoing family financial support, and emergency disaster assistance.</p>
      </div>
      <span class="badge" style="background:rgba(183,133,43,0.15);color:var(--gold);font-size:14px;padding:6px 14px">Tier: ₹10,000 & Above</span>
    </div>

    <div class="split">
      <article class="scheme">
        <span class="tag">Support Tier from ₹10,000 · 60 Payout Cycles</span>
        <h3>Monthly Income Aid & EMI Cover</h3>
        <p>Direct financial relief paid monthly for 60 consecutive cycles to member families in need.</p>
        <ul>
          <li><strong>₹3,000 × 60 cycles</strong> guaranteed monthly support payout</li>
          <li>EMI assistance for hospital and medical loans over ₹50,000</li>
          <li>Accidental bereavement grant for immediate dependants</li>
        </ul>
      </article>

      <article class="scheme" style="border-top-color:var(--blue)">
        <span class="tag" style="color:var(--blue)">Comprehensive Medical Insurance</span>
        <h3>Family Health Insurance Policy</h3>
        <p>Full hospitalization coverage protecting member households against critical medical emergencies.</p>
        <ul>
          <li><strong>₹10 Lakh</strong> maximum cover for female members</li>
          <li><strong>₹5 Lakh</strong> maximum cover for male members</li>
          <li>Cashless admission across partnered medical centers</li>
        </ul>
      </article>
    </div>

    <!-- Eligibility Box -->
    <div class="panel" style="margin-top:32px;background:var(--card);border:1px solid var(--line)">
      <h3 style="font-size:1rem;margin-bottom:8px">Eligibility & Claim Process</h3>
      <p style="font-size:14px;margin-bottom:12px;color:var(--muted)">Must be an active Giveoraemart member with verified profile details. Submit valid medical discharge certificates, hospital bills, or emergency documentation directly through your account dashboard or support helpline.</p>
    </div>
  </div>
</section>

<!-- Scheme 2: Vidya Vikas Scheme -->
<section id="vidya-vikas" class="s">
  <div class="wrap">
    <div class="head">
      <div>
        <h2>Vidya Vikas Educational Scheme</h2>
        <p>Empowering young minds through health cover, accident protection, and merit scholarships.</p>
      </div>
      <span class="badge" style="background:rgba(31,138,91,0.15);color:var(--green);font-size:14px;padding:6px 14px">Youth Protection</span>
    </div>

    <div class="split">
      <article class="scheme" style="border-top-color:var(--green)">
        <span class="tag" style="color:var(--green)">Student Health & Accident Cover</span>
        <h3>Student Medical & Safety Net</h3>
        <p>Dedicated medical coverage and accidental death cover for active students and parents.</p>
        <ul>
          <li><strong>₹2 Lakh</strong> health insurance cover for enrolled students</li>
          <li><strong>₹50,000</strong> accidental death protection for students</li>
          <li><strong>₹4 Lakh</strong> accidental death cover for parent policyholder</li>
        </ul>
      </article>

      <article class="scheme" style="border-top-color:var(--blue)">
        <span class="tag" style="color:var(--blue)">Education Grants & Merit Awards</span>
        <h3>Scholarships & Digital Learning</h3>
        <p>Subsidies for tuition fees, digital learning equipment, and academic excellence prizes.</p>
        <ul>
          <li>Tuition & exam fee grants for primary and higher secondary students</li>
          <li>Sponsorship for digital learning tablets and laptops</li>
          <li>Excellence awards for students scoring above 85%</li>
        </ul>
      </article>
    </div>

    <!-- Eligibility Box -->
    <div class="panel" style="margin-top:32px;background:var(--card);border:1px solid var(--line)">
      <h3 style="font-size:1rem;margin-bottom:8px">Who Qualifies & Required Documents</h3>
      <p style="font-size:14px;margin-bottom:12px;color:var(--muted)">Dependant children of registered members enrolled in recognized schools or universities. Upload recent marksheets, student ID cards, and tuition receipts to initiate scholarship or claim requests.</p>
    </div>
  </div>
</section>

<!-- CTA Bar -->
<section class="s welfare" style="padding:48px 0">
  <div class="wrap text-center" style="text-align:center">
    <h2>Ready to join a community that gives back?</h2>
    <p style="color:#c5d1e0;margin:12px auto 24px;max-width:500px">Every repurchase and package bundle purchase directly sustains our community welfare pool.</p>
    <a class="btn" style="background:#fff;color:var(--navy);border-color:#fff" href="/register.php">Register Account Today</a>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
