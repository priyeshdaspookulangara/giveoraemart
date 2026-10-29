<?php
// welfare-schemes.php
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Giveoraemart – Welfare Schemes & Community Protection';
require_once __DIR__ . '/includes/header.php';

$claimSubmitted = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_claim'])) {
    $claimSubmitted = true;
}
?>

<style>
.wf-step-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 20px;
  margin-top: 24px;
}
.wf-step {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: 10px;
  padding: 24px;
  position: relative;
  box-shadow: var(--shadow);
}
.wf-step-num {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: var(--blue);
  color: #fff;
  display: grid;
  place-items: center;
  font-weight: 700;
  font-size: 16px;
  margin-bottom: 14px;
}
.faq-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 20px;
  margin-top: 24px;
}
.faq-item {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: 10px;
  padding: 22px;
}
.faq-item h4 {
  font-size: 1.05rem;
  margin: 0 0 8px;
  color: var(--text);
  font-family: var(--sans);
  font-weight: 600;
}
.faq-item p {
  margin: 0;
  color: var(--muted);
  font-size: 14px;
  line-height: 1.6;
}
</style>

<!-- Hero Section -->
<section class="hero" style="background:var(--navy)">
  <div class="wrap hero-grid">
    <div>
      <h1>Social Upliftment & Community Welfare Protection</h1>
      <p>Commerce meets compassion at Giveoraemart. A dedicated portion of ecosystem earnings powers financial aid, medical coverage, and student scholarship grants for our active community members.</p>
      <div class="cta">
        <a class="btn" href="#charity"><i class="fa-solid fa-hand-holding-heart" style="margin-right:8px"></i> Charity Support Scheme</a>
        <a class="btn ghost" href="#vidya-vikas"><i class="fa-solid fa-graduation-cap" style="margin-right:8px"></i> Vidya Vikas Scheme</a>
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
      <div class="item">
        <div><strong>Student Health & Safety Net</strong><span>Accident & Education Cover</span></div>
        <b style="color:var(--green)">₹2 Lakh</b>
      </div>
    </aside>
  </div>
</section>

<!-- Facts Bar -->
<div class="facts">
  <div class="wrap">
    <div class="fact"><b>₹60K</b><span>Total Charity Support per Member</span></div>
    <div class="fact"><b>₹10 Lakh</b><span>Female Health Cover Limit</span></div>
    <div class="fact"><b>₹5 Lakh</b><span>Male Health Cover Limit</span></div>
    <div class="fact"><b>₹4 Lakh</b><span>Parent Accidental Death Cover</span></div>
  </div>
</div>

<?php if ($claimSubmitted): ?>
  <div class="wrap" style="margin-top:32px">
    <div class="panel" style="background:#eefbf3;border:1px solid #1f8a5b;color:#135e3d">
      <div style="display:flex;align-items:center;gap:14px">
        <i class="fa-solid fa-circle-check" style="font-size:28px;color:#1f8a5b"></i>
        <div>
          <h3 style="margin:0 0 4px;color:#135e3d">Welfare Claim Request Submitted Successfully!</h3>
          <p style="margin:0;font-size:14px">Our welfare committee will review your uploaded documents and contact you within 24–48 business hours. Reference Request ID: <strong>WFR-<?= rand(100000, 999999) ?></strong>.</p>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Scheme 1: Charity Support Scheme -->
<section id="charity" class="s">
  <div class="wrap">
    <div class="head">
      <div>
        <h2>Charity Support Scheme</h2>
        <p>Comprehensive health protection, ongoing family financial support, and emergency disaster assistance.</p>
      </div>
      <span class="badge" style="background:rgba(183,133,43,0.15);color:var(--gold);font-size:14px;padding:6px 14px"><i class="fa-solid fa-ribbon"></i> Tier: ₹10,000 & Above</span>
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
          <li>Emergency household ration kits during natural calamities</li>
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
          <li>Day care procedure reimbursement within 48 hours</li>
        </ul>
      </article>
    </div>

    <!-- Details Grid -->
    <div class="cards" style="margin-top:24px">
      <div class="card">
        <div class="ic"><i class="fa-solid fa-hospital-user"></i></div>
        <h3>Medical Emergency Relief</h3>
        <p>Covers ICU charges, major surgical operations, and post-hospitalization medication costs for active members.</p>
      </div>
      <div class="card">
        <div class="ic"><i class="fa-solid fa-handshake-angle"></i></div>
        <h3>Family Survival Support</h3>
        <p>Monthly financial assistance disbursed directly into the nominated bank account of the primary dependant.</p>
      </div>
      <div class="card">
        <div class="ic"><i class="fa-solid fa-shield-heart"></i></div>
        <h3>Zero Copay Guarantee</h3>
        <p>Approved claims under the scheme are paid 100% without deductions or hidden co-payment fees.</p>
      </div>
    </div>
  </div>
</section>

<!-- Scheme 2: Vidya Vikas Scheme -->
<section id="vidya-vikas" class="s" style="background:var(--card);border-top:1px solid var(--line);border-bottom:1px solid var(--line)">
  <div class="wrap">
    <div class="head">
      <div>
        <h2>Vidya Vikas Educational Scheme</h2>
        <p>Empowering young minds through health cover, accident protection, and merit scholarships.</p>
      </div>
      <span class="badge" style="background:rgba(31,138,91,0.15);color:var(--green);font-size:14px;padding:6px 14px"><i class="fa-solid fa-graduation-cap"></i> Youth & Student Protection</span>
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
          <li>Sports injury medical reimbursement up to ₹25,000</li>
        </ul>
      </article>

      <article class="scheme" style="border-top-color:var(--blue)">
        <span class="tag" style="color:var(--blue)">Education Grants & Merit Awards</span>
        <h3>Scholarships & Digital Learning</h3>
        <p>Subsidies for tuition fees, digital learning equipment, and academic excellence prizes.</p>
        <ul>
          <li>Tuition & exam fee grants for primary and higher secondary students</li>
          <li>Sponsorship for digital learning tablets and laptops</li>
          <li>Excellence awards for students scoring above 85% in Board Exams</li>
          <li>Higher education & vocational skill fee subsidies</li>
        </ul>
      </article>
    </div>

    <div class="cards" style="margin-top:24px">
      <div class="card">
        <div class="ic"><i class="fa-solid fa-book-open-reader"></i></div>
        <h3>School & College Subsidies</h3>
        <p>Direct tuition grants sent to educational institutions to prevent student dropouts due to financial strain.</p>
      </div>
      <div class="card">
        <div class="ic"><i class="fa-solid fa-laptop"></i></div>
        <h3>Digital Tablet Grants</h3>
        <p>Free e-learning devices distributed annually to top-performing students from member households.</p>
      </div>
      <div class="card">
        <div class="ic"><i class="fa-solid fa-award"></i></div>
        <h3>Merit Scholarships</h3>
        <p>Cash prizes of ₹10,000 to ₹50,000 awarded for top rankers in SSLC, PUC, and undergraduate courses.</p>
      </div>
    </div>
  </div>
</section>

<!-- Claim Workflow & Application Steps -->
<section class="s">
  <div class="wrap">
    <div class="head">
      <div>
        <h2>How to File a Claim or Apply</h2>
        <p>Follow four simple steps to request welfare support or scholarship disbursements.</p>
      </div>
    </div>

    <div class="wf-step-grid">
      <div class="wf-step">
        <div class="wf-step-num">1</div>
        <h3 style="font-size:1.1rem;margin:0 0 8px">Verify Profile</h3>
        <p style="font-size:14px;color:var(--muted);margin:0">Ensure your Giveoraemart account profile is active and identity details are updated.</p>
      </div>
      <div class="wf-step">
        <div class="wf-step-num">2</div>
        <h3 style="font-size:1.1rem;margin:0 0 8px">Submit Documents</h3>
        <p style="font-size:14px;color:var(--muted);margin:0">Upload hospital discharge summary, medical bills, or academic marksheets.</p>
      </div>
      <div class="wf-step">
        <div class="wf-step-num">3</div>
        <h3 style="font-size:1.1rem;margin:0 0 8px">Desk Review</h3>
        <p style="font-size:14px;color:var(--muted);margin:0">Our welfare board verifies the documents within 24 to 48 business hours.</p>
      </div>
      <div class="wf-step">
        <div class="wf-step-num">4</div>
        <h3 style="font-size:1.1rem;margin:0 0 8px">Direct Disbursement</h3>
        <p style="font-size:14px;color:var(--muted);margin:0">Approved funds are transferred straight to your bank account or member wallet.</p>
      </div>
    </div>

    <!-- Quick Claim Application Form -->
    <div class="panel" style="margin-top:36px;background:var(--card);border:1px solid var(--line)">
      <h3 style="font-size:1.2rem;margin-bottom:6px">Submit a Welfare Claim or Scholarship Application</h3>
      <p style="font-size:14px;margin-bottom:20px;color:var(--muted)">Fill in the details below to start your application process immediately.</p>

      <form method="POST" action="/welfare-schemes.php">
        <div class="form-grid">
          <div class="form-group">
            <label for="applicant_name">Applicant Full Name</label>
            <input type="text" id="applicant_name" name="applicant_name" placeholder="Enter your full name" required>
          </div>
          <div class="form-group">
            <label for="scheme_type">Select Scheme</label>
            <select id="scheme_type" name="scheme_type" required>
              <option value="Charity Support - Monthly Aid">Charity Support - Monthly Aid</option>
              <option value="Charity Support - Medical Insurance Claim">Charity Support - Medical Insurance Claim</option>
              <option value="Vidya Vikas - Student Health Cover">Vidya Vikas - Student Health Cover</option>
              <option value="Vidya Vikas - Merit Scholarship">Vidya Vikas - Merit Scholarship</option>
            </select>
          </div>
          <div class="form-group">
            <label for="phone">Contact Mobile Number</label>
            <input type="tel" id="phone" name="phone" placeholder="Enter 10-digit mobile number" required>
          </div>
          <div class="form-group">
            <label for="claim_amount">Estimated Claim Amount (₹)</label>
            <input type="number" id="claim_amount" name="claim_amount" placeholder="e.g. 25000" required>
          </div>
        </div>
        <div class="form-group" style="margin-bottom:20px">
          <label for="details">Claim Reason & Supporting Details</label>
          <textarea id="details" name="details" rows="3" style="width:100%;padding:10px;border-radius:8px;border:1px solid var(--line);background:var(--bg);color:var(--text)" placeholder="Provide details regarding medical condition, hospital name, or academic score..." required></textarea>
        </div>
        <button type="submit" name="submit_claim" class="btn">
          <i class="fa-solid fa-paper-plane" style="margin-right:8px"></i> Submit Application Request
        </button>
      </form>
    </div>

  </div>
</section>

<!-- Frequently Asked Questions -->
<section class="s" style="background:var(--bg);border-top:1px solid var(--line)">
  <div class="wrap">
    <div class="head">
      <div>
        <h2>Frequently Asked Questions</h2>
        <p>Everything you need to know about eligibility, payouts, and coverage limits.</p>
      </div>
    </div>

    <div class="faq-grid">
      <div class="faq-item">
        <h4>Who is eligible for the Charity Support Scheme?</h4>
        <p>All active registered members of Giveoraemart who maintain an active repurchase account or package package subscription qualify automatically.</p>
      </div>
      <div class="faq-item">
        <h4>How is the ₹3,000 monthly support paid out?</h4>
        <p>The support payout is credited directly into the member's registered bank account on the 1st of every month for up to 60 consecutive cycles.</p>
      </div>
      <div class="faq-item">
        <h4>Can I claim student health cover for multiple children?</h4>
        <p>Yes, all dependent children listed in the primary member's verified profile are covered under the Vidya Vikas Scheme up to ₹2 Lakh each.</p>
      </div>
      <div class="faq-item">
        <h4>What documents are required for hospital claim reimbursement?</h4>
        <p>You need to submit hospital discharge summary, itemized original medical bills, payment receipts, and doctor prescription notes.</p>
      </div>
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
